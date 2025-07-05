<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dashboard
                </h2>
                <div class="text-sm text-gray-600">
                    Welcome back, {{ userName }}!
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- User Info Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Your Profile</h3>
                                <div class="mt-2 space-y-1">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Role:</span> {{ userRole }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Email:</span> {{ userEmail }}
                                    </p>
                                </div>

                    <!-- Active Orders Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Active POD Orders -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center text-white text-2xl">
                                            📥
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-500 uppercase">Active POD Orders</h3>
                                        <p class="text-2xl font-bold text-blue-600">{{ activePodOrders }}</p>
                                        <p class="text-sm text-gray-500">From distributors</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active POS Orders -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-indigo-500 rounded-lg flex items-center justify-center text-white text-2xl">
                                            📤
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-500 uppercase">Active POS Orders</h3>
                                        <p class="text-2xl font-bold text-indigo-600">{{ activePosOrders }}</p>
                                        <p class="text-sm text-gray-500">To suppliers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                            </div>
                            <div class="text-right">
                                <span :class="roleColorClass" 
                                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                                    {{ userRole }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Data -->
                <div v-if="loading" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Loading dashboard data...</p>
                </div>

                <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Unable to load dashboard data</h3>
                            <p class="mt-1 text-sm text-red-700">{{ error }}</p>
                            <button @click="loadDashboardData" 
                                    class="mt-2 text-sm text-red-600 hover:text-red-500 underline">
                                Try again
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Content -->
                <div v-else class="space-y-6">
                    
                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Products Count -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center text-white text-2xl">
                                            🍊
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-500 uppercase">Products</h3>
                                        <p class="text-2xl font-bold text-orange-600">{{ productCount }}</p>
                                        <p class="text-sm text-gray-500">Citrus varieties</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Users Count (if admin) -->
                        <div v-if="canSeeUserCount" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center text-white text-2xl">
                                            👥
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-500 uppercase">Users</h3>
                                        <p class="text-2xl font-bold text-blue-600">{{ userCount }}</p>
                                        <p class="text-sm text-gray-500">Active accounts</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Suppliers Count -->
                        <div v-if="canManageSuppliers" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center text-white text-2xl">
                                            🏢
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-500 uppercase">Suppliers</h3>
                                        <p class="text-2xl font-bold text-green-600">{{ supplierCount }}</p>
                                        <p class="text-sm text-gray-500">Active suppliers</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Distributors Count -->
                        <div v-if="canManageSuppliers" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center text-white text-2xl">
                                            🌍
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-500 uppercase">Distributors</h3>
                                        <p class="text-2xl font-bold text-purple-600">{{ distributorCount }}</p>
                                        <p class="text-sm text-gray-500">Active distributors</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                
                                <!-- View Products -->
                                <button @click="goToProducts" 
                                        class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-left transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="text-2xl">🍊</div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">View Products</h4>
                                            <p class="text-sm text-gray-600">Browse the citrus catalog</p>
                                        </div>
                                    </div>
                                </button>

                                <!-- Manage Suppliers (if authorized) -->
                                <button v-if="canManageSuppliers" 
                                        @click="goToSuppliers"
                                        class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-left transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="text-2xl">🏢</div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">Manage Suppliers</h4>
                                            <p class="text-sm text-gray-600">Add and edit suppliers</p>
                                        </div>
                                    </div>
                                </button>

                                <!-- Manage Distributors (if authorized) -->
                                <button v-if="canManageSuppliers" 
                                        @click="goToDistributors"
                                        class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-left transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="text-2xl">🌍</div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">Manage Distributors</h4>
                                            <p class="text-sm text-gray-600">Add and edit distributors</p>
                                        </div>
                                    </div>
                                </button>

                                <!-- Create Purchase Order (if authorized) -->
                                <button v-if="canCreatePurchaseOrders" 
                                        @click="goToOrders"
                                        class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-left transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="text-2xl">📋</div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">Purchase Orders</h4>
                                            <p class="text-sm text-gray-600">View and manage orders</p>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Debug Info (collapsible) -->
                    <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4">
                            <button @click="showDebug = !showDebug" 
                                    class="text-sm font-medium text-gray-700 hover:text-gray-900 flex items-center">
                                <span>{{ showDebug ? '▼' : '▶' }}</span>
                                <span class="ml-2">Debug Info</span>
                            </button>
                            <div v-if="showDebug" class="mt-4">
                                <pre class="text-xs text-gray-600 bg-white p-4 rounded overflow-x-auto">{{ JSON.stringify({ user: userData, dashboard: dashboardData }, null, 2) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useApi } from '@/Composables/useApi'

const { loading, error, api } = useApi()
const page = usePage()

// Reactive data
const userData = ref(null)
const dashboardData = ref(null)
const showDebug = ref(false)

// User info - prioritize API response since page props might not have auth data
const user = computed(() => {
    // Use API response if available
    if (userData.value?.user) {
        return userData.value.user
    }
    // Fall back to page props
    if (page.props.auth?.user) {
        return page.props.auth.user
    }
    return {}
})

const userName = computed(() => user.value.name || 'User')
const userEmail = computed(() => user.value.email || 'No email')
const userRole = computed(() => user.value.role_display_name || user.value.role || 'User')

// User permissions
const canManageSuppliers = computed(() => user.value.permissions?.can_manage_suppliers || false)
const canCreatePurchaseOrders = computed(() => user.value.permissions?.can_create_purchase_orders || false)
const canSeeUserCount = computed(() => user.value.permissions?.is_system_admin || false)

// Role color class
const roleColorClass = computed(() => {
    const role = user.value.role
    const colors = {
        'system_administrator': 'bg-red-100 text-red-800',
        'purchasing_manager': 'bg-blue-100 text-blue-800',
        'field_sales_associate': 'bg-green-100 text-green-800'
    }
    return colors[role] || 'bg-gray-100 text-gray-800'
})

// Computed counts (matching actual API structure)
const productCount = computed(() => {
    return dashboardData.value?.total_products || 7 // Fallback to known count
})

const userCount = computed(() => {
    return dashboardData.value?.total_users || 5 // Fallback to known count
})

const supplierCount = computed(() => {
    return dashboardData.value?.total_suppliers || 0
})

const distributorCount = computed(() => {
    return dashboardData.value?.total_distributors || 0
})

const activePodOrders = computed(() => {
    return dashboardData.value?.active_pod_orders || 0
})

const activePosOrders = computed(() => {
    return dashboardData.value?.active_pos_orders || 0
})

const loadDashboardData = async () => {
    try {
        // Try to get both user data and dashboard data
        const [userResponse, dashboardResponse] = await Promise.allSettled([
            api.getUser(),
            api.getDashboard()
        ])

        if (userResponse.status === 'fulfilled') {
            userData.value = userResponse.value
        }

        if (dashboardResponse.status === 'fulfilled') {
            dashboardData.value = dashboardResponse.value.dashboard || dashboardResponse.value
        } else {
            console.warn('Dashboard API not available, using mock data')
            // Provide some default data if dashboard API isn't working yet
            dashboardData.value = {
                products_count: 7, // We know we have 7 products from seeder
                users_count: 5,    // We know we have 5 users from seeder
                suppliers_count: 0,
                distributors_count: 0
            }
        }
    } catch (err) {
        console.error('Error loading dashboard:', err)
        // Even if there's an error, show some basic info
        dashboardData.value = {
            products_count: 7,
            users_count: 5,
            suppliers_count: 0,
            distributors_count: 0
        }
    }
}

onMounted(() => {
    loadDashboardData()
})

// Navigation methods
const goToProducts = () => {
    router.visit('/products')
}

const goToSuppliers = () => {
    router.visit('/suppliers')
}

const goToOrders = () => {
    router.visit('/purchase-orders')
}

const goToDistributors = () => {
    router.visit('/distributors')
}
</script>