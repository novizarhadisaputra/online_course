<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\PrivateClass;
use App\Models\Price;
use App\Models\Cart;
use App\Models\ConfigApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class PrivateClassCartTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected PrivateClass $privateClass;
    protected Price $price;
    protected ConfigApp $configApp;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create();
        
        // Create config app
        $this->configApp = ConfigApp::factory()->create([
            'tax_fee' => 10
        ]);
        
        // Create price
        $this->price = Price::factory()->create([
            'qty' => 1,
            'units' => 'person',
            'value' => 100000,
            'description' => 'Standard price'
        ]);
        
        // Create private class
        $this->privateClass = PrivateClass::factory()->create([
            'name' => 'Test Private Class',
            'slug' => 'test-private-class',
            'status' => true,
            'is_paid' => true,
            'max_participants' => 10,
            'price_id' => $this->price->id
        ]);
    }

    /** @test */
    public function it_can_add_new_private_class_to_empty_cart()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 2,
            'price_id' => $this->price->id
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

        // Verify cart item was created in database
        $this->assertDatabaseHas('carts', [
            'model_id' => $this->privateClass->id,
            'model_type' => PrivateClass::class,
            'qty' => 2,
            'tax_fee' => $this->configApp->tax_fee,
            'price_id' => $this->price->id,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_update_existing_cart_item_quantity()
    {
        Sanctum::actingAs($this->user);

        // First, add item to cart
        Cart::create([
            'model_id' => $this->privateClass->id,
            'model_type' => PrivateClass::class,
            'qty' => 1,
            'tax_fee' => $this->configApp->tax_fee,
            'price_id' => $this->price->id,
            'user_id' => $this->user->id
        ]);

        // Then, update the quantity
        $response = $this->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 3,
            'price_id' => $this->price->id
        ]);

        $response->assertStatus(200);

        // Verify cart item was updated, not duplicated
        $this->assertDatabaseCount('carts', 1);
        $this->assertDatabaseHas('carts', [
            'model_id' => $this->privateClass->id,
            'model_type' => PrivateClass::class,
            'qty' => 3,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_update_existing_cart_item_price()
    {
        Sanctum::actingAs($this->user);

        // Create another price
        $newPrice = Price::factory()->create([
            'qty' => 1,
            'units' => 'person',
            'value' => 150000,
            'description' => 'Premium price'
        ]);

        // First, add item to cart
        Cart::create([
            'model_id' => $this->privateClass->id,
            'model_type' => PrivateClass::class,
            'qty' => 1,
            'tax_fee' => $this->configApp->tax_fee,
            'price_id' => $this->price->id,
            'user_id' => $this->user->id
        ]);

        // Then, update the price
        $response = $this->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 1,
            'price_id' => $newPrice->id
        ]);

        $response->assertStatus(200);

        // Verify cart item price was updated
        $this->assertDatabaseHas('carts', [
            'model_id' => $this->privateClass->id,
            'model_type' => PrivateClass::class,
            'price_id' => $newPrice->id,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_prevents_adding_zero_quantity_to_cart()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 0,
            'price_id' => $this->price->id
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['qty']);
    }

    /** @test */
    public function it_prevents_adding_negative_quantity_to_cart()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => -1,
            'price_id' => $this->price->id
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['qty']);
    }

    /** @test */
    public function it_prevents_adding_non_numeric_quantity_to_cart()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 'invalid',
            'price_id' => $this->price->id
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['qty']);
    }

    /** @test */
    public function it_prevents_adding_invalid_uuid_to_cart()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/private-classes/cart', [
            'id' => 'invalid-uuid',
            'qty' => 1,
            'price_id' => $this->price->id
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['id']);
    }

    /** @test */
    public function it_prevents_adding_non_existent_private_class_to_cart()
    {
        Sanctum::actingAs($this->user);

        $fakeUuid = $this->faker->uuid();

        $response = $this->postJson('/api/private-classes/cart', [
            'id' => $fakeUuid,
            'qty' => 1,
            'price_id' => $this->price->id
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['id']);
    }

    /** @test */
    public function it_prevents_adding_non_existent_price_to_cart()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 1,
            'price_id' => 99999
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['price_id']);
    }

    /** @test */
    public function it_handles_database_transaction_rollback_on_error()
    {
        Sanctum::actingAs($this->user);

        // Delete config app to trigger error
        $this->configApp->delete();

        $response = $this->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 1,
            'price_id' => $this->price->id
        ]);

        $response->assertStatus(422);

        // Verify no cart item was created due to rollback
        $this->assertDatabaseCount('carts', 0);
    }

    /** @test */
    public function it_maintains_separate_carts_for_different_users()
    {
        $anotherUser = User::factory()->create();

        // Add item to first user's cart
        Sanctum::actingAs($this->user);
        $this->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 1,
            'price_id' => $this->price->id
        ]);

        // Add item to second user's cart
        Sanctum::actingAs($anotherUser);
        $this->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 2,
            'price_id' => $this->price->id
        ]);

        // Verify both cart items exist separately
        $this->assertDatabaseCount('carts', 2);
        $this->assertDatabaseHas('carts', [
            'user_id' => $this->user->id,
            'qty' => 1
        ]);
        $this->assertDatabaseHas('carts', [
            'user_id' => $anotherUser->id,
            'qty' => 2
        ]);
    }

    /** @test */
    public function it_can_handle_multiple_private_classes_in_same_cart()
    {
        Sanctum::actingAs($this->user);

        // Create another private class
        $anotherPrivateClass = PrivateClass::factory()->create([
            'name' => 'Another Private Class',
            'slug' => 'another-private-class',
            'status' => true,
            'is_paid' => true,
            'price_id' => $this->price->id
        ]);

        // Add first private class to cart
        $this->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 1,
            'price_id' => $this->price->id
        ]);

        // Add second private class to cart
        $this->postJson('/api/private-classes/cart', [
            'id' => $anotherPrivateClass->id,
            'qty' => 2,
            'price_id' => $this->price->id
        ]);

        // Verify both items exist in cart
        $this->assertDatabaseCount('carts', 2);
        $this->assertDatabaseHas('carts', [
            'model_id' => $this->privateClass->id,
            'user_id' => $this->user->id
        ]);
        $this->assertDatabaseHas('carts', [
            'model_id' => $anotherPrivateClass->id,
            'user_id' => $this->user->id
        ]);
    }
}