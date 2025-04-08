<script setup lang="ts">
import { onMounted } from 'vue';
import { CheckCircleIcon } from 'lucide-vue-next';

onMounted(() => {
    window.opener.postMessage("payment_failed", "*");
    window.close();
});
</script>

<template>
    <div class="flex flex-col items-center justify-center h-screen bg-red-100">
        <div class="bg-white p-6 rounded-2xl shadow-lg text-center max-w-md">
            <CheckCircleIcon class="w-16 h-16 text-red-500 mx-auto" />
            <h2 class="text-2xl font-bold text-red-700 mt-4">Pembayaran Gagal!</h2>
            <p class="text-gray-600 mt-2">Silakan melakukan pesanan ulang.</p>
            <p class="text-gray-500 text-sm">Halaman ini akan ditutup secara otomatis...</p>
        </div>
    </div>
</template>
