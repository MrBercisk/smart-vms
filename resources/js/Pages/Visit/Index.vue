<!-- resources/js/Pages/Visit/Index.vue -->
<template>
    <AppLayout title="Kunjungan">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Kunjungan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Check in / check out tamu</p>
            </div>
            <div class="flex gap-2">
                <button @click="openCheckout()" class="btn-secondary">
                    Check Out
                </button>
                <button @click="openCheckin()" class="btn-primary">
                    + Check In
                </button>
            </div>
        </div>

        <!-- Filter Status -->
        <div class="flex gap-2 mb-4">
            <button
                v-for="s in statusFilters"
                :key="s.value"
                @click="filterStatus = s.value"
                :class="[
                    'px-3 py-1.5 rounded-lg text-sm font-medium transition-colors',
                    filterStatus === s.value
                        ? 'bg-primary-800 text-white'
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'
                ]"
            >
                {{ s.label }}
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="th">No. Kunjungan</th>
                        <th class="th">Tamu</th>
                        <th class="th">Host</th>
                        <th class="th">Departemen</th>
                        <th class="th">Check In</th>
                        <th class="th">Check Out</th>
                        <th class="th">Durasi</th>
                        <th class="th">Status</th>
                        <th class="th">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="filtered.length === 0">
                        <td colspan="9" class="text-center py-8 text-gray-400">Tidak ada data</td>
                    </tr>
                    <tr
                        v-for="v in filtered"
                        :key="v.id"
                        class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                    >
                        <td class="td text-gray-400 font-mono text-xs">{{ v.visit_number }}</td>
                        <td class="td font-medium text-gray-900 dark:text-white">{{ v.visitor?.full_name }}</td>
                        <td class="td text-gray-500 dark:text-gray-400">{{ v.employee?.employee_name }}</td>
                        <td class="td text-gray-500 dark:text-gray-400">{{ v.department?.department_name }}</td>
                        <td class="td">{{ formatTime(v.arrival_time) }}</td>
                        <td class="td">{{ v.exit_time ? formatTime(v.exit_time) : '-' }}</td>
                        <td class="td">{{ durationLabel(v.duration_minutes) }}</td>
                        <td class="td">
                            <span :class="statusBadge(v.status)">{{ statusLabel(v.status) }}</span>
                        </td>
                        <td class="td">
                            <a
                                :href="`/api/visits/${v.id}/pass`"
                                target="_blank"
                                class="text-blue-600 hover:text-blue-800 text-xs font-medium"
                            >
                                Cetak Pass
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Check In -->
        <div v-if="showCheckin" class="modal-overlay" @click.self="closeCheckin">
            <div class="modal">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Check In Tamu</h3>
                    <button @click="closeCheckin" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="label">Tamu <span class="text-red-500">*</span></label>
                        <select v-model="form.visitor_id" class="input">
                            <option value="">-- Pilih Tamu --</option>
                            <option v-for="vis in visitors" :key="vis.id" :value="vis.id">
                                {{ vis.full_name }} {{ vis.company_name ? `(${vis.company_name})` : '' }}
                            </option>
                        </select>
                        <p v-if="errors.visitor_id" class="text-red-500 text-xs mt-1">{{ errors.visitor_id[0] }}</p>
                    </div>
                    <div>
                        <label class="label">Departemen <span class="text-red-500">*</span></label>
                        <select v-model="form.department_id" class="input" @change="form.employee_id = ''">
                            <option value="">-- Pilih Departemen --</option>
                            <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.department_name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Host / Karyawan <span class="text-red-500">*</span></label>
                        <select v-model="form.employee_id" class="input">
                            <option value="">-- Pilih Karyawan --</option>
                            <option v-for="e in filteredEmployees" :key="e.id" :value="e.id">{{ e.employee_name }}</option>
                        </select>
                        <p v-if="errors.employee_id" class="text-red-500 text-xs mt-1">{{ errors.employee_id[0] }}</p>
                    </div>
                    <div>
                        <label class="label">Keperluan <span class="text-red-500">*</span></label>
                        <textarea v-model="form.purpose" class="input" rows="3" placeholder="Tujuan kunjungan..." />
                        <p v-if="errors.purpose" class="text-red-500 text-xs mt-1">{{ errors.purpose[0] }}</p>
                    </div>

                    <!-- Photo uploads -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Foto Tamu</label>
                            <label class="btn-secondary cursor-pointer block text-center">
                                {{ visitorPhotoFile ? 'Foto dipilih' : 'Pilih Foto' }}
                                <input type="file" accept="image/*" class="hidden" @change="e => visitorPhotoFile = e.target.files[0]" />
                            </label>
                        </div>
                        <div>
                            <label class="label">Foto Identitas</label>
                            <label class="btn-secondary cursor-pointer block text-center">
                                {{ identityPhotoFile ? 'Foto dipilih' : 'Pilih Foto' }}
                                <input type="file" accept="image/*" class="hidden" @change="e => identityPhotoFile = e.target.files[0]" />
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="closeCheckin" class="btn-secondary">Batal</button>
                    <button @click="submitCheckin" :disabled="loading" class="btn-primary">
                        {{ loading ? 'Memproses...' : 'Check In' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Check Out -->
        <div v-if="showCheckoutModal" class="modal-overlay" @click.self="showCheckoutModal = false">
            <div class="modal max-w-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Check Out Tamu</h3>
                <label class="label">No. Kunjungan <span class="text-red-500">*</span></label>
                <input
                    v-model="checkoutNumber"
                    type="text"
                    class="input"
                    placeholder="Scan atau ketik No. Kunjungan"
                />
                <p v-if="checkoutError" class="text-red-500 text-xs mt-2">{{ checkoutError }}</p>

                <div v-if="checkoutResult" class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg text-sm">
                    <p class="text-green-700 dark:text-green-400 font-medium">Check out berhasil</p>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">Durasi: {{ checkoutResult.duration_label }}</p>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="showCheckoutModal = false" class="btn-secondary">Tutup</button>
                    <button @click="submitCheckout" :disabled="loading || !checkoutNumber" class="btn-primary">
                        {{ loading ? 'Memproses...' : 'Check Out' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import api from '@/lib/api'

const visits        = ref([])
const visitors       = ref([])
const employees       = ref([])
const departments     = ref([])
const filterStatus    = ref('all')
const showCheckin     = ref(false)
const showCheckoutModal = ref(false)
const loading         = ref(false)
const errors          = ref({})
const visitorPhotoFile  = ref(null)
const identityPhotoFile = ref(null)
const checkoutNumber  = ref('')
const checkoutError   = ref('')
const checkoutResult  = ref(null)

const statusFilters = [
    { label: 'Semua',       value: 'all' },
    { label: 'Checked In',  value: 'checked_in' },
    { label: 'Checked Out', value: 'checked_out' },
]

const form = ref({
    visitor_id: '',
    employee_id: '',
    department_id: '',
    purpose: '',
})

const filtered = computed(() =>
    filterStatus.value === 'all'
        ? visits.value
        : visits.value.filter(v => v.status === filterStatus.value)
)

const filteredEmployees = computed(() =>
    form.value.department_id
        ? employees.value.filter(e => e.department_id === form.value.department_id)
        : employees.value
)

const statusBadge = (status) => {
    const map = {
        waiting:     'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
        checked_in:  'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
        checked_out: 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
    }
    return `px-2 py-1 rounded-full text-xs font-medium ${map[status]}`
}

const statusLabel = (status) => {
    const map = { waiting: 'Menunggu', checked_in: 'Sedang Berkunjung', checked_out: 'Selesai' }
    return map[status] ?? status
}

const formatTime = (datetime) => {
    if (!datetime) return '-'
    return new Date(datetime).toLocaleString('id-ID', {
        day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'
    })
}

const durationLabel = (minutes) => {
    if (!minutes) return '-'
    const h = Math.floor(minutes / 60)
    const m = minutes % 60
    if (h > 0 && m > 0) return `${h}j ${m}m`
    if (h > 0) return `${h}j`
    return `${m}m`
}

const fetchVisits = async () => {
    const res = await api.get('/visits')
    visits.value = res.data
}

const fetchVisitors = async () => {
    const res = await api.get('/visitors')
    visitors.value = res.data
}

const fetchEmployees = async () => {
    const res = await api.get('/employees')
    employees.value = res.data
}

const fetchDepartments = async () => {
    const res = await api.get('/departments')
    departments.value = res.data
}

const openCheckin = () => {
    errors.value = {}
    visitorPhotoFile.value = null
    identityPhotoFile.value = null
    form.value = { visitor_id: '', employee_id: '', department_id: '', purpose: '' }
    showCheckin.value = true
}

const closeCheckin = () => {
    showCheckin.value = false
    errors.value = {}
}

const submitCheckin = async () => {
    loading.value = true
    errors.value  = {}
    try {
        const formData = new FormData()
        Object.entries(form.value).forEach(([key, val]) => {
            if (val !== null && val !== '') formData.append(key, val)
        })
        if (visitorPhotoFile.value) formData.append('visitor_photo', visitorPhotoFile.value)
        if (identityPhotoFile.value) formData.append('identity_photo', identityPhotoFile.value)

        await api.post('/visits/checkin', formData, true)
        await fetchVisits()
        closeCheckin()
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors
        }
    } finally {
        loading.value = false
    }
}

const openCheckout = () => {
    checkoutNumber.value = ''
    checkoutError.value  = ''
    checkoutResult.value = null
    showCheckoutModal.value = true
}

const submitCheckout = async () => {
    loading.value = true
    checkoutError.value  = ''
    checkoutResult.value = null
    try {
        const res = await api.post('/visits/checkout', { visit_number: checkoutNumber.value })
        checkoutResult.value = res.data
        await fetchVisits()
    } catch (e) {
        checkoutError.value = e.response?.data?.message ?? 'Gagal melakukan check out'
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchVisits()
    fetchVisitors()
    fetchEmployees()
    fetchDepartments()
})
</script>