<template>
    <Head title="Product Catalog" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Product Catalog
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Citrus fruits from South African producers
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Year Selector -->
                    <select v-model="selectedYear" 
                            @change="loadProducts"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option v-for="year in availableYears" :key="year" :value="year">
                            {{ year }} Pricing
                        </option>
                    </select>
                    
                    <!-- View Toggle -->
                    <div class="flex border border-gray-300 rounded-lg">
                        <button @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
                                class="px-3 py-2 text-sm font-medium rounded-l-lg border-r border-gray-300">
                            Grid
                        </button>
                        <button @click="viewMode = 'list'"
                                :class="viewMode === 'list' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
                                class="px-3 py-2 text-sm font-medium rounded-r-lg">
                            List
                        </button>
                    </div>
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
                                           @input="filterProducts"
                                           type="text" 
                                           placeholder="Search products..."
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <!-- Category Filter -->
                                <select v-model="selectedCategory" 
                                        @change="filterProducts"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All Categories</option>
                                    <option value="FR">Citrusdal Products</option>
                                    <option value="GT">Grahamstown Products</option>
                                </select>
                                
                                <!-- Grade Filter -->
                                <select v-model="selectedGrade" 
                                        @change="filterProducts"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All Grades</option>
                                    <option value="Grade A">Grade A</option>
                                    <option value="Grade B">Grade B</option>
                                    <option value="Grade 1">Grade 1</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Loading products...</p>
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
                            <h3 class="text-sm font-medium text-red-800">Error loading products</h3>
                            <p class="mt-1 text-sm text-red-700">{{ error }}</p>
                            <button @click="loadProducts" 
                                    class="mt-2 text-sm text-red-600 hover:text-red-500 underline">
                                Try again
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Grid View -->
                <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <ProductCard 
                        v-for="product in filteredProducts" 
                        :key="product.id" 
                        :product="product"
                        :selected-year="selectedYear"
                    />
                </div>

                <!-- Product List View -->
                <div v-else class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Product
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Code
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ selectedYear }} Price
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="product in filteredProducts" :key="product.id" 
                                class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div :class="getProductIconClass(product.product_code)" 
                                                 class="h-10 w-10 rounded-lg flex items-center justify-center text-white">
                                                {{ getProductIcon(product.product_code) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ product.description }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="getProductColorClass(product.product_code)">
                                        {{ product.product_code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ getProductPrice(product) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ getProductCategory(product.product_code) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Available
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="!loading && !error && filteredProducts.length === 0" 
                     class="text-center py-12 bg-white rounded-lg shadow-sm">
                    <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-4 4-4-4m-6 0l4 4 4-4" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ searchQuery || selectedCategory || selectedGrade ? 'Try adjusting your search criteria.' : 'No products available.' }}
                    </p>
                    <button v-if="searchQuery || selectedCategory || selectedGrade" 
                            @click="clearFilters"
                            class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                        Clear filters
                    </button>
                </div>

                <!-- Summary Stats -->
                <div v-if="!loading && !error && filteredProducts.length > 0" 
                     class="mt-6 bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ filteredProducts.length }}</div>
                            <div class="text-sm text-gray-500">Products</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-orange-600">{{ citrusdalCount }}</div>
                            <div class="text-sm text-gray-500">Citrusdal</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ grahamstownCount }}</div>
                            <div class="text-sm text-gray-500">Grahamstown</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-yellow-600">{{ premiumCount }}</div>
                            <div class="text-sm text-gray-500">Premium Grade</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'
import { useApi } from '@/Composables/useApi'

const { loading, error, api } = useApi()

// Reactive data
const products = ref([])
const searchQuery = ref('')
const selectedCategory = ref('')
const selectedGrade = ref('')
const selectedYear = ref(new Date().getFullYear())
const viewMode = ref('grid')
const availableYears = ref([2023, 2024, 2025])

// Computed properties
const filteredProducts = computed(() => {
    let filtered = products.value

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(product => 
            product.description.toLowerCase().includes(query) ||
            product.product_code.toLowerCase().includes(query)
        )
    }

    // Filter by category
    if (selectedCategory.value) {
        filtered = filtered.filter(product => 
            product.product_code.startsWith(selectedCategory.value)
        )
    }

    // Filter by grade
    if (selectedGrade.value) {
        filtered = filtered.filter(product => 
            product.description.includes(selectedGrade.value)
        )
    }

    return filtered
})

// Summary stats
const citrusdalCount = computed(() => 
    filteredProducts.value.filter(p => p.product_code.startsWith('FR')).length
)

const grahamstownCount = computed(() => 
    filteredProducts.value.filter(p => p.product_code.startsWith('GT')).length
)

const premiumCount = computed(() => 
    filteredProducts.value.filter(p => 
        p.description.includes('Grade A') || p.description.includes('Grade 1')
    ).length
)

// Methods
const loadProducts = async () => {
    try {
        const response = await api.getProducts()
        
        // Handle different response structures
        if (response.data) {
            products.value = response.data
        } else if (Array.isArray(response)) {
            products.value = response
        } else {
            products.value = []
        }
        
        console.log('Loaded products:', products.value)
    } catch (err) {
        console.error('Error loading products:', err)
        products.value = []
    }
}

const filterProducts = () => {
    // Filtering is handled by computed property
}

const clearFilters = () => {
    searchQuery.value = ''
    selectedCategory.value = ''
    selectedGrade.value = ''
}

// Helper methods for list view
const getProductPrice = (product) => {
    if (!product.prices || product.prices.length === 0) {
        return 'No pricing'
    }
    
    const price = product.prices.find(p => p.year === selectedYear.value)
    if (price) {
        return new Intl.NumberFormat('en-ZA', {
            style: 'currency',
            currency: 'ZAR'
        }).format(price.price_per_kg) + '/kg'
    }
    return 'No pricing'
}

const getProductCategory = (productCode) => {
    if (productCode.startsWith('FR')) return 'Citrusdal'
    if (productCode.startsWith('GT')) return 'Grahamstown'
    return 'Other'
}

const getProductIcon = (productCode) => {
    if (productCode.startsWith('FR')) return '🍊'
    if (productCode.startsWith('GT')) return '🍋'
    return '🍎'
}

const getProductIconClass = (productCode) => {
    if (productCode.startsWith('FR')) return 'bg-orange-500'
    if (productCode.startsWith('GT')) return 'bg-green-500'
    return 'bg-gray-500'
}

const getProductColorClass = (productCode) => {
    if (productCode.startsWith('FR')) return 'bg-orange-100 text-orange-800'
    if (productCode.startsWith('GT')) return 'bg-green-100 text-green-800'
    return 'bg-gray-100 text-gray-800'
}

// Load products on mount
onMounted(() => {
    loadProducts()
})
</script>