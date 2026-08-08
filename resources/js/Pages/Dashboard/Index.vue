<template>
    <AppLayout title="Dashboard">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <StatCard icon="👥" :value="summary.total_today"      label="Tamu Hari Ini" />
            <StatCard icon="🟢" :value="summary.active_visitor"   label="Tamu Aktif" />
            <StatCard icon="📅" :value="summary.total_this_month" label="Tamu Bulan Ini" />
            <StatCard icon="🗓️" :value="summary.scheduled"        label="Terjadwal" />
            <StatCard icon="✅" :value="summary.completed_today"  label="Selesai Hari Ini" />
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Visitor Trend -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                    Tren Kunjungan (7 Hari)
                </h3>
                <canvas ref="trendChart" height="120"></canvas>
            </div>

            <!-- Peak Hours -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                    Jam Kunjungan Tersibuk
                </h3>
                <canvas ref="peakChart" height="120"></canvas>
            </div>
        </div>

        <!-- Top Department & Employee -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                    Top Departemen
                </h3>
                <div v-for="(item, i) in topDepartments" :key="i" class="flex items-center gap-3 mb-3">
                    <span class="text-sm font-medium text-gray-500 w-4">{{ i + 1 }}</span>
                    <div class="flex-1">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 dark:text-gray-300">{{ item.department }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ item.total }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                            <div
                                class="bg-primary-600 h-1.5 rounded-full"
                                :style="{ width: (item.total / topDepartments[0].total * 100) + '%' }"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                    Top Karyawan
                </h3>
                <div v-for="(item, i) in topEmployees" :key="i" class="flex items-center gap-3 mb-3">
                    <span class="text-sm font-medium text-gray-500 w-4">{{ i + 1 }}</span>
                    <div class="flex-1">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 dark:text-gray-300">{{ item.employee }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ item.total }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                            <div
                                class="bg-green-500 h-1.5 rounded-full"
                                :style="{ width: (item.total / topEmployees[0].total * 100) + '%' }"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatCard from '@/Components/StatCard.vue'
import api from '@/lib/api'
import Chart from 'chart.js/auto'

const summary        = ref({})
const topDepartments = ref([])
const topEmployees   = ref([])
const trendChart     = ref(null)
const peakChart      = ref(null)

onMounted(async () => {
    const [sumRes, trendRes, peakRes, deptRes, empRes] = await Promise.all([
        api.get('/dashboard/summary'),
        api.get('/dashboard/trend'),
        api.get('/dashboard/peak-hours'),
        api.get('/dashboard/top-departments'),
        api.get('/dashboard/top-employees'),
    ])

    summary.value        = sumRes.data
    topDepartments.value = deptRes.data
    topEmployees.value   = empRes.data

    // Trend chart
    new Chart(trendChart.value, {
        type: 'line',
        data: {
            labels: trendRes.data.map(d => d.date),
            datasets: [{
                label: 'Kunjungan',
                data: trendRes.data.map(d => d.total),
                borderColor: '#1e40af',
                backgroundColor: 'rgba(30,64,175,0.1)',
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    })

    // Peak hours chart
    new Chart(peakChart.value, {
        type: 'bar',
        data: {
            labels: peakRes.data.map(d => d.hour),
            datasets: [{
                label: 'Kunjungan',
                data: peakRes.data.map(d => d.total),
                backgroundColor: '#1e40af',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    })
})
</script>