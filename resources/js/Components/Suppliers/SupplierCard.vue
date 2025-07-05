<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 rounded-lg bg-green-500 flex items-center justify-center text-white font-bold">
                        {{ initials }}
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ supplier.business_name }}
                        </h3>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ supplier.country }}
                        </span>
                    </div>
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
                        <p class="text-sm font-medium text-gray-900">{{ supplier.primary_sales_contact_name }}</p>
                        <p class="text-sm text-gray-600">{{ supplier.primary_sales_contact_email }}</p>
                        <p class="text-sm text-gray-600">{{ supplier.primary_sales_contact_telephone }}</p>
                    </div>
                </div>

                <!-- Logistics Contact -->
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 mt-1">
                        <div class="h-6 w-6 rounded bg-orange-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 uppercase">Logistics Contact</p>
                        <p class="text-sm font-medium text-gray-900">{{ supplier.primary_logistics_contact_name }}</p>
                        <p class="text-sm text-gray-600">{{ supplier.primary_logistics_contact_email }}</p>
                        <p class="text-sm text-gray-600">{{ supplier.primary_logistics_contact_telephone }}</p>
                    </div>
                </div>

                <!-- Business Details -->
                <div class="pt-3 border-t border-gray-200">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">VAT Number:</span>
                        <span class="font-medium text-gray-900">{{ supplier.vat_number }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex space-x-2">
                    <button @click="$emit('view', supplier.id)"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                        View Details
                    </button>
                    <button @click="$emit('edit', supplier.id)"
                            class="text-green-600 hover:text-green-900 text-sm font-medium">
                        Edit
                    </button>
                </div>
                <button @click="$emit('delete', supplier.id)"
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
    supplier: {
        type: Object,
        required: true
    }
})

defineEmits(['view', 'edit', 'delete'])

const initials = computed(() => {
    return props.supplier.business_name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2)
})
</script>