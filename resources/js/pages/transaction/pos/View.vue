<script setup lang="ts">
import { Minus, Plus } from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type ItemCart, type SharedData, ProductPosPrice } from '@/types';
import Heading from '@/components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';
import { formatRupiah } from '@/lib/utils';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import Select2ProductPos from '@/components/Select2ProductPos.vue';
import axios from 'axios';
import { useToast } from 'vue-toast-notification';

const inputProduct = ref<any>();
const inputProductCode = ref<string>("");
const productSelected = ref<ProductPosPrice>();
const resetSelectedProduct = ref<any>(false);
const isLoading = ref(false);
const cart = ref<Array<ItemCart>>([]);
const paidAmount = ref<number>();
const lastIdCart = ref<number>(0);
const invoiceNo = ref<string | null>();

interface Props {
    noInvoice: string;
    metodePenjualan: Object;
    metodePembayaran: Object;
}

const props = defineProps<Props>();
const page = usePage<SharedData>(); // Ambil dari props
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Point of Sale',
        href: route('trn.pos.index'),
    },
];

const formSale = useForm({
    no_invoice: props.noInvoice,
    metode_penjualan: 'walk_in_customer',
    metode_pembayaran: 'cash',
});

// Fungsi pencarian
const fetchProductByKode = async (productCode: string = '') => {
    if (isLoading.value) return;

    isLoading.value = true;
    try {
        const response = await axios.get(`/api/product?tipe=kode_produk&search=${productCode}`);
        const result = response.data;

        // Jika produk ditemukan, kembalikan datanya
        if (result.data) {
            return result.data;
        } else {
            return null; // Kembalikan null jika tidak ditemukan
        }
    } catch (error) {
        console.error("Error:", error);
        return null; // Pastikan return null jika terjadi error
    } finally {
        isLoading.value = false;
    }
};

const addProductToCart = () => {
    if (productSelected.value && !productSelected.value.stock) {
        toast.error(`Stok produk ${productSelected.value.nama_produk} belum diatur!`);
        return;
    }

    if (productSelected.value && productSelected.value.stock.jumlah <= 0) {
        toast.error(`Stok produk ${productSelected.value.nama_produk} sudah habis!`);
        return;
    }

    if (productSelected.value) {
        lastIdCart.value++;
        const item: ItemCart = {
            ...productSelected.value,
            cart_id: lastIdCart.value,
            quantity: 1,
            satuan_cart: productSelected.value.satuan_dasar_id,
        }
        addToCart(item);
        nextTick(() => {
            inputProduct.value = null;
            inputProductCode.value = '';
            productSelected.value = undefined;
        })
    }
}

// Menambahkan produk ke keranjang
const addToCart = (paramProduct: ItemCart) => {
    cart.value.push({ ...paramProduct, quantity: 1 });
};

// Menghapus produk dari keranjang
const removeFromCart = (cartId: number) => {
    cart.value = cart.value.filter(p => p.cart_id !== cartId);
};

const checkStockAvailability = (item: ItemCart, quantity: number) => {
    if (!item.stock || !item.stock.jumlah) {
        toast.error(`Stok produk ${item.nama_produk} belum diatur!`);
        return false;
    }

    // Cari satuan yang dipilih dan ambil nilai konversi
    const selectedUnit: any = item.harga_produk.find(satuan => satuan.satuan_id === item.satuan_cart);

    const konversi = selectedUnit ? parseFloat(selectedUnit.konversi) : 1;

    // Hitung jumlah stok dalam satuan dasar
    const totalRequestedStock = quantity * konversi;
    const availableStock = parseFloat(item.stock.jumlah.toString());

    if (totalRequestedStock > availableStock) {
        toast.error(`Stok produk ${item.nama_produk} tidak mencukupi!`);
        return false;
    }

    return true;
};

// Menambah jumlah produk
const increaseQuantity = (cartId: number) => {
    const item = cart.value.find(p => p.cart_id === cartId);
    console.log(item);

    if (item) {
        const newQuantity = item.quantity + 1;

        if (checkStockAvailability(item, newQuantity)) {
            item.quantity++;
        }
    }
};

// Mengurangi jumlah produk
const decreaseQuantity = (cartId: number) => {
    const item = cart.value.find(p => p.cart_id === cartId);
    if (item) {
        if (item.quantity > 1) {
            item.quantity--;
        } else {
            removeFromCart(cartId);
        }
    }
};

