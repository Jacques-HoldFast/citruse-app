<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div :class="iconClasses" class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl">
                        {{ icon }}
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">
                        {{ title }}
                    </h3>
                    <p :class="valueClasses" class="text-2xl font-bold">
                        {{ formattedValue }}
                    </p>
                    <p v-if="subtitle" class="text-sm text-gray-500 mt-1">
                        {{ subtitle }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [Number, String],
        required: true
    },
    icon: {
        type: String,
        required: true
    },
    color: {
        type: String,
        default: 'blue',
        validator: (value) => ['blue', 'green', 'purple', 'orange', 'red', 'yellow'].includes(value)
    },
    subtitle: {
        type: String,
        default: null
    },
    format: {
        type: String,
        default: 'number',
        validator: (value) => ['number', 'currency', 'percentage'].includes(value)
    }
})

const iconClasses = computed(() => {
    const baseClasses = 'text-white'
    const colorClasses = {
        blue: 'bg-blue-500',
        green: 'bg-green-500',
        purple: 'bg-purple-500',
        orange: 'bg-orange-500',
        red: 'bg-red-500',
        yellow: 'bg-yellow-500'
    }
    return `${baseClasses} ${colorClasses[props.color]}`
})

const valueClasses = computed(() => {
    const colorClasses = {
        blue: 'text-blue-600',
        green: 'text-green-600',
        purple: 'text-purple-600',
        orange: 'text-orange-600',
        red: 'text-red-600',
        yellow: 'text-yellow-600'
    }
    return colorClasses[props.color]
})

const formattedValue = computed(() => {
    const value = props.value
    
    if (props.format === 'currency') {
        return new Intl.NumberFormat('en-ZA', {
            style: 'currency',
            currency: 'ZAR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value)
    }
    
    if (props.format === 'percentage') {
        return `${value}%`
    }
    
    if (props.format === 'number') {
        return new Intl.NumberFormat('en-ZA').format(value)
    }
    
    return value
})
</script>