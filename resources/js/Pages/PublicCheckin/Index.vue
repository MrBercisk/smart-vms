<template>
    <div class="min-h-screen bg-gradient-to-br from-primary-800 to-primary-900 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8">

            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-white text-2xl font-bold">V</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Selamat Datang</h1>
                <p class="text-gray-500 mt-1">Silakan isi buku tamu digital</p>
            </div>

            <!-- Success State -->
            <div v-if="success" class="text-center py-8">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">✓</span>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Check-in Berhasil!</h2>
                <p class="text-gray-500 mb-1">No. Kunjungan: <strong>{{ success.visit_number }}</strong></p>
                <p class="text-gray-500 mb-6">{{ success.message }}</p>

                <div class="flex flex-col gap-3">
                    <a
                    
                        :href="`/api/public/visits/${success.visit_id}/pass`"
                        target="_blank"
                        class="btn-primary inline-flex items-center justify-center gap-2 py-3"
                    >
                        🖨️ Cetak Visitor Pass
                    </a>
                    <button @click="resetForm" class="btn-secondary py-3">
                        Check-in Tamu Baru
                    </button>
                </div>
            </div>

            <!-- Form -->
            <form v-else @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input v-model="form.full_name" type="text" class="input text-lg py-3" placeholder="Nama Anda" required />
                    <p v-if="errors.full_name" class="text-red-500 text-xs mt-1">{{ errors.full_name[0] }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Perusahaan/Instansi</label>
                        <input v-model="form.company_name" type="text" class="input text-lg py-3" placeholder="Opsional" />
                    </div>
                    <div>
                        <label class="label">No. HP <span class="text-red-500">*</span></label>
                        <input v-model="form.phone" type="text" class="input text-lg py-3" placeholder="08xxxxxxxxxx" required />
                        <p v-if="errors.phone" class="text-red-500 text-xs mt-1">{{ errors.phone[0] }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Jenis Identitas <span class="text-red-500">*</span></label>
                        <select v-model="form.identity_type" class="input text-lg py-3" required>
                            <option value="">-- Pilih --</option>
                            <option value="KTP">KTP</option>
                            <option value="SIM">SIM</option>
                            <option value="Passport">Passport</option>
                        </select>
                        <p v-if="errors.identity_type" class="text-red-500 text-xs mt-1">{{ errors.identity_type[0] }}</p>
                    </div>
                    <div>
                        <label class="label">No. Identitas <span class="text-red-500">*</span></label>
                        <input v-model="form.identity_number" type="text" class="input text-lg py-3" placeholder="Nomor identitas" required />
                        <p v-if="errors.identity_number" class="text-red-500 text-xs mt-1">{{ errors.identity_number[0] }}</p>
                    </div>
                </div>

                <hr class="my-2" />

                <div>
                    <label class="label">Tujuan Departemen <span class="text-red-500">*</span></label>
                    <select v-model="form.department_id" class="input text-lg py-3" required @change="onDepartmentChange">
                        <option value="">-- Pilih Departemen --</option>
                        <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.department_name }}</option>
                    </select>
                </div>

                <div>
                    <label class="label">Bertemu Dengan <span class="text-red-500">*</span></label>
                    <select v-model="form.employee_id" class="input text-lg py-3" required :disabled="!form.department_id">
                        <option value="">-- Pilih Karyawan --</option>
                        <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.employee_name }}</option>
                    </select>
                    <p v-if="errors.employee_id" class="text-red-500 text-xs mt-1">{{ errors.employee_id[0] }}</p>
                </div>

                <div>
                    <label class="label">Keperluan <span class="text-red-500">*</span></label>
                    <textarea v-model="form.purpose" class="input text-lg py-3" rows="3" placeholder="Tujuan kunjungan Anda..." required />
                    <p v-if="errors.purpose" class="text-red-500 text-xs mt-1">{{ errors.purpose[0] }}</p>
                </div>

                <!-- Photo capture (opsional, pakai webcam tablet) -->
                <div>
                    <label class="label">Ambil Foto (Opsional)</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-lg bg-gray-100 overflow-hidden flex items-center justify-center border border-gray-200">
                            <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
                            <span v-else class="text-3xl text-gray-300">📷</span>
                        </div>
                        <label class="btn-secondary cursor-pointer">
                            Ambil Foto
                            <input type="file" accept="image/*" capture="user" class="hidden" @change="onPhotoChange" />
                        </label>
                    </div>
                </div>

                <p v-if="submitError" class="text-red-500 text-sm text-center">{{ submitError }}</p>

                <button type="submit" :disabled="loading" class="btn-primary w-full py-4 text-lg">
                    {{ loading ? 'Memproses...' : 'Check In Sekarang' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const API_BASE = '/api/public'

const departments  = ref([])
const employees    = ref([])
const loading       = ref(false)
const errors         = ref({})
const submitError    = ref('')
const success         = ref(null)
const photoFile       = ref(null)
const photoPreview    = ref(null)

const form = ref({
    full_name: '',
    company_name: '',
    phone: '',
    identity_type: '',
    identity_number: '',
    department_id: '',
    employee_id: '',
    purpose: '',
})

const fetchDepartments = async () => {
    const res = await fetch(`${API_BASE}/departments`)
    departments.value = await res.json()
}

const onDepartmentChange = async () => {
    form.value.employee_id = ''
    if (!form.value.department_id) {
        employees.value = []
        return
    }
    const res = await fetch(`${API_BASE}/employees?department_id=${form.value.department_id}`)
    employees.value = await res.json()
}

const onPhotoChange = (e) => {
    const file = e.target.files[0]
    if (!file) return
    photoFile.value = file
    photoPreview.value = URL.createObjectURL(file)
}

const getCookie = (name) => {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'))
    return match ? decodeURIComponent(match[2]) : null
}

const submit = async () => {
    loading.value = true
    errors.value = {}
    submitError.value = ''

    try {
        const formData = new FormData()
        Object.entries(form.value).forEach(([key, val]) => {
            if (val) formData.append(key, val)
        })
        if (photoFile.value) {
            formData.append('visitor_photo', photoFile.value)
        }

        const res = await fetch(`${API_BASE}/checkin`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
            },
            body: formData,
        })

        const data = await res.json()

        if (!res.ok) {
            if (res.status === 422) {
                errors.value = data.errors
            } else {
                submitError.value = data.message ?? 'Terjadi kesalahan, silakan coba lagi'
            }
            return
        }

        success.value = data
    } catch (e) {
        submitError.value = 'Gagal terhubung ke server, silakan coba lagi'
    } finally {
        loading.value = false
    }
}

const resetForm = () => {
    form.value = {
        full_name: '', company_name: '', phone: '', identity_type: '',
        identity_number: '', department_id: '', employee_id: '', purpose: '',
    }
    photoFile.value = null
    photoPreview.value = null
    success.value = null
    errors.value = {}
    employees.value = []
}

onMounted(fetchDepartments)
</script>