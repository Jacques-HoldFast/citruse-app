<template>
    <Head :title="`Edit Purchase Order ${purchaseOrder?.po_number || ''}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Edit Purchase Order
                    </h2>
                    <p v-if="purchaseOrder" class="text-sm text-gray-600 mt-1">
                        Editing {{ purchaseOrder.po_number }} - {{ getCompanyName(purchaseOrder) }}
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <Link :href="route('purchase-orders.show', orderId)"
                          class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        View Details
                    </Link>
                    <Link :href="route('purchase-orders.index')"
                          class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        ← Back to Orders
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Loading purchase order for editing...</p>
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
                    <div v-if="purchaseOrder" class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800">
                                    <span class="font-medium">Editing:</span> {{ purchaseOrder.po_number }}
                                    <span class="text-blue-600 mx-2">•</span>
                                    <span class="font-medium">Status:</span> {{ formatStatus(purchaseOrder.status) }}
                                    <span class="text-blue-600 mx-2">•</span>
                                    <span class="font-medium">Created:</span> {{ formatDate(purchaseOrder.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Order Form -->
                    <div v-if="form" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <form @submit.prevent="handleSubmit" class="p-6 space-y-8">
                            
                            <!-- Basic Information Section -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-6">Purchase Order Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Order Type (Read-only in edit mode) -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Order Type
                                        </label>
                                        <div class="bg-gray-50 border border-gray-300 rounded-md p-4">
                                            <div class="flex items-center">
                                                <div class="text-2xl mr-3">
                                                    {{ form.type === 'distributor_order' ? '📥' : '📤' }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900">
                                                        {{ form.type === 'distributor_order' ? 'POD - From Distributor' : 'POS - To Supplier' }}
                                                    </div>
                                                    <div class="text-sm text-gray-600">
                                                        Order type cannot be changed after creation
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Company (Read-only in edit mode) -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ form.type === 'distributor_order' ? 'Distributor' : 'Supplier' }}
                                        </label>
                                        <div class="bg-gray-50 border border-gray-300 rounded-md p-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ getCompanyName(purchaseOrder) }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ getCompanyCountry(purchaseOrder) }}
                                            </div>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Company cannot be changed after creation
                                        </p>
                                    </div>

                                    <!-- Linked POD (Read-only if exists) -->
                                    <div v-if="form.type === 'supplier_order'" class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Linked POD
                                        </label>
                                        <div v-if="purchaseOrder.linked_po" class="bg-gray-50 border border-gray-300 rounded-md p-3">
                                            <Link :href="route('purchase-orders.show', purchaseOrder.linked_po.id)" 
                                                  class="text-blue-600 hover:text-blue-800 font-medium">
                                                {{ purchaseOrder.linked_po.po_number }}
                                            </Link>
                                            <div class="text-sm text-gray-500">
                                                Linked POD cannot be changed after creation
                                            </div>
                                        </div>
                                        <div v-else class="text-sm text-gray-500">
                                            No linked POD
                                        </div>
                                    </div>

                                    <!-- Required Delivery Date -->
                                    <div>
                                        <label for="required_delivery_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            Required Delivery Date
                                        </label>
                                        <input type="date" 
                                               id="required_delivery_date"
                                               v-model="form.required_delivery_date"
                                               :min="tomorrow"
                                               class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                        <div v-if="validationErrors.required_delivery_date" class="mt-1 text-sm text-red-600">
                                            {{ validationErrors.required_delivery_date[0] }}
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="md:col-span-2">
                                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes
                                        </label>
                                        <textarea id="notes"
                                                  v-model="form.notes"
                                                  rows="3"
                                                  placeholder="Additional notes or special instructions..."
                                                  class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Line Items Section -->
                            <div>
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                                    <button type="button" 
                                            @click="addLineItem"
                                            :disabled="!canEditItems"
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        + Add Item
                                    </button>
                                </div>

                                <!-- Edit Restrictions Notice -->
                                <div v-if="!canEditItems" class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-800">
                                                <strong>Limited Editing:</strong> Items cannot be modified for orders with status "{{ formatStatus(purchaseOrder?.status || '') }}". Only notes and delivery dates can be updated.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Items Table -->
                                <div v-if="form.items.length > 0" class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Product
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Quantity (kg)
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Delivery Date
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Unit Price
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Line Total
                                                </th>
                                                <th v-if="canEditItems" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(item, index) in form.items" :key="item.id || index">
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div v-if="canEditItems">
                                                        <select v-model="item.product_id" 
                                                                @change="updateItemPrice(index)"
                                                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                                                            <option value="">Select Product</option>
                                                            <option v-for="product in products" 
                                                                    :key="product.id" 
                                                                    :value="product.id">
                                                                {{ product.product_code }} - {{ product.description }}
                                                            </option>
                                                        </select>
                                                        <div v-if="validationErrors[`items.${index}.product_id`]" class="mt-1 text-xs text-red-600">
                                                            {{ validationErrors[`items.${index}.product_id`][0] }}
                                                        </div>
                                                    </div>
                                                    <div v-else>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ getProductName(item) }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ getProductDescription(item) }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <input v-if="canEditItems"
                                                           type="number" 
                                                           v-model.number="item.quantity_kg"
                                                           @input="calculateItemTotal(index)"
                                                           step="0.01" 
                                                           min="0" 
                                                           placeholder="0.00"
                                                           class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                                                    <div v-else class="text-sm text-gray-900">
                                                        {{ formatNumber(item.quantity_kg) }} kg
                                                    </div>
                                                    <div v-if="validationErrors[`items.${index}.quantity_kg`]" class="mt-1 text-xs text-red-600">
                                                        {{ validationErrors[`items.${index}.quantity_kg`][0] }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <input type="date" 
                                                           v-model="item.required_delivery_date"
                                                             @input="updateItemDeliveryDate(index, $event.target.value)"
                                                           :min="tomorrow"
                                                           class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                                                    <div v-if="validationErrors[`items.${index}.required_delivery_date`]" class="mt-1 text-xs text-red-600">
                                                        {{ validationErrors[`items.${index}.required_delivery_date`][0] }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">
                                                        R{{ formatCurrency(item.unit_price_ex_vat || 0) }}
                                                    </div>
                                                    <div v-if="!item.unit_price_ex_vat && item.product_id" class="text-xs text-red-600">
                                                        No price available
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        R{{ formatCurrency(item.total_value_ex_vat || 0) }}
                                                    </div>
                                                </td>
                                                <td v-if="canEditItems" class="px-4 py-4 whitespace-nowrap">
                                                    <button type="button" 
                                                            @click="removeLineItem(index)"
                                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                        Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td :colspan="canEditItems ? 4 : 3" class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                                                    Total Order Value (ex VAT):
                                                </td>
                                                <td class="px-4 py-3 text-sm font-bold text-gray-900">
                                                    R{{ formatCurrency(totalOrderValue) }}
                                                </td>
                                                <td v-if="canEditItems" class="px-4 py-3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                                <Link :href="route('purchase-orders.show', orderId)"
                                      class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                                    Cancel
                                </Link>
                                <button type="submit" 
                                        :disabled="submitting || !canSubmit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    {{ submitting ? 'Updating...' : 'Update Purchase Order' }}
                                </button>
                            </div>
                        </form>
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
const submitting = ref(false)
const successMessage = ref(null)
const errorMessage = ref(null)
const validationErrors = ref({})
const userData = ref(null)
const products = ref([])
const form = ref(null)

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

// Computed properties
const tomorrow = computed(() => {
    const date = new Date()
    date.setDate(date.getDate() + 1)
    return date.toISOString().split('T')[0]
})

const totalOrderValue = computed(() => {
    if (!form.value?.items) return 0
    return form.value.items.reduce((total, item) => {
        return total + (item.total_value_ex_vat || 0)
    }, 0)
})

const canEditItems = computed(() => {
    // Can only edit items if status is 'new' or 'rejected_by_supplier'
    return purchaseOrder.value?.status === 'new' || 
           purchaseOrder.value?.status === 'rejected_by_supplier'
})
const updateItemDeliveryDate = (index, newDate) => {
    console.log("GETTING " , newDate);
    if (form.value && form.value.items[index]) {
        form.value.items[index].required_delivery_date = newDate
        console.log(`Updated item ${index} delivery date to:`, newDate)
        console.log('Full item:', form.value.items[index])
    }
}

const canSubmit = computed(() => {
    if (!form.value) return false
    
    return form.value.items.length > 0 &&
           form.value.items.every(item => 
               item.product_id && 
               item.quantity_kg > 0 && 
               item.required_delivery_date &&
               item.unit_price_ex_vat > 0
           )
})

// Methods
onMounted(async () => {
    await loadUserData()
    await loadProducts()
    await loadPurchaseOrder()
})

const loadUserData = async () => {
    try {
        const response = await api.getUser()
        userData.value = response
    } catch (error) {
        console.error('Error loading user data:', error)
    }
}

const loadProducts = async () => {
    try {
        const response = await api.getProducts()
        products.value = response.data || response || []
    } catch (error) {
        console.error('Error loading products:', error)
    }
}

const loadPurchaseOrder = async () => {
    loading.value = true
    loadError.value = null

    try {
        const response = await api.getPurchaseOrder(props.orderId)
        purchaseOrder.value = response.purchase_order || response
        
        // Initialize form with purchase order data
        form.value = {
            type: purchaseOrder.value.type,
            supplier_id: purchaseOrder.value.supplier_id,
            distributor_id: purchaseOrder.value.distributor_id,
            linked_po_id: purchaseOrder.value.linked_po_id,
            // Format the date to YYYY-MM-DD for HTML date input
            required_delivery_date: purchaseOrder.value.required_delivery_date 
                ? new Date(purchaseOrder.value.required_delivery_date).toISOString().split('T')[0] 
                : '',
            notes: purchaseOrder.value.notes,
            items: purchaseOrder.value.items.map(item => ({
                id: item.id,
                product_id: item.product_id,
                quantity_kg: item.quantity_kg,
                // Also format item delivery dates
                required_delivery_date: item.required_delivery_date 
                    ? new Date(item.required_delivery_date).toISOString().split('T')[0] 
                    : '',
                unit_price_ex_vat: item.unit_price_ex_vat,
                total_value_ex_vat: item.total_value_ex_vat
            }))
        }
        
        console.log('Loaded purchase order for edit:', purchaseOrder.value)
        console.log('Initialized form:', form.value)
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

const getCompanyCountry = (order) => {
    if (order.supplier) return order.supplier.country
    if (order.distributor) return order.distributor.country
    return ''
}

const getProductName = (item) => {
    const product = products.value.find(p => p.id === item.product_id)
    return product?.product_code || 'Unknown Product'
}

const getProductDescription = (item) => {
    const product = products.value.find(p => p.id === item.product_id)
    return product?.description || ''
}

const addLineItem = () => {
    if (!canEditItems.value) return
    
    form.value.items.push({
        product_id: null,
        quantity_kg: 0,
        required_delivery_date: '',
        unit_price_ex_vat: 0,
        total_value_ex_vat: 0
    })
}

const removeLineItem = (index) => {
    form.value.items.splice(index, 1)
}

const updateItemPrice = async (index) => {
    const item = form.value.items[index]
    if (!item.product_id) return

    try {
        const response = await api.getProduct(item.product_id)
        const currentPrice = response.current_price
        
        if (currentPrice) {
            item.unit_price_ex_vat = currentPrice
            calculateItemTotal(index)
        } else {
            item.unit_price_ex_vat = 0
            console.warn('No current pricing found for product')
        }
    } catch (error) {
        console.error('Error fetching product price:', error)
        item.unit_price_ex_vat = 0
    }
}

const calculateItemTotal = (index) => {
    const item = form.value.items[index]
    item.total_value_ex_vat = (item.quantity_kg || 0) * (item.unit_price_ex_vat || 0)
}

const handleSubmit = async () => {
    submitting.value = true
    successMessage.value = null
    errorMessage.value = null
    validationErrors.value = {}

    try {
        const formData = {
            ...form.value,
            total_value_ex_vat: totalOrderValue.value,
            // Ensure all dates are properly formatted
            items: form.value.items.map(item => ({
                ...item,
                // Make sure delivery dates are in the right format
                required_delivery_date: item.required_delivery_date || null
            }))
        }

        console.log('Submitting form data:', formData) // Debug log

        await api.updatePurchaseOrder(props.orderId, formData)
        
        successMessage.value = 'Purchase order updated successfully!'
        
        // Redirect to show page after a short delay
        setTimeout(() => {
            router.visit(`/purchase-orders/${props.orderId}`)
        }, 2000)
        
    } catch (error) {
        console.error('Error updating purchase order:', error)
        
        if (error.response?.status === 422) {
            validationErrors.value = error.response.data.errors || {}
            errorMessage.value = 'Please correct the validation errors below.'
        } else if (error.response?.status === 404) {
            errorMessage.value = 'Purchase order not found. It may have been deleted.'
        } else {
            errorMessage.value = error.response?.data?.message || 'Failed to update purchase order. Please try again.'
        }
    } finally {
        submitting.value = false
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
</script>