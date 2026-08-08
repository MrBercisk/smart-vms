<!-- resources/js/Pages/Appointment/Index.vue -->
<template>
    <AppLayout title="Appointment">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Appointment</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola jadwal kunjungan tamu</p>
            </div>
            <button @click="openModal()" class="btn-primary">
                + Buat Appointment
            </button>
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
                        <th class="th">No. Appointment</th>
                        <th class="th">Tamu</th>
                        <th class="th">Host</th>
                        <th class="th">Tanggal</th>
                        <th class="th">Jam</th>
                        <th class="th">Keperluan</th>
                        <th class="th">Status</th>
                        <th class="th">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="filtered.length === 0">
                        <td colspan="8" class="text-center py-8 text-gray-400">Tidak ada data</td>
                    </tr>
                    <tr
                        v-for="a in filtered"
                        :key="a.id"
                        class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                    >
                        <td class="td text-gray-400 font-mono text-xs">{{ a.appointment_number }}</td>
                        <td class="td font-medium text-gray-900 dark:text-white">{{ a.visitor?.full_name }}</td>
                        <td class="td text-gray-500 dark:text-gray-400">{{ a.employee?.employee_name }}</td>
                        <td class="td">{{ formatDate(a.appointment_date) }}</td>
                        <td class="td">{{ a.appointment_time?.slice(0, 5) }}</td>
                        <td class="td text-gray-500 dark:text-gray-400 max-w-[200px] truncate">{{ a.purpose }}</td>
                        <td class="td">
                            <span :class="statusBadge(a.status)">
                                {{ statusLabel(a.status) }}
                            </span>
                        </td>
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <template v-if="a.status === 'pending'">
                                    <button @click="approve(a)" class="text-green-600 hover:text-green-800 text-xs font-medium">Approve</button>
                                    <button @click="openReject(a)" class="text-red-500 hover:text-red-700 text-xs font-medium">Reject</button>
                                </template>
                                <span v-else-if="a.status === 'rejected'" class="text-xs text-gray-400" :title="a.rejection_reason">
                                    Lihat alasan
                                </span>
                                <button @click="confirmDelete(a)" class="text-gray-400 hover:text-red-600 text-xs">Hapus</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Form Buat Appointment -->
        <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
            <div class="modal">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Buat Appointment</h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="label">Tamu <span class="text-red-500">*</span></label>
                        <select v-model="form.visitor_id" class="input">
                            <option value="">-- Pilih Tamu --</option>
                            <option v-for="v in visitors" :key="v.id" :value="v.id">{{ v.full_name }}</option>
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
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Tanggal <span class="text-red-500">*</span></label>
                            <input v-model="form.appointment_date" type="date" class="input" />
                            <p v-if="errors.appointment_date" class="text-red-500 text-xs mt-1">{{ errors.appointment_date[0] }}</p>
                        </div>
                        <div>
                            <label class="label">Jam <span class="text-red-500">*</span></label>
                            <input v-model="form.appointment_time" type="time" class="input" />
                            <p v-if="errors.appointment_time" class="text-red-500 text-xs mt-1">{{ errors.appointment_time[0] }}</p>
                        </div>
                    </div>
                    <div>
                        <label class="label">Keperluan <span class="text-red-500">*</span></label>
                        <textarea v-model="form.purpose" class="input" rows="3" placeholder="Tujuan kunjungan..." />
                        <p v-if="errors.purpose" class="text-red-500 text-xs mt-1">{{ errors.purpose[0] }}</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="closeModal" class="btn-secondary">Batal</button>
                    <button @click="submit" :disabled="loading" class="btn-primary">
                        {{ loading ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Reject -->
        <div v-if="showReject" class="modal-overlay" @click.self="showReject = false">
            <div class="modal max-w-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tolak Appointment</h3>
                <label class="label">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea v-model="rejectReason" class="input" rows="3" placeholder="Tuliskan alasan..." />
                <div class="flex justify-end gap-3 mt-6">
                    <button @click="showReject = false" class="btn-secondary">Batal</button>
                    <button @click="reject" :disabled="loading || !rejectReason" class="btn-danger">
                        {{ loading ? 'Memproses...' : 'Tolak' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <div v-if="showDelete" class="modal-overlay" @click.self="showDelete = false">
            <div class="modal max-w-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Hapus Appointment</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    Yakin ingin menghapus appointment <strong>{{ selected?.appointment_number }}</strong>?
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="showDelete = false" class="btn-secondary">Batal</button>
                    <button @click="deleteAppointment" :disabled="loading" class="btn-danger">
                        {{ loading ? 'Menghapus...' : 'Hapus' }}
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

const appointments = ref([])
const visitors      = ref([])
const employees     = ref([])
const departments   = ref([])
const filterStatus  = ref('all')
const showModal     = ref(false)
const showReject    = ref(false)
const showDelete    = ref(false)
const loading       = ref(false)
const selected      = ref(null)
const rejectReason  = ref('')
const errors        = ref({})

const statusFilters = [
    { label: 'Semua',    value: 'all' },
    { label: 'Pending',  value: 'pending' },
    { label: 'Approved', value: 'approved' },
    { label: 'Rejected', value: 'rejected' },
    { label: 'Completed',value: 'completed' },
]

const form = ref({
    visitor_id: '',
    employee_id: '',
    department_id: '',
    appointment_date: '',
    appointment_time: '',
    purpose: '',
})

const filtered = computed(() =>
    filterStatus.value === 'all'
        ? appointments.value
        : appointments.value.filter(a => a.status === filterStatus.value)
)

const filteredEmployees = computed(() =>
    form.value.department_id
        ? employees.value.filter(e => e.department_id === form.value.department_id)
        : employees.value
)

const statusBadge = (status) => {
    const map = {
        pending:   'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
        approved:  'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
        rejected:  'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400',
        completed: 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
    }
    return `px-2 py-1 rounded-full text-xs font-medium ${map[status]}`
}

const statusLabel = (status) => {
    const map = { pending: 'Pending', approved: 'Disetujui', rejected: 'Ditolak', completed: 'Selesai' }
    return map[status] ?? status
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const fetchAppointments = async () => {
    const res = await api.get('/appointments')
    appointments.value = res.data
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

const openModal = () => {
    errors.value = {}
    form.value = {
        visitor_id: '', employee_id: '', department_id: '',
        appointment_date: '', appointment_time: '', purpose: '',
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    errors.value = {}
}

const submit = async () => {
    loading.value = true
    errors.value  = {}
    try {
        await api.post('/appointments', form.value)
        await fetchAppointments()
        closeModal()
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors
        }
    } finally {
        loading.value = false
    }
}

const approve = async (appointment) => {
    loading.value = true
    try {
        await api.post(`/appointments/${appointment.id}/approve`)
        await fetchAppointments()
    } finally {
        loading.value = false
    }
}

const openReject = (appointment) => {
    selected.value     = appointment
    rejectReason.value = ''
    showReject.value   = true
}

const reject = async () => {
    loading.value = true
    try {
        await api.post(`/appointments/${selected.value.id}/reject`, {
            rejection_reason: rejectReason.value
        })
        await fetchAppointments()
        showReject.value = false
    } finally {
        loading.value = false
    }
}

const confirmDelete = (appointment) => {
    selected.value   = appointment
    showDelete.value = true
}

const deleteAppointment = async () => {
    loading.value = true
    try {
        await api.delete(`/appointments/${selected.value.id}`)
        await fetchAppointments()
        showDelete.value = false
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchAppointments()
    fetchVisitors()
    fetchEmployees()
    fetchDepartments()
})
</script>