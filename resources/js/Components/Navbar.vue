<template>
    <header class="sticky top-0 z-10 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between px-6 py-4">

            <!-- Hamburger -->
            <button
                @click="$emit('toggle-sidebar')"
                class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
                ☰
            </button>

            <!-- Page title -->
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ title }}
            </h1>

            <!-- Right side -->
            <div class="flex items-center gap-3">
                <!-- Dark mode toggle -->
                <button
                    @click="theme.toggle()"
                    class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                    {{ theme.dark ? '☀️' : '🌙' }}
                </button>

                <!-- User menu -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-primary-800 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-bold">
                            {{ user?.name?.charAt(0)?.toUpperCase() }}
                        </span>
                    </div>
                    <span class="text-sm text-gray-700 dark:text-gray-300 hidden sm:block">
                        {{ user?.name }}
                    </span>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="text-sm text-red-500 hover:text-red-700 ml-2"
                    >
                        Logout
                    </Link>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useThemeStore } from '@/stores/theme'

defineEmits(['toggle-sidebar'])

const theme = useThemeStore()
const page  = usePage()
const user  = computed(() => page.props.auth.user)
const title = computed(() => page.props.title ?? 'Dashboard')
</script>