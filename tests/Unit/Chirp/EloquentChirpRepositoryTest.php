<?php

use App\Application\Chirp\DTOs\ChirpView;
use App\Domain\Chirp\ValueObjects\ChirpId;
use App\Domain\Shared\ValueObjects\UserId;
use App\Infrastructure\Chirp\Repositories\EloquentChirpRepository;
use App\Models\Chirp;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('repository returns chirp views for current user', function () {
    $user = User::factory()->create(['name' => 'Alice']);
    $other = User::factory()->create(['name' => 'Bob']);

    Chirp::query()->create([
        'user_id' => $user->id,
        'title' => 'Mi chirp',
        'description' => 'Descripcion',
    ]);

    Chirp::query()->create([
        'user_id' => $other->id,
        'title' => 'Otro chirp',
        'description' => 'No debe aparecer',
    ]);

    $repo = new EloquentChirpRepository();
    $views = $repo->forUser(new UserId($user->id));

    expect($views)->toHaveCount(1)
        ->and($views->first())->toBeInstanceOf(ChirpView::class)
        ->and($views->first()->userName)->toBe('Alice');
});

test('repository only updates and deletes chirp for owner', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $chirp = Chirp::query()->create([
        'user_id' => $owner->id,
        'title' => 'Original',
        'description' => 'Descripcion',
    ]);

    $repo = new EloquentChirpRepository();

    $repo->updateForUser(
        new ChirpId($chirp->id),
        new UserId($attacker->id),
        'Hack',
        'No autorizado',
    );

    expect($chirp->fresh()?->title)->toBe('Original');

    $repo->deleteForUser(
        new ChirpId($chirp->id),
        new UserId($attacker->id),
    );

    $this->assertDatabaseHas('chirps', ['id' => $chirp->id]);

    $repo->deleteForUser(
        new ChirpId($chirp->id),
        new UserId($owner->id),
    );

    $this->assertDatabaseMissing('chirps', ['id' => $chirp->id]);
});
