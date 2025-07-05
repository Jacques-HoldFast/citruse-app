<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Business Information -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Business Information</h3>
                <p class="text-sm text-gray-600">Basic details about the supplier business</p>
            </div>
            <div class="px-6 py-4 space-y-4">
                <!-- Business Name -->
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700">
                        Business Name *
                    </label>
                    <input type="text" 
                           id="business_name"
                           v-model="form.business_name"
                           :class="inputClass('business_name')"
                           class="mt-1 block w-full rounded-md shadow-sm"
                           placeholder="e.g., Citrusdal Farming Co."
                           required>
                    <p v-if="errors.business_name" class="mt-1 text-sm text-red-600">{{ errors.business_name }}</p>
                </div>

                <!-- Country -->
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700">
                        Country *
                    </label>
                    <select id="country"
                            v-model="form.country"
                            :class="inputClass('country')"
                            class="mt-1 block w-full rounded-md shadow-sm"
                            required>
                        <option value="">Select Country</option>
                        <option value="South Africa">South Africa</option>
                        <option value="Namibia">Namibia</option>
                        <option value="Botswana">Botswana</option>
                        <option value="Swaziland">Swaziland</option>
                        <option value="Lesotho">Lesotho</option>
                    </select>
                    <p v-if="errors.country" class="mt-1 text-sm text-red-600">{{ errors.country }}</p>
                </div>

                <!-- VAT Number -->
                <div>
                    <label for="vat_number" class="block text-sm font-medium text-gray-700">
                        VAT Number *
                    </label>
                    <input type="text" 
                           id="vat_number"
                           v-model="form.vat_number"
                           :class="inputClass('vat_number')"
                           class="mt-1 block w-full rounded-md shadow-sm"
                           placeholder="e.g., 4123456789"
                           required>
                    <p v-if="errors.vat_number" class="mt-1 text-sm text-red-600">{{ errors.vat_number }}</p>
                </div>

                <!-- Registered Address -->
                <div>
                    <label for="registered_address" class="block text-sm font-medium text-gray-700">
                        Registered Address *
                    </label>
                    <textarea id="registered_address"
                              v-model="form.registered_address"
                              :class="inputClass('registered_address')"
                              class="mt-1 block w-full rounded-md shadow-sm"
                              rows="3"
                              placeholder="Full business address"
                              required></textarea>
                    <p v-if="errors.registered_address" class="mt-1 text-sm text-red-600">{{ errors.registered_address }}</p>
                </div>
            </div>
        </div>

        <!-- Sales Contact -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Primary Sales Contact</h3>
                <p class="text-sm text-gray-600">Main contact for sales inquiries and orders</p>
            </div>
            <div class="px-6 py-4 space-y-4">
                <!-- Sales Contact Name -->
                <div>
                    <label for="primary_sales_contact_name" class="block text-sm font-medium text-gray-700">
                        Contact Name *
                    </label>
                    <input type="text" 
                           id="primary_sales_contact_name"
                           v-model="form.primary_sales_contact_name"
                           :class="inputClass('primary_sales_contact_name')"
                           class="mt-1 block w-full rounded-md shadow-sm"
                           placeholder="e.g., John Smith"
                           required>
                    <p v-if="errors.primary_sales_contact_name" class="mt-1 text-sm text-red-600">{{ errors.primary_sales_contact_name }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Sales Contact Email -->
                    <div>
                        <label for="primary_sales_contact_email" class="block text-sm font-medium text-gray-700">
                            Email Address *
                        </label>
                        <input type="email" 
                               id="primary_sales_contact_email"
                               v-model="form.primary_sales_contact_email"
                               :class="inputClass('primary_sales_contact_email')"
                               class="mt-1 block w-full rounded-md shadow-sm"
                               placeholder="sales@example.com"
                               required>
                        <p v-if="errors.primary_sales_contact_email" class="mt-1 text-sm text-red-600">{{ errors.primary_sales_contact_email }}</p>
                    </div>

                    <!-- Sales Contact Phone -->
                    <div>
                        <label for="primary_sales_contact_telephone" class="block text-sm font-medium text-gray-700">
                            Telephone *
                        </label>
                        <input type="tel" 
                               id="primary_sales_contact_telephone"
                               v-model="form.primary_sales_contact_telephone"
                               :class="inputClass('primary_sales_contact_telephone')"
                               class="mt-1 block w-full rounded-md shadow-sm"
                               placeholder="+27 123 456 7890"
                               required>
                        <p v-if="errors.primary_sales_contact_telephone" class="mt-1 text-sm text-red-600">{{ errors.primary_sales_contact_telephone }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logistics Contact -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Primary Logistics Contact</h3>
                <p class="text-sm text-gray-600">Main contact for shipping and logistics coordination</p>
            </div>
            <div class="px-6 py-4 space-y-4">
                <!-- Copy Sales Contact Button -->
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">Fill in logistics contact details:</p>
                    <button type="button" 
                            @click="copySalesContact"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Copy from Sales Contact
                    </button>
                </div>

                <!-- Logistics Contact Name -->
                <div>
                    <label for="primary_logistics_contact_name" class="block text-sm font-medium text-gray-700">
                        Contact Name *
                    </label>
                    <input type="text" 
                           id="primary_logistics_contact_name"
                           v-model="form.primary_logistics_contact_name"
                           :class="inputClass('primary_logistics_contact_name')"
                           class="mt-1 block w-full rounded-md shadow-sm"
                           placeholder="e.g., Jane Doe"
                           required>
                    <p v-if="errors.primary_logistics_contact_name" class="mt-1 text-sm text-red-600">{{ errors.primary_logistics_contact_name }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Logistics Contact Email -->
                    <div>
                        <label for="primary_logistics_contact_email" class="block text-sm font-medium text-gray-700">
                            Email Address *
                        </label>
                        <input type="email" 
                               id="primary_logistics_contact_email"
                               v-model="form.primary_logistics_contact_email"
                               :class="inputClass('primary_logistics_contact_email')"
                               class="mt-1 block w-full rounded-md shadow-sm"
                               placeholder="logistics@example.com"
                               required>
                        <p v-if="errors.primary_logistics_contact_email" class="mt-1 text-sm text-red-600">{{ errors.primary_logistics_contact_email }}</p>
                    </div>

                    <!-- Logistics Contact Phone -->
                    <div>
                        <label for="primary_logistics_contact_telephone" class="block text-sm font-medium text-gray-700">
                            Telephone *
                        </label>
                        <input type="tel" 
                               id="primary_logistics_contact_telephone"
                               v-model="form.primary_logistics_contact_telephone"
                               :class="inputClass('primary_logistics_contact_telephone')"
                               class="mt-1 block w-full rounded-md shadow-sm"
                               placeholder="+27 123 456 7890"
                               required>
                        <p v-if="errors.primary_logistics_contact_telephone" class="mt-1 text-sm text-red-600">{{ errors.primary_logistics_contact_telephone }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4 pt-6">
            <button type="button" 
                    @click="$emit('cancel')"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </button>
            <button type="submit" 
                    :disabled="submitting"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="submitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ isEdit ? 'Updating...' : 'Creating...' }}
                </span>
                <span v-else>
                    {{ isEdit ? 'Update Supplier' : 'Create Supplier' }}
                </span>
            </button>
        </div>
    </form>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
    supplier: {
        type: Object,
        default: () => ({})
    },
    errors: {
        type: Object,
        default: () => ({})
    },
    submitting: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['submit', 'cancel'])

const isEdit = computed(() => !!props.supplier.id)

// Form data
const form = ref({
    business_name: '',
    country: '',
    vat_number: '',
    registered_address: '',
    primary_sales_contact_name: '',
    primary_sales_contact_email: '',
    primary_sales_contact_telephone: '',
    primary_logistics_contact_name: '',
    primary_logistics_contact_email: '',
    primary_logistics_contact_telephone: '',
})

// Initialize form with supplier data
const initializeForm = () => {
    if (props.supplier) {
        form.value = {
            business_name: props.supplier.business_name || '',
            country: props.supplier.country || '',
            vat_number: props.supplier.vat_number || '',
            registered_address: props.supplier.registered_address || '',
            primary_sales_contact_name: props.supplier.primary_sales_contact_name || '',
            primary_sales_contact_email: props.supplier.primary_sales_contact_email || '',
            primary_sales_contact_telephone: props.supplier.primary_sales_contact_telephone || '',
            primary_logistics_contact_name: props.supplier.primary_logistics_contact_name || '',
            primary_logistics_contact_email: props.supplier.primary_logistics_contact_email || '',
            primary_logistics_contact_telephone: props.supplier.primary_logistics_contact_telephone || '',
        }
    }
}

// Watch for supplier changes (when data loads)
watch(() => props.supplier, () => {
    initializeForm()
}, { immediate: true, deep: true })

// Helper methods
const inputClass = (field) => {
    const baseClass = 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'
    const errorClass = 'border-red-300 focus:border-red-500 focus:ring-red-500'
    return props.errors[field] ? errorClass : baseClass
}

const copySalesContact = () => {
    form.value.primary_logistics_contact_name = form.value.primary_sales_contact_name
    form.value.primary_logistics_contact_email = form.value.primary_sales_contact_email
    form.value.primary_logistics_contact_telephone = form.value.primary_sales_contact_telephone
}

const handleSubmit = () => {
    emit('submit', form.value)
}
</script>