<?php

namespace App\Application\Chirp\Support;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TimelineCache
{
    public const TTL_SECONDS = 60;

    public static function key(int $userId, string $feed): string
    {
        return "timeline:{$userId}:{$feed}";
    }

    /**
     * @param Closure(): Collection<int, mixed> $resolver
     * @return Collection<int, mixed>
     */
    public static function remember(int $userId, string $feed, Closure $resolver): Collection
    {
        $key = self::key($userId, $feed);
        $cached = Cache::get($key);

        if ($cached instanceof Collection && ! self::containsIncompleteClass($cached)) {
            /** @var Collection<int, mixed> $cached */
            return $cached;
        }

        Cache::forget($key);

        /** @var Collection<int, mixed> $items */
        $items = $resolver();
        Cache::put($key, $items, self::TTL_SECONDS);

        return $items;
    }

    public static function flushForUser(int $userId): void
    {
        Cache::forget(self::key($userId, 'fyp'));
        Cache::forget(self::key($userId, 'following'));
    }

    /**
     * @param Collection<int, mixed> $items
     */
    private static function containsIncompleteClass(Collection $items): bool
    {
        foreach ($items as $item) {
            if (is_object($item) && get_class($item) === '__PHP_Incomplete_Class') {
                return true;
            }
        }

        return false;
    }
}
