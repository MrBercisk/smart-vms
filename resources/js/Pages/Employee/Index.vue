<!-- resources/js/Pages/Employee/Index.vue -->
<template>
    <AppLayout title="Karyawan">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Karyawan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data karyawan</p>
            </div>
            <button @click="openModal()" class="btn-primary">
                + Tambah Karyawan
            </button>
        </div>

        <!-- Search & Filter -->
        <div class="flex gap-3 mb-4">
            <input
                v-model="search"
                type="text"
                placeholder="Cari nama atau email..."
                class="input w-full max-w-xs"
            />
            <select v-model="filterDept" class="input w-full max-w-xs">
                <option value="">Semua Departemen</option>
                <option v-for="d in departments" :key="d.id" :value="d.id">
                    {{ d.department_name }}
                </option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="th">ID</th>
                        <th class="th">Nama</th>
                        <th class="th">Email</th>
                        <th class="th">Departemen</th>
                        <th class="th">Posisi</th>
                        <th class="th">Status</th>
                        <th class="th">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="filtered.length === 0">
                        <td colspan="7" class="text-center py-8 text-gray-400">Tidak ada data</td>
                    </tr>
                    <tr
                        v-for="emp in filtered"
                        :key="emp.id"
                        class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                    >
                        <td class="td text-gray-400 font-mono text-xs">{{ emp.employee_id }}</td>
                        <td class="td font-medium text-gray-900 dark:text-white">{{ emp.employee_name }}</td>
                        <td class="td text-gray-500 dark:text-gray-400">{{ emp.email }}</td>
                        <td class="td">{{ emp.department?.department_name ?? '-' }}</td>
                        <td class="td text-gray-500 dark:text-gray-400">{{ emp.position }}</td>
                        <td class="td">
                            <span
                                :class="[
                                    'px-2 py-1 rounded-full text-xs font-medium',
                                    emp.status === 'active'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400'
                                        : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'
                                ]"
                            >
                                {{ emp.status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <button @click="openModal(emp)" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</button>
                                <button @click="confirmDelete(emp)" class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Form -->
        <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
            <div class="modal">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ form.id ? 'Edit Karyawan' : 'Tambah Karyawan' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input v-model="form.employee_name" type="text" class="input" placeholder="Nama karyawan" />
                        <p v-if="errors.employee_name" class="text-red-500 text-xs mt-1">{{ errors.employee_name[0] }}</p>
                    </div>
                    <div>
                        <label class="label">Email <span class="text-red-500">*</span></label>
                        <input v-model="form.email" type="email" class="input" placeholder="email@company.com" />
                        <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</p>
                    </div>
                    <div>
                        <label class="label">No. Telepon</label>
                        <input v-model="form.phone" type="text" class="input" placeholder="08xxxxxxxxxx" />
                    </div>
                    <div>
                        <label class="label">Departemen <span class="text-red-500">*</span></label>
                        <select v-model="form.department_id" class="input">
                            <option value="">-- Pilih Departemen --</option>
                            <option v-for="d in departments" :key="d.id" :value="d.id">
                                {{ d.department_name }}
                            </option>
                        </select>
                        <p v-if="errors.department_id" class="text-red-500 text-xs mt-1">{{ errors.department_id[0] }}</p>
                    </div>
                    <div>
                        <label class="label">Posisi <span class="text-red-500">*</span></label>
                        <input v-model="form.position" type="text" class="input" placeholder="Contoh: Backend Developer" />
                        <p v-if="errors.position" class="text-red-500 text-xs mt-1">{{ errors.position[0] }}</p>
                    </div>
                    <div v-if="form.id">
                        <label class="label">Status</label>
                        <select v-model="form.status" class="input">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
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

        <!-- Modal Konfirmasi Hapus -->
        <div v-if="showDelete" class="modal-overlay" @click.self="showDelete = false">
            <div class="modal max-w-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Hapus Karyawan</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    Yakin ingin menghapus <strong>{{ selected?.employee_name }}</strong>?
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="showDelete = false" class="btn-secondary">Batal</button>
                    <button @click="deleteEmp" :disabled="loading" class="btn-danger">
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

const employees   = ref([])
const departments = ref([])
const search      = ref('')
const filterDept  = ref('')
const showModal   = ref(false)
const showDelete  = ref(false)
const loading     = ref(false)
const selected    = ref(null)
const errors      = ref({})

const form = ref({
    id: null,
    employee_name: '',
    email: '',
    phone: '',
    department_id: '',
    position: '',
    status: 'active',
})

const filtered = computed(() =>
    employees.value.filter(e => {
        const matchSearch = e.employee_name.toLowerCase().includes(search.value.toLowerCase())
            || e.email.toLowerCase().includes(search.value.toLowerCase())
        const matchDept = !filterDept.value || e.department_id === filterDept.value
        return matchSearch && matchDept
    })
)

const fetchEmployees = async () => {
    const res = await api.get('/employees')
    employees.value = res.data
}

const fetchDepartments = async () => {
    const res = await api.get('/departments')
    departments.value = res.data
}

const openModal = (emp = null) => {
    errors.value = {}
    if (emp) {
        form.value = {
            id: emp.id,
            employee_name: emp.employee_name,
            email: emp.email,
            phone: emp.phone ?? '',
            department_id: emp.department_id,
            position: emp.position,
            status: emp.status,
        }
    } else {
        form.value = {
            id: null, employee_name: '', email: '', phone: '',
            department_id: '', position: '', status: 'active',
        }
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
        if (form.value.id) {
            await api.put(`/employees/${form.value.id}`, form.value)
        } else {
            await api.post('/employees', form.value)
        }
        await fetchEmployees()
        closeModal()
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors
        }
    } finally {
        loading.value = false
    }
}

const confirmDelete = (emp) => {
    selected.value   = emp
    showDelete.value = true
}

const deleteEmp = async () => {
    loading.value = true
    try {
        await api.delete(`/employees/${selected.value.id}`)
        await fetchEmployees()
        showDelete.value = false
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchEmployees()
    fetchDepartments()
})
</script>