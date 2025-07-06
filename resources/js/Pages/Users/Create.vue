<template>
    <Head title="Create User" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Create User
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Add a new user to the system
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <Link :href="route('users.index')"
                          class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        ← Back to Users
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="createUser" class="p-6 space-y-6">
                        
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Full Name
                            </label>
                            <input v-model="form.name"
                                   id="name"
                                   type="text"
                                   required
                                   :class="errors.name ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                   class="mt-1 block w-full rounded-md shadow-sm"
                                   placeholder="Enter full name">
                            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email Address
                            </label>
                            <input v-model="form.email"
                                   id="email"
                                   type="email"
                                   required
                                   :class="errors.email ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                   class="mt-1 block w-full rounded-md shadow-sm"
                                   placeholder="Enter email address">
                            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <input v-model="form.password"
                                   id="password"
                                   type="password"
                                   required
                                   :class="errors.password ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                   class="mt-1 block w-full rounded-md shadow-sm"
                                   placeholder="Enter secure password">
                            <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
                            <p class="mt-1 text-sm text-gray-500">Password must be at least 8 characters long.</p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirm Password
                            </label>
                            <input v-model="form.password_confirmation"
                                   id="password_confirmation"
                                   type="password"
                                   required
                                   :class="passwordMismatch ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                   class="mt-1 block w-full rounded-md shadow-sm"
                                   placeholder="Confirm password">
                            <p v-if="passwordMismatch" class="mt-1 text-sm text-red-600">Passwords do not match</p>
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">
                                Role
                            </label>
                            <select v-model="form.role"
                                    id="role"
                                    required
                                    :class="errors.role ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                    class="mt-1 block w-full rounded-md shadow-sm">
                                <option value="">Select a role</option>
                                <option value="system_administrator">System Administrator</option>
                                <option value="purchasing_manager">Purchasing Manager</option>
                                <option value="field_sales_associate">Field Sales Associate</option>
                            </select>
                            <p v-if="errors.role" class="mt-1 text-sm text-red-600">{{ errors.role }}</p>
                            
                            <!-- Role Descriptions -->
                            <div v-if="form.role" class="mt-2 p-3 bg-gray-50 rounded-md">
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Role Permissions:</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li v-if="form.role === 'system_administrator'">
                                        • Full system access and user management<br>
                                        • Can create and manage all purchase orders<br>
                                        • Can manage suppliers and distributors<br>
                                        • Can view all reports and analytics
                                    </li>
                                    <li v-if="form.role === 'purchasing_manager'">
                                        • Can create and manage purchase orders<br>
                                        • Can manage suppliers and distributors<br>
                                        • Can view order reports and forecasts<br>
                                        • Cannot manage users
                                    </li>
                                    <li v-if="form.role === 'field_sales_associate'">
                                        • Can update purchase order status and delivery information<br>
                                        • Can view supplier orders assigned to them<br>
                                        • Limited access to reports<br>
                                        • Cannot create orders or manage users
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div v-if="submitError" class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Error creating user</h3>
                                    <p class="mt-1 text-sm text-red-700">{{ submitError }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-4">
                            <Link :href="route('users.index')"
                                  class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </Link>
                            <button type="submit"
                                    :disabled="submitting || !isFormValid"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ submitting ? 'Creating...' : 'Create User' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useApi } from '@/Composables/useApi'

const { api } = useApi()

// Form data
const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: ''
})

const errors = ref({})
const submitError = ref(null)
const submitting = ref(false)

// Computed properties
const passwordMismatch = computed(() => {
    return form.value.password && form.value.password_confirmation && 
           form.value.password !== form.value.password_confirmation
})

const isFormValid = computed(() => {
    return form.value.name && 
           form.value.email && 
           form.value.password && 
           form.value.password_confirmation &&
           form.value.role &&
           !passwordMismatch.value &&
           form.value.password.length >= 8
})

// Methods
const createUser = async () => {
    if (!isFormValid.value) return
    
    submitting.value = true
    errors.value = {}
    submitError.value = null

    try {
        const response = await api.createUser({
            name: form.value.name,
            email: form.value.email,
            password: form.value.password,
            role: form.value.role
        })

        // Redirect to users index with success message
        router.visit('/users', {
            onSuccess: () => {
                // You could show a success message here
                console.log('User created successfully')
            }
        })
    } catch (err) {
        console.error('Error creating user:', err)
        
        if (err.response?.data?.errors) {
            errors.value = err.response.data.errors
        } else {
            submitError.value = err.response?.data?.message || 'Failed to create user'
        }
    } finally {
        submitting.value = false
    }
}
</script>