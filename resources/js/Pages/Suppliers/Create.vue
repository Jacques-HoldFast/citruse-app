<template>
    <Head title="Add Supplier" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Add New Supplier
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Create a new supplier record for citrus fruit procurement
                    </p>
                </div>
                <div>
                    <Link :href="route('suppliers.index')"
                          class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        ← Back to Suppliers
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                
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

                <!-- Supplier Form -->
                <SupplierForm 
                    :errors="validationErrors"
                    :submitting="submitting"
                    @submit="handleSubmit"
                    @cancel="handleCancel"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SupplierForm from '@/Components/Suppliers/SupplierForm.vue'
import { useApi } from '@/Composables/useApi'

const { api } = useApi()

// Reactive data
const submitting = ref(false)
const successMessage = ref(null)
const errorMessage = ref(null)
const validationErrors = ref({})

const handleSubmit = async (formData) => {
    submitting.value = true
    successMessage.value = null
    errorMessage.value = null
    validationErrors.value = {}

    try {
        const response = await api.createSupplier(formData)
        
        successMessage.value = 'Supplier created successfully!'
        
        // Redirect to suppliers list after a short delay
        setTimeout(() => {
            router.visit('/suppliers')
        }, 2000)
        
    } catch (error) {
        console.error('Error creating supplier:', error)
        
        if (error.response?.status === 422) {
            // Validation errors
            validationErrors.value = error.response.data.errors || {}
            errorMessage.value = 'Please correct the validation errors below.'
        } else {
            // General error
            errorMessage.value = error.response?.data?.message || 'Failed to create supplier. Please try again.'
        }
    } finally {
        submitting.value = false
    }
}

const handleCancel = () => {
    router.visit('/suppliers')
}
</script>