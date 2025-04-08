<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, useForm } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Unit } from '@/types';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Heading from '@/components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';

interface Props {
    status: string;
    unit?: Unit;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Units ${(props.status == 'edit' && props.unit) ? 'Edit' : 'Create'}`,
        href: (props.status == 'edit' && props.unit) ? route('md.units.edit', props.unit.id) : route('md.units.create'),
    },
];

const form = useForm({
    nama_satuan: (props.status == 'edit' && props.unit) ? props.unit.nama_satuan : '',
});


const submit = () => {
    if (props.status == 'edit' && props.unit) {
        form.patch(route('md.units.update', props.unit.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('md.units.store'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Units" />

        <div class="px-4 py-6">
            <Heading title="Master Data Units" description="Manage your master data units" />

            <div class="max-w-xl">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="nama_satuan">Nama Satuan</Label>
                        <Input id="nama_satuan" class="mt-1 block w-full" v-model="form.nama_satuan" required autocomplete="nama_satuan"
                            placeholder="Nama Satuan" />
                        <InputError class="mt-2" :message="form.errors.nama_satuan" />
                    </div>
    
                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ (props.status == 'edit' && props.unit) ? 'Update' : 'Save' }}</Button>
    
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
