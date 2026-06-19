<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl">{{ icon }}</span>
            <span
                :class="[
                    'text-xs px-2 py-1 rounded-full font-medium',
                    trend >= 0
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400'
                        : 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400'
                ]"
                v-if="trend !== null"
            >
                {{ trend >= 0 ? '↑' : '↓' }} {{ Math.abs(trend) }}%
            </span>
        </div>
        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
            {{ value }}
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ label }}
        </div>
    </div>
</template>

<script setup>
defineProps({
    icon:  { type: String, default: '📊' },
    value: { type: [String, Number], default: 0 },
    label: { type: String, default: '' },
    trend: { type: Number, default: null },
})
</script>