<template>
    <Head :title="`Purchase Order ${purchaseOrder?.po_number || ''}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Purchase Order Details
                    </h2>
                    <p v-if="purchaseOrder" class="text-sm text-gray-600 mt-1">
                        {{ purchaseOrder.po_number }} - {{ getCompanyName(purchaseOrder) }}
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <button v-if="canUpdateStatus" 
                            @click="showingStatusModal = true"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Update Status
                    </button>
                    <Link v-if="canEdit" 
                          :href="route('purchase-orders.edit', orderId)"
                          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Edit Order
                    </Link>
                    <Link :href="route('purchase-orders.index')"
                          class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        ← Back to Orders
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Loading purchase order details...</p>
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
                            <h3 class="text-sm font-medium text-red-800">Error loading purchase order</h3>
                            <p class="mt-1 text-sm text-red-700">{{ loadError }}</p>
                            <button @click="loadPurchaseOrder" 
                                    class="mt-2 text-sm text-red-600 hover:text-red-500 underline">
                                Try again
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Purchase Order Content -->
                <div v-else-if="purchaseOrder" class="space-y-6">
                    
                    <!-- Header Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center space-x-4">
                                    <div :class="getTypeIconClass(purchaseOrder.type)" 
                                         class="h-16 w-16 rounded-lg flex items-center justify-center text-white text-2xl">
                                        {{ purchaseOrder.type === 'distributor_order' ? '📥' : '📤' }}
                                    </div>
                                    <div>
                                        <h1 class="text-2xl font-bold text-gray-900 font-mono">{{ purchaseOrder.po_number }}</h1>
                                        <div class="mt-1 flex items-center space-x-4">
                                            <span :class="getTypeBadgeClass(purchaseOrder.type)" 
                                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium">
                                                {{ purchaseOrder.type === 'distributor_order' ? 'POD - From Distributor' : 'POS - To Supplier' }}
                                            </span>
                                            <span :class="getStatusBadgeClass(purchaseOrder.status)" 
                                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium">
                                                {{ formatStatus(purchaseOrder.status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-3xl font-bold text-gray-900">
                                        R{{ formatCurrency(purchaseOrder.total_value_ex_vat) }}
                                    </div>
                                    <div class="text-sm text-gray-500">Total Value (ex VAT)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Product
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Quantity (kg)
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Unit Price
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Delivery Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Line Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in purchaseOrder.items" :key="item.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ item.product?.product_code }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ item.product?.description }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatNumber(item.quantity_kg) }} kg
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            R{{ formatCurrency(item.unit_price_ex_vat) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(item.required_delivery_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            R{{ formatCurrency(item.total_value_ex_vat) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                            Total Order Value (ex VAT):
                                        </td>
                                        <td class="px-6 py-3 text-sm font-bold text-gray-900">
                                            R{{ formatCurrency(purchaseOrder.total_value_ex_vat) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
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
                            Update Status - {{ purchaseOrder?.po_number }}
                        </h3>
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">
                                Current Status: <strong>{{ formatStatus(purchaseOrder?.status || '') }}</strong>
                            </p>
                        </div>
                        <div class="space-y-3">
                            <div v-for="status in availableStatuses" :key="status">
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
                        <button @click="showingStatusModal = false"
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

const props = defineProps({
    orderId: {
        type: [String, Number],
        required: true
    }
})

const { api } = useApi()
const page = usePage()

// Reactive data
const purchaseOrder = ref(null)
const loading = ref(true)
const loadError = ref(null)
const showingStatusModal = ref(false)
const newStatus = ref('')
const statusUpdating = ref(false)
const userData = ref(null)

// User permissions - use API data like other components
const user = computed(() => {
    if (userData.value?.user) {
        return userData.value.user
    }
    if (page.props.auth?.user) {
        return page.props.auth.user
    }
    return {}
})

const canEdit = computed(() => {
    if (!purchaseOrder.value || !user.value) return false
    
    if (user.value.role === 'system_administrator') return true
    if (user.value.role === 'purchasing_manager') {
        return purchaseOrder.value.status === 'new' || 
               purchaseOrder.value.status === 'rejected_by_supplier'
    }
    return false
})

const canUpdateStatus = computed(() => {
    if (!purchaseOrder.value || !user.value) return false
    
    if (user.value.role === 'field_sales_associate') {
        return purchaseOrder.value.type === 'supplier_order' && 
               ['new', 'accepted_by_supplier', 'in_delivery'].includes(purchaseOrder.value.status)
    }
    return canEdit.value
})

const availableStatuses = computed(() => {
    if (!purchaseOrder.value) return []
    
    const statusTransitions = {
        'new': ['accepted_by_supplier', 'rejected_by_supplier', 'cancelled'],
        'accepted_by_supplier': ['in_delivery', 'cancelled'],
        'in_delivery': ['delivered', 'rejected_by_distributor'],
        'delivered': [],
        'rejected_by_supplier': ['new', 'cancelled'],
        'rejected_by_distributor': ['in_delivery', 'cancelled'],
        'cancelled': []
    }
    
    return statusTransitions[purchaseOrder.value.status] || []
})

// Methods
onMounted(() => {
    loadUserData()
    loadPurchaseOrder()
})

const loadUserData = async () => {
    try {
        const response = await api.getUser()
        userData.value = response
    } catch (error) {
        console.error('Error loading user data:', error)
    }
}

const loadPurchaseOrder = async () => {
    loading.value = true
    loadError.value = null

    try {
        const response = await api.getPurchaseOrder(props.orderId)
        purchaseOrder.value = response.purchase_order || response
        console.log('Loaded purchase order:', purchaseOrder.value)
    } catch (error) {
        console.error('Error loading purchase order:', error)
        loadError.value = error.response?.data?.message || 'Failed to load purchase order'
    } finally {
        loading.value = false
    }
}

const getCompanyName = (order) => {
    if (order.supplier) return order.supplier.business_name
    if (order.distributor) return order.distributor.business_name
    return 'Unknown Company'
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

const formatNumber = (value) => {
    return new Intl.NumberFormat('en-ZA').format(value || 0)
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString('en-ZA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const updateStatus = async () => {
    if (!newStatus.value || !purchaseOrder.value) return
    
    statusUpdating.value = true
    try {
        await api.updatePurchaseOrder(purchaseOrder.value.id, { status: newStatus.value })
        
        // Update local data
        purchaseOrder.value.status = newStatus.value
        
        showingStatusModal.value = false
        newStatus.value = ''
        
        // Optionally reload the order to get updated data
        await loadPurchaseOrder()
    } catch (error) {
        console.error('Error updating status:', error)
        alert('Failed to update status: ' + (error.response?.data?.message || 'Unknown error'))
    } finally {
        statusUpdating.value = false
    }
}
</script>