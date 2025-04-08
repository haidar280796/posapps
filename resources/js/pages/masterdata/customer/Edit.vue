<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, type Customer } from '@/types';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Heading from '@/components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';

interface Props {
    status: string;
    customer?: Customer;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Customers ${(props.status == 'edit' && props.customer) ? 'Edit' : 'Create'}`,
        href: (props.status == 'edit' && props.customer) ? route('md.customers.edit', props.customer.id) : route('md.customers.create'),
    },
];

const page = usePage<SharedData>();

const form = useForm({
    nama: (props.status == 'edit' && props.customer) ? props.customer.nama : '',
    telepon: (props.status == 'edit' && props.customer) ? props.customer.telepon : '',
    email: (props.status == 'edit' && props.customer) ? props.customer.email : '',
    alamat: (props.status == 'edit' && props.customer) ? props.customer.alamat : '',
});


const submit = () => {
    if (props.status == 'edit' && props.customer) {
        form.patch(route('md.customers.update', props.customer.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('md.customers.store'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Customers" />

        <div class="px-4 py-6">
            <Heading title="Master Data Customers" description="Manage your master data customers" />

            <div class="max-w-xl">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="nama">Nama Pelanggan</Label>
                        <Input id="nama" class="mt-1 block w-full" v-model="form.nama" required autocomplete="nama"
                            placeholder="Nama Pelanggan" />
                        <InputError class="mt-2" :message="form.errors.nama" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" class="mt-1 block w-full" v-model="form.email" autocomplete="email"
                            placeholder="Email" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="telepon">Telepon</Label>
                        <Input id="telepon" class="mt-1 block w-full" v-model="form.telepon" autocomplete="telepon"
                            placeholder="Telepon" />
                        <InputError class="mt-2" :message="form.errors.telepon" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="alamat">Alamat</Label>
                        <Input id="alamat" class="mt-1 block w-full" v-model="form.alamat" autocomplete="alamat"
                            placeholder="Alamat" />
                        <InputError class="mt-2" :message="form.errors.alamat" />
                    </div>
    
                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ (props.status == 'edit' && props.customer) ? 'Update' : 'Save' }}</Button>
    
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
