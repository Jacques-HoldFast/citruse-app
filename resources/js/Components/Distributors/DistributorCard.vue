<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 rounded-lg bg-purple-500 flex items-center justify-center text-white font-bold">
                        {{ initials }}
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ distributor.business_name }}
                        </h3>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            {{ distributor.country }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        🌍 Customer
                    </span>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="space-y-3">
                <!-- Sales Contact -->
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 mt-1">
                        <div class="h-6 w-6 rounded bg-blue-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 uppercase">Sales Contact</p>
                        <p class="text-sm font-medium text-gray-900">{{ distributor.primary_sales_contact_name }}</p>
                        <p class="text-sm text-gray-600">{{ distributor.primary_sales_contact_email }}</p>
                        <p class="text-sm text-gray-600">{{ distributor.primary_sales_contact_telephone }}</p>
                    </div>
                </div>

                <!-- Logistics Contact -->
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 mt-1">
                        <div class="h-6 w-6 rounded bg-orange-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-4 4-4-4m-6 0l4 4 4-4" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 uppercase">Logistics Contact</p>
                        <p class="text-sm font-medium text-gray-900">{{ distributor.primary_logistics_contact_name }}</p>
                        <p class="text-sm text-gray-600">{{ distributor.primary_logistics_contact_email }}</p>
                        <p class="text-sm text-gray-600">{{ distributor.primary_logistics_contact_telephone }}</p>
                    </div>
                </div>

                <!-- Business Details -->
                <div class="pt-3 border-t border-gray-200">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">VAT Number:</span>
                        <span class="font-medium text-gray-900">{{ distributor.vat_number }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex space-x-2">
                    <button @click="$emit('view', distributor.id)"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                        View Details
                    </button>
                    <button @click="$emit('edit', distributor.id)"
                            class="text-green-600 hover:text-green-900 text-sm font-medium">
                        Edit
                    </button>
                </div>
                <button @click="$emit('delete', distributor.id)"
                        class="text-red-600 hover:text-red-900 text-sm font-medium">
                    Delete
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    distributor: {
        type: Object,
        required: true
    }
})

defineEmits(['view', 'edit', 'delete'])

const initials = computed(() => {
    return props.distributor.business_name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2)
})
</script>