const changeSatuan = (paramProduct: ItemCart, cart_id: number, satuan_id: string | number) => {
    const satuanObj = paramProduct.harga_produk.find(p => p.satuan_id === satuan_id);
    const item = cart.value.find(p => p.cart_id === cart_id);
    if (item && satuanObj) {
        const oldKonversi = parseFloat(
            (paramProduct.harga_produk.find(p => p.satuan_id === item.satuan_cart)?.konversi || "1").toString()
        );
        const newKonversi = parseFloat(satuanObj.konversi.toString());

        // Konversi jumlah ke satuan baru
        const newQuantity = Math.ceil((item.quantity * oldKonversi) / newKonversi);

        if (checkStockAvailability(item, newQuantity)) {
            item.satuan_cart = satuan_id;
            item.harga_jual = satuanObj.harga_jual ? parseInt(satuanObj.harga_jual.toString()) : 0;
            item.quantity = newQuantity;
        }
    }

}

// Menghitung total harga
const totalPrice = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.harga_jual * item.quantity, 0);
});

watch(inputProductCode, async (newValue) => {
    if (newValue !== "") {
        const itemProduct: ProductPosPrice | null = await fetchProductByKode(newValue);

        if (itemProduct && !itemProduct.stock) {
            toast.error(`Stok produk ${itemProduct.nama_produk} belum diatur!`);
            return;
        }

        if (itemProduct && itemProduct.stock.jumlah <= 0) {
            toast.error(`Stok produk ${itemProduct.nama_produk} sudah habis!`);
            return;
        }

        if (itemProduct) {
            lastIdCart.value++;
            const item: ItemCart = {
                ...itemProduct,
                cart_id: lastIdCart.value,
                quantity: 1,
                satuan_cart: itemProduct.satuan_dasar_id,
            };
            addToCart(item);
        } else {
            console.warn("Produk tidak ditemukan.");
        }
    }
});

watch(() => formSale.metode_pembayaran, (newValue) => {
    if (newValue === 'qris') {
        paidAmount.value = totalPrice.value;
    }
});

// Menghitung kembalian
const changeAmount = computed<number>(() => paidAmount.value ? Math.max(paidAmount.value - totalPrice.value, 0) : 0);

const handleSubmit = () => {

    invoiceNo.value = formSale.no_invoice;

    if (!paidAmount.value) {
        toast.error('Jumlah pembayaran tidak boleh kosong!');
        return;
    }

    if (paidAmount.value < totalPrice.value) {
        toast.error('Jumlah pembayaran kurang dari total harga');
        return;
    }

    let paymentPopup = null;
    if (formSale.metode_pembayaran == 'qris') {
        // Buka pop-up lebih dulu
        paymentPopup = window.open(
            'about:blank',
            'PaymentPopup',
            'width=500,height=600,scrollbars=yes'
        );
    }

    if (formSale.metode_pembayaran == 'qris' && page.props.paymentUrl && paymentPopup) {
        paymentPopup.location.href = page.props.paymentUrl;
        return;
    }

    formSale
        .transform((data) => ({
            ...data,
            paid_amount: paidAmount.value,
            items: cart.value,
        })).post(route('trn.pos.store'), {
            preserveScroll: true,

            onSuccess: async () => {
                // Jika metode pembayaran adalah QRIS, arahkan pop-up ke URL pembayaran
                if (formSale.metode_pembayaran === 'qris' && paymentPopup) {
                    paymentPopup.location.href = page.props.paymentUrl;
                } else {
                    formSale.reset();
                    cart.value = [];
                    paidAmount.value = undefined;
                    nextTick(() => {
                        inputProduct.value = null;
                        inputProductCode.value = '';
                        productSelected.value = undefined;
                    });

                    toast.success(`🎉 Pembayaran berhasil! `, {
                        duration: 4000,
                    });

                    await printinvoice();

                }


            },
        });
}

