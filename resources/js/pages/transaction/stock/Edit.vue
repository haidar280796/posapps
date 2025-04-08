<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, useForm } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { type Unit, type BreadcrumbItem, type StockTransaction } from '@/types';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Heading from '@/components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';
import Select2Warehouse from '@/components/Select2Warehouse.vue';
import Select2Product from '@/components/Select2Product.vue';
import { Select } from '@/components/ui/select';

interface Props {
    status: string;
    units: Array<Unit>;
    stockTransaction?: StockTransaction
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Stocks ${(props.status == 'edit' && props.stockTransaction) ? 'Edit' : 'Create'}`,
        href: (props.status == 'edit' && props.stockTransaction) ? route('trn.product_stocks.edit', props.stockTransaction?.id) : route('trn.product_stocks.create'),
    },
];

const form = useForm({
    warehouse_id: (props.status == 'edit' && props.stockTransaction) ? props.stockTransaction.warehouse_id : '',
    product_id: (props.status == 'edit' && props.stockTransaction) ? props.stockTransaction.product_id : '',
    jumlah: (props.status == 'edit' && props.stockTransaction) ? props.stockTransaction.jumlah : '',
    satuan_id: (props.status == 'edit' && props.stockTransaction) ? props.stockTransaction.jumlah : '',
});


const submit = () => {
    form.post(route('trn.product_stocks.store'), {
        preserveScroll: true,
    });
};

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Stocks" />

        <div class="px-4 py-6">
            <Heading title="Item Stocks" description="Manage your data item stocks" />

            <div class="w-full">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label>Nama Gudang/Toko</Label>
                        <Select2Warehouse class="w-full" v-model="form.warehouse_id" />
                        <InputError class="mt-2" :message="form.errors.warehouse_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Produk</Label>
                        <Select2Product class="w-full" v-model="form.product_id" />
                        <InputError class="mt-2" :message="form.errors.product_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="jumlah">Jumlah Stok</Label>
                        <Input id="jumlah" tipe="number" class="mt-1 block w-full" v-model="form.jumlah" required
                            autocomplete="jumlah" placeholder="Jumlah" />
                        <InputError class="mt-2" :message="form.errors.jumlah" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="satuan_id">Satuan Stok</Label>
                        <Select id="satuan_id" class="mt-1 block w-full" v-model="form.satuan_id" required
                            autocomplete="satuan_id" placeholder="Satuan Stok"
                            :options="units.map((unit) => ({ label: unit.nama_satuan, value: unit.id }))" />
                        <InputError class="mt-2" :message="form.errors.satuan_id" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ (props.status == 'edit' && props.stockTransaction) ?
                            'Update' :
                            'Save' }}</Button>

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
