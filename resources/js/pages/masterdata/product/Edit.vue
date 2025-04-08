<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Category, type Unit, type Product, type ProductPrice, type Warehouse } from '@/types';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Heading from '@/components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';
import { RefreshCw, Save, Trash2 } from 'lucide-vue-next';
import { Select } from '@/components/ui/select';
import axios from 'axios';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { computed, onMounted, ref, watch } from 'vue';

interface Props {
    status: string;
    product?: Product
    categories: Array<Category>;
    warehouses: Array<Warehouse>;
    units: Array<Unit>;
    productPrices: Array<ProductPrice>;
}

const page = usePage();
const flashMessage = computed<any>(() => page.props.flash);
// State untuk menampilkan/hilangnya pesan
const showFlash = ref<boolean>(false);

// Cek jika ada flash message error, lalu tampilkan dan hilangkan otomatis
watch(flashMessage, (newValue) => {
    if (newValue.error || newValue.success) {
        showFlash.value = true;
        setTimeout(() => {
            showFlash.value = false;
        }, 3000);
    }
});

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Products ${(props.status == 'edit' && props.product) ? 'Edit' : 'Create'}`,
        href: (props.status == 'edit' && props.product) ? route('md.products.edit', props.product.id) : route('md.products.create'),
    },
];

const form = useForm({
    kode_produk: (props.status == 'edit' && props.product) ? props.product.kode_produk : '',
    nama_produk: (props.status == 'edit' && props.product) ? props.product.nama_produk : '',
    kategori_id: (props.status == 'edit' && props.product) ? props.product.kategori_id : '',
    satuan_dasar_id: (props.status == 'edit' && props.product) ? props.product.satuan_dasar_id : '',
    barcode: (props.status == 'edit' && props.product) ? props.product.barcode : '',
    harga_beli: (props.status == 'edit' && props.product) ? props.product.harga_beli : '',
    harga_jual: (props.status == 'edit' && props.product) ? props.product.harga_jual : '',
    deskripsi: (props.status == 'edit' && props.product) ? props.product.deskripsi : '',
});


const submit = () => {
    if (props.status == 'edit' && props.product) {
        form.patch(route('md.products.update', props.product.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('md.products.store'), {
            preserveScroll: true,
        });
    }
};

const generateCode = async () => {
    try {
        const response = await axios.post('/api/products/code/generate');
        form.kode_produk = response.data.code;
    } catch (error) {
        console.error('Error generate code:', error);
    }
};

const useMultiUnit = ref<boolean>(false);

const productPrices = ref<Array<ProductPrice>>([]);

const addRow = () => {
    if (props.product)
        productPrices.value.push({ warehouse_id: '', satuan_id: '', konversi: '', harga_beli: '', harga_jual: '', barcode: '' });
};

let formProductPriceSave = useForm({});
let formProductPriceDelete = useForm({});

const saveProductPrice = (productPrice: ProductPrice) => {
    if (!productPrice.konversi || !productPrice.warehouse_id || !productPrice.satuan_id || !productPrice.harga_beli || !productPrice.harga_jual) {
        return
    }

    formProductPriceSave
        .transform((data) => ({
            ...data,
            warehouse_id: productPrice.warehouse_id,
            satuan_id: productPrice.satuan_id,
            konversi: productPrice.konversi,
            harga_beli: productPrice.harga_beli,
            harga_jual: productPrice.harga_jual,
            barcode: productPrice.barcode,
        }));

    if (!productPrice.id) {
        formProductPriceSave.post(route('md.product_prices.store', props.product?.id), {
            preserveScroll: true,
            onFinish: () => {
                // Setelah submit, reload data produk dari server
                formProductPriceSave.reset(); // Reset form ke state awal
            },
            onError: (errors) => {
                console.error('Error:', errors);
            },
        });
    } else {
        formProductPriceSave.patch(route('md.product_prices.update', [props.product?.id, productPrice.id]), {
            preserveScroll: true,
            onFinish: () => {
                // Setelah submit, reload data produk dari server
                formProductPriceSave.reset(); // Reset form ke state awal
            },
            onError: (errors) => {
                console.error('Error:', errors);
            },
        });
    }
};

const deleteProductPrice = (productPrice: ProductPrice, index: number) => {
    if (!productPrice.id) {
        productPrices.value.splice(index, 1);
    } else {
        formProductPriceDelete.delete(route('md.product_prices.destroy', [props.product?.id, productPrice.id]), {
            preserveScroll: true,
            onFinish: () => {
                // Setelah submit, reload data produk dari server
                formProductPriceDelete.reset(); // Reset form ke state awal
            },
            onError: (errors) => {
                console.error('Error:', errors);
            },
        });
    }
};

onMounted(() => {
    productPrices.value = props.status == 'edit' ? props.productPrices : [];
});


</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Products" />

        <div class="px-4 py-6">
            <Heading title="Master Data Products" description="Manage your master data products" />

            <div class="w-full">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="grid gap-2">
                            <Label for="kode_produk">Kode Produk</Label>
                            <div class="flex items-center gap-2">
                                <Input id="kode_produk" class="mt-1 block w-full" v-model="form.kode_produk" required
                                    autocomplete="kode_produk" placeholder="Kode Produk"
                                    :disabled="props.status == 'edit'" />
                                <button v-show="props.status != 'edit' && !props.product"
                                    @click.prevent="props.status == 'create' ? generateCode() : null"
                                    class="inline-block px-2 py-2 text-sm font-medium text-gray-700 rounded-md border bg-white shadow-xs dark:border-gray-800 dark:bg-primary hover:bg-gray-50 focus:relative dark:text-gray-900 dark:hover:bg-blue-600 dark:hover:text-gray-200">
                                    <RefreshCw />
                                </button>
                            </div>
                            <InputError class="mt-2" :message="form.errors.kode_produk" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="barcode">Barcode</Label>
                            <Input id="barcode" class="mt-1 block w-full" v-model="form.barcode" autocomplete="barcode"
                                placeholder="Barcode" />
                            <InputError class="mt-2" :message="form.errors.barcode" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="kategori_id">Kategori Produk</Label>
                            <Select id="kategori_id" class="mt-1 block w-full" v-model="form.kategori_id" required
                                autocomplete="kategori_id" placeholder="Kategori Produk"
                                :options="categories.map((category) => ({ label: category.nama_kategori, value: category.id }))" />
                            <InputError class="mt-2" :message="form.errors.kategori_id" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="nama_produk">Nama Produk</Label>
                        <Input id="nama_produk" class="mt-1 block w-full" v-model="form.nama_produk" required
                            autocomplete="nama_produk" placeholder="Nama Produk" />
                        <InputError class="mt-2" :message="form.errors.nama_produk" />
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="grid gap-2">
                            <Label for="satuan_dasar_id">Satuan Dasar</Label>
                            <Select id="satuan_dasar_id" class="mt-1 block w-full" v-model="form.satuan_dasar_id"
                                required autocomplete="satuan_dasar_id" placeholder="Satuan Dasar"
                                :options="units.map((unit) => ({ label: unit.nama_satuan, value: unit.id }))" />
                            <InputError class="mt-2" :message="form.errors.satuan_dasar_id" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="deskripsi">Deskripsi</Label>
                        <Textarea id="deskripsi" class="mt-1 block w-full h-auto" v-model="form.deskripsi"
                            autocomplete="deskripsi" placeholder="Deskripsi produk" :rows="5" />
                        <InputError class="mt-2" :message="form.errors.deskripsi" />
                    </div>

                    <div class="overflow-x-auto">
                        <button @click.prevent="addRow"
                            class="inline-block px-4 py-2 text-sm font-medium text-gray-700 rounded-md border bg-white shadow-xs dark:border-gray-800 dark:bg-primary hover:bg-gray-50 focus:relative dark:text-gray-900 dark:hover:bg-blue-600 dark:hover:text-gray-200 mb-4">
                            + Add New Row
                        </button>

                        <table
                            class="min-w-full divide-y-2 divide-neutral-200 bg-white text-sm dark:divide-gray-700 dark:bg-neutral-900">
                            <thead class="ltr:text-left rtl:text-right">
                                <tr>
                                    <th
                                        class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                        Toko/Gudang
                                    </th>
                                    <th
                                        class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                        Satuan Konversi
                                    </th>
                                    <th
                                        class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                        Konversi
                                    </th>
                                    <th
                                        class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                        Harga Beli
                                    </th>
                                    <th
                                        class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                        Harga Jual
                                    </th>
                                    <th
                                        class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                        Barcode
                                    </th>
                                    <th class="px-4 py-2 w-20"></th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template v-if="productPrices.length === 0">
                                    <tr>
                                        <td colspan="7" class="px-4 py-2 text-gray-900 dark:text-white text-center">
                                            Belum ada data
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr v-for="(productPrice, index) in productPrices" :key="productPrice.id">
                                        <td class="px-4 py-2">
                                            <Select v-model="productPrice.warehouse_id" class="w-full"
                                                :options="warehouses.map((warehouse) => ({ label: warehouse.nama_gudang, value: warehouse.id }))" />
                                        </td>
                                        <td class="px-4 py-2">
                                            <Select v-model="productPrice.satuan_id" class="w-full"
                                                :options="units.map((unit) => ({ label: unit.nama_satuan, value: unit.id }))" />
                                        </td>
                                        <td class="px-4 py-2">
                                            <Input v-model="productPrice.konversi" class="w-full" />
                                        </td>
                                        <td class="px-4 py-2">
                                            <Input v-model="productPrice.harga_beli" class="w-full" />
                                        </td>
                                        <td class="px-4 py-2">
                                            <Input v-model="productPrice.harga_jual" class="w-full" />
                                        </td>
                                        <td class="px-4 py-2">
                                            <Input v-model="productPrice.barcode" class="w-full" />
                                        </td>
                                        <td class="px-4 py-2 text-center w-60">
                                            <span
                                                class="inline-flex overflow-hidden rounded-md border bg-white shadow-xs dark:border-gray-800 dark:bg-neutral-900">
                                                <button @click.prevent="saveProductPrice(productPrice)"
                                                    :disabled="formProductPriceSave.processing"
                                                    class="inline-block border-e p-3 text-gray-700 hover:bg-gray-50 focus:relative dark:border-e-gray-800 dark:text-gray-200 dark:hover:bg-blue-600"
                                                    title="Edit Data">
                                                    <Save :size="16" />
                                                </button>

                                                <button @click.prevent="deleteProductPrice(productPrice, index)"
                                                    :disabled="formProductPriceDelete.processing"
                                                    class="inline-block p-3 text-gray-700 hover:bg-gray-50 focus:relative dark:text-gray-200 dark:hover:bg-blue-600"
                                                    title="Delete Data">
                                                    <Trash2 :size="16" />
                                                </button>
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <TransitionRoot :show="showFlash" enter="transition ease-in-out" enter-from="opacity-0"
                            leave="transition ease-in-out" leave-to="opacity-0">
                            <p class="text-sm text-neutral-600">{{ flashMessage.success }}</p>
                        </TransitionRoot>

                        <TransitionRoot :show="showFlash" enter="transition ease-in-out" enter-from="opacity-0"
                            leave="transition ease-in-out" leave-to="opacity-0">
                            <p class="text-sm text-neutral-600">{{ flashMessage.error }}</p>
                        </TransitionRoot>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ (props.status == 'edit' && props.product) ? 'Update' :
                            'Save' }}</Button>

                        <TransitionRoot :show="form.recentlySuccessful" enter="transition ease-in-out"
                            enter-from="opacity-0" leave="transition ease-in-out" leave-to="opacity-0">
                            <p class="text-sm text-neutral-600">Product Saved.</p>
                        </TransitionRoot>
                    </div>
                </form>
            </div>

        </div>

    </AppLayout>
</template>
