<template>
    <Head title="Create Purchase Order" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Create Purchase Order
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Create a new purchase order from distributor (POD) or to supplier (POS)
                    </p>
                </div>
                <div>
                    <Link :href="route('purchase-orders.index')"
                          class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        ← Back to Purchase Orders
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                
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

                <!-- Purchase Order Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="handleSubmit" class="p-6 space-y-8">
                        
                        <!-- Basic Information Section -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-6">Purchase Order Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Order Type -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Order Type *
                                    </label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <label class="relative cursor-pointer">
                                            <input type="radio" 
                                                   value="distributor_order" 
                                                   v-model="form.type"
                                                   class="sr-only">
                                            <div :class="form.type === 'distributor_order' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-300'"
                                                 class="border-2 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                                <div class="flex items-center">
                                                    <div class="text-2xl mr-3">📥</div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">POD - From Distributor</div>
                                                        <div class="text-sm text-gray-600">Order received from a distributor</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                        
                                        <label class="relative cursor-pointer">
                                            <input type="radio" 
                                                   value="supplier_order" 
                                                   v-model="form.type"
                                                   class="sr-only">
                                            <div :class="form.type === 'supplier_order' ? 'border-purple-500 ring-2 ring-purple-500' : 'border-gray-300'"
                                                 class="border-2 rounded-lg p-4 hover:border-purple-300 transition-colors">
                                                <div class="flex items-center">
                                                    <div class="text-2xl mr-3">📤</div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">POS - To Supplier</div>
                                                        <div class="text-sm text-gray-600">Order sent to a supplier</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div v-if="validationErrors.type" class="mt-1 text-sm text-red-600">
                                        {{ validationErrors.type[0] }}
                                    </div>
                                </div>

                                <!-- Distributor Selection (for POD) -->
                                <div v-if="form.type === 'distributor_order'" class="md:col-span-2">
                                    <label for="distributor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Distributor *
                                    </label>
                                    <select id="distributor_id" 
                                            v-model="form.distributor_id"
                                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                        <option value="">Select Distributor</option>
                                        <option v-for="distributor in distributors" 
                                                :key="distributor.id" 
                                                :value="distributor.id">
                                            {{ distributor.business_name }} ({{ distributor.country }})
                                        </option>
                                    </select>
                                    <div v-if="validationErrors.distributor_id" class="mt-1 text-sm text-red-600">
                                        {{ validationErrors.distributor_id[0] }}
                                    </div>
                                </div>

                                <!-- Supplier Selection (for POS) -->
                                <div v-if="form.type === 'supplier_order'" class="md:col-span-2">
                                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Supplier *
                                    </label>
                                    <select id="supplier_id" 
                                            v-model="form.supplier_id"
                                            class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm">
                                        <option value="">Select Supplier</option>
                                        <option v-for="supplier in suppliers" 
                                                :key="supplier.id" 
                                                :value="supplier.id">
                                            {{ supplier.business_name }} ({{ supplier.country }})
                                        </option>
                                    </select>
                                    <div v-if="validationErrors.supplier_id" class="mt-1 text-sm text-red-600">
                                        {{ validationErrors.supplier_id[0] }}
                                    </div>
                                </div>

                                <!-- Linked POD (for POS orders) -->
                                <div v-if="form.type === 'supplier_order'" class="md:col-span-2">
                                    <label for="linked_po_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Link to POD (Optional)
                                    </label>
                                    <select id="linked_po_id" 
                                            v-model="form.linked_po_id"
                                            class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm">
                                        <option value="">Select POD to link (Optional)</option>
                                        <option v-for="pod in availablePODs" 
                                                :key="pod.id" 
                                                :value="pod.id">
                                            {{ pod.po_number }} - {{ pod.distributor?.business_name }}
                                        </option>
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Link this POS to an existing POD to track fulfillment
                                    </p>
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
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    + Add Item
                                </button>
                            </div>

                            <!-- No Items State -->
                            <div v-if="form.items.length === 0" 
                                 class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <div class="text-gray-400 text-4xl mb-4">📦</div>
                                <h3 class="text-sm font-medium text-gray-900 mb-2">No items added yet</h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    Add products to this purchase order
                                </p>
                                <button type="button" 
                                        @click="addLineItem"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Add First Item
                                </button>
                            </div>

                            <!-- Items Table -->
                            <div v-else class="overflow-x-auto">
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
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(item, index) in form.items" :key="index">
                                            <td class="px-4 py-4 whitespace-nowrap">
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
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <input type="number" 
                                                       v-model.number="item.quantity_kg"
                                                       @input="calculateItemTotal(index)"
                                                       step="0.01" 
                                                       min="0" 
                                                       placeholder="0.00"
                                                       class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                                                <div v-if="validationErrors[`items.${index}.quantity_kg`]" class="mt-1 text-xs text-red-600">
                                                    {{ validationErrors[`items.${index}.quantity_kg`][0] }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <input type="date" 
                                                       v-model="item.required_delivery_date"
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
                                            <td class="px-4 py-4 whitespace-nowrap">
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
                                            <td colspan="4" class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                                                Total Order Value (ex VAT):
                                            </td>
                                            <td class="px-4 py-3 text-sm font-bold text-gray-900">
                                                R{{ formatCurrency(totalOrderValue) }}
                                            </td>
                                            <td class="px-4 py-3"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <Link :href="route('purchase-orders.index')"
                                  class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                                Cancel
                            </Link>
                            <button type="submit" 
                                    :disabled="submitting || !canSubmit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ submitting ? 'Creating...' : 'Create Purchase Order' }}
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

const { api } = useApi()

// Reactive data
const submitting = ref(false)
const successMessage = ref(null)
const errorMessage = ref(null)
const validationErrors = ref({})

const suppliers = ref([])
const distributors = ref([])
const products = ref([])
const availablePODs = ref([])

const form = ref({
    type: '',
    supplier_id: null,
    distributor_id: null,
    linked_po_id: null,
    required_delivery_date: '',
    notes: '',
    items: []
})

// Computed properties
const tomorrow = computed(() => {
    const date = new Date()
    date.setDate(date.getDate() + 1)
    return date.toISOString().split('T')[0]
})

const totalOrderValue = computed(() => {
    return form.value.items.reduce((total, item) => {
        return total + (item.total_value_ex_vat || 0)
    }, 0)
})

const canSubmit = computed(() => {
    return form.value.type && 
           form.value.items.length > 0 &&
           form.value.items.every(item => 
               item.product_id && 
               item.quantity_kg > 0 && 
               item.required_delivery_date &&
               item.unit_price_ex_vat > 0
           ) &&
           ((form.value.type === 'distributor_order' && form.value.distributor_id) ||
            (form.value.type === 'supplier_order' && form.value.supplier_id))
})

// Methods
onMounted(async () => {
    await Promise.all([
        loadSuppliers(),
        loadDistributors(), 
        loadProducts(),
        loadAvailablePODs()
    ])
})

const loadSuppliers = async () => {
    try {
        const response = await api.getSuppliers()
        suppliers.value = response.data || response || []
    } catch (error) {
        console.error('Error loading suppliers:', error)
    }
}

const loadDistributors = async () => {
    try {
        const response = await api.getDistributors()
        distributors.value = response.data || response || []
    } catch (error) {
        console.error('Error loading distributors:', error)
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

const loadAvailablePODs = async () => {
    try {
        const response = await api.getPurchaseOrders({ type: 'distributor_order', status: 'new' })
        availablePODs.value = response.data || response || []
    } catch (error) {
        console.error('Error loading available PODs:', error)
    }
}

const addLineItem = () => {
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
            total_value_ex_vat: totalOrderValue.value
        }

        const response = await api.createPurchaseOrder(formData)
        
        successMessage.value = 'Purchase order created successfully!'
        
        // Redirect to the created order after a short delay
        setTimeout(() => {
            router.visit(`/purchase-orders/${response.purchase_order?.id || response.id}`)
        }, 2000)
        
    } catch (error) {
        console.error('Error creating purchase order:', error)
        
        if (error.response?.status === 422) {
            validationErrors.value = error.response.data.errors || {}
            errorMessage.value = 'Please correct the validation errors below.'
        } else {
            errorMessage.value = error.response?.data?.message || 'Failed to create purchase order. Please try again.'
        }
    } finally {
        submitting.value = false
    }
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-ZA', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value || 0)
}
</script>