const printinvoice = async () => {
    // Panggil API untuk generate PDF
    try {
        const response = await axios.get(route('trn.pos.generate.invoice'), {
            params: { invoice_no: invoiceNo.value },
        });

        let popupPrint: any = null;
        // 🚀 Open PDF in Popup & Auto Print
        if (response.data.pdf_url) {
            popupPrint = window.open(response.data.pdf_url, "invoicePrint", "width=640,height=600");

            if (popupPrint) {
                popupPrint.onload = () => {
                    popupPrint.print(); // Auto print saat PDF terbuka
                };

                let timer = setInterval(() => {
                    if (popupPrint.closed) {
                        clearInterval(timer);
                        window.location.reload(); // Refresh halaman setelah popup ditutup
                    }
                }, 500);

            } else {
                toast.error("Popup blocked! Allow pop-ups to print invoice.");
                return;
            }
        }

    } catch (error) {
        toast.error("Gagal memuat invoice.");
        return;
    }
}

window.addEventListener("message", (event) => {
    if (event.data === "payment_success") {
        formSale.reset();
        cart.value = [];
        paidAmount.value = undefined;
        nextTick(() => {
            inputProduct.value = null;
            inputProductCode.value = '';
            productSelected.value = undefined;
        });

        toast.success(`🎉 Pembayaran berhasil! `, {
            duration: 4000,
        });

        printinvoice();
        // Refresh transaksi atau lakukan aksi lain
    }

    if (event.data === "payment_pending") {
        toast.error(`❌ Pembayaran belum dilakukan! `, {
            duration: 4000,
        });
        // Refresh transaksi atau lakukan aksi lain
    }
    if (event.data === "payment_failed") {
        formSale.reset();
        cart.value = [];
        paidAmount.value = undefined;
        nextTick(() => {
            inputProduct.value = null;
            inputProductCode.value = '';
            productSelected.value = undefined;
        });

        toast.error(`❌ Pembayaran dibatalkan! `, {
            duration: 4000,
        });

        window.location.reload();
        // Refresh transaksi atau lakukan aksi lain
    }
});

