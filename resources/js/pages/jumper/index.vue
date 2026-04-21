<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ImagePlus } from 'lucide-vue-next';
import { computed, defineAsyncComponent, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '../../components/ui/button';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '../../components/ui/collapsible';
import { Input } from '../../components/ui/input';
import { Label } from '../../components/ui/label';
import { cn } from '../../lib/utils';
import { dashboard } from '../../routes';
import jumper from '../../routes/jumper';
import type { Chirp } from '../../types';

const ChirpTile = defineAsyncComponent(
    () => import('../../components/chirp/ChirpTile.vue'),
);

const props = defineProps<{
    chirps: Chirp[] | { data?: Chirp[] } | null;
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
            {
                title: 'Jumper',
                href: jumper.index(),
            },
        ],
    },
});

const createOpen = ref(false);
const imageInputRef = ref<HTMLInputElement | null>(null);
const form = useForm({
    title: '',
    description: '',
    image: null as File | null,
});

const textareaClass = cn(
    'placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex min-h-[120px] w-full rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none md:text-sm',
    'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
    'disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50',
);

function resetForm() {
    form.reset();
    form.clearErrors();
}

function cancelCreate() {
    createOpen.value = false;
    resetForm();
}

function submitJumper() {
    if (!form.title.trim()) {
        toast.error('El título es obligatorio.');

        return;
    }

    form.post('/jumper', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            createOpen.value = false;
            resetForm();
        },
    });
}

function onImageSelected(event: Event) {
    const input = event.target as HTMLInputElement;
    form.image = input.files?.[0] ?? null;
}

function openImagePicker() {
    imageInputRef.value?.click();
}
</script>

<template>
    <div>
        <Head title="Jumper" />
        <div class="grid auto-rows-min gap-4 p-6 md:grid-cols-3">
            <div class="flex flex-col gap-4 md:col-span-1">
                <h1
                    class="text-2xl font-semibold tracking-tight text-foreground"
                >
                    Jumper
                </h1>

                <Collapsible
                    :open="createOpen"
                    class="flex flex-col gap-2"
                    @update:open="createOpen = $event"
                >
                    <div class="flex flex-wrap items-center gap-2">
                        <CollapsibleTrigger as-child>
                            <Button type="button" variant="default">
                                {{
                                    createOpen
                                        ? 'Ocultar formulario'
                                        : 'Crear Jumper'
                                }}
                            </Button>
                        </CollapsibleTrigger>
                    </div>

                    <CollapsibleContent class="overflow-hidden">
                        <form
                            class="mt-4 space-y-4 rounded-lg border border-sidebar-border/70 bg-muted/30 p-4 dark:border-sidebar-border"
                            @submit.prevent="submitJumper"
                        >
                            <div class="grid gap-2">
                                <Label for="jumper-title">Título</Label>
                                <Input
                                    id="jumper-title"
                                    v-model="form.title"
                                    name="title"
                                    type="text"
                                    required
                                    maxlength="255"
                                    autocomplete="off"
                                    placeholder="Título del jumper"
                                />
                                <p
                                    v-if="form.errors.title"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.title }}
                                </p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="jumper-description">Descripción</Label>
                                <textarea
                                    id="jumper-description"
                                    v-model="form.description"
                                    name="description"
                                    :class="textareaClass"
                                    rows="4"
                                    maxlength="2000"
                                    placeholder="Escribe el contenido del jumper..."
                                />
                                <p
                                    v-if="form.errors.description"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="jumper-image">Imagen</Label>
                                <div class="flex items-center gap-2">
                                    <input
                                        ref="imageInputRef"
                                        id="jumper-image"
                                        name="image"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="onImageSelected"
                                    />
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        class="h-8 px-2 text-xs"
                                        @click="openImagePicker"
                                    >
                                        <ImagePlus class="mr-1 size-4" />
                                        Imagen
                                    </Button>
                                    <span
                                        v-if="form.image"
                                        class="max-w-[180px] truncate text-xs text-muted-foreground"
                                    >
                                        {{ form.image.name }}
                                    </span>
                                </div>
                                <p class="text-xs text-muted-foreground">
                                    Opcional. JPG, PNG, WEBP, GIF (max 5MB).
                                </p>
                                <p
                                    v-if="form.errors.image"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.image }}
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <Button
                                    type="submit"
                                    variant="default"
                                    :disabled="form.processing"
                                >
                                    Guardar jumper
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="cancelCreate"
                                >
                                    Cancelar
                                </Button>
                            </div>
                        </form>
                    </CollapsibleContent>
                </Collapsible>

                <div>
                    <Link
                        :href="dashboard()"
                        class="text-sm font-medium text-primary underline-offset-4 hover:underline"
                    >
                        Volver al Dashboard
                    </Link>
                </div>
            </div>
            <div class="md:col-span-2">
                <ChirpTile
                    title="Mis jumpers"
                    :chirps="chirpList"
                    :editable="true"
                    :current-user-id="props.auth.user.id"
                />
            </div>
        </div>
    </div>
</template>
