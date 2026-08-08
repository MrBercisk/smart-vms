<!-- resources/js/Layouts/AppLayout.vue -->
<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-200">

        <!-- Sidebar -->
        <Sidebar :show="sidebarOpen" @close="sidebarOpen = false" />

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Navbar -->
            <Navbar @toggle-sidebar="sidebarOpen = !sidebarOpen" />

            <!-- Page content -->
            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'
import Navbar from '@/Components/Navbar.vue'
import { useThemeStore } from '@/stores/theme'

const sidebarOpen = ref(false)
const theme = useThemeStore()

onMounted(() => {
    theme.init()
})
</script>