const addAmount = (amount:any) => {
    paidAmount.value = (paidAmount.value || 0) + amount;
}

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Point of Sale" />

        <div class="px-4 py-6">
            <Heading title="Point of Sale" description="Empower Your Business with the Best POS!" />

            <div class="w-full dark:bg-neutral-700 px-4 py-4 rounded-md">
                <div class="mb-4">
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <div class="flex flex-col mb-4 sm:mb-0 sm:grid sm:grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="no_invoice">No Penjualan</Label>
                                    <div class="flex items-center gap-2">
                                        <Input id="no_invoice" class="mt-1 block w-full" v-model="formSale.no_invoice"
                                            placeholder="#No Invoice" readonly />
                                    </div>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="metode_penjualan">Tipe Penjualan</Label>
                                    <div class="flex items-center gap-2">
                                        <Select id="metode_penjualan" class="mt-1 block w-full"
                                            v-model="formSale.metode_penjualan" required placeholder="Pilih"
                                            :options="Object.entries(props.metodePenjualan).map(([key, value]) => ({ label: value, value: key }))" />
                                        <InputError class="mt-2" :message="formSale.errors.metode_penjualan" />
                                    </div>
                                    <InputError class="mt-2" :message="formSale.errors.metode_penjualan" />
                                </div>

                                <div class="grid gap-2">
                                    <Label>Item/Barang</Label>
                                    <Select2ProductPos class="w-full" v-model="inputProduct"
                                        v-model:kode_produk="inputProductCode"
                                        v-model:selected-product="productSelected"
                                        v-model:reset="resetSelectedProduct" />
                                </div>
                                <div class="grid gap-2">
                                    <Label>Metode Pembayaran</Label>
                                    <Select id="metode_pembayaran" class="mt-1 block w-full"
                                        v-model="formSale.metode_pembayaran" required placeholder="Pilih"
                                        :options="Object.entries(props.metodePembayaran).map(([key, value]) => ({ label: value, value: key }))" />
                                </div>

                                <div class="grid col-span-2 gap-2">
                                    <div class="w-full h-full flex items-center justify-between">
                                        <Button @click="addProductToCart()" :disabled="formSale.processing">Tambah
                                            Item</Button>
                                    </div>
                                </div>
                            </div>
                            <!-- Detail Sales -->
                            <div class="overflow-x-auto mt-4">
                                <table
                                    class="min-w-full divide-y-2 divide-neutral-200 bg-white text-sm dark:divide-neutral-700 dark:bg-neutral-900">
                                    <thead class="ltr:text-left rtl:text-right">
                                        <tr>
                                            <th
                                                class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                                #</th>
                                            <th
                                                class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                                Item
                                            </th>
                                            <th
                                                class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                                Satuan
                                            </th>
                                            <th
                                                class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                                Jumlah
                                            </th>
                                            <th
                                                class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                                Harga
                                            </th>
                                            <th
                                                class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white text-left">
                                                Total
                                            </th>
                                            <th class="px-4 py-2 w-20"></th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                        <template v-if="cart.length === 0">
                                            <tr>
                                                <td colspan="7"
                                                    class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-white text-center">
                                                    Belum ada data
                                                </td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr v-for="item in cart" :key="item.id">
                                                <td
                                                    class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200">
                                                    {{
                                                        item.cart_id
                                                    }}</td>
                                                <td
                                                    class="px-4 py-2 font-medium whitespace-nowrap text-gray-900 dark:text-white">
                                                    {{ item.nama_produk }}
                                                </td>
                                                <td
                                                    class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200">
                                                    <Select :id="`satuan-${item.cart_id}`" class="mt-1 block w-full"
                                                        v-model="item.satuan_cart" required
                                                        @change="changeSatuan(item, item.cart_id, item.satuan_cart)"
                                                        placeholder="Pilih"
                                                        :options="item.harga_produk.map((option) => ({ label: option.satuan?.nama_satuan ?? '', value: option.satuan_id }))" />
                                                </td>
                                                <td
                                                    class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200">
                                                    <div class="flex items-center gap-2">
                                                        <button @click.prevent="() => decreaseQuantity(item.cart_id)"
                                                            class="inline-block p-3 text-gray-700 hover:bg-gray-50 focus:relative dark:text-gray-200 dark:hover:bg-blue-600"
                                                            title="Decrease Quantity">
                                                            <Minus class="size-4" />
                                                        </button>
                                                        <span>
                                                            {{ item.quantity }}
                                                        </span>
                                                        <button @click.prevent="() => increaseQuantity(item.cart_id)"
                                                            class="inline-block p-3 text-gray-700 hover:bg-gray-50 focus:relative dark:text-gray-200 dark:hover:bg-blue-600"
                                                            title="Increase Quantity">
                                                            <Plus class="size-4" />
                                                        </button>
                                                    </div>
                                                </td>
                                                <td
                                                    class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200">
                                                    {{
                                                        formatRupiah(item.harga_jual) }}</td>
                                                <td
                                                    class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200">
                                                    {{
                                                        formatRupiah(item.harga_jual * item.quantity) }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap w-20">
                                                    <span
                                                        class="inline-flex overflow-hidden rounded-md border bg-white shadow-xs dark:border-gray-800 dark:bg-neutral-900">
                                                        <button @click.prevent="() => removeFromCart(item.cart_id)"
                                                            method="delete"
                                                            class="inline-block p-3 text-gray-700 hover:bg-gray-50 focus:relative dark:text-gray-200 dark:hover:bg-blue-600"
                                                            title="Delete Data">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="size-4">
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
                            <!-- Table Items -->
                        </div>
                        <!-- Main Content -->
                        <div class="h-min flex flex-col justify-center gap-2">
                            <div class="w-full h-full flex items-center justify-between px-4 py-4 border border-white">
                                <p class="w-full text-2xl font-medium text-left">Total</p>
                                <p class="w-full text-2xl font-medium text-right">{{ formatRupiah(totalPrice) }}</p>
                            </div>
                            <div class="w-full h-full flex items-center justify-between px-4 py-4 border border-white">
                                <p class="w-full text-2xl font-medium text-left">Kembalian</p>
                                <p class="w-full text-2xl font-medium text-right">{{ formatRupiah(changeAmount) }}
                                </p>
                            </div>
                            <div class="grid gap-2">
                                <div class="w-full h-full flex items-center justify-between gap-4">
                                    <Input id="paid_amount" class="mt-1 block w-full" v-model="paidAmount"
                                        placeholder="Jumlah Pembayaran" autocomplete="off" />

                                    <Button @click="handleSubmit()" :disabled="formSale.processing">Bayar</Button>
                                </div>
                            </div>
                            <!-- Tombol Nominal Pembayaran -->
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-2">
                                <button v-for="amount in [1000, 5000, 10000, 15000, 20000, 25000, 50000, 100000]"
                                    :key="amount" @click="addAmount(amount)"
                                    class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                                    {{ formatRupiah(amount) }}
                                </button>
                            </div>
                        </div>
                        <!-- Summary Content -->
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>