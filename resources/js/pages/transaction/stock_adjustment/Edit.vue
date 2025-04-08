<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, useForm } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { type Unit, type BreadcrumbItem, type StockAdjustment } from '@/types';
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
    adjustmentTypes: Object;
    stockAdjustment?: StockAdjustment
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Stock Adjustments ${(props.status == 'edit' && props.stockAdjustment) ? 'Edit' : 'Create'}`,
        href: (props.status == 'edit' && props.stockAdjustment) ? route('trn.stock_adjustments.edit', props.stockAdjustment?.id) : route('trn.stock_adjustments.create'),
    },
];

const form = useForm({
    warehouse_id: (props.status == 'edit' && props.stockAdjustment) ? props.stockAdjustment.warehouse_id : '',
    warehouse_target_id: (props.status == 'edit' && props.stockAdjustment) ? props.stockAdjustment.warehouse_target_id : '',
    product_id: (props.status == 'edit' && props.stockAdjustment) ? props.stockAdjustment.product_id : '',
    adjustment_type: (props.status == 'edit' && props.stockAdjustment) ? props.stockAdjustment.adjustment_type : '',
    jumlah: (props.status == 'edit' && props.stockAdjustment) ? props.stockAdjustment.jumlah : '',
    satuan_id: (props.status == 'edit' && props.stockAdjustment) ? props.stockAdjustment.jumlah : '',
    reason: (props.status == 'edit' && props.stockAdjustment) ? props.stockAdjustment.reason : '',
});


const submit = () => {
    form.post(route('trn.stock_adjustments.store'), {
        preserveScroll: true,
    });
};

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Stock Adjustments" />

        <div class="px-4 py-6">
            <Heading title="Stock Adjustments" description="Manage your data item stock adjustments" />

            <div class="w-full">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="adjustment_type">Adjustment Type</Label>
                        <Select id="adjustment_type" class="mt-1 block w-full" v-model="form.adjustment_type" required
                            autocomplete="adjustment_type" placeholder="Tipe"
                            :options="Object.entries(props.adjustmentTypes).map(([key, value]) => ({ label: value, value: key }))" />
                        <InputError class="mt-2" :message="form.errors.satuan_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Nama Gudang/Toko Asal</Label>
                        <Select2Warehouse class="w-full" v-model="form.warehouse_id" />
                        <InputError class="mt-2" :message="form.errors.warehouse_id" />
                    </div>

                    <div class="grid gap-2" v-if="form.adjustment_type == 'transfer'">
                        <Label>Nama Gudang/Toko Tujuan</Label>
                        <Select2Warehouse class="w-full" v-model="form.warehouse_target_id" />
                        <InputError class="mt-2" :message="form.errors.warehouse_target_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Produk</Label>
                        <Select2Product class="w-full" v-model="form.product_id" />
                        <InputError class="mt-2" :message="form.errors.product_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="jumlah">Jumlah</Label>
                        <Input id="jumlah" tipe="number" class="mt-1 block w-full" v-model="form.jumlah" required
                            autocomplete="jumlah" placeholder="Jumlah" />
                        <InputError class="mt-2" :message="form.errors.jumlah" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="satuan_id">Satuan</Label>
                        <Select id="satuan_id" class="mt-1 block w-full" v-model="form.satuan_id" required
                            autocomplete="satuan_id" placeholder="Satuan Stok"
                            :options="units.map((unit) => ({ label: unit.nama_satuan, value: unit.id }))" />
                        <InputError class="mt-2" :message="form.errors.satuan_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="reason">Reason</Label>
                        <Input id="reason" tipe="text" class="mt-1 block w-full" v-model="form.reason"
                            autocomplete="reason" placeholder="Reason" />
                        <InputError class="mt-2" :message="form.errors.reason" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ (props.status == 'edit' && props.stockAdjustment) ?
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
