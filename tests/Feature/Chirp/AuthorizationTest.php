<?php

use App\Models\Chirp;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user cannot update chirp from another user', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $chirp = Chirp::query()->create([
        'user_id' => $owner->id,
        'title' => 'Original',
        'description' => 'Contenido',
    ]);

    $response = $this->actingAs($attacker)->patch(route('jumper.update', $chirp), [
        'title' => 'Hack',
        'description' => 'Intento',
    ]);

    $response->assertForbidden();

    expect($chirp->fresh()?->title)->toBe('Original');
});

test('user cannot delete chirp from another user', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $chirp = Chirp::query()->create([
        'user_id' => $owner->id,
        'title' => 'Original',
        'description' => 'Contenido',
    ]);

    $response = $this->actingAs($attacker)->delete(route('jumper.destroy', $chirp));

    $response->assertForbidden();
    $this->assertDatabaseHas('chirps', ['id' => $chirp->id]);
});

test('owner can update own chirp', function () {
    $owner = User::factory()->create();

    $chirp = Chirp::query()->create([
        'user_id' => $owner->id,
        'title' => 'Original',
        'description' => 'Contenido',
    ]);

    $response = $this->actingAs($owner)->patch(route('jumper.update', $chirp), [
        'title' => 'Actualizado',
        'description' => 'Nuevo contenido',
    ]);

    $response->assertRedirect(route('jumper.index'));

    expect($chirp->fresh()?->title)->toBe('Actualizado')
        ->and($chirp->fresh()?->description)->toBe('Nuevo contenido');
});

test('owner can delete own chirp', function () {
    $owner = User::factory()->create();

    $chirp = Chirp::query()->create([
        'user_id' => $owner->id,
        'title' => 'Original',
        'description' => 'Contenido',
    ]);

    $response = $this->actingAs($owner)->delete(route('jumper.destroy', $chirp));

    $response->assertRedirect(route('jumper.index'));
    $this->assertDatabaseMissing('chirps', ['id' => $chirp->id]);
});
