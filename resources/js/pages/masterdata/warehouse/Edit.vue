<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, useForm } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Warehouse } from '@/types';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Heading from '@/components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';
import { Select } from '@/components/ui/select';

interface Props {
    status: string;
    warehouse?: Warehouse
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Products ${(props.status == 'edit' && props.warehouse) ? 'Edit' : 'Create'}`,
        href: (props.status == 'edit' && props.warehouse) ? route('md.products.edit', props.warehouse?.id) : route('md.warehouses.create'),
    },
];

const form = useForm({
    nama_gudang: (props.status == 'edit' && props.warehouse) ? props.warehouse.nama_gudang : '',
    lokasi: (props.status == 'edit' && props.warehouse) ? props.warehouse.lokasi : '',
    phone: (props.status == 'edit' && props.warehouse) ? props.warehouse.phone : '',
    tipe: (props.status == 'edit' && props.warehouse) ? props.warehouse.tipe : '',
});


const submit = () => {
    if (props.status == 'edit' && props.warehouse) {
        form.patch(route('md.warehouses.update', props.warehouse.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('md.warehouses.store'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Products" />

        <div class="px-4 py-6">
            <Heading title="Master Data Products" description="Manage your master data products" />

            <div class="w-full">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="nama_gudang">Nama Gudang/Toko</Label>
                        <Input id="nama_gudang" class="mt-1 block w-full" v-model="form.nama_gudang" required
                            autocomplete="nama_gudang" placeholder="Nama Gudang/Toko" />
                        <InputError class="mt-2" :message="form.errors.nama_gudang" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="lokasi">Lokasi</Label>
                        <Input id="lokasi" class="mt-1 block w-full" v-model="form.lokasi" required
                            autocomplete="lokasi" placeholder="Lokasi" />
                        <InputError class="mt-2" :message="form.errors.lokasi" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="phone">Telepon</Label>
                            <Input id="phone" class="mt-1 block w-full" v-model="form.phone" required
                            autocomplete="phone" placeholder="Telepon" />
                            <InputError class="mt-2" :message="form.errors.phone" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="tipe">Tipe</Label>
                            <Select id="tipe" class="mt-1 block w-full" v-model="form.tipe"
                                required autocomplete="tipe" placeholder="Tipe"
                                :options="[{ label: 'Gudang', value: 'gudang' }, { label: 'Toko', value: 'toko' }]" />
                            <InputError class="mt-2" :message="form.errors.tipe" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ (props.status == 'edit' && props.warehouse) ? 'Update' :
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
