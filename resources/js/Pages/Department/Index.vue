<!-- resources/js/Pages/Department/Index.vue -->
<template>
    <AppLayout title="Departemen">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Departemen</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data departemen</p>
            </div>
            <button @click="openModal()" class="btn-primary">
                + Tambah Departemen
            </button>
        </div>

        <!-- Search -->
        <div class="mb-4">
            <input
                v-model="search"
                type="text"
                placeholder="Cari departemen..."
                class="input w-full max-w-xs"
            />
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="th">No</th>
                        <th class="th">Nama Departemen</th>
                        <th class="th">Deskripsi</th>
                        <th class="th">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="filtered.length === 0">
                        <td colspan="4" class="text-center py-8 text-gray-400">
                            Tidak ada data
                        </td>
                    </tr>
                    <tr
                        v-for="(dept, i) in filtered"
                        :key="dept.id"
                        class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                    >
                        <td class="td text-gray-400">{{ i + 1 }}</td>
                        <td class="td font-medium text-gray-900 dark:text-white">
                            {{ dept.department_name }}
                        </td>
                        <td class="td text-gray-500 dark:text-gray-400">
                            {{ dept.description ?? '-' }}
                        </td>
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <button
                                    @click="openModal(dept)"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="confirmDelete(dept)"
                                    class="text-red-500 hover:text-red-700 text-xs font-medium"
                                >
                                    Hapus
                                </button>
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
                        {{ form.id ? 'Edit Departemen' : 'Tambah Departemen' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="label">Nama Departemen <span class="text-red-500">*</span></label>
                        <input v-model="form.department_name" type="text" class="input" placeholder="Contoh: IT" />
                        <p v-if="errors.department_name" class="text-red-500 text-xs mt-1">
                            {{ errors.department_name[0] }}
                        </p>
                    </div>
                    <div>
                        <label class="label">Deskripsi</label>
                        <textarea v-model="form.description" class="input" rows="3" placeholder="Deskripsi departemen..." />
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Hapus Departemen</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    Yakin ingin menghapus <strong>{{ selected?.department_name }}</strong>? Aksi ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="showDelete = false" class="btn-secondary">Batal</button>
                    <button @click="deleteDept" :disabled="loading" class="btn-danger">
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


const departments = ref([])
const search      = ref('')
const showModal   = ref(false)
const showDelete  = ref(false)
const loading     = ref(false)
const selected    = ref(null)
const errors      = ref({})

const form = ref({
    id: null,
    department_name: '',
    description: '',
})

const filtered = computed(() =>
    departments.value.filter(d =>
        d.department_name.toLowerCase().includes(search.value.toLowerCase())
    )
)

const fetchDepartments = async () => {
    const res = await api.get('/departments')
    departments.value = res.data
}

const openModal = (dept = null) => {
    errors.value = {}
    if (dept) {
        form.value = { id: dept.id, department_name: dept.department_name, description: dept.description ?? '' }
    } else {
        form.value = { id: null, department_name: '', description: '' }
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
            await api.put(`/departments/${form.value.id}`, form.value)
        } else {
            await api.post('/departments', form.value)
        }
        await fetchDepartments()
        closeModal()
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors
        }
    } finally {
        loading.value = false
    }
}

const confirmDelete = (dept) => {
    selected.value   = dept
    showDelete.value = true
}

const deleteDept = async () => {
    loading.value = true
    try {
        await api.delete(`/departments/${selected.value.id}`)
        await fetchDepartments()
        showDelete.value = false
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    setTimeout(fetchDepartments, 500)
})
</script>