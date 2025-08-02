<template>
    <div class="bg-white rounded-xl shadow p-5 transition-all hover:shadow-lg">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">{{ label }}</p>
                <p class="text-3xl font-bold">{{ value }}</p>
            </div>
            <div :class="['p-3 rounded-lg', bgColorClass]">
                <component :is="icon" class="h-6 w-6 text-white" />
            </div>
        </div>
        <div v-if="change !== null" class="mt-3 flex items-center">
            <span
                :class="[
                    change >= 0 ? 'text-green-600' : 'text-red-600',
                    'font-medium',
                ]"
            >
                {{ change >= 0 ? "↑" : "↓" }} {{ Math.abs(change) }}%
            </span>
            <span class="text-gray-500 text-sm ml-2"
                >depuis le mois dernier</span
            >
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    label: String,
    value: [Number, String],
    color: {
        type: String,
        default: "blue",
    },
    change: Number,
    icon: Object,
});

const bgColorClass = computed(() => {
    const colors = {
        blue: "bg-blue-100",
        green: "bg-green-100",
        amber: "bg-amber-100",
        purple: "bg-purple-100",
        red: "bg-red-100",
        pink: "bg-pink-100",
        indigo: "bg-indigo-100",
        teal: "bg-teal-100",
    };
    return colors[props.color] || colors.blue;
});
</script>
