<template>
    <Head :title="`${distributor?.business_name || 'Distributor'} Details`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Distributor Details
                    </h2>
                    <p v-if="distributor" class="text-sm text-gray-600 mt-1">
                        {{ distributor.business_name }}
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <Link v-if="canEdit" 
                          :href="route('distributors.edit', distributorId)"
                          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Edit Distributor
                    </Link>
                    <Link :href="route('distributors.index')"
                          class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        ←  Back to Distributors
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Loading distributor details...</p>
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

                <!-- Distributor Details -->
                <div v-else-if="distributor" class="space-y-6">
                    
                    <!-- Main Info Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h1 class="text-2xl font-bold text-gray-900">{{ distributor.business_name }}</h1>
                                    <div class="mt-2 space-y-1">
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Sales Contact:</span> {{ distributor.primary_sales_contact_name }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Email:</span> {{ distributor.primary_sales_contact_email }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Phone:</span> {{ distributor.primary_sales_contact_telephone }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">
                                        Added {{ formatDate(distributor.created_at) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Address Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Registered Address</h4>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p class="whitespace-pre-line">{{ distributor.registered_address }}</p>
                                        <p class="font-medium">{{ distributor.country }}</p>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Contact Information</h4>
                                    <div class="text-sm text-gray-600 space-y-2">
                                        <div>
                                            <p class="font-medium text-gray-700">Sales Contact:</p>
                                            <p>{{ distributor.primary_sales_contact_name }}</p>
                                            <p>{{ distributor.primary_sales_contact_email }}</p>
                                            <p>{{ distributor.primary_sales_contact_telephone }}</p>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-700">Logistics Contact:</p>
                                            <p>{{ distributor.primary_logistics_contact_name }}</p>
                                            <p>{{ distributor.primary_logistics_contact_email }}</p>
                                            <p>{{ distributor.primary_logistics_contact_telephone }}</p>
                                        </div>
                                        <div>
                                            <p><span class="font-medium">VAT Number:</span> 
                                                {{ distributor.vat_number || 'Not provided' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Orders -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Purchase Orders</h3>
                        </div>
                        <div class="p-6">
                            <div v-if="loadingOrders" class="text-center py-8">
                                <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                                <p class="mt-2 text-sm text-gray-600">Loading purchase orders...</p>
                            </div>
                            <div v-else-if="purchaseOrders.length === 0" class="text-center py-8">
                                <p class="text-gray-500">No purchase orders found for this distributor.</p>
                            </div>
                            <div v-else class="space-y-3">
                                <div v-for="order in purchaseOrders" :key="order.id" 
                                     class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-blue-100 p-2 rounded-lg">
                                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ order.po_number }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ formatStatus(order.status) }} " {{ formatDate(order.created_at) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="text-right">
                                            <div class="font-medium text-gray-900">R{{ formatCurrency(order.total_value_ex_vat) }}</div>
                                            <div class="text-sm text-gray-500">Total Value</div>
                                        </div>
                                        <Link :href="route('purchase-orders.show', order.id)"
                                              class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View �
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
import { Head, Link, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useApi } from '@/Composables/useApi'

const props = defineProps({
    distributorId: {
        type: [String, Number],
        required: true
    }
})

const { api } = useApi()
const page = usePage()

// Reactive data
const distributor = ref(null)
const purchaseOrders = ref([])
const loading = ref(true)
const loadingOrders = ref(false)
const loadError = ref(null)
const userData = ref(null)

// User permissions
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
    return user.value.role === 'system_administrator' || 
           user.value.role === 'purchasing_manager'
})

// Methods
onMounted(() => {
    loadUserData()
    loadDistributor()
})

const loadUserData = async () => {
    try {
        const response = await api.getUser()
        userData.value = response
    } catch (error) {
        console.error('Error loading user data:', error)
    }
}

const loadDistributor = async () => {
    loading.value = true
    loadError.value = null

    try {
        const response = await api.getDistributor(props.distributorId)
        distributor.value = response.distributor || response
        await loadPurchaseOrders()
    } catch (error) {
        console.error('Error loading distributor:', error)
        loadError.value = error.response?.data?.message || 'Failed to load distributor'
    } finally {
        loading.value = false
    }
}

const loadPurchaseOrders = async () => {
    if (!distributor.value) return
    
    loadingOrders.value = true
    try {
        const response = await api.getPurchaseOrders({ distributor_id: props.distributorId })
        purchaseOrders.value = response.data || response || []
    } catch (error) {
        console.error('Error loading purchase orders:', error)
        purchaseOrders.value = []
    } finally {
        loadingOrders.value = false
    }
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
        month: 'long',
        day: 'numeric'
    })
}
</script>