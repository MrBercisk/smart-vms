<!-- resources/js/Pages/Error/Index.vue -->
<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4">
        <div class="text-center max-w-md">
            <div class="text-7xl font-bold text-primary-800 dark:text-primary-400 mb-2">
                {{ status }}
            </div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                {{ title }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mb-8">
                {{ description }}
            </p>
            <Link href="/dashboard" class="btn-primary inline-block">
                Kembali ke Dashboard
            </Link>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({ status: Number })

const messages = {
    403: {
        title: 'Akses Ditolak',
        description: 'Anda tidak memiliki izin untuk mengakses halaman ini.',
    },
    404: {
        title: 'Halaman Tidak Ditemukan',
        description: 'Halaman yang Anda cari tidak ada atau telah dipindahkan.',
    },
    419: {
        title: 'Sesi Berakhir',
        description: 'Halaman ini sudah kedaluwarsa, silakan refresh dan coba lagi.',
    },
    429: {
        title: 'Terlalu Banyak Permintaan',
        description: 'Anda mengirim terlalu banyak request. Coba lagi sebentar.',
    },
    500: {
        title: 'Terjadi Kesalahan Server',
        description: 'Maaf, ada masalah di sisi server kami. Tim kami sudah diberi tahu.',
    },
    503: {
        title: 'Layanan Sedang Maintenance',
        description: 'Sistem sedang dalam pemeliharaan, silakan coba beberapa saat lagi.',
    },
}

const title = computed(() => messages[props.status]?.title ?? 'Terjadi Kesalahan')
const description = computed(() => messages[props.status]?.description ?? 'Silakan coba lagi nanti.')
</script>