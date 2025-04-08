<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<style>
.style-chooser .vs__search {
    font-size: 0.875rem;
}

.vs--single .vs__selected {
    color: hsl(var(--sidebar-foreground));
}

.style-chooser .vs__search::placeholder,
.style-chooser .vs__dropdown-toggle,
.style-chooser .vs__dropdown-menu {
    /* background: hsl(var(--background)); */
    border: none;
    color: hsl(var(--sidebar-foreground));
    text-transform: lowercase;
    font-variant: normal;
    height: 100%;
    text-transform: none !important;
    font-style: unset;
    font-family: inherit !important;
}

.style-chooser .vs__dropdown-menu {
  height: 100%;
  max-height: fit-content; /* Atur tinggi maksimal */
  min-height: min-content;
  overflow-y: auto; /* Aktifkan scroll jika item banyak */
}

.style-chooser .vs__clear,
.style-chooser .vs__open-indicator {
    fill: gray;
}

.style-chooser .vs__search::placeholder {
    text-transform: none !important;
    color: gray;
    font-size: 0.875rem;
    font-family: inherit !important;
    background-color: hsl(var(--background));
}

.style-chooser .vs__dropdown-menu .vs__dropdown-option {
    background-color: white;
    /* Warna default */
    color: hsl(var(--background));
}

.style-chooser .vs__dropdown-menu .vs__dropdown-option:hover {
    background-color: oklch(0.546 0.245 262.881);
    /* Warna highlight saat hover */
    color: white;
    /* Warna teks saat hover */
}
</style>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
