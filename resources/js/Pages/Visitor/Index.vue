<!-- resources/js/Pages/Visitor/Index.vue -->
<template>
    <AppLayout title="Tamu">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Tamu</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data tamu</p>
            </div>
            <button @click="openModal()" class="btn-primary">
                + Tambah Tamu
            </button>
        </div>

        <!-- Search -->
        <div class="mb-4">
            <input
                v-model="search"
                type="text"
                placeholder="Cari nama, perusahaan, atau no identitas..."
                class="input w-full max-w-xs"
            />
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="th">Foto</th>
                        <th class="th">Kode</th>
                        <th class="th">Nama</th>
                        <th class="th">Perusahaan</th>
                        <th class="th">No. Identitas</th>
                        <th class="th">Tipe</th>
                        <th class="th">Status</th>
                        <th class="th">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="filtered.length === 0">
                        <td colspan="8" class="text-center py-8 text-gray-400">Tidak ada data</td>
                    </tr>
                    <tr
                        v-for="v in filtered"
                        :key="v.id"
                        class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                    >
                        <td class="td">
                            <img
                                v-if="v.photo"
                                :src="`/storage/${v.photo}`"
                                class="w-9 h-9 rounded-full object-cover"
                            />
                            <div v-else class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs text-gray-400">
                                {{ v.full_name?.charAt(0) }}
                            </div>
                        </td>
                        <td class="td text-gray-400 font-mono text-xs">{{ v.visitor_code }}</td>
                        <td class="td font-medium text-gray-900 dark:text-white">{{ v.full_name }}</td>
                        <td class="td text-gray-500 dark:text-gray-400">{{ v.company_name ?? '-' }}</td>
                        <td class="td text-gray-500 dark:text-gray-400">{{ v.identity_number }}</td>
                        <td class="td">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">
                                {{ v.identity_type }}
                            </span>
                        </td>
                        <td class="td">
                            <span
                                :class="[
                                    'px-2 py-1 rounded-full text-xs font-medium',
                                    v.status === 'active'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400'
                                        : 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400'
                                ]"
                            >
                                {{ v.status === 'active' ? 'Aktif' : 'Blacklist' }}
                            </span>
                        </td>
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <button @click="openModal(v)" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</button>
                                <button @click="confirmDelete(v)" class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Form -->
        <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
            <div class="modal max-w-lg">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ form.id ? 'Edit Tamu' : 'Tambah Tamu' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="space-y-4">
                    <!-- Photo upload -->
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden flex items-center justify-center">
                            <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
                            <span v-else class="text-2xl text-gray-300">📷</span>
                        </div>
                        <div>
                            <label class="btn-secondary cursor-pointer inline-block">
                                Pilih Foto
                                <input type="file" accept="image/*" class="hidden" @change="onPhotoChange" />
                            </label>
                            <p class="text-xs text-gray-400 mt-1">JPG/PNG, maks 2MB</p>
                        </div>
                    </div>

                    <div>
                        <label class="label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input v-model="form.full_name" type="text" class="input" placeholder="Nama tamu" />
                        <p v-if="errors.full_name" class="text-red-500 text-xs mt-1">{{ errors.full_name[0] }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Perusahaan</label>
                            <input v-model="form.company_name" type="text" class="input" placeholder="Nama perusahaan" />
                        </div>
                        <div>
                            <label class="label">No. Telepon <span class="text-red-500">*</span></label>
                            <input v-model="form.phone" type="text" class="input" placeholder="08xxxxxxxxxx" />
                            <p v-if="errors.phone" class="text-red-500 text-xs mt-1">{{ errors.phone[0] }}</p>
                        </div>
                    </div>
                    <div>
                        <label class="label">Email</label>
                        <input v-model="form.email" type="email" class="input" placeholder="email@example.com" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Tipe Identitas <span class="text-red-500">*</span></label>
                            <select v-model="form.identity_type" class="input">
                                <option value="">-- Pilih --</option>
                                <option value="KTP">KTP</option>
                                <option value="SIM">SIM</option>
                                <option value="Passport">Passport</option>
                            </select>
                            <p v-if="errors.identity_type" class="text-red-500 text-xs mt-1">{{ errors.identity_type[0] }}</p>
                        </div>
                        <div>
                            <label class="label">No. Identitas <span class="text-red-500">*</span></label>
                            <input v-model="form.identity_number" type="text" class="input" placeholder="Nomor identitas" />
                            <p v-if="errors.identity_number" class="text-red-500 text-xs mt-1">{{ errors.identity_number[0] }}</p>
                        </div>
                    </div>
                    <div v-if="form.id">
                        <label class="label">Status</label>
                        <select v-model="form.status" class="input">
                            <option value="active">Aktif</option>
                            <option value="blacklist">Blacklist</option>
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Hapus Tamu</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    Yakin ingin menghapus <strong>{{ selected?.full_name }}</strong>?
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="showDelete = false" class="btn-secondary">Batal</button>
                    <button @click="deleteVisitor" :disabled="loading" class="btn-danger">
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

const visitors      = ref([])
const search        = ref('')
const showModal     = ref(false)
const showDelete    = ref(false)
const loading       = ref(false)
const selected      = ref(null)
const errors        = ref({})
const photoFile     = ref(null)
const photoPreview  = ref(null)

const form = ref({
    id: null,
    full_name: '',
    company_name: '',
    phone: '',
    email: '',
    identity_type: '',
    identity_number: '',
    status: 'active',
})

const filtered = computed(() =>
    visitors.value.filter(v => {
        const q = search.value.toLowerCase()
        return v.full_name.toLowerCase().includes(q)
            || (v.company_name ?? '').toLowerCase().includes(q)
            || v.identity_number.includes(q)
    })
)

const fetchVisitors = async () => {
    const res = await api.get('/visitors')
    visitors.value = res.data
}

const onPhotoChange = (e) => {
    const file = e.target.files[0]
    if (!file) return
    photoFile.value = file
    photoPreview.value = URL.createObjectURL(file)
}

const openModal = (visitor = null) => {
    errors.value = {}
    photoFile.value = null
    if (visitor) {
        form.value = {
            id: visitor.id,
            full_name: visitor.full_name,
            company_name: visitor.company_name ?? '',
            phone: visitor.phone,
            email: visitor.email ?? '',
            identity_type: visitor.identity_type,
            identity_number: visitor.identity_number,
            status: visitor.status,
        }
        photoPreview.value = visitor.photo ? `/storage/${visitor.photo}` : null
    } else {
        form.value = {
            id: null, full_name: '', company_name: '', phone: '', email: '',
            identity_type: '', identity_number: '', status: 'active',
        }
        photoPreview.value = null
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
        const formData = new FormData()
        Object.entries(form.value).forEach(([key, val]) => {
            if (val !== null && val !== '') formData.append(key, val)
        })
        if (photoFile.value) {
            formData.append('photo', photoFile.value)
        }

        if (form.value.id) {
            formData.append('_method', 'PUT')
            await api.post(`/visitors/${form.value.id}`, formData, true)
        } else {
            await api.post('/visitors', formData, true)
        }

        await fetchVisitors()
        closeModal()
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors
        }
    } finally {
        loading.value = false
    }
}

const confirmDelete = (visitor) => {
    selected.value   = visitor
    showDelete.value = true
}

const deleteVisitor = async () => {
    loading.value = true
    try {
        await api.delete(`/visitors/${selected.value.id}`)
        await fetchVisitors()
        showDelete.value = false
    } finally {
        loading.value = false
    }
}

onMounted(fetchVisitors)
</script>