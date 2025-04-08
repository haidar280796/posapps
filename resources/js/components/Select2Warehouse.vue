<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { cn } from '@/lib/utils';
import axios from 'axios';
import vSelect from 'vue-select';
import "vue-select/dist/vue-select.css";
import { useVModel } from '@vueuse/core';
import type { HTMLAttributes } from 'vue';

const warehouses = ref<Array<any>>([]); // Data kategori
const searchQueryWarehouse = ref(""); // Query pencarian
const isLoading = ref(false);
const page = ref(1);
const lastPage = ref(1);

const props = defineProps<{
    defaultValue?: string | number;
    modelValue?: string | number;
    class?: HTMLAttributes['class'];
    disabled?: boolean;
}>();

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});

const fetchWerehouses = async () => {
    if (isLoading.value) return;

    isLoading.value = true;
    try {
        const response = await axios.get(`/api/warehouses?search=${searchQueryWarehouse.value}&page=${page.value}`);
        const result = response.data;

        warehouses.value = page.value === 1 ? result.data : [...warehouses.value, ...result.data];

        lastPage.value = result.pagination.last_page;
    } catch (error) {
        console.error("Error:", error);
    } finally {
        isLoading.value = false;
    }

}
onMounted(() => {
    fetchWerehouses();
});

watch(searchQueryWarehouse, () => {
    page.value = 1;
    fetchWerehouses();
});
</script>


<template>
    <div class="w-full relative">
        <label class="sr-only"> Search for... </label>
        <v-select id="select_warehouses" class="style-chooser" 
        v-model="modelValue" :options="warehouses" :filterable="false" label="name" placeholder="Pilih Toko/Gudang"
        :class="
            cn(
                'h-10 w-full rounded-xl !border !border-input bg-background text-base !ring-offset-background placeholder:text-muted-foreground !focus-visible:outline-none !focus-visible:ring-2 !focus-visible:ring-ring !focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                props.class,
            )
        "
            :reduce="(warehouse: any) => warehouse.id" @search="(query: any) => searchQueryWarehouse = query">
            <template #no-options>
                <span v-if="isLoading" class="!text-gray-900">Loading...</span>
                <span v-else class="!text-gray-900">Tidak ada hasil</span>
            </template>
        </v-select>
    </div>
</template>

