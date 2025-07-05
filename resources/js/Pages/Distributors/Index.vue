<template>
    <Head title="Distributors" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Distributors
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Manage your citrus fruit distributors and customers
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <Link :href="route('distributors.create')"
                          class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Add Distributor
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Search and Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                            <div class="flex-1 max-w-lg">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input v-model="searchQuery"
                                           @input="filterDistributors"
                                           type="text" 
                                           placeholder="Search distributors..."
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <!-- Country Filter -->
                                <select v-model="selectedCountry" 
                                        @change="filterDistributors"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All Countries</option>
                                    <option value="Mauritius">Mauritius</option>
                                    <option value="Mozambique">Mozambique</option>
                                    <option value="Tanzania">Tanzania</option>
                                    <option value="South Africa">South Africa</option>
                                </select>
                                
                                <!-- View Toggle -->
                                <div class="flex border border-gray-300 rounded-lg">
                                    <button @click="viewMode = 'grid'"
                                            :class="viewMode === 'grid' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700'"
                                            class="px-3 py-2 text-sm font-medium rounded-l-lg border-r border-gray-300">
                                        Grid
                                    </button>
                                    <button @click="viewMode = 'list'"
                                            :class="viewMode === 'list' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700'"
                                            class="px-3 py-2 text-sm font-medium rounded-r-lg">
                                        List
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                    <p class="mt-2 text-gray-600">Loading distributors...</p>
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
                            <h3 class="text-sm font-medium text-red-800">Error loading distributors</h3>
                            <p class="mt-1 text-sm text-red-700">{{ error }}</p>
                            <button @click="loadDistributors" 
                                    class="mt-2 text-sm text-red-600 hover:text-red-500 underline">
                                Try again
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid View -->
                <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <DistributorCard 
                        v-for="distributor in filteredDistributors" 
                        :key="distributor.id" 
                        :distributor="distributor"
                        @view="viewDistributor"
                        @edit="editDistributor"
                        @delete="deleteDistributor"
                    />
                </div>

                <!-- List View -->
                <div v-else class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Business
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sales Contact
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    VAT Number
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="distributor in filteredDistributors" :key="distributor.id" 
                                class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-purple-500 flex items-center justify-center text-white font-bold text-sm">
                                                {{ getInitials(distributor.business_name) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ distributor.business_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ distributor.country }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ distributor.primary_sales_contact_name }}</div>
                                    <div class="text-sm text-gray-500">{{ distributor.primary_sales_contact_email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ distributor.vat_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button @click="viewDistributor(distributor.id)"
                                            class="text-indigo-600 hover:text-indigo-900">
                                        View
                                    </button>
                                    <button @click="editDistributor(distributor.id)"
                                            class="text-green-600 hover:text-green-900">
                                        Edit
                                    </button>
                                    <button @click="deleteDistributor(distributor.id)"
                                            class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="!loading && !error && filteredDistributors.length === 0" 
                     class="text-center py-12 bg-white rounded-lg shadow-sm">
                    <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No distributors found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ searchQuery || selectedCountry ? 'Try adjusting your search criteria.' : 'Get started by adding your first distributor.' }}
                    </p>
                    <div class="mt-6">
                        <Link :href="route('distributors.create')"
                              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Distributor
                        </Link>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div v-if="!loading && !error && filteredDistributors.length > 0" 
                     class="mt-6 bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ filteredDistributors.length }}</div>
                            <div class="text-sm text-gray-500">Total Distributors</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-purple-600">{{ uniqueCountries.length }}</div>
                            <div class="text-sm text-gray-500">Countries</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ distributors.length }}</div>
                            <div class="text-sm text-gray-500">Total in Database</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DistributorCard from '@/Components/Distributors/DistributorCard.vue'
import { useApi } from '@/Composables/useApi'

const { loading, error, api } = useApi()

// Reactive data
const distributors = ref([])
const searchQuery = ref('')
const selectedCountry = ref('')
const viewMode = ref('grid')

// Computed properties
const filteredDistributors = computed(() => {
    let filtered = distributors.value

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(distributor => 
            distributor.business_name.toLowerCase().includes(query) ||
            distributor.primary_sales_contact_name.toLowerCase().includes(query) ||
            distributor.vat_number.toLowerCase().includes(query)
        )
    }

    // Filter by country
    if (selectedCountry.value) {
        filtered = filtered.filter(distributor => 
            distributor.country === selectedCountry.value
        )
    }

    return filtered
})

const uniqueCountries = computed(() => {
    const countries = distributors.value.map(d => d.country)
    return [...new Set(countries)]
})

// Methods
const loadDistributors = async () => {
    try {
        const response = await api.getDistributors()
        
        // Handle different response structures
        if (response.data) {
            distributors.value = response.data
        } else if (Array.isArray(response)) {
            distributors.value = response
        } else {
            distributors.value = []
        }
        
        console.log('Loaded distributors:', distributors.value)
    } catch (err) {
        console.error('Error loading distributors:', err)
        distributors.value = []
    }
}

const filterDistributors = () => {
    // Filtering is handled by computed property
}

const viewDistributor = (id) => {
    router.visit(`/distributors/${id}`)
}

const editDistributor = (id) => {
    router.visit(`/distributors/${id}/edit`)
}

const deleteDistributor = async (id) => {
    if (confirm('Are you sure you want to delete this distributor?')) {
        try {
            await api.deleteDistributor(id)
            await loadDistributors() // Reload the list
        } catch (err) {
            console.error('Error deleting distributor:', err)
            alert('Failed to delete distributor')
        }
    }
}

const getInitials = (name) => {
    return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2)
}

// Load distributors on mount
onMounted(() => {
    loadDistributors()
})
</script>