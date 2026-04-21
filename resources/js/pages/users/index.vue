<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Search, UserPlus, UserX } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '../../components/ui/button';
import { Input } from '../../components/ui/input';
import { dashboard } from '../../routes';

type DiscoverUser = {
    id: number;
    name: string;
    email: string;
    is_following: boolean;
};

const props = defineProps<{
    filters: { q: string };
    users: DiscoverUser[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'People', href: '/people' },
        ],
    },
});

const form = useForm({ q: props.filters.q ?? '' });
const users = computed(() => props.users ?? []);

function searchUsers() {
    router.get('/people', { q: form.q }, { preserveState: true, replace: true });
}

function follow(userId: number) {
    router.post(`/people/${userId}/follow`, {}, { preserveScroll: true });
}

function unfollow(userId: number) {
    router.delete(`/people/${userId}/follow`, { preserveScroll: true });
}
</script>

<template>
    <div>
        <Head title="People" />
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-6 p-6">
            <div class="rounded-xl border border-sidebar-border/70 bg-card p-4">
                <form class="flex items-center gap-2" @submit.prevent="searchUsers">
                    <Search class="size-4 text-muted-foreground" />
                    <Input
                        v-model="form.q"
                        name="q"
                        placeholder="Buscar por nombre o email"
                        autocomplete="off"
                    />
                    <Button type="submit">Buscar</Button>
                </form>
            </div>

            <div class="grid gap-3">
                <div
                    v-for="user in users"
                    :key="user.id"
                    class="flex items-center justify-between rounded-xl border border-sidebar-border/70 bg-card p-4"
                >
                    <div class="min-w-0">
                        <p class="truncate font-medium">{{ user.name }}</p>
                        <p class="truncate text-sm text-muted-foreground">{{ user.email }}</p>
                    </div>
                    <Button
                        v-if="!user.is_following"
                        type="button"
                        size="sm"
                        class="gap-2"
                        @click="follow(user.id)"
                    >
                        <UserPlus class="size-4" />
                        Follow
                    </Button>
                    <Button
                        v-else
                        type="button"
                        size="sm"
                        variant="outline"
                        class="gap-2"
                        @click="unfollow(user.id)"
                    >
                        <UserX class="size-4" />
                        Unfollow
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
