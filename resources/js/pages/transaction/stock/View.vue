<script setup lang="ts">
import { X } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Stock } from '@/types';
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
import Select2Warehouse from '@/components/Select2Warehouse.vue';
import { formatRibu } from '@/lib/utils';
import Select2Product from '@/components/Select2Product.vue';

const confirmingDeletion = ref(false);
const confirmingDeletionid = ref<string | number | null>(null);

interface Props {
    stocks: {
        data: Array<Stock>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    },
    filters: Object,
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Stocks',
        href: route('trn.product_stocks.index'),
    },
];

const formSearch = useForm({
    warehouse_id: (props.filters as any)?.warehouse_id || "",
    product_id: (props.filters as any)?.product_id || "",
});

// Fungsi pencarian
const search = () => {
    formSearch.get(route("trn.product_stocks.index"), {
        preserveState: true, // Mempertahankan state saat berpindah halaman
        preserveScroll: true, // Tidak mereset scroll
    });
};

const formDelete = useForm({});

const confirmDeletion = (id: string | number | null) => {
    confirmingDeletion.value = true;
    confirmingDeletionid.value = id;
};

const deleteData = (e: Event) => {
    e.preventDefault();

    formDelete.delete(route('trn.product_stocks.destroy', `${confirmingDeletionid.value}`), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => formDelete.reset(),
    });
};

const closeModal = () => {
    confirmingDeletion.value = false;
    confirmingDeletionid.value = null;

    formDelete.clearErrors();
    formDelete.reset();
};

watch(() => formSearch.warehouse_id, () => {
    search();
})

watch(() => formSearch.product_id, () => {
    search();
})

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Stocks" />

        <div class="px-4 py-6">
            <Heading title="Item Stocks" description="Manage your data item stocks" />


            <div class="flex items-center gap-4 mb-6">
                <Link :href="route('trn.product_stocks.create')"
                    class="inline-block px-4 py-2 text-sm font-medium text-gray-700 rounded-md border bg-white shadow-xs dark:border-gray-800 dark:bg-primary hover:bg-gray-50 focus:relative dark:text-gray-900 dark:hover:bg-blue-600 dark:hover:text-gray-200">
                Create
                </Link>
                <form @submit.prevent="search" class="relative ml-auto">
                    <div class="flex gap-2">
                        <Select2Warehouse class="w-80" v-model="formSearch.warehouse_id" />
                        <Select2Product class="w-80" v-model="formSearch.product_id" />
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table
                    class="min-w-full divide-y-2 divide-neutral-200 bg-white text-sm dark:divide-gray-700 dark:bg-neutral-900">
                    <thead class="ltr:text-left rtl:text-right">
                        <tr>
                            <th class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                Nama Toko</th>
                            <th class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                Item
                            </th>
                            <th class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                Jumlah
                            </th>
                            <th class="px-4 py-2 w-20"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template v-if="stocks.data.length === 0">
                            <tr>
                                <td colspan="4"
                                    class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-white text-center">
                                    Belum ada data
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr v-for="stock in stocks.data" v-bind:key="stock.id">
                                <td class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ stock.warehouse?.nama_gudang }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200">{{
                                    stock.product?.nama_produk }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200">{{
                                   `${formatRibu(stock.jumlah)} ${stock.product?.satuan_dasar?.nama_satuan}`  }}</td>
                                <td class="px-4 py-2 whitespace-nowrap w-20">
                                    <span
                                        class="inline-flex overflow-hidden rounded-md border bg-white shadow-xs dark:border-gray-800 dark:bg-neutral-900">
                                        <button @click.prevent="() => confirmDeletion(stock.id)" method="delete"
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
                    <template v-for="(link, index) in stocks.links" :key="index">
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

                            <Button variant="destructive" :disabled="formDelete.processing">
                                <button type="submit">Delete</button>
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>

    </AppLayout>
</template>