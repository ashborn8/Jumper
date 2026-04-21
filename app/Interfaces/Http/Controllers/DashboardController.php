<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\Chirp\Handlers\Query\ListRecentChirpsQueryHandler;
use App\Application\Chirp\Queries\ListRecentChirpsQuery;
use App\Application\Chirp\DTOs\ChirpView;
use App\Http\Controllers\Controller;
use App\Interfaces\Http\Auth\CurrentUserProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly CurrentUserProvider $currentUserProvider,
        private readonly ListRecentChirpsQueryHandler $listRecentChirpsQueryHandler,
    ) {
    }

    /**
     * Show the dashboard page with recent chirps.
     */
    public function index(Request $request): Response
    {
        $feed = $request->string('feed')->toString();
        $feed = in_array($feed, ['fyp', 'following'], true) ? $feed : 'fyp';

        $chirps = $this->listRecentChirpsQueryHandler->handle(
            new ListRecentChirpsQuery(
                userId: $this->currentUserProvider->id(),
                feed: $feed,
            ),
        );

        return Inertia::render('Dashboard', [
            'feed' => $feed,
            'chirps' => $chirps->map(
                fn (ChirpView $chirp): array => $chirp->toArray(),
            )->values(),
        ]);
    }
}
