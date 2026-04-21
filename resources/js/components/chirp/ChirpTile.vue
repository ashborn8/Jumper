<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { Ellipsis } from 'lucide-vue-next';
import { ref } from 'vue';
import type { Chirp } from '../../types';
import { Button } from '../ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '../ui/dropdown-menu';
import { Input } from '../ui/input';
import { Label } from '../ui/label';

const props = withDefaults(
    defineProps<{
        title?: string;
        chirps: Chirp[];
        editable?: boolean;
        currentUserId?: number | null;
    }>(),
    {
        editable: false,
        currentUserId: null,
    },
);

const editingId = ref<number | null>(null);
const editForm = useForm({
    title: '',
    description: '',
});

function canManage(chirp: Chirp): boolean {
    return props.editable && props.currentUserId === chirp.user?.id;
}

function startEdit(chirp: Chirp): void {
    editingId.value = chirp.id;
    editForm.title = chirp.title;
    editForm.description = chirp.description ?? '';
    editForm.clearErrors();
}

function cancelEdit(): void {
    editingId.value = null;
    editForm.reset();
    editForm.clearErrors();
}

function submitEdit(chirpId: number): void {
    editForm.patch(`/jumper/${chirpId}`, {
        preserveScroll: true,
        onSuccess: () => {
            cancelEdit();
        },
    });
}

function deleteChirp(chirpId: number): void {
    if (!window.confirm('Seguro que quieres eliminar este jumper?')) {
        return;
    }

    router.delete(`/jumper/${chirpId}`, {
        preserveScroll: true,
        onSuccess: () => {
            if (editingId.value === chirpId) {
                cancelEdit();
            }
        },
    });
}
</script>

<template>
    <div class="h-full overflow-y-auto p-3">
        <h2
            v-if="title"
            class="mb-3 break-words text-sm font-semibold text-muted-foreground"
        >
            {{ title }}
        </h2>

        <div v-if="chirps.length === 0" class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground">
            Aun no hay jumpers en esta columna.
        </div>

        <div v-else class="space-y-3">
            <Card v-for="chirp in chirps" :key="chirp.id" class="border-sidebar-border/70">
                <CardHeader class="pb-2">
                    <div class="flex items-start justify-between gap-2">
                        <CardTitle class="min-w-0 break-words text-base leading-tight">
                            {{ chirp.title }}
                        </CardTitle>
                        <div
                            v-if="canManage(chirp)"
                            class="flex shrink-0 items-center justify-end"
                        >
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="size-8"
                                    >
                                        <Ellipsis class="size-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" class="w-36">
                                    <DropdownMenuItem @click="startEdit(chirp)">
                                        Editar
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        class="text-destructive focus:text-destructive"
                                        @click="deleteChirp(chirp.id)"
                                    >
                                        Eliminar
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Por {{ chirp.user?.name ?? 'Usuario' }}
                    </p>
                </CardHeader>
                <CardContent class="pt-0">
                    <form
                        v-if="editingId === chirp.id"
                        class="space-y-3"
                        @submit.prevent="submitEdit(chirp.id)"
                    >
                        <div class="grid gap-2">
                            <Label :for="`edit-title-${chirp.id}`">Título</Label>
                            <Input
                                :id="`edit-title-${chirp.id}`"
                                v-model="editForm.title"
                                required
                                maxlength="255"
                            />
                            <p v-if="editForm.errors.title" class="text-sm text-destructive">
                                {{ editForm.errors.title }}
                            </p>
                        </div>
                        <div class="grid gap-2">
                            <Label :for="`edit-description-${chirp.id}`">Descripción</Label>
                            <textarea
                                :id="`edit-description-${chirp.id}`"
                                v-model="editForm.description"
                                class="min-h-[100px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                                maxlength="2000"
                            />
                            <p
                                v-if="editForm.errors.description"
                                class="text-sm text-destructive"
                            >
                                {{ editForm.errors.description }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <Button
                                type="submit"
                                size="sm"
                                :disabled="editForm.processing"
                            >
                                Guardar cambios
                            </Button>
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                @click="cancelEdit"
                            >
                                Cancelar
                            </Button>
                        </div>
                    </form>
                    <p v-else class="break-words whitespace-pre-wrap text-sm text-foreground/90">
                        {{ chirp.description || 'Sin descripcion.' }}
                    </p>
                    <img
                        v-if="chirp.image_url"
                        :src="chirp.image_url"
                        alt="Imagen del jumper"
                        class="mt-3 block h-auto max-h-80 w-auto max-w-full rounded-md border border-sidebar-border/70 object-contain"
                        loading="lazy"
                    />
                </CardContent>
            </Card>
        </div>
    </div>
</template>
