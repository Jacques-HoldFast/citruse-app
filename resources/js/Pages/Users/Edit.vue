<template>
    <Head :title="`Edit ${user?.name || 'User'}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Edit User
                    </h2>
                    <p v-if="user" class="text-sm text-gray-600 mt-1">
                        {{ user.name }} ({{ user.email }})
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
                
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Loading user details...</p>
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
                            <h3 class="text-sm font-medium text-red-800">Error loading user</h3>
                            <p class="mt-1 text-sm text-red-700">{{ loadError }}</p>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div v-else-if="user" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="updateUser" class="p-6 space-y-6">
                        
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

                        <!-- Password (Optional) -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password (Optional)</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">
                                        New Password
                                    </label>
                                    <input v-model="form.password"
                                           id="password"
                                           type="password"
                                           :class="errors.password ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                           class="mt-1 block w-full rounded-md shadow-sm"
                                           placeholder="Enter new password (leave blank to keep current)">
                                    <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
                                    <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password. If changing, must be at least 8 characters.</p>
                                </div>

                                <div v-if="form.password">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                        Confirm New Password
                                    </label>
                                    <input v-model="form.password_confirmation"
                                           id="password_confirmation"
                                           type="password"
                                           :class="passwordMismatch ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                           class="mt-1 block w-full rounded-md shadow-sm"
                                           placeholder="Confirm new password">
                                    <p v-if="passwordMismatch" class="mt-1 text-sm text-red-600">Passwords do not match</p>
                                </div>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Created:</span>
                                    <span class="text-gray-600 ml-2">{{ formatDate(user.created_at) }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Last Updated:</span>
                                    <span class="text-gray-600 ml-2">{{ formatDate(user.updated_at) }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">User ID:</span>
                                    <span class="text-gray-600 ml-2">{{ user.id }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Current Role:</span>
                                    <span class="text-gray-600 ml-2">{{ getRoleDisplayName(user.role) }}</span>
                                </div>
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
                                    <h3 class="text-sm font-medium text-red-800">Error updating user</h3>
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
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ submitting ? 'Updating...' : 'Update User' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useApi } from '@/Composables/useApi'

const props = defineProps({
    userId: {
        type: [String, Number],
        required: true
    }
})

const { api } = useApi()

// Reactive data
const user = ref(null)
const loading = ref(true)
const loadError = ref(null)
const submitting = ref(false)
const submitError = ref(null)
const errors = ref({})

const form = ref({
    name: '',
    email: '',
    role: '',
    password: '',
    password_confirmation: ''
})

// Computed properties
const passwordMismatch = computed(() => {
    return form.value.password && form.value.password_confirmation && 
           form.value.password !== form.value.password_confirmation
})

const isFormValid = computed(() => {
    const basicValid = form.value.name && form.value.email && form.value.role
    
    if (form.value.password) {
        return basicValid && 
               form.value.password.length >= 8 && 
               form.value.password_confirmation &&
               !passwordMismatch.value
    }
    
    return basicValid
})

// Methods
onMounted(() => {
    loadUser()
})

const loadUser = async () => {
    loading.value = true
    loadError.value = null

    try {
        // For now, we'll need to implement a getUser by ID method
        // or use the existing users list and filter
        const response = await api.getUsers()
        const users = response.data || response
        const foundUser = users.find(u => u.id == props.userId)
        
        if (!foundUser) {
            throw new Error('User not found')
        }
        
        user.value = foundUser
        
        // Populate form
        form.value.name = user.value.name
        form.value.email = user.value.email
        form.value.role = user.value.role
        
        console.log('Loaded user:', user.value)
    } catch (err) {
        console.error('Error loading user:', err)
        loadError.value = err.response?.data?.message || 'Failed to load user'
    } finally {
        loading.value = false
    }
}

const updateUser = async () => {
    if (!isFormValid.value) return
    
    submitting.value = true
    errors.value = {}
    submitError.value = null

    try {
        const updateData = {
            name: form.value.name,
            email: form.value.email,
            role: form.value.role
        }
        
        // Only include password if it's being changed
        if (form.value.password) {
            updateData.password = form.value.password
        }

        const response = await api.updateUser(props.userId, updateData)

        // Redirect to users index with success message
        router.visit('/users', {
            onSuccess: () => {
                console.log('User updated successfully')
            }
        })
    } catch (err) {
        console.error('Error updating user:', err)
        
        if (err.response?.data?.errors) {
            errors.value = err.response.data.errors
        } else {
            submitError.value = err.response?.data?.message || 'Failed to update user'
        }
    } finally {
        submitting.value = false
    }
}

const getRoleDisplayName = (role) => {
    const roles = {
        'system_administrator': 'System Administrator',
        'purchasing_manager': 'Purchasing Manager',
        'field_sales_associate': 'Field Sales Associate'
    }
    return roles[role] || 'Unknown'
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString('en-ZA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>