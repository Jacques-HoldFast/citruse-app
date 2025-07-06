// PurchaseOrders/Index.vue
<template>
    <Head title="Purchase Orders" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Purchase Orders
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Manage purchase orders from distributors and to suppliers
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <Link v-if="canCreatePO" :href="route('purchase-orders.create')"
                          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Purchase Order
                    </Link>
                    <Link :href="route('purchase-orders.forecast')"
                          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        📊 Pipeline Forecast
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
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 flex-1">
                                <!-- Search -->
                                <div class="md:col-span-2">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <input v-model="searchQuery"
                                               @input="filterPurchaseOrders"
                                               type="text" 
                                               placeholder="Search by PO number, company..."
                                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <!-- Type Filter -->
                                <div>
                                    <select v-model="selectedType" 
                                            @change="filterPurchaseOrders"
                                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                        <option value="">All Types</option>
                                        <option value="distributor_order">POD (From Distributors)</option>
                                        <option value="supplier_order">POS (To Suppliers)</option>
                                    </select>
                                </div>
                                
                                <!-- Status Filter -->
                                <div>
                                    <select v-model="selectedStatus" 
                                            @change="filterPurchaseOrders"
                                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                        <option value="">All Statuses</option>
                                        <option value="new">New</option>
                                        <option value="accepted_by_supplier">Accepted by Supplier</option>
                                        <option value="in_delivery">In Delivery</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="rejected_by_supplier">Rejected by Supplier</option>
                                        <option value="rejected_by_distributor">Rejected by Distributor</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Quick Create Button -->
                            <div class="flex-shrink-0">
                                <Link v-if="canCreatePO" :href="route('purchase-orders.create')"
                                      class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center whitespace-nowrap">
                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Quick Create
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Priority Orders - Orders with Shortages -->
                <div v-if="ordersWithShortages.length > 0" class="mb-6">
                    <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-orange-800">
                                    {{ ordersWithShortages.length }} order(s) have supply shortages
                                </h3>
                                <p class="mt-1 text-sm text-orange-700">
                                    These orders need immediate attention to ensure delivery commitments are met.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        <div v-for="order in ordersWithShortages" :key="'shortage-' + order.id" 
                             class="bg-white border-l-4 border-orange-400 rounded-lg shadow-sm p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="font-mono font-semibold text-gray-900">{{ order.po_number }}</div>
                                    <div class="text-sm text-gray-600">{{ getCompanyName(order) }}</div>
                                </div>
                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-medium">
                                    Shortage
                                </span>
                            </div>
                            <div class="text-sm text-gray-700 mb-3">
                                Due: {{ formatDate(order.required_delivery_date) }}
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="text-lg font-semibold text-gray-900">
                                    R{{ formatCurrency(order.total_value_ex_vat) }}
                                </div>
                                <Link :href="route('purchase-orders.show', order.id)"
                                      class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View Details →
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Loading purchase orders...</p>
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
                            <h3 class="text-sm font-medium text-red-800">Error loading purchase orders</h3>
                            <p class="mt-1 text-sm text-red-700">{{ error }}</p>
                            <button @click="loadPurchaseOrders" 
                                    class="mt-2 text-sm text-red-600 hover:text-red-500 underline">
                                Try again
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Purchase Orders Table -->
                <div v-else class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    PO Number
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Company
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Value
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
                            <tr v-for="order in filteredPurchaseOrders" :key="order.id" 
                                class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div :class="getTypeIconClass(order.type)" 
                                                 class="h-8 w-8 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                                                {{ order.type === 'distributor_order' ? '📥' : '📤' }}
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 font-mono">
                                                {{ order.po_number }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getTypeBadgeClass(order.type)" 
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        {{ order.type === 'distributor_order' ? 'POD' : 'POS' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ getCompanyName(order) }}</div>
                                    <div class="text-sm text-gray-500">{{ getCompanyCountry(order) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusBadgeClass(order.status)" 
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        {{ formatStatus(order.status) }}
                                    </span>
                                    <div v-if="order.has_shortages" class="text-xs text-orange-600 mt-1">
                                        ⚠️ Has shortages
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    R{{ formatCurrency(order.total_value_ex_vat) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(order.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <Link :href="route('purchase-orders.show', order.id)"
                                          class="text-blue-600 hover:text-blue-900">
                                        View
                                    </Link>
                                    <button v-if="canEditPO(order)" 
                                            @click="editPurchaseOrder(order.id)"
                                            class="text-green-600 hover:text-green-900">
                                        Edit
                                    </button>
                                    <button v-if="canUpdateStatus(order)" 
                                            @click="showStatusModal(order)"
                                            class="text-purple-600 hover:text-purple-900">
                                        Update Status
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="!loading && !error && filteredPurchaseOrders.length === 0" 
                     class="text-center py-12 bg-white rounded-lg shadow-sm">
                    <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No purchase orders found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ searchQuery || selectedType || selectedStatus ? 'Try adjusting your search criteria.' : 'Get started by creating your first purchase order.' }}
                    </p>
                    <div v-if="canCreatePO" class="mt-6">
                        <Link :href="route('purchase-orders.create')"
                              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create Purchase Order
                        </Link>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div v-if="!loading && !error && filteredPurchaseOrders.length > 0" 
                     class="mt-6 bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ filteredPurchaseOrders.length }}</div>
                            <div class="text-sm text-gray-500">Showing Orders</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ podCount }}</div>
                            <div class="text-sm text-gray-500">POD Orders</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-purple-600">{{ posCount }}</div>
                            <div class="text-sm text-gray-500">POS Orders</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">
                                R{{ formatCurrency(totalValue) }}
                            </div>
                            <div class="text-sm text-gray-500">Total Value</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Update Modal -->
        <div v-if="showingStatusModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Update Status - {{ selectedOrder?.po_number }}
                        </h3>
                        <div class="space-y-3">
                            <div v-for="status in getAvailableStatuses(selectedOrder)" :key="status">
                                <label class="flex items-center">
                                    <input type="radio" 
                                           :value="status" 
                                           v-model="newStatus"
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                    <span class="ml-3 text-sm text-gray-700">{{ formatStatus(status) }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="updateStatus" 
                                :disabled="!newStatus || statusUpdating"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            {{ statusUpdating ? 'Updating...' : 'Update Status' }}
                        </button>
                        <button @click="hideStatusModal"
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
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useApi } from '@/Composables/useApi'

const { loading, error, api } = useApi()
const page = usePage()

// Reactive data
const purchaseOrders = ref([])
const searchQuery = ref('')
const selectedType = ref('')
const selectedStatus = ref('')
const showingStatusModal = ref(false)
const selectedOrder = ref(null)
const newStatus = ref('')
const statusUpdating = ref(false)

// User permissions
const user = computed(() => page.props.auth?.user || {})
const canCreatePO = computed(() => 
    user.value.role === 'system_administrator' || 
    user.value.role === 'purchasing_manager'
)

// Computed properties
const filteredPurchaseOrders = computed(() => {
    let filtered = purchaseOrders.value

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(order => 
            order.po_number.toLowerCase().includes(query) ||
            getCompanyName(order).toLowerCase().includes(query)
        )
    }

    if (selectedType.value) {
        filtered = filtered.filter(order => order.type === selectedType.value)
    }

    if (selectedStatus.value) {
        filtered = filtered.filter(order => order.status === selectedStatus.value)
    }

    return filtered
})

const ordersWithShortages = computed(() => {
    return purchaseOrders.value.filter(order => order.has_shortages)
})

const podCount = computed(() => 
    filteredPurchaseOrders.value.filter(o => o.type === 'distributor_order').length
)

const posCount = computed(() => 
    filteredPurchaseOrders.value.filter(o => o.type === 'supplier_order').length
)

const totalValue = computed(() => 
    filteredPurchaseOrders.value.reduce((sum, order) => sum + (order.total_value_ex_vat || 0), 0)
)

// Methods
const loadPurchaseOrders = async () => {
    try {
        const response = await api.getPurchaseOrders()
        
        if (response.data) {
            purchaseOrders.value = response.data
        } else if (Array.isArray(response)) {
            purchaseOrders.value = response
        } else {
            purchaseOrders.value = []
        }
        
        console.log('Loaded purchase orders:', purchaseOrders.value)
    } catch (err) {
        console.error('Error loading purchase orders:', err)
        purchaseOrders.value = []
    }
}

const filterPurchaseOrders = () => {
    // Filtering handled by computed property
}

const getCompanyName = (order) => {
    if (order.supplier) return order.supplier.business_name
    if (order.distributor) return order.distributor.business_name
    return 'Unknown Company'
}

const getCompanyCountry = (order) => {
    if (order.supplier) return order.supplier.country
    if (order.distributor) return order.distributor.country
    return ''
}

const getTypeIconClass = (type) => {
    return type === 'distributor_order' ? 'bg-blue-500' : 'bg-purple-500'
}

const getTypeBadgeClass = (type) => {
    return type === 'distributor_order' 
        ? 'bg-blue-100 text-blue-800' 
        : 'bg-purple-100 text-purple-800'
}

const getStatusBadgeClass = (status) => {
    const classes = {
        'new': 'bg-yellow-100 text-yellow-800',
        'accepted_by_supplier': 'bg-green-100 text-green-800',
        'in_delivery': 'bg-blue-100 text-blue-800',
        'delivered': 'bg-green-100 text-green-800',
        'rejected_by_supplier': 'bg-red-100 text-red-800',
        'rejected_by_distributor': 'bg-red-100 text-red-800',
        'cancelled': 'bg-gray-100 text-gray-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatStatus = (status) => {
    return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-ZA', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value || 0)
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-ZA', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const canEditPO = (order) => {
    if (user.value.role === 'system_administrator') return true
    if (user.value.role === 'purchasing_manager') {
        return order.status === 'new' || order.status === 'rejected_by_supplier'
    }
    return false
}

const canUpdateStatus = (order) => {
    if (user.value.role === 'field_sales_associate') {
        return order.type === 'supplier_order' && 
               ['new', 'accepted_by_supplier', 'in_delivery'].includes(order.status)
    }
    return canEditPO(order)
}

const editPurchaseOrder = (id) => {
    router.visit(`/purchase-orders/${id}/edit`)
}

const showStatusModal = (order) => {
    selectedOrder.value = order
    newStatus.value = ''
    showingStatusModal.value = true
}

const hideStatusModal = () => {
    selectedOrder.value = null
    newStatus.value = ''
    showingStatusModal.value = false
}

const getAvailableStatuses = (order) => {
    if (!order) return []
    
    const statusTransitions = {
        'new': ['accepted_by_supplier', 'rejected_by_supplier', 'cancelled'],
        'accepted_by_supplier': ['in_delivery', 'cancelled'],
        'in_delivery': ['delivered', 'rejected_by_distributor'],
        'delivered': [],
        'rejected_by_supplier': ['new', 'cancelled'],
        'rejected_by_distributor': ['in_delivery', 'cancelled'],
        'cancelled': []
    }
    
    return statusTransitions[order.status] || []
}

const updateStatus = async () => {
    if (!newStatus.value || !selectedOrder.value) return
    
    statusUpdating.value = true
    try {
        await api.updatePurchaseOrderStatus(selectedOrder.value.id, newStatus.value)
        
        // Update local data
        const orderIndex = purchaseOrders.value.findIndex(o => o.id === selectedOrder.value.id)
        if (orderIndex !== -1) {
            purchaseOrders.value[orderIndex].status = newStatus.value
        }
        
        hideStatusModal()
    } catch (err) {
        console.error('Error updating status:', err)
        alert('Failed to update status')
    } finally {
        statusUpdating.value = false
    }
}

// Load data on mount
onMounted(() => {
    loadPurchaseOrders()
})
</script>