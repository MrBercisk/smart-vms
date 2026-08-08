<template>
    <div>
        <!-- Overlay mobile -->
        <div
            v-if="show"
            class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
            @click="$emit('close')"
        />

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800',
                'border-r border-gray-200 dark:border-gray-700',
                'transform transition-transform duration-200',
                show ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
        >
            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <div class="w-8 h-8 bg-primary-800 rounded-lg flex items-center justify-center">
                    <span class="text-white text-sm font-bold">V</span>
                </div>
                <span class="font-semibold text-gray-900 dark:text-white">Smart VMS</span>
            </div>

            <!-- Menu -->
            <nav class="px-4 py-4 space-y-1">
                <Link
                    v-for="item in menu"
                    :key="item.route"
                    :href="route(item.route)"
                    :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors',
                        isActive(item.route)
                            ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-medium'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                    ]"
                >
                    <span class="text-lg">{{ item.icon }}</span>
                    {{ item.label }}
                </Link>
            </nav>
        </aside>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

defineProps({ show: Boolean })
defineEmits(['close'])

const page = usePage()
const permissions = computed(() => page.props.auth.user?.permissions ?? [])

const allMenu = [
    { label: 'Dashboard',    icon: '📊', route: 'dashboard',          permission: 'view dashboard' },
    { label: 'Departemen',   icon: '🏢', route: 'departments.index',  permission: 'manage departments' },
    { label: 'Karyawan',     icon: '👤', route: 'employees.index',    permission: 'manage employees' },
    { label: 'Tamu',         icon: '🙋', route: 'visitors.index',     permission: null }, // semua role login
    { label: 'Appointment',  icon: '📅', route: 'appointments.index', permission: null },
    { label: 'Kunjungan',    icon: '✅', route: 'visits.index',       permission: 'checkin visitor' },
    { label: 'Laporan',      icon: '📄', route: 'reports.index',      permission: 'view reports' },
    { label: 'Audit Log',    icon: '🔍', route: 'audit-logs.index',   permission: 'manage roles' },
]

const menu = computed(() =>
    allMenu.filter(item => !item.permission || permissions.value.includes(item.permission))
)

const isActive = (routeName) => {
    return page.url.startsWith('/' + routeName.replace('.index', '').replace('.', '/'))
}
</script>