<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\Chirp\Support\TimelineCache;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserDiscoveryController extends Controller
{
    public function index(Request $request): Response
    {
        $query = $request->string('q')->toString();
        $authUser = $request->user();
        $followingIds = $authUser->following()->pluck('users.id')->all();

        $users = User::query()
            ->where('id', '!=', $authUser->id)
            ->when($query !== '', function ($q) use ($query): void {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(30)
            ->get(['id', 'name', 'email'])
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_following' => in_array($user->id, $followingIds, true),
            ])
            ->values();

        return Inertia::render('users/index', [
            'filters' => ['q' => $query],
            'users' => $users,
        ]);
    }

    public function follow(Request $request, User $user): RedirectResponse
    {
        $authUser = $request->user();

        if ($authUser->id !== $user->id) {
            $authUser->following()->syncWithoutDetaching([$user->id]);
            TimelineCache::flushForUser($authUser->id);
        }

        return back();
    }

    public function unfollow(Request $request, User $user): RedirectResponse
    {
        $authUser = $request->user();

        if ($authUser->id !== $user->id) {
            $authUser->following()->detach($user->id);
            TimelineCache::flushForUser($authUser->id);
        }

        return back();
    }
}
