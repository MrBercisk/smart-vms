<!-- resources/js/Pages/AuditLog/Index.vue -->
<template>
    <AppLayout title="Audit Log">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Audit Log</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Riwayat aktivitas sistem</p>
        </div>

        <!-- Filter -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="label">Tanggal</label>
                    <input v-model="filter.date" type="date" class="input" @change="fetchLogs(1)" />
                </div>
                <div>
                    <label class="label">Module</label>
                    <select v-model="filter.module" class="input" @change="fetchLogs(1)">
                        <option value="">Semua</option>
                        <option value="Visit">Visit</option>
                        <option value="Appointment">Appointment</option>
                        <option value="Department">Department</option>
                        <option value="Employee">Employee</option>
                        <option value="Visitor">Visitor</option>
                    </select>
                </div>
                <div>
                    <label class="label">Aktivitas</label>
                    <select v-model="filter.activity" class="input" @change="fetchLogs(1)">
                        <option value="">Semua</option>
                        <option value="check_in">Check In</option>
                        <option value="check_out">Check Out</option>
                        <option value="create_appointment">Buat Appointment</option>
                        <option value="approve_appointment">Approve Appointment</option>
                        <option value="reject_appointment">Reject Appointment</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button @click="resetFilter" class="btn-secondary w-full">Reset Filter</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="th">Waktu</th>
                        <th class="th">User</th>
                        <th class="th">Aktivitas</th>
                        <th class="th">Module</th>
                        <th class="th">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="logs.length === 0">
                        <td colspan="5" class="text-center py-8 text-gray-400">Tidak ada data</td>
                    </tr>
                    <tr
                        v-for="log in logs"
                        :key="log.id"
                        class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                    >
                        <td class="td text-gray-500 dark:text-gray-400">{{ formatTime(log.created_at) }}</td>
                        <td class="td font-medium text-gray-900 dark:text-white">{{ log.user?.name ?? 'System' }}</td>
                        <td class="td">
                            <span :class="activityBadge(log.activity)">{{ activityLabel(log.activity) }}</span>
                        </td>
                        <td class="td text-gray-500 dark:text-gray-400">{{ log.module }}</td>
                        <td class="td text-gray-400 font-mono text-xs">{{ log.ip_address ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4" v-if="meta.last_page > 1">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Halaman {{ meta.current_page }} dari {{ meta.last_page }} ({{ meta.total }} data)
            </p>
            <div class="flex gap-2">
                <button
                    @click="fetchLogs(meta.current_page - 1)"
                    :disabled="meta.current_page <= 1"
                    class="btn-secondary disabled:opacity-40"
                >
                    Sebelumnya
                </button>
                <button
                    @click="fetchLogs(meta.current_page + 1)"
                    :disabled="meta.current_page >= meta.last_page"
                    class="btn-secondary disabled:opacity-40"
                >
                    Selanjutnya
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import api from '@/lib/api'

const logs = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })

const filter = ref({
    date: '',
    module: '',
    activity: '',
})

const activityBadge = (activity) => {
    const map = {
        check_in:            'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
        check_out:           'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
        create_appointment:  'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400',
        approve_appointment: 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
        reject_appointment:  'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400',
    }
    return `px-2 py-1 rounded-full text-xs font-medium ${map[activity] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'}`
}

const activityLabel = (activity) => {
    const map = {
        check_in: 'Check In',
        check_out: 'Check Out',
        create_appointment: 'Buat Appointment',
        approve_appointment: 'Approve Appointment',
        reject_appointment: 'Reject Appointment',
    }
    return map[activity] ?? activity
}

const formatTime = (datetime) => {
    return new Date(datetime).toLocaleString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
    })
}

const fetchLogs = async (page = 1) => {
    const params = new URLSearchParams({ page })
    if (filter.value.date) params.append('date', filter.value.date)
    if (filter.value.module) params.append('module', filter.value.module)
    if (filter.value.activity) params.append('activity', filter.value.activity)

    const res = await api.get(`/audit-logs?${params}`)
    logs.value = res.data.data
    meta.value = {
        current_page: res.data.current_page,
        last_page: res.data.last_page,
        total: res.data.total,
    }
}

const resetFilter = () => {
    filter.value = { date: '', module: '', activity: '' }
    fetchLogs(1)
}

onMounted(() => fetchLogs(1))
</script>