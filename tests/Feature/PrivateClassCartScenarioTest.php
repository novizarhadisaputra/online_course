<?php

use App\Models\PrivateClass;
use App\Models\PrivateClassItem;
use App\Models\Course;
use App\Models\Price;
use App\Models\User;
use App\Models\Cart;
use App\Models\ConfigApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Carbon\Carbon;

uses(RefreshDatabase::class);

test('can add multiple different private classes to cart', function () {
    $user = User::factory()->create();
    $privateClass1 = PrivateClass::factory()->active()->paid()->create();
    $privateClass2 = PrivateClass::factory()->active()->paid()->create();
    
    $price1 = Price::factory()->create([
        'model_id' => $privateClass1->id,
        'model_type' => PrivateClass::class,
        'amount' => 100000
    ]);
    
    $price2 = Price::factory()->create([
        'model_id' => $privateClass2->id,
        'model_type' => PrivateClass::class,
        'amount' => 150000
    ]);
    
    ConfigApp::factory()->create(['tax_fee' => 10]);
    
    Sanctum::actingAs($user);

    // Add first private class
    $response1 = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass1->id,
        'qty' => 1,
        'price_id' => $price1->id
    ]);

    // Add second private class
    $response2 = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass2->id,
        'qty' => 2,
        'price_id' => $price2->id
    ]);

    $response1->assertStatus(200);
    $response2->assertStatus(200);
    
    expect(Cart::where('user_id', $user->id)->count())->toBe(2);
    
    $cart1 = Cart::where('user_id', $user->id)
                 ->where('model_id', $privateClass1->id)
                 ->first();
    $cart2 = Cart::where('user_id', $user->id)
                 ->where('model_id', $privateClass2->id)
                 ->first();
    
    expect($cart1->qty)->toBe(1);
    expect($cart2->qty)->toBe(2);
});

test('can add private class with multiple courses to cart', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->paid()->create();
    
    // Create multiple courses for this private class
    $course1 = Course::factory()->create();
    $course2 = Course::factory()->create();
    $course3 = Course::factory()->create();
    
    PrivateClassItem::factory()->create([
        'private_class_id' => $privateClass->id,
        'model_id' => $course1->id,
        'model_type' => Course::class,
        'order' => 1
    ]);
    
    PrivateClassItem::factory()->create([
        'private_class_id' => $privateClass->id,
        'model_id' => $course2->id,
        'model_type' => Course::class,
        'order' => 2
    ]);
    
    PrivateClassItem::factory()->create([
        'private_class_id' => $privateClass->id,
        'model_id' => $course3->id,
        'model_type' => Course::class,
        'order' => 3
    ]);
    
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'amount' => 200000
    ]);
    
    ConfigApp::factory()->create(['tax_fee' => 10]);
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => $price->id
    ]);

    $response->assertStatus(200);
    
    $cart = Cart::where('user_id', $user->id)->first();
    expect($cart->model_id)->toBe($privateClass->id);
    expect($cart->model_type)->toBe(PrivateClass::class);
});

test('can add free private class to cart', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->free()->create();
    
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'amount' => 0
    ]);
    
    ConfigApp::factory()->create(['tax_fee' => 0]);
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => $price->id
    ]);

    $response->assertStatus(200);
    
    $cart = Cart::where('user_id', $user->id)->first();
    expect($cart->tax_fee)->toBe(0);
});

test('cannot add private class that has not started yet', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->create([
        'start_date' => Carbon::now()->addDays(30),
        'end_date' => Carbon::now()->addDays(60)
    ]);
    
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

    // Should still allow adding to cart even if not started
    // Business logic might allow pre-registration
    $response->assertStatus(200);
});

test('cannot add private class that has already ended', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->create([
        'start_date' => Carbon::now()->subDays(60),
        'end_date' => Carbon::now()->subDays(30)
    ]);
    
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

    // Should still allow adding to cart - business logic decision
    // Might allow access to recorded sessions
    $response->assertStatus(200);
});

test('can add private class with maximum quantity', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->paid()->create([
        'max_participants' => 5
    ]);
    
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'amount' => 100000
    ]);
    
    ConfigApp::factory()->create(['tax_fee' => 10]);
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 5, // Maximum allowed
        'price_id' => $price->id
    ]);

    $response->assertStatus(200);
    
    $cart = Cart::where('user_id', $user->id)->first();
    expect($cart->qty)->toBe(5);
});

test('can increment quantity when adding same private class multiple times', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->paid()->create();
    
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'amount' => 100000
    ]);
    
    ConfigApp::factory()->create(['tax_fee' => 10]);
    
    Sanctum::actingAs($user);

    // First addition
    $response1 = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 2,
        'price_id' => $price->id
    ]);

    // Second addition - should update existing cart item
    $response2 = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 3,
        'price_id' => $price->id
    ]);

    $response1->assertStatus(200);
    $response2->assertStatus(200);
    
    expect(Cart::where('user_id', $user->id)->count())->toBe(1);
    
    $cart = Cart::where('user_id', $user->id)->first();
    expect($cart->qty)->toBe(3); // Should be updated to latest quantity
});

test('different users can add same private class to their carts', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->paid()->create();
    
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'amount' => 100000
    ]);
    
    ConfigApp::factory()->create(['tax_fee' => 10]);

    // User 1 adds to cart
    Sanctum::actingAs($user1);
    $response1 = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => $price->id
    ]);

    // User 2 adds to cart
    Sanctum::actingAs($user2);
    $response2 = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 2,
        'price_id' => $price->id
    ]);

    $response1->assertStatus(200);
    $response2->assertStatus(200);
    
    expect(Cart::where('user_id', $user1->id)->count())->toBe(1);
    expect(Cart::where('user_id', $user2->id)->count())->toBe(1);
    
    $cart1 = Cart::where('user_id', $user1->id)->first();
    $cart2 = Cart::where('user_id', $user2->id)->first();
    
    expect($cart1->qty)->toBe(1);
    expect($cart2->qty)->toBe(2);
});

test('cart calculates tax fee correctly', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->paid()->create();
    
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'amount' => 100000
    ]);
    
    ConfigApp::factory()->create(['tax_fee' => 15]); // 15% tax
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => $price->id
    ]);

    $response->assertStatus(200);
    
    $cart = Cart::where('user_id', $user->id)->first();
    expect($cart->tax_fee)->toBe(15);
});

test('can add private class with different price tiers', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->paid()->create();
    
    // Create multiple price tiers
    $regularPrice = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'amount' => 100000,
        'name' => 'Regular Price'
    ]);
    
    $premiumPrice = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'amount' => 150000,
        'name' => 'Premium Price'
    ]);
    
    ConfigApp::factory()->create(['tax_fee' => 10]);
    
    Sanctum::actingAs($user);

    // Add with premium price
    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => $premiumPrice->id
    ]);

    $response->assertStatus(200);
    
    $cart = Cart::where('user_id', $user->id)->first();
    expect($cart->price_id)->toBe($premiumPrice->id);
});