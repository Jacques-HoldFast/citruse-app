<template>
    <Head title="Pipeline Forecast" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        📊 Pipeline Forecast
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        View upcoming orders and supply commitments for the next {{ months }} months
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <select v-model="months" 
                            @change="loadForecast"
                            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                        <option value="3">3 Months</option>
                        <option value="6">6 Months</option>
                        <option value="12">12 Months</option>
                    </select>
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
                    <p class="mt-2 text-gray-600">Loading pipeline forecast...</p>
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
                            <h3 class="text-sm font-medium text-red-800">Error loading forecast</h3>
                            <p class="mt-1 text-sm text-red-700">{{ error }}</p>
                            <button @click="loadForecast" 
                                    class="mt-2 text-sm text-red-600 hover:text-red-500 underline">
                                Try again
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Forecast Data -->
                <div v-else-if="forecastData" class="space-y-6">
                    
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div class="bg-white rounded-lg shadow-sm p-4">
                            <div class="text-2xl font-bold text-blue-600">{{ forecastData.summary.total_pod_orders }}</div>
                            <div class="text-sm text-gray-500">POD Orders</div>
                        </div>
                        <div class="bg-white rounded-lg shadow-sm p-4">
                            <div class="text-2xl font-bold text-purple-600">{{ forecastData.summary.total_pos_orders }}</div>
                            <div class="text-sm text-gray-500">POS Orders</div>
                        </div>
                        <div class="bg-white rounded-lg shadow-sm p-4">
                            <div class="text-2xl font-bold text-orange-600">{{ forecastData.summary.orders_with_shortages }}</div>
                            <div class="text-sm text-gray-500">Orders with Shortages</div>
                        </div>
                        <div class="bg-white rounded-lg shadow-sm p-4">
                            <div class="text-2xl font-bold text-green-600">R{{ formatCurrency(forecastData.summary.total_pod_value) }}</div>
                            <div class="text-sm text-gray-500">POD Value</div>
                        </div>
                        <div class="bg-white rounded-lg shadow-sm p-4">
                            <div class="text-2xl font-bold text-green-600">R{{ formatCurrency(forecastData.summary.total_pos_value) }}</div>
                            <div class="text-sm text-gray-500">POS Value</div>
                        </div>
                    </div>

                    <!-- Priority Alerts -->
                    <div v-if="ordersWithShortages.length > 0" class="bg-orange-50 border-l-4 border-orange-400 p-4">
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

                    <!-- Tabs -->
                    <div class="bg-white shadow-sm rounded-lg">
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex">
                                <button @click="activeTab = 'distributor'"
                                        :class="activeTab === 'distributor' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="py-2 px-4 border-b-2 font-medium text-sm">
                                    POD Orders ({{ distributorOrders.length }})
                                </button>
                                <button @click="activeTab = 'supplier'"
                                        :class="activeTab === 'supplier' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="py-2 px-4 border-b-2 font-medium text-sm">
                                    POS Orders ({{ supplierOrders.length }})
                                </button>
                                <button @click="activeTab = 'shortages'"
                                        :class="activeTab === 'shortages' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="py-2 px-4 border-b-2 font-medium text-sm">
                                    Shortages ({{ ordersWithShortages.length }})
                                </button>
                            </nav>
                        </div>

                        <!-- POD Orders Tab -->
                        <div v-if="activeTab === 'distributor'" class="p-6">
                            <div v-if="distributorOrders.length === 0" class="text-center py-8">
                                <p class="text-gray-500">No POD orders found for the selected period.</p>
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="order in distributorOrders" :key="'pod-' + order.id" 
                                     class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-blue-100 p-2 rounded-lg">
                                                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ order.po_number }}</div>
                                                <div class="text-sm text-gray-500">{{ order.distributor?.business_name }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-medium text-gray-900">R{{ formatCurrency(order.total_value_ex_vat) }}</div>
                                            <div class="text-sm text-gray-500">{{ formatDate(order.required_delivery_date) }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Status and Shortage Info -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span :class="getStatusBadgeClass(order.status)" 
                                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                {{ formatStatus(order.status) }}
                                            </span>
                                            <span v-if="order.has_shortages" class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-medium">
                                                ⚠️ Has Shortages
                                            </span>
                                        </div>
                                        <Link :href="route('purchase-orders.show', order.id)"
                                              class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View Details →
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- POS Orders Tab -->
                        <div v-if="activeTab === 'supplier'" class="p-6">
                            <div v-if="supplierOrders.length === 0" class="text-center py-8">
                                <p class="text-gray-500">No POS orders found for the selected period.</p>
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="order in supplierOrders" :key="'pos-' + order.id" 
                                     class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-purple-100 p-2 rounded-lg">
                                                <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ order.po_number }}</div>
                                                <div class="text-sm text-gray-500">{{ order.supplier?.business_name }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-medium text-gray-900">R{{ formatCurrency(order.total_value_ex_vat) }}</div>
                                            <div class="text-sm text-gray-500">{{ formatDate(order.required_delivery_date) }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Status -->
                                    <div class="flex items-center justify-between">
                                        <span :class="getStatusBadgeClass(order.status)" 
                                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            {{ formatStatus(order.status) }}
                                        </span>
                                        <Link :href="route('purchase-orders.show', order.id)"
                                              class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View Details →
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shortages Tab -->
                        <div v-if="activeTab === 'shortages'" class="p-6">
                            <div v-if="ordersWithShortages.length === 0" class="text-center py-8">
                                <p class="text-gray-500">No orders with shortages found. Great job!</p>
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="order in ordersWithShortages" :key="'shortage-' + order.id" 
                                     class="border-l-4 border-orange-400 bg-orange-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-orange-100 p-2 rounded-lg">
                                                <svg class="h-5 w-5 text-orange-600" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ order.po_number }}</div>
                                                <div class="text-sm text-gray-600">{{ order.distributor?.business_name }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-medium text-gray-900">R{{ formatCurrency(order.total_value_ex_vat) }}</div>
                                            <div class="text-sm text-orange-600 font-medium">Due: {{ formatDate(order.required_delivery_date) }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Shortage Details -->
                                    <div class="bg-white rounded-lg p-3 mb-3">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Fulfillment Analysis</h4>
                                        <div v-if="order.fulfillment_analysis" class="space-y-2 text-sm">
                                            <div v-for="item in order.fulfillment_analysis" :key="item.product_id" 
                                                 class="flex justify-between items-center">
                                                <span class="text-gray-700">{{ item.product_code }}</span>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-gray-600">{{ item.required_kg }}kg needed</span>
                                                    <span class="text-green-600">{{ item.committed_kg }}kg committed</span>
                                                    <span v-if="item.shortage_kg > 0" class="text-orange-600 font-medium">
                                                        {{ item.shortage_kg }}kg short
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-medium">
                                            Priority: Supply Shortage
                                        </span>
                                        <Link :href="route('purchase-orders.show', order.id)"
                                              class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View Details →
                                        </Link>
                                    </div>
                                </div>
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
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useApi } from '@/Composables/useApi'

const { api } = useApi()

// Reactive data
const forecastData = ref(null)
const loading = ref(true)
const error = ref(null)
const months = ref(6)
const activeTab = ref('distributor')

// Computed properties
const distributorOrders = computed(() => {
    return forecastData.value?.distributor_orders || []
})

const supplierOrders = computed(() => {
    return forecastData.value?.supplier_orders || []
})

const ordersWithShortages = computed(() => {
    return distributorOrders.value.filter(order => order.has_shortages)
})

// Methods
onMounted(() => {
    loadForecast()
})

const loadForecast = async () => {
    loading.value = true
    error.value = null

    try {
        const response = await api.getPipelineForecast(months.value)
        forecastData.value = response
        console.log('Loaded forecast data:', forecastData.value)
    } catch (err) {
        console.error('Error loading forecast:', err)
        error.value = err.response?.data?.message || 'Failed to load forecast data'
    } finally {
        loading.value = false
    }
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
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString('en-ZA', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}
</script>