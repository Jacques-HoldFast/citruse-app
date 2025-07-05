<template>
    <Head :title="supplier?.business_name || 'Supplier Details'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Supplier Details
                    </h2>
                    <p v-if="supplier" class="text-sm text-gray-600 mt-1">
                        Complete information for {{ supplier.business_name }}
                    </p>
                    <p v-else class="text-sm text-gray-600 mt-1">
                        Loading supplier information...
                    </p>
                </div>
                <div v-if="supplier" class="flex items-center space-x-4">
                    <Link :href="route('suppliers.edit', supplierId)"
                          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Edit Supplier
                    </Link>
                    <Link :href="route('suppliers.index')"
                          class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        ← Back to Suppliers
                    </Link>
                </div>
                <div v-else class="flex items-center space-x-4">
                    <Link :href="route('suppliers.index')"
                          class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        ← Back to Suppliers
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Loading supplier information...</p>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-700">{{ error }}</p>
                </div>

                <!-- Supplier Details -->
                <div v-else-if="supplier" class="space-y-6">
                    
                    <!-- Business Information -->
                    <div class="bg-white shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Business Information</h3>
                        </div>
                        <div class="px-6 py-4">
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Business Name</dt>
                                    <dd class="text-sm text-gray-900">{{ supplier.business_name || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Country</dt>
                                    <dd class="text-sm text-gray-900">{{ supplier.country || 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">VAT Number</dt>
                                    <dd class="text-sm text-gray-900">{{ supplier.vat_number || 'N/A' }}</dd>
                                </div>
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Registered Address</dt>
                                    <dd class="text-sm text-gray-900">{{ supplier.registered_address || 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Sales Contact -->
                        <div class="bg-white shadow-sm rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Sales Contact</h3>
                            </div>
                            <div class="px-6 py-4">
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                                        <dd class="text-sm text-gray-900">{{ supplier.primary_sales_contact_name || 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="text-sm text-gray-900">
                                            <a v-if="supplier.primary_sales_contact_email" 
                                               :href="`mailto:${supplier.primary_sales_contact_email}`" 
                                               class="text-blue-600 hover:text-blue-800">
                                                {{ supplier.primary_sales_contact_email }}
                                            </a>
                                            <span v-else>N/A</span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Telephone</dt>
                                        <dd class="text-sm text-gray-900">
                                            <a v-if="supplier.primary_sales_contact_telephone" 
                                               :href="`tel:${supplier.primary_sales_contact_telephone}`" 
                                               class="text-blue-600 hover:text-blue-800">
                                                {{ supplier.primary_sales_contact_telephone }}
                                            </a>
                                            <span v-else>N/A</span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Logistics Contact -->
                        <div class="bg-white shadow-sm rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Logistics Contact</h3>
                            </div>
                            <div class="px-6 py-4">
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                                        <dd class="text-sm text-gray-900">{{ supplier.primary_logistics_contact_name || 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="text-sm text-gray-900">
                                            <a v-if="supplier.primary_logistics_contact_email" 
                                               :href="`mailto:${supplier.primary_logistics_contact_email}`" 
                                               class="text-blue-600 hover:text-blue-800">
                                                {{ supplier.primary_logistics_contact_email }}
                                            </a>
                                            <span v-else>N/A</span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Telephone</dt>
                                        <dd class="text-sm text-gray-900">
                                            <a v-if="supplier.primary_logistics_contact_telephone" 
                                               :href="`tel:${supplier.primary_logistics_contact_telephone}`" 
                                               class="text-blue-600 hover:text-blue-800">
                                                {{ supplier.primary_logistics_contact_telephone }}
                                            </a>
                                            <span v-else>N/A</span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Information -->
                    <div class="bg-gray-50 shadow-sm rounded-lg">
                        <div class="px-6 py-4">
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-500">Created</dt>
                                    <dd class="text-gray-900">{{ formatDate(supplier.created_at) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-gray-900">{{ formatDate(supplier.updated_at) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- No Supplier Found -->
                <div v-else-if="!loading && !error" class="text-center py-12 bg-white rounded-lg shadow-sm">
                    <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No supplier data</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Unable to load supplier information.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useApi } from '@/Composables/useApi'

const props = defineProps({
    supplierId: {
        type: [String, Number],
        required: true
    }
})

const { loading, error, api } = useApi()
const supplier = ref(null)

const loadSupplier = async () => {
    try {
        const response = await api.getSupplier(props.supplierId)
        supplier.value = response.supplier || response
    } catch (err) {
        console.error('Error loading supplier:', err)
    }
}

const formatDate = (dateString) => {
    if (!dateString) return 'Unknown'
    return new Date(dateString).toLocaleDateString('en-ZA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

onMounted(() => {
    loadSupplier()
})
</script>