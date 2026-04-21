<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, defineAsyncComponent } from 'vue';
import { dashboard } from '../routes';
import type { Chirp } from '../types';
import { Button } from '../components/ui/button';

const PageGridShell = defineAsyncComponent(
    () => import('../layouts/app/PageGridShell.vue'),
);
const ChirpTile = defineAsyncComponent(
    () => import('../components/chirp/ChirpTile.vue'),
);

const props = defineProps<{
    chirps: Chirp[] | { data?: Chirp[] } | null;
    feed: 'fyp' | 'following';
    auth: {
        user: {
            id: number;
        };
    };
}>();

const chirpList = computed(() =>
    Array.isArray(props.chirps) ? props.chirps : (props.chirps?.data ?? []),
);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <div>
        <Head title="Dashboard" />
        <PageGridShell :show-tiles="false">
            <div class="border-b border-sidebar-border/70 px-4 py-3">
                <div class="flex items-center gap-2">
                    <Button
                        as-child
                        size="sm"
                        :variant="props.feed === 'fyp' ? 'default' : 'outline'"
                    >
                        <Link :href="dashboard({ query: { feed: 'fyp' } })">
                            For You
                        </Link>
                    </Button>
                    <Button
                        as-child
                        size="sm"
                        :variant="props.feed === 'following' ? 'default' : 'outline'"
                    >
                        <Link
                            :href="dashboard({ query: { feed: 'following' } })"
                        >
                            Following
                        </Link>
                    </Button>
                </div>
            </div>
            <ChirpTile
                :chirps="chirpList"
                :editable="true"
                :current-user-id="props.auth.user.id"
            />
        </PageGridShell>
    </div>
</template>
