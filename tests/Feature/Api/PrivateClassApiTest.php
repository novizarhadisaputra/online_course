<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\PrivateClass;
use App\Models\Price;
use App\Models\ConfigApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class PrivateClassApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected PrivateClass $privateClass;
    protected Price $price;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create();
        
        // Create config app
        ConfigApp::factory()->create([
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
            'price_id' => $this->price->id
        ]);
    }

    /** @test */
    public function it_can_get_private_classes_list_with_english_locale()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'en',
            'Accept' => 'application/json'
        ])->getJson('/api/private-classes');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'image',
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
                    'pagination'
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Private classes retrieved successfully'
                ]);
    }

    /** @test */
    public function it_can_get_private_classes_list_with_indonesian_locale()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'id',
            'Accept' => 'application/json'
        ])->getJson('/api/private-classes');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Daftar kelas privat berhasil diambil'
                ]);
    }

    /** @test */
    public function it_can_get_private_class_detail_with_english_locale()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'en',
            'Accept' => 'application/json'
        ])->getJson("/api/private-classes/{$this->privateClass->slug}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'slug',
                        'image',
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
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Private class details retrieved successfully',
                    'data' => [
                        'id' => $this->privateClass->id,
                        'name' => $this->privateClass->name,
                        'slug' => $this->privateClass->slug
                    ]
                ]);
    }

    /** @test */
    public function it_can_get_private_class_detail_with_indonesian_locale()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'id',
            'Accept' => 'application/json'
        ])->getJson("/api/private-classes/{$this->privateClass->slug}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Detail kelas privat berhasil diambil'
                ]);
    }

    /** @test */
    public function it_returns_not_found_error_with_english_locale()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'en',
            'Accept' => 'application/json'
        ])->getJson('/api/private-classes/non-existent-slug');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug'])
                ->assertJsonFragment([
                    'slug' => ['Private class not found']
                ]);
    }

    /** @test */
    public function it_returns_not_found_error_with_indonesian_locale()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'id',
            'Accept' => 'application/json'
        ])->getJson('/api/private-classes/non-existent-slug');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug'])
                ->assertJsonFragment([
                    'slug' => ['Kelas privat tidak ditemukan']
                ]);
    }

    /** @test */
    public function authenticated_user_can_add_private_class_to_cart_with_english_locale()
    {
        Sanctum::actingAs($this->user);

        $response = $this->withHeaders([
            'Accept-Language' => 'en',
            'Accept' => 'application/json'
        ])->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 1,
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
                        'user_id',
                        'created_at',
                        'updated_at'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Private class added to cart successfully'
                ]);
    }

    /** @test */
    public function authenticated_user_can_add_private_class_to_cart_with_indonesian_locale()
    {
        Sanctum::actingAs($this->user);

        $response = $this->withHeaders([
            'Accept-Language' => 'id',
            'Accept' => 'application/json'
        ])->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 1,
            'price_id' => $this->price->id
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Kelas privat berhasil ditambahkan ke keranjang'
                ]);
    }

    /** @test */
    public function it_validates_required_fields_with_english_locale()
    {
        Sanctum::actingAs($this->user);

        $response = $this->withHeaders([
            'Accept-Language' => 'en',
            'Accept' => 'application/json'
        ])->postJson('/api/private-classes/cart', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['id', 'qty', 'price_id']);
    }

    /** @test */
    public function it_validates_required_fields_with_indonesian_locale()
    {
        Sanctum::actingAs($this->user);

        $response = $this->withHeaders([
            'Accept-Language' => 'id',
            'Accept' => 'application/json'
        ])->postJson('/api/private-classes/cart', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['id', 'qty', 'price_id']);
    }

    /** @test */
    public function it_prevents_adding_inactive_private_class_to_cart_with_english_locale()
    {
        Sanctum::actingAs($this->user);
        
        $inactivePrivateClass = PrivateClass::factory()->create([
            'status' => false,
            'price_id' => $this->price->id
        ]);

        $response = $this->withHeaders([
            'Accept-Language' => 'en',
            'Accept' => 'application/json'
        ])->postJson('/api/private-classes/cart', [
            'id' => $inactivePrivateClass->id,
            'qty' => 1,
            'price_id' => $this->price->id
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['id'])
                ->assertJsonFragment([
                    'id' => ['Private class is inactive']
                ]);
    }

    /** @test */
    public function it_prevents_adding_inactive_private_class_to_cart_with_indonesian_locale()
    {
        Sanctum::actingAs($this->user);
        
        $inactivePrivateClass = PrivateClass::factory()->create([
            'status' => false,
            'price_id' => $this->price->id
        ]);

        $response = $this->withHeaders([
            'Accept-Language' => 'id',
            'Accept' => 'application/json'
        ])->postJson('/api/private-classes/cart', [
            'id' => $inactivePrivateClass->id,
            'qty' => 1,
            'price_id' => $this->price->id
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['id'])
                ->assertJsonFragment([
                    'id' => ['Kelas privat tidak aktif']
                ]);
    }

    /** @test */
    public function it_can_filter_private_classes_by_status()
    {
        // Create inactive private class
        PrivateClass::factory()->create([
            'status' => false
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->getJson('/api/private-classes?status=1');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        foreach ($data as $item) {
            $this->assertTrue($item['status']);
        }
    }

    /** @test */
    public function it_can_filter_private_classes_by_payment_type()
    {
        // Create free private class
        PrivateClass::factory()->create([
            'is_paid' => false
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->getJson('/api/private-classes?is_paid=1');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        foreach ($data as $item) {
            $this->assertTrue($item['is_paid']);
        }
    }

    /** @test */
    public function it_can_search_private_classes()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->getJson('/api/private-classes?search=Test');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertStringContainsString('Test', $data[0]['name']);
    }

    /** @test */
    public function it_can_sort_private_classes()
    {
        // Create another private class
        PrivateClass::factory()->create([
            'name' => 'Another Private Class',
            'created_at' => now()->subDay()
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->getJson('/api/private-classes?sort_by=name&sort_order=asc');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertEquals('Another Private Class', $data[0]['name']);
    }

    /** @test */
    public function unauthenticated_user_cannot_add_to_cart()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->postJson('/api/private-classes/cart', [
            'id' => $this->privateClass->id,
            'qty' => 1,
            'price_id' => $this->price->id
        ]);

        $response->assertStatus(401);
    }
}