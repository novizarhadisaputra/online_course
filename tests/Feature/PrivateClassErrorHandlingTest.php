<?php

use App\Models\PrivateClass;
use App\Models\Price;
use App\Models\User;
use App\Models\ConfigApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

// Test cases for GET /api/private-classes endpoint errors
test('returns empty result when no private classes exist', function () {
    $response = $this->getJson('/api/private-classes');

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'data' => []
             ]);
});

test('handles invalid sort_by parameter gracefully', function () {
    PrivateClass::factory()->create();

    $response = $this->getJson('/api/private-classes?sort_by=invalid_field');

    // Should still return results, ignoring invalid sort
    $response->assertStatus(200);
});

test('handles invalid sort_order parameter gracefully', function () {
    PrivateClass::factory()->create();

    $response = $this->getJson('/api/private-classes?sort_order=invalid_order');

    // Should still return results, defaulting to desc
    $response->assertStatus(200);
});

test('handles invalid status filter gracefully', function () {
    PrivateClass::factory()->create();

    $response = $this->getJson('/api/private-classes?status=invalid_status');

    // Should still return results, ignoring invalid filter
    $response->assertStatus(200);
});

test('handles invalid is_paid filter gracefully', function () {
    PrivateClass::factory()->create();

    $response = $this->getJson('/api/private-classes?is_paid=invalid_paid');

    // Should still return results, ignoring invalid filter
    $response->assertStatus(200);
});

test('handles very long search query', function () {
    PrivateClass::factory()->create();

    $longQuery = Str::repeat('a', 1000);
    $response = $this->getJson('/api/private-classes?search=' . $longQuery);

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'data' => []
             ]);
});

test('handles special characters in search query', function () {
    PrivateClass::factory()->create(['name' => 'Test Class']);

    $specialQuery = '!@#$%^&*()_+-=[]{}|;:,.<>?';
    $response = $this->getJson('/api/private-classes?search=' . urlencode($specialQuery));

    $response->assertStatus(200);
});

// Test cases for GET /api/private-classes/{slug} endpoint errors
test('returns validation error for non-existent slug', function () {
    $response = $this->getJson('/api/private-classes/non-existent-slug');

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['slug'])
             ->assertJsonFragment([
                 'slug' => ['The selected slug is invalid.']
             ]);
});

test('returns validation error for empty slug', function () {
    $response = $this->getJson('/api/private-classes/');

    // This should hit the route not found
    $response->assertStatus(404);
});

test('returns validation error for slug with special characters', function () {
    $invalidSlug = 'invalid@slug#with$special%characters';
    $response = $this->getJson('/api/private-classes/' . $invalidSlug);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['slug']);
});

test('returns validation error for very long slug', function () {
    $longSlug = Str::repeat('a', 300);
    $response = $this->getJson('/api/private-classes/' . $longSlug);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['slug']);
});

// Test cases for POST /api/private-classes/add-to-cart endpoint errors
test('returns 401 when user is not authenticated', function () {
    $privateClass = PrivateClass::factory()->create();
    $price = Price::factory()->create();

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => $price->id
    ]);

    $response->assertStatus(401)
             ->assertJson([
                 'message' => 'Unauthenticated.'
             ]);
});

test('returns validation errors for missing required fields', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['id', 'qty', 'price_id']);
});

test('returns validation error for invalid UUID format', function () {
    $user = User::factory()->create();
    $price = Price::factory()->create();
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => 'invalid-uuid-format',
        'qty' => 1,
        'price_id' => $price->id
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['id']);
});

test('returns validation error for non-existent private class ID', function () {
    $user = User::factory()->create();
    $price = Price::factory()->create();
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => Str::uuid(),
        'qty' => 1,
        'price_id' => $price->id
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['id']);
});

test('returns validation error for inactive private class', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->inactive()->create();
    $price = Price::factory()->create();
    
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

test('returns validation error for zero quantity', function () {
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

test('returns validation error for negative quantity', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->create();
    $price = Price::factory()->create();
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => -1,
        'price_id' => $price->id
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['qty']);
});

test('returns validation error for non-numeric quantity', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->create();
    $price = Price::factory()->create();
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 'invalid',
        'price_id' => $price->id
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['qty']);
});

test('returns validation error for non-existent price ID', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->create();
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => Str::uuid()
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['price_id']);
});

test('returns validation error for invalid price ID format', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->create();
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => 'invalid-price-id'
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['price_id']);
});

test('handles missing ConfigApp gracefully', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->create();
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class
    ]);
    
    // Don't create ConfigApp to test default behavior
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => $price->id
    ]);

    // Should handle gracefully with default tax_fee of 0
    $response->assertStatus(200);
});

test('handles extremely large quantity', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->create();
    $price = Price::factory()->create();
    
    ConfigApp::factory()->create();
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 999999999,
        'price_id' => $price->id
    ]);

    // Should either accept or validate based on business rules
    $response->assertStatus(200);
});

test('handles malformed JSON request', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', 'invalid-json');

    $response->assertStatus(400);
});

test('handles request with extra unexpected fields', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->create();
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class
    ]);
    
    ConfigApp::factory()->create();
    
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => $price->id,
        'unexpected_field' => 'unexpected_value',
        'another_field' => 123
    ]);

    // Should ignore extra fields and process normally
    $response->assertStatus(200);
});

test('handles concurrent requests to add same private class', function () {
    $user = User::factory()->create();
    $privateClass = PrivateClass::factory()->active()->create();
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class
    ]);
    
    ConfigApp::factory()->create();
    
    Sanctum::actingAs($user);

    // Simulate concurrent requests
    $response1 = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 1,
        'price_id' => $price->id
    ]);

    $response2 = $this->postJson('/api/private-classes/add-to-cart', [
        'id' => $privateClass->id,
        'qty' => 2,
        'price_id' => $price->id
    ]);

    $response1->assertStatus(200);
    $response2->assertStatus(200);
    
    // Should have only one cart item with updated quantity
    expect(\App\Models\Cart::where('user_id', $user->id)->count())->toBe(1);
});