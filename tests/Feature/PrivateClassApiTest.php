<?php

use App\Models\PrivateClass;
use App\Models\PrivateClassItem;
use App\Models\Course;
use App\Models\Price;
use App\Models\Metadata;
use App\Models\User;
use App\Models\Cart;
use App\Models\ConfigApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('can get list of private classes', function () {
    // Create test data
    $activeClass = PrivateClass::factory()->active()->create();
    $inactiveClass = PrivateClass::factory()->inactive()->create();
    $paidClass = PrivateClass::factory()->paid()->create();
    $freeClass = PrivateClass::factory()->free()->create();

    $response = $this->getJson('/api/private-classes');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     '*' => [
                         'id',
                         'name',
                         'slug',
                         'short_description',
                         'description',
                         'duration',
                         'duration_units',
                         'max_participants',
                         'start_date',
                         'end_date',
                         'status',
                         'is_paid',
                         'courses',
                         'metadata',
                         'price',
                         'created_at',
                         'updated_at'
                     ]
                 ],
                 'meta'
             ]);
});

test('can filter private classes by status', function () {
    PrivateClass::factory()->active()->create();
    PrivateClass::factory()->inactive()->create();

    $response = $this->getJson('/api/private-classes?status=1');

    $response->assertStatus(200);
    $data = $response->json('data');
    
    expect($data)->toHaveCount(1);
    expect($data[0]['status'])->toBeTrue();
});

test('can filter private classes by payment type', function () {
    PrivateClass::factory()->paid()->create();
    PrivateClass::factory()->free()->create();

    $response = $this->getJson('/api/private-classes?is_paid=1');

    $response->assertStatus(200);
    $data = $response->json('data');
    
    expect($data)->toHaveCount(1);
    expect($data[0]['is_paid'])->toBeTrue();
});

test('can search private classes by name', function () {
    PrivateClass::factory()->create(['name' => 'Laravel Advanced Course']);
    PrivateClass::factory()->create(['name' => 'React Basics']);

    $response = $this->getJson('/api/private-classes?search=Laravel');

    $response->assertStatus(200);
    $data = $response->json('data');
    
    expect($data)->toHaveCount(1);
    expect($data[0]['name'])->toContain('Laravel');
});

test('can sort private classes', function () {
    $class1 = PrivateClass::factory()->create(['name' => 'A Class']);
    $class2 = PrivateClass::factory()->create(['name' => 'B Class']);

    $response = $this->getJson('/api/private-classes?sort_by=name&sort_order=asc');

    $response->assertStatus(200);
    $data = $response->json('data');
    
    expect($data[0]['name'])->toBe('A Class');
    expect($data[1]['name'])->toBe('B Class');
});

test('can get private class detail by slug', function () {
    $privateClass = PrivateClass::factory()->create(['slug' => 'test-private-class']);
    $course = Course::factory()->create();
    
    PrivateClassItem::factory()->create([
        'private_class_id' => $privateClass->id,
        'model_id' => $course->id,
        'model_type' => Course::class,
        'order' => 1
    ]);

    $response = $this->getJson('/api/private-classes/test-private-class');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     'id',
                     'name',
                     'slug',
                     'short_description',
                     'description',
                     'duration',
                     'duration_units',
                     'max_participants',
                     'start_date',
                     'end_date',
                     'status',
                     'is_paid',
                     'courses',
                     'metadata',
                     'price',
                     'created_at',
                     'updated_at'
                 ]
             ]);
    
    expect($response->json('data.slug'))->toBe('test-private-class');
});

test('returns 404 for non-existent private class', function () {
    $response = $this->getJson('/api/private-classes/non-existent-slug');

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['slug']);
});

test('authenticated user can add private class to cart', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->paid()->create();
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'amount' => 100000
    ]);
    
    ConfigApp::factory()->create(['tax_fee' => 10]);
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => $price->id
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     'id',
                     'model_id',
                     'model_type',
                     'qty',
                     'tax_fee',
                     'price_id',
                     'user_id'
                 ]
             ]);
    
    expect(Cart::where('user_id', $user->id)->count())->toBe(1);
});

test('can update existing cart item when adding same private class', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->paid()->create();
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'amount' => 100000
    ]);
    
    ConfigApp::factory()->create(['tax_fee' => 10]);
    
    // Create existing cart item
    Cart::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'user_id' => $user->id,
        'qty' => 1,
        'price_id' => $price->id
    ]);
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 2,
        'price_id' => $price->id
    ]);

    $response->assertStatus(200);
    
    $cart = Cart::where('user_id', $user->id)->first();
    expect($cart->qty)->toBe(2);
    expect(Cart::where('user_id', $user->id)->count())->toBe(1);
});

test('cannot add inactive private class to cart', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->inactive()->create();
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class
    ]);
    
    ConfigApp::factory()->create();
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => $price->id
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['id']);
});

test('cannot add non-existent private class to cart', function () {
    $user = User::factory()->create();
    $price = Price::factory()->create();
    
    ConfigApp::factory()->create();
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => fake()->uuid(),
        'qty' => 1,
        'price_id' => $price->id
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['id']);
});

test('unauthenticated user cannot add private class to cart', function () {
    $privateClass = PrivateClass::factory()->active()->create();
    $price = Price::factory()->create();

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => $price->id
    ]);

    $response->assertStatus(401);
});

test('validates required fields when adding to cart', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['id', 'qty', 'price_id']);
});

test('validates quantity must be at least 1', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->create();
    $price = Price::factory()->create();
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 0,
        'price_id' => $price->id
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['qty']);
});

test('validates price exists', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->create();
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => fake()->uuid()
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['price_id']);
});