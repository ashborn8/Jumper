<?php

namespace App\Providers;

use App\Domain\Chirp\Repositories\ChirpRepositoryInterface;
use App\Domain\Chirp\Events\ChirpCreated;
use App\Domain\Chirp\Events\ChirpDeleted;
use App\Domain\Chirp\Events\ChirpUpdated;
use App\Application\Chirp\Listeners\ProcessChirpCreated;
use App\Application\Chirp\Listeners\ProcessChirpDeleted;
use App\Application\Chirp\Listeners\ProcessChirpUpdated;
use App\Infrastructure\Chirp\Repositories\EloquentChirpRepository;
use App\Infrastructure\Auth\LaravelCurrentUserProvider;
use App\Interfaces\Http\Auth\CurrentUserProvider;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ChirpRepositoryInterface::class, EloquentChirpRepository::class);
        $this->app->bind(CurrentUserProvider::class, LaravelCurrentUserProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerDomainEventListeners();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Register asynchronous listeners for chirp domain events.
     */
    protected function registerDomainEventListeners(): void
    {
        Event::listen(ChirpCreated::class, ProcessChirpCreated::class);
        Event::listen(ChirpUpdated::class, ProcessChirpUpdated::class);
        Event::listen(ChirpDeleted::class, ProcessChirpDeleted::class);
    }
}
