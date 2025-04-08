<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, type Category } from '@/types';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Heading from '@/components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';

interface Props {
    status: string;
    category?: Category;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Categories ${(props.status == 'edit' && props.category) ? 'Edit' : 'Create'}`,
        href: (props.status == 'edit' && props.category) ? route('md.categories.edit', props.category.id) : route('md.categories.create'),
    },
];

const page = usePage<SharedData>();

const form = useForm({
    nama_kategori: (props.status == 'edit' && props.category) ? props.category.nama_kategori : '',
});


const submit = () => {
    if (props.status == 'edit' && props.category) {
        form.patch(route('md.categories.update', props.category.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('md.categories.store'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Categories" />

        <div class="px-4 py-6">
            <Heading title="Master Data Categories" description="Manage your master data categories" />

            <div class="max-w-xl">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="nama_kategori">Nama Kategori</Label>
                        <Input id="nama_kategori" class="mt-1 block w-full" v-model="form.nama_kategori" required autocomplete="nama_kategori"
                            placeholder="Nama Kategori" />
                        <InputError class="mt-2" :message="form.errors.nama_kategori" />
                    </div>
    
                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ (props.status == 'edit' && props.category) ? 'Update' : 'Save' }}</Button>
    
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
