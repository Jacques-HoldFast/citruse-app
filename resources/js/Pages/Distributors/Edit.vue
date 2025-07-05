<template>
    <Head :title="`Edit ${distributor?.business_name || 'Distributor'}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Edit Distributor
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Update distributor information for {{ distributor?.business_name }}
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <Link :href="route('distributors.show', distributorId)"
                          class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        View Details
                    </Link>
                    <Link :href="route('distributors.index')"
                          class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        ← Back to Distributors
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                    <p class="mt-2 text-gray-600">Loading distributor information...</p>
                </div>

                <!-- Error State -->
                <div v-else-if="loadError" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Error loading distributor</h3>
                            <p class="mt-1 text-sm text-red-700">{{ loadError }}</p>
                            <button @click="loadDistributor" 
                                    class="mt-2 text-sm text-red-600 hover:text-red-500 underline">
                                Try again
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div v-else>
                    <!-- Success Message -->
                    <div v-if="successMessage" 
                         class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button @click="successMessage = null" class="text-green-400 hover:text-green-600">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMessage" 
                         class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button @click="errorMessage = null" class="text-red-400 hover:text-red-600">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Info Banner -->
                    <div class="mb-6 bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-purple-800">
                                    <span class="font-medium">Editing:</span> {{ distributor?.business_name }}
                                    <span class="text-purple-600">•</span>
                                    Created: {{ formatDate(distributor?.created_at) }}
                                    <span class="text-purple-600">•</span>
                                    Last updated: {{ formatDate(distributor?.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Distributor Form -->
                    <DistributorForm 
                        :distributor="distributor"
                        :errors="validationErrors"
                        :submitting="submitting"
                        @submit="handleSubmit"
                        @cancel="handleCancel"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DistributorForm from '@/Components/Distributors/DistributorForm.vue'
import { useApi } from '@/Composables/useApi'

const props = defineProps({
    distributorId: {
        type: [String, Number],
        required: true
    }
})

const { api } = useApi()

// Reactive data
const distributor = ref(null)
const loading = ref(true)
const submitting = ref(false)
const loadError = ref(null)
const successMessage = ref(null)
const errorMessage = ref(null)
const validationErrors = ref({})

const loadDistributor = async () => {
    loading.value = true
    loadError.value = null

    try {
        const response = await api.getDistributor(props.distributorId)
        distributor.value = response.distributor || response
        console.log('Loaded distributor for edit:', distributor.value)
    } catch (error) {
        console.error('Error loading distributor:', error)
        loadError.value = error.response?.data?.message || 'Failed to load distributor information'
    } finally {
        loading.value = false
    }
}

const handleSubmit = async (formData) => {
    submitting.value = true
    successMessage.value = null
    errorMessage.value = null
    validationErrors.value = {}

    try {
        await api.updateDistributor(props.distributorId, formData)
        
        // Update local distributor data
        distributor.value = { ...distributor.value, ...formData }
        
        successMessage.value = 'Distributor updated successfully!'
        
        // Optionally redirect after a delay
        setTimeout(() => {
            router.visit('/distributors')
        }, 2000)
        
    } catch (error) {
        console.error('Error updating distributor:', error)
        
        if (error.response?.status === 422) {
            // Validation errors
            validationErrors.value = error.response.data.errors || {}
            errorMessage.value = 'Please correct the validation errors below.'
        } else if (error.response?.status === 404) {
            errorMessage.value = 'Distributor not found. It may have been deleted.'
        } else {
            // General error
            errorMessage.value = error.response?.data?.message || 'Failed to update distributor. Please try again.'
        }
    } finally {
        submitting.value = false
    }
}

const handleCancel = () => {
    router.visit('/distributors')
}

const formatDate = (dateString) => {
    if (!dateString) return 'Unknown'
    return new Date(dateString).toLocaleDateString('en-ZA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

// Load distributor data on mount
onMounted(() => {
    loadDistributor()
})
</script>