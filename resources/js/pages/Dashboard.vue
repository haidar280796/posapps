<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { formatRupiah } from '@/lib/utils';
import LineChart from '@/components/LineChart.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface Props {
    dashboard: any,
    pendapatanBulanan: any,
    lowStockItems: Array<any>,
        topProducts: Array<any>,
}

const props = defineProps<Props>();

const totalPendapatan = ref(props.dashboard.total_pendapatan);
const totalPengeluaran = ref(props.dashboard.total_pengeluaran);
const labaBersih = ref(props.dashboard.laba_bersih);
const piutangBelumLunas = ref(props.dashboard.piutang_belum_lunas);
const saldoKas = ref(props.dashboard.saldo_kas);

const pendapatanBulanan = ref(props.pendapatanBulanan);

const monthlyChartData = computed(() => ({
    labels: pendapatanBulanan.value.map((item: { bulan: any; }) => item.bulan),
    values: pendapatanBulanan.value.map((item: { total: any; }) => item.total)
}));
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-sidebar p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold">Total Pendapatan</h2>
                    <p class="text-green-400 text-2xl font-bold">{{ formatRupiah(totalPendapatan) }}</p>
                </div>

                <div class="bg-sidebar p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold">Total Pengeluaran</h2>
                    <p class="text-red-400 text-2xl font-bold">{{ formatRupiah(totalPengeluaran) }}</p>
                </div>

                <div class="bg-sidebar p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold">Laba Bersih</h2>
                    <p class="text-blue-400 text-2xl font-bold">{{ formatRupiah(labaBersih) }}</p>
                </div>

                <div class="bg-sidebar p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold">Piutang Belum Lunas</h2>
                    <p class="text-yellow-400 text-2xl font-bold">{{ formatRupiah(piutangBelumLunas) }}</p>
                </div>

                <div class="bg-sidebar p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold">Saldo Kas</h2>
                    <p class="text-green-300 text-2xl font-bold">{{ formatRupiah(saldoKas) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1">
                <div class="bg-sidebar p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-2">Pendapatan Bulanan</h2>
                    <div class="bg-sidebar p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-2">Pendapatan Bulanan</h2>
                        <LineChart :chartData="monthlyChartData" chartLabel="Pendapatan Bulanan" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-sidebar p-6 rounded-lg shadow text-white">
                    <h2 class="text-xl font-semibold mb-4">📉 Stok Barang Hampir Habis</h2>
                    <table class="w-full border border-sidebar-accent">
                        <thead>
                            <tr class="bg-sidebar-accent">
                                <th class="py-2 px-4 text-left">Produk</th>
                                <th class="py-2 px-4 text-center">Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in lowStockItems" :key="item.id" class="border-t border-gray-700">
                                <td class="py-2 px-4">{{ item.product.nama_produk }}</td>
                                <td class="py-2 px-4 text-center text-red-400 font-bold">{{ item.jumlah }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-sidebar p-6 rounded-lg shadow text-white">
                    <h2 class="text-xl font-semibold mb-4">🔥 Produk Terlaris</h2>
                    <table class="w-full border border-sidebar-accent">
                        <thead>
                            <tr class="bg-sidebar-accent">
                                <th class="py-2 px-4 text-left">Produk</th>
                                <th class="py-2 px-4 text-center">Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in topProducts" :key="product.product_id" class="border-t border-gray-700">
                                <td class="py-2 px-4">{{ product.product.nama_produk }}</td>
                                <td class="py-2 px-4 text-center text-green-400 font-bold">{{ product.total_sold }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
