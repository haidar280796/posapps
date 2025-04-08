<script setup lang="ts">
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { cn } from '@/lib/utils';
import axios from 'axios';
import vSelect from 'vue-select';
import "vue-select/dist/vue-select.css";
import { useVModel } from '@vueuse/core';
import type { HTMLAttributes } from 'vue';
import { type ProductPosPrice } from '@/types';

const products = ref<Array<any>>([]); // Data kategori
const searchQueryProduct = ref(""); // Query pencarian
const isLoading = ref(false);
const page = ref(1);
const lastPage = ref(1);
const selectRef = ref<InstanceType<typeof vSelect> | null>(null);

const props = defineProps<{
    defaultValue?: string | number;
    modelValue?: string | number | null;
    class?: HTMLAttributes['class'];
    disabled?: boolean;
    reset?: boolean;
}>();

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number | null): void;
    (e: 'update:selectedProduct', payload: ProductPosPrice): void;
    (e: 'enter', payload: ProductPosPrice): void;
    (e: 'update:reset', payload: boolean): void;
    (e: 'update:kode_produk', payload: string | null): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});

const initialProductData: ProductPosPrice = {
    id: '',
    nama_produk: '',
    kode_produk: '',
    barcode: '',
    harga_beli: 0,
    harga_jual: 0,
    satuan_dasar_id: '',
    harga_produk: [],
    stock: {
        id: '',
        product_id: '',
        warehouse_id: '',
        jumlah: 0
    }
}

const selectedProduct = computed(() => {
    return products.value.find(p => p.id === modelValue.value) || initialProductData;

});

watch(modelValue, (newValue) => {
    const product = products.value.find(p => p.id === newValue);
    if (product) {
        emits('update:selectedProduct', {
            id: product.id,
            nama_produk: product.nama_produk,
            kode_produk: product.kode_produk,
            barcode: product.barcode,
            harga_beli: product.harga_beli,
            harga_jual: product.harga_jual,
            satuan_dasar_id: product.satuan_dasar_id,
            harga_produk: product.product_pricings,
            stock: product.stock
        });

        // nextTick(() => emits('update:reset', true));
    }

});

const fetchProducts = async () => {
    if (isLoading.value) return;

    isLoading.value = true;
    try {
        const response = await axios.get(`/api/products?search=${searchQueryProduct.value}&page=${page.value}`);
        const result = response.data;
        products.value = page.value === 1 ? result.data : [...products.value, ...result.data];
        lastPage.value = result.pagination.last_page;
    } catch (error) {
        console.error("Error:", error);
    } finally {
        isLoading.value = false;
    }

}

const handleEnter = (event: KeyboardEvent) => {
    if (event.key === 'Enter') {
        emits('update:kode_produk', searchQueryProduct.value);

        // Reset nilai pencarian & pilihan produk
        searchQueryProduct.value = '';
        modelValue.value = null; 

        nextTick(() => {
            emits('update:modelValue', null);
            emits('update:kode_produk', '');
        });

        // Paksa input v-select mereset tampilannya
        nextTick(() => {
            if (selectRef.value) {
                (selectRef.value as any).search = '';
            }
        });
    }
};

const resetSelection = () => {
    modelValue.value = null;
    searchQueryProduct.value = '';
    nextTick(() => {
        if (selectRef.value) {
            selectRef.value.$emit('input', undefined);
        }
    })
};

watch(() => props.reset, (newReset) => {
    if (newReset) {
        resetSelection();
        nextTick(() => emits('update:reset', false));
    }
});

onMounted(() => {
    fetchProducts();
});

watch(searchQueryProduct, () => {
    page.value = 1;
    fetchProducts();
    // if (searchQueryProduct.value) fetchProducts();
    // if (!searchQueryProduct.value) products.value = []; // Reset data saat pencarian kosong;

});

</script>

<template>
    <div class="w-full relative">
        <label class="sr-only"> Search for... </label>
        <v-select id="select_products" ref="selectRef" class="style-chooser" v-model="modelValue"
            :options="products.map((product) => ({ name: product.nama_produk, id: product.id }))" :filterable="false"
            label="name" placeholder="Pilih Produk" :class="cn(
                'h-10 w-full rounded-xl border border-input bg-background text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm rounded-md',
                props.class,
            )
                " :reduce="(product: any) => product.id" @search="(query: any) => searchQueryProduct = query"
            @clear="resetSelection"
            @keydown.enter="handleEnter">
            <template #no-options>
                <span v-if="isLoading" class="!text-gray-900">Loading...</span>
                <span v-else class="!text-gray-900">Tidak ada hasil</span>
            </template>
        </v-select>
    </div>
</template>
