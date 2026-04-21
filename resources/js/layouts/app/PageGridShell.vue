<script setup lang="ts">
import { defineAsyncComponent } from 'vue';

withDefaults(
    defineProps<{
        showTiles?: boolean;
    }>(),
    {
        showTiles: true,
    },
);

const PlaceholderPattern = defineAsyncComponent(
    () => import('../../components/PlaceholderPattern.vue'),
);

const tileClass =
    'relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border';
const mainClass =
    'relative min-h-[100vh] flex-1 rounded-2xl border border-sidebar-border/70 bg-gradient-to-b from-background to-muted/20 shadow-sm md:min-h-min dark:border-sidebar-border';
</script>

<template>
    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div v-if="showTiles" class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div :class="tileClass">
                <slot name="tile-1">
                    <PlaceholderPattern />
                </slot>
            </div>
            <div :class="tileClass">
                <slot name="tile-2">
                    <PlaceholderPattern />
                </slot>
            </div>
            <div :class="tileClass">
                <slot name="tile-3">
                    <PlaceholderPattern />
                </slot>
            </div>
        </div>
        <div :class="showTiles ? mainClass : 'relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border'">
            <slot>
                <PlaceholderPattern />
            </slot>
        </div>
    </div>
</template>
