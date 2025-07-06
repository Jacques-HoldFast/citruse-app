<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Users
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Manage system users and their roles
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <Link :href="route('users.create')"
                          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add User
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Search and Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
                                <!-- Search -->
                                <div class="md:col-span-2">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <input v-model="searchQuery"
                                               @input="filterUsers"
                                               type="text" 
                                               placeholder="Search by name or email..."
                                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <!-- Role Filter -->
                                <div>
                                    <select v-model="selectedRole" 
                                            @change="filterUsers"
                                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                        <option value="">All Roles</option>
                                        <option value="system_administrator">System Administrator</option>
                                        <option value="purchasing_manager">Purchasing Manager</option>
                                        <option value="field_sales_associate">Field Sales Associate</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permission Check -->
                <div v-if="!loading && currentUser && currentUser.role !== 'system_administrator'" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Access Restricted</h3>
                            <p class="mt-1 text-sm text-yellow-700">
                                You need System Administrator privileges to manage users. 
                                Current role: {{ getRoleDisplayName(currentUser.role) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Loading users...</p>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Error loading users</h3>
                            <p class="mt-1 text-sm text-red-700">{{ error }}</p>
                            <button @click="loadUsers" 
                                    class="mt-2 text-sm text-red-600 hover:text-red-500 underline">
                                Try again
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div v-else class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in filteredUsers" :key="user.id" 
                                class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ getUserInitials(user.name) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ user.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ID: {{ user.id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getRoleBadgeClass(user.role)" 
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        {{ getRoleDisplayName(user.role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ user.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(user.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <Link :href="route('users.edit', user.id)"
                                          class="text-green-600 hover:text-green-900">
                                        Edit
                                    </Link>
                                    <button v-if="user.id !== currentUser?.id" 
                                            @click="confirmDeleteUser(user)"
                                            class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="!loading && !error && filteredUsers.length === 0" 
                     class="text-center py-12 bg-white rounded-lg shadow-sm">
                    <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ searchQuery || selectedRole ? 'Try adjusting your search criteria.' : 'Get started by adding your first user.' }}
                    </p>
                    <div class="mt-6">
                        <Link :href="route('users.create')"
                              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add User
                        </Link>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div v-if="!loading && !error && filteredUsers.length > 0" 
                     class="mt-6 bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ filteredUsers.length }}</div>
                            <div class="text-sm text-gray-500">Total Users</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600">{{ adminCount }}</div>
                            <div class="text-sm text-gray-500">System Admins</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ purchasingManagerCount }}</div>
                            <div class="text-sm text-gray-500">Purchasing Managers</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ fieldSalesCount }}</div>
                            <div class="text-sm text-gray-500">Field Sales Associates</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showingDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Delete User
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete <strong>{{ userToDelete?.name }}</strong>? 
                                        This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="deleteUser" 
                                :disabled="deleting"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            {{ deleting ? 'Deleting...' : 'Delete' }}
                        </button>
                        <button @click="hideDeleteModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useApi } from '@/Composables/useApi'

const { api } = useApi()
const page = usePage()

// Reactive data
const users = ref([])
const searchQuery = ref('')
const selectedRole = ref('')
const loading = ref(true)
const error = ref(null)
const showingDeleteModal = ref(false)
const userToDelete = ref(null)
const deleting = ref(false)
const userData = ref(null)

// Current user
const currentUser = computed(() => {
    if (userData.value?.user) {
        return userData.value.user
    }
    if (page.props.auth?.user) {
        return page.props.auth.user
    }
    return null
})

// Computed properties
const filteredUsers = computed(() => {
    let filtered = users.value

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(user => 
            user.name.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query)
        )
    }

    if (selectedRole.value) {
        filtered = filtered.filter(user => user.role === selectedRole.value)
    }

    return filtered
})

const adminCount = computed(() => 
    filteredUsers.value.filter(u => u.role === 'system_administrator').length
)

const purchasingManagerCount = computed(() => 
    filteredUsers.value.filter(u => u.role === 'purchasing_manager').length
)

const fieldSalesCount = computed(() => 
    filteredUsers.value.filter(u => u.role === 'field_sales_associate').length
)

// Methods
onMounted(() => {
    loadUserData()
    loadUsers()
})

const loadUserData = async () => {
    try {
        const response = await api.getUser()
        userData.value = response
    } catch (err) {
        console.error('Error loading user data:', err)
    }
}

const loadUsers = async () => {
    loading.value = true
    error.value = null

    try {
        const params = {}
        if (searchQuery.value && searchQuery.value.trim()) {
            params.search = searchQuery.value.trim()
        }
        if (selectedRole.value) {
            params.role = selectedRole.value
        }
        
        console.log('API params being sent:', params)
        const response = await api.getUsers(params)
        console.log('API response:', response);
        console.log('response.data:', response.data);
        console.log('Is response.data an array?', Array.isArray(response.data));
        console.log('Is response an array?', Array.isArray(response));
        console.log('Response keys:', Object.keys(response));
        
        if (response.data) {
            users.value = response.data
        } else if (Array.isArray(response)) {
            users.value = response
        } else {
            users.value = []
        }
        
        console.log('Loaded users:', users.value)
        console.log('Current user role:', currentUser.value?.role)
    } catch (err) {
        console.error('Error loading users:', err)
        error.value = err.response?.data?.message || 'Failed to load users'
        users.value = []
    } finally {
        loading.value = false
    }
}

const filterUsers = () => {
    // Filtering handled by computed property
    // Could implement server-side filtering here if needed
}

const getUserInitials = (name) => {
    return name.split(' ').map(word => word[0]).join('').toUpperCase()
}

const getRoleDisplayName = (role) => {
    const roles = {
        'system_administrator': 'System Administrator',
        'purchasing_manager': 'Purchasing Manager',
        'field_sales_associate': 'Field Sales Associate'
    }
    return roles[role] || 'Unknown'
}

const getRoleBadgeClass = (role) => {
    const classes = {
        'system_administrator': 'bg-red-100 text-red-800',
        'purchasing_manager': 'bg-blue-100 text-blue-800',
        'field_sales_associate': 'bg-green-100 text-green-800'
    }
    return classes[role] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-ZA', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const confirmDeleteUser = (user) => {
    userToDelete.value = user
    showingDeleteModal.value = true
}

const hideDeleteModal = () => {
    userToDelete.value = null
    showingDeleteModal.value = false
}

const deleteUser = async () => {
    if (!userToDelete.value) return
    
    deleting.value = true
    try {
        await api.deleteUser(userToDelete.value.id)
        
        // Remove user from local list
        const index = users.value.findIndex(u => u.id === userToDelete.value.id)
        if (index !== -1) {
            users.value.splice(index, 1)
        }
        
        hideDeleteModal()
    } catch (err) {
        console.error('Error deleting user:', err)
        alert('Failed to delete user: ' + (err.response?.data?.message || 'Unknown error'))
    } finally {
        deleting.value = false
    }
}
</script>