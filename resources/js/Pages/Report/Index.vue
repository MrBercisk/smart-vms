<!-- resources/js/Pages/Report/Index.vue -->
<template>
    <AppLayout title="Laporan">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Laporan Kunjungan</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Lihat dan unduh laporan kunjungan tamu</p>
        </div>

        <!-- Filter -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="label">Dari Tanggal</label>
                    <input v-model="filter.start_date" type="date" class="input" />
                </div>
                <div>
                    <label class="label">Sampai Tanggal</label>
                    <input v-model="filter.end_date" type="date" class="input" />
                </div>
                <div>
                    <label class="label">Departemen</label>
                    <select v-model="filter.department_id" class="input">
                        <option value="">Semua</option>
                        <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.department_name }}</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button @click="loadReport" class="btn-primary w-full">Tampilkan</button>
                </div>
            </div>
        </div>

        <!-- Export buttons -->
        <div class="flex gap-2 mb-4">
            <a :href="exportExcelUrl" class="btn-secondary inline-flex items-center gap-2">
                📊 Export Excel
            </a>
            <a :href="exportPdfUrl" class="btn-secondary inline-flex items-center gap-2">
                📄 Export PDF
            </a>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6" v-if="visits.length > 0">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ visits.length }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Kunjungan</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ avgDuration }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Rata-rata Durasi</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ uniqueCompanies }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Perusahaan Berbeda</div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="th">No. Kunjungan</th>
                        <th class="th">Nama Tamu</th>
                        <th class="th">Perusahaan</th>
                        <th class="th">Departemen</th>
                        <th class="th">Host</th>
                        <th class="th">Check In</th>
                        <th class="th">Check Out</th>
                        <th class="th">Durasi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="visits.length === 0">
                        <td colspan="8" class="text-center py-8 text-gray-400">
                            {{ loaded ? 'Tidak ada data pada periode ini' : 'Pilih periode lalu klik Tampilkan' }}
                        </td>
                    </tr>
                    <tr
                        v-for="v in visits"
                        :key="v.id"
                        class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                    >
                        <td class="td text-gray-400 font-mono text-xs">{{ v.visit_number }}</td>
                        <td class="td font-medium text-gray-900 dark:text-white">{{ v.visitor?.full_name }}</td>
                        <td class="td text-gray-500 dark:text-gray-400">{{ v.visitor?.company_name ?? '-' }}</td>
                        <td class="td">{{ v.department?.department_name }}</td>
                        <td class="td text-gray-500 dark:text-gray-400">{{ v.employee?.employee_name }}</td>
                        <td class="td">{{ formatTime(v.arrival_time) }}</td>
                        <td class="td">{{ v.exit_time ? formatTime(v.exit_time) : '-' }}</td>
                        <td class="td">{{ durationLabel(v.duration_minutes) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import api from '@/lib/api'

const visits      = ref([])
const departments = ref([])
const loaded      = ref(false)

const today = new Date().toISOString().slice(0, 10)
const firstOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0, 10)

const filter = ref({
    start_date: firstOfMonth,
    end_date: today,
    department_id: '',
})

const avgDuration = computed(() => {
    const withDuration = visits.value.filter(v => v.duration_minutes)
    if (withDuration.length === 0) return '-'
    const avg = Math.round(withDuration.reduce((sum, v) => sum + v.duration_minutes, 0) / withDuration.length)
    return durationLabel(avg)
})

const uniqueCompanies = computed(() => {
    const companies = visits.value
        .map(v => v.visitor?.company_name)
        .filter(Boolean)
    return new Set(companies).size
})

const exportExcelUrl = computed(() => {
    const params = new URLSearchParams({
        start_date: filter.value.start_date,
        end_date: filter.value.end_date,
        ...(filter.value.department_id ? { department_id: filter.value.department_id } : {}),
    })
    return `/api/reports/export-excel?${params}`
})

const exportPdfUrl = computed(() => {
    const params = new URLSearchParams({
        start_date: filter.value.start_date,
        end_date: filter.value.end_date,
        ...(filter.value.department_id ? { department_id: filter.value.department_id } : {}),
    })
    return `/api/reports/export-pdf?${params}`
})

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

const fetchDepartments = async () => {
    const res = await api.get('/departments')
    departments.value = res.data
}

const loadReport = async () => {
    const params = new URLSearchParams({
        date: filter.value.start_date, // fallback for daily endpoint compatibility
    })
    // Gunakan endpoint daily per tanggal tunggal jika start = end,
    // selain itu ambil semua visits lalu filter manual by date range.
    const res = await api.get('/visits')
    visits.value = res.data.filter(v => {
        if (!v.arrival_time) return false
        const date = v.arrival_time.slice(0, 10)
        const matchDate = date >= filter.value.start_date && date <= filter.value.end_date
        const matchDept = !filter.value.department_id || v.department_id === filter.value.department_id
        return matchDate && matchDept
    })
    loaded.value = true
}

onMounted(() => {
    fetchDepartments()
    loadReport()
})
</script>