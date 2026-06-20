<template>
    <div class="min-h-screen bg-gradient-to-br from-primary-800 to-primary-900 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">

            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-white text-2xl font-bold">V</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Check Out</h1>
                <p class="text-gray-500 mt-1">Terima kasih telah berkunjung</p>
            </div>

            <!-- Success State -->
            <div v-if="success" class="text-center py-8">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">✓</span>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Check-out Berhasil!</h2>
                <p class="text-gray-500 mb-1">Durasi kunjungan: <strong>{{ success.duration_label }}</strong></p>
                <p class="text-gray-500 mb-6">{{ success.message }}</p>
                <button @click="resetForm" class="btn-primary">
                    Selesai
                </button>
            </div>

            <!-- Lookup confirmation -->
            <div v-else-if="visitInfo" class="space-y-4">
                <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">No. Kunjungan</span>
                        <span class="font-medium text-gray-900">{{ visitInfo.visit_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nama</span>
                        <span class="font-medium text-gray-900">{{ visitInfo.visitor_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Menemui</span>
                        <span class="font-medium text-gray-900">{{ visitInfo.employee }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Waktu Masuk</span>
                        <span class="font-medium text-gray-900">{{ visitInfo.arrival_time }}</span>
                    </div>
                </div>

                <p v-if="checkoutError" class="text-red-500 text-sm text-center">{{ checkoutError }}</p>

                <div class="flex gap-3">
                    <button @click="visitInfo = null" class="btn-secondary flex-1 py-3">Batal</button>
                    <button @click="confirmCheckout" :disabled="loading" class="btn-primary flex-1 py-3">
                        {{ loading ? 'Memproses...' : 'Konfirmasi Check Out' }}
                    </button>
                </div>
            </div>

            <!-- Input form -->
            <form v-else @submit.prevent="lookupVisit" class="space-y-4">
                <div>
                    <label class="label">No. Kunjungan <span class="text-red-500">*</span></label>
                    <input
                        v-model="visitNumber"
                        type="text"
                        class="input text-lg py-3 text-center font-mono"
                        placeholder="Contoh: VST202506170001"
                        required
                    />
                    <p class="text-xs text-gray-400 mt-2 text-center">
                        Lihat No. Kunjungan pada visitor pass Anda, atau scan QR code di pass
                    </p>
                </div>

                <p v-if="lookupError" class="text-red-500 text-sm text-center">{{ lookupError }}</p>

                <button type="submit" :disabled="loading" class="btn-primary w-full py-4 text-lg">
                    {{ loading ? 'Mencari...' : 'Cari Kunjungan' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const API_BASE = '/api/public'

const visitNumber  = ref('')
const visitInfo     = ref(null)
const loading        = ref(false)
const lookupError    = ref('')
const checkoutError  = ref('')
const success         = ref(null)

const getCookie = (name) => {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'))
    return match ? decodeURIComponent(match[2]) : null
}

// Auto-fill dari query string kalau diakses lewat QR (?visit=VST202506170001)
onMounted(() => {
    const params = new URLSearchParams(window.location.search)
    const fromQr = params.get('visit')
    if (fromQr) {
        visitNumber.value = fromQr
        lookupVisit()
    }
})

const lookupVisit = async () => {
    loading.value = true
    lookupError.value = ''
    try {
        const res = await fetch(`${API_BASE}/visits/lookup/${visitNumber.value}`, {
            headers: { 'Accept': 'application/json' }
        })
        const data = await res.json()

        if (!res.ok) {
            lookupError.value = data.message ?? 'Nomor kunjungan tidak ditemukan'
            return
        }

        visitInfo.value = data
    } catch (e) {
        lookupError.value = 'Gagal terhubung ke server'
    } finally {
        loading.value = false
    }
}

const confirmCheckout = async () => {
    loading.value = true
    checkoutError.value = ''
    try {
        const res = await fetch(`${API_BASE}/checkout`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
            },
            body: JSON.stringify({ visit_number: visitInfo.value.visit_number }),
        })
        const data = await res.json()

        if (!res.ok) {
            checkoutError.value = data.message ?? 'Gagal melakukan check-out'
            return
        }

        success.value = data
    } catch (e) {
        checkoutError.value = 'Gagal terhubung ke server'
    } finally {
        loading.value = false
    }
}

const resetForm = () => {
    visitNumber.value = ''
    visitInfo.value = null
    success.value = null
    lookupError.value = ''
    checkoutError.value = ''
}
</script>