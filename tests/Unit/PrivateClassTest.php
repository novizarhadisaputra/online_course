<?php

use App\Models\PrivateClass;
use App\Models\PrivateClassItem;
use App\Models\Course;
use App\Models\Price;
use App\Models\Metadata;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('private class can be created with valid data', function () {
    $privateClass = PrivateClass::factory()->create([
        'name' => 'Test Private Class',
        'slug' => 'test-private-class',
        'status' => true,
        'is_paid' => true,
        'max_participants' => 10
    ]);

    expect($privateClass->name)->toBe('Test Private Class');
    expect($privateClass->slug)->toBe('test-private-class');
    expect($privateClass->status)->toBeTrue();
    expect($privateClass->is_paid)->toBeTrue();
    expect($privateClass->max_participants)->toBe(10);
});

test('private class has many private class items', function () {
    $privateClass = PrivateClass::factory()->create();
    $course = Course::factory()->create();
    
    $item = PrivateClassItem::factory()->create([
        'private_class_id' => $privateClass->id,
        'model_id' => $course->id,
        'model_type' => Course::class,
        'order' => 1
    ]);

    expect($privateClass->items)->toHaveCount(1);
    expect($privateClass->items->first()->id)->toBe($item->id);
});

test('private class can have many courses through items', function () {
    $privateClass = PrivateClass::factory()->create();
    $course1 = Course::factory()->create();
    $course2 = Course::factory()->create();
    
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

    expect($privateClass->courses)->toHaveCount(2);
    expect($privateClass->courses->pluck('id')->toArray())->toContain($course1->id, $course2->id);
});

test('private class can have price relationship', function () {
    $privateClass = PrivateClass::factory()->create();
    $price = Price::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'amount' => 100000
    ]);

    expect($privateClass->price)->not->toBeNull();
    expect($privateClass->price->amount)->toBe(100000);
});

test('private class can have metadata relationship', function () {
    $privateClass = PrivateClass::factory()->create();
    $metadata = Metadata::factory()->create([
        'model_id' => $privateClass->id,
        'model_type' => PrivateClass::class,
        'data' => ['key' => 'value']
    ]);

    expect($privateClass->metadata)->not->toBeNull();
    expect($privateClass->metadata->data)->toBe(['key' => 'value']);
});

test('private class active scope returns only active classes', function () {
    PrivateClass::factory()->create(['status' => true]);
    PrivateClass::factory()->create(['status' => false]);
    PrivateClass::factory()->create(['status' => true]);

    $activeClasses = PrivateClass::active()->get();
    
    expect($activeClasses)->toHaveCount(2);
    expect($activeClasses->every(fn($class) => $class->status === true))->toBeTrue();
});

test('private class paid scope returns only paid classes', function () {
    PrivateClass::factory()->create(['is_paid' => true]);
    PrivateClass::factory()->create(['is_paid' => false]);
    PrivateClass::factory()->create(['is_paid' => true]);

    $paidClasses = PrivateClass::paid()->get();
    
    expect($paidClasses)->toHaveCount(2);
    expect($paidClasses->every(fn($class) => $class->is_paid === true))->toBeTrue();
});

test('private class free scope returns only free classes', function () {
    PrivateClass::factory()->create(['is_paid' => true]);
    PrivateClass::factory()->create(['is_paid' => false]);
    PrivateClass::factory()->create(['is_paid' => false]);

    $freeClasses = PrivateClass::free()->get();
    
    expect($freeClasses)->toHaveCount(2);
    expect($freeClasses->every(fn($class) => $class->is_paid === false))->toBeTrue();
});

test('private class item belongs to private class', function () {
    $privateClass = PrivateClass::factory()->create();
    $course = Course::factory()->create();
    
    $item = PrivateClassItem::factory()->create([
        'private_class_id' => $privateClass->id,
        'model_id' => $course->id,
        'model_type' => Course::class
    ]);

    expect($item->privateClass->id)->toBe($privateClass->id);
});

test('private class item has polymorphic model relationship', function () {
    $privateClass = PrivateClass::factory()->create();
    $course = Course::factory()->create();
    
    $item = PrivateClassItem::factory()->create([
        'private_class_id' => $privateClass->id,
        'model_id' => $course->id,
        'model_type' => Course::class
    ]);

    expect($item->model)->toBeInstanceOf(Course::class);
    expect($item->model->id)->toBe($course->id);
});

test('private class can be cast to array with proper attributes', function () {
    $privateClass = PrivateClass::factory()->create([
        'start_date' => '2024-01-01 10:00:00',
        'end_date' => '2024-01-31 18:00:00'
    ]);

    $array = $privateClass->toArray();
    
    expect($array)->toHaveKeys([
        'id', 'name', 'slug', 'short_description', 'description',
        'duration', 'duration_units', 'max_participants',
        'start_date', 'end_date', 'status', 'is_paid',
        'created_at', 'updated_at'
    ]);
});