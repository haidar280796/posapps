<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, Warehouse } from '@/types';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import Heading from '@/components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';
import { Select } from '@/components/ui/select';

interface Props {
    warehouses: Array<Warehouse>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Open Cashier`,
        href: route('trn.pos.open'),
    },
];

const page = usePage<SharedData>();

const form = useForm({
    warehouse_id: '',
});


const submit = () => {
    form.post(route('trn.pos.open_store'), {
            preserveScroll: true,
        });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Point of Sale" />

        <div class="px-4 py-6">
            <Heading title="Point of Sale" description="Open Your Cashier" />

            <div class="max-w-xl">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                            <Label for="warehouse_id">Pilih Lokasi</Label>
                            <Select id="warehouse_id" class="mt-1 block w-full" v-model="form.warehouse_id" required
                                autocomplete="warehouse_id" placeholder="Kategori Produk"
                                :options="warehouses.map((warehouse) => ({ label: warehouse.nama_gudang, value: warehouse.id }))" />
                            <InputError class="mt-2" :message="form.errors.warehouse_id" />
                        </div>
    
                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Open Cashier</Button>
    
                        <TransitionRoot :show="form.recentlySuccessful" enter="transition ease-in-out"
                            enter-from="opacity-0" leave="transition ease-in-out" leave-to="opacity-0">
                            <p class="text-sm text-neutral-600">Saved.</p>
                        </TransitionRoot>
                    </div>
                </form>
            </div>

        </div>

    </AppLayout>
</template>
