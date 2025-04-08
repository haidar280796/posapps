<script setup lang="ts">
import { X } from 'lucide-vue-next';
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Category } from '@/types';
import Heading from '@/components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

const confirmingDeletion = ref(false);
const confirmingDeletionid = ref<string | number | null>(null);

interface Props {
    categories: {
        data: Array<Category>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    },
    filters: Object,
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categories',
        href: route('md.categories.index'),
    },
];

const form = useForm({});
const formSearch = useForm({
    search: (props.filters as any)?.search || "",
});

// Fungsi pencarian
const search = () => {
    formSearch.get(route("md.categories.index"), {
        preserveState: true, // Mempertahankan state saat berpindah halaman
        preserveScroll: true, // Tidak mereset scroll
    });
};

const confirmDeletion = (id: string | number | null) => {
    confirmingDeletion.value = true;
    confirmingDeletionid.value = id;
};

const deleteData = (e: Event) => {
    e.preventDefault();

    form.delete(route('md.categories.destroy', `${confirmingDeletionid.value}`), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingDeletion.value = false;
    confirmingDeletionid.value = null;

    form.clearErrors();
    form.reset();
};

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Categories" />

        <div class="px-4 py-6">
            <Heading title="Master Data Categories" description="Manage your master data categories" />

            <div class="flex items-center mb-6">
                <Link :href="route('md.categories.create')"
                    class="inline-block px-4 py-2 text-sm font-medium text-gray-700 rounded-md border bg-white shadow-xs dark:border-gray-800 dark:bg-primary hover:bg-gray-50 focus:relative dark:text-gray-900 dark:hover:bg-blue-600 dark:hover:text-gray-200">
                Create
                </Link>

                <form @submit.prevent="search" class="relative ml-4">
                    <label for="Search" class="sr-only"> Search for... </label>

                    <input type="text" id="Search" placeholder="Pencarian" v-model="formSearch.search"
                        class="w-full rounded-md border border-gray-200 py-2  ps-4 pe-10 shadow-xs sm:text-sm dark:border-gray-700 dark:bg-primary dark:text-gray-900" />

                    <span class="absolute inset-y-0 end-0 grid w-10 place-content-center">
                        <button type="submit"
                            class="text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <span class="sr-only">Search</span>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </button>
                    </span>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table
                    class="min-w-full divide-y-2 divide-neutral-200 bg-white text-sm dark:divide-gray-700 dark:bg-neutral-900">
                    <thead class="ltr:text-left rtl:text-right">
                        <tr>
                            <th class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                Nama
                                Kategori</th>
                            <th class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                Slug
                            </th>
                            <th class="px-4 py-2 w-20"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template v-if="categories.data.length === 0">
                            <tr>
                                <td colspan="3"
                                    class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-white text-center">
                                    Belum ada data
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr v-for="category in categories.data" v-bind:key="category.id">
                                <td class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ category.nama_kategori }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200">{{
                                    category.slug }}</td>
                                <td class="px-4 py-2 whitespace-nowrap w-20">
                                    <span
                                        class="inline-flex overflow-hidden rounded-md border bg-white shadow-xs dark:border-gray-800 dark:bg-neutral-900">
                                        <Link :href="route('md.categories.edit', category.id)"
                                            class="inline-block border-e p-3 text-gray-700 hover:bg-gray-50 focus:relative dark:border-e-gray-800 dark:text-gray-200 dark:hover:bg-blue-600"
                                            title="Edit Data">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                        </Link>

                                        <button @click.prevent="() => confirmDeletion(category.id)" method="delete"
                                            class="inline-block p-3 text-gray-700 hover:bg-gray-50 focus:relative dark:text-gray-200 dark:hover:bg-blue-600"
                                            title="Delete Data">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Start -->
            <div class="py-2 mt-4">
                <ol class="flex justify-end gap-1 text-xs font-medium">
                    <template v-for="(link, index) in categories.links" :key="index">
                        <li>
                            <Link :href="link.url ? link.url : '#'" @click.prevent="!link.url"
                                :class="{ 'block min-w-4 px-4 rounded-sm border border-gray-200 bg-gray-200 text-center leading-8 text-gray-900 dark:border-gray-800 dark:bg-gray-900 dark:text-white': link.active, 'block min-w-4 px-4 rounded-sm border border-gray-100 bg-white dark:border-blue-600 dark:bg-blue-600 text-center leading-8 dark:text-white': !link.active }">
                            <span v-html="link.label"></span>
                            </Link>
                        </li>
                    </template>
                </ol>
            </div>
            <!-- Pagination End -->

            <Dialog v-bind:open="confirmingDeletion">
                <DialogContent>
                    <slot name="close">
                        <button @click.prevent="closeModal"
                            class="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none data-[state=open]:bg-accent data-[state=open]:text-muted-foreground">
                            <X class="h-4 w-4" />
                            <span class="sr-only">Close</span>
                        </button>
                    </slot>

                    <form class="space-y-6" @submit="deleteData">
                        <DialogHeader class="space-y-3">
                            <DialogTitle>Are you sure you want to delete this data?</DialogTitle>
                            <DialogDescription>
                                Once this data is deleted, all of its resources and data will also be permanently
                                deleted.
                            </DialogDescription>
                        </DialogHeader>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button variant="secondary" @click="closeModal"> Cancel </Button>
                            </DialogClose>

                            <Button variant="destructive" :disabled="form.processing">
                                <button type="submit">Delete</button>
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>

    </AppLayout>
</template>
