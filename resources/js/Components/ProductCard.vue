<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="categoryColorClass">
                            {{ product.product_code }}
                        </span>
                        <span v-if="isPremium" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            ⭐ Premium
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        {{ product.description }}
                    </h3>
                    
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ selectedYear }} Price:</span>
                            <span class="text-lg font-semibold text-gray-900">
                                {{ currentPrice }}
                            </span>
                        </div>
                        
                        <!-- Price History Toggle -->
                        <button v-if="showPriceHistory && product.prices && product.prices.length > 1" 
                                @click.stop="togglePriceHistory"
                                class="text-xs text-blue-600 hover:text-blue-800">
                            {{ showAllPrices ? 'Hide' : 'Show' }} price history
                        </button>
                    </div>
                    
                    <!-- Price History -->
                    <div v-if="showAllPrices && product.prices" class="mt-3 pt-3 border-t border-gray-200">
                        <div class="space-y-1">
                            <div v-for="price in sortedPrices" :key="price.year"
                                 class="flex items-center justify-between text-sm">
                                <span :class="price.year === selectedYear ? 'font-medium text-gray-900' : 'text-gray-500'">
                                    {{ price.year }}:
                                </span>
                                <span :class="price.year === selectedYear ? 'font-medium text-gray-900' : 'text-gray-500'">
                                    {{ formatPrice(price.price_per_kg) }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Price Change Indicator -->
                        <div v-if="priceChange" class="mt-2 text-xs">
                            <span :class="priceChange.isIncrease ? 'text-red-600' : 'text-green-600'">
                                {{ priceChange.isIncrease ? '↗' : '↘' }} 
                                {{ priceChange.percentage }}% vs {{ priceChange.previousYear }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Category Icon -->
                <div class="ml-4">
                    <div :class="categoryIconClass" class="w-12 h-12 rounded-lg flex items-center justify-center text-white text-xl">
                        {{ categoryIcon }}
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-xs text-gray-500">
                    {{ categoryName }}
                </div>
                <div class="text-xs text-gray-400">
                    per kg
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    selectedYear: {
        type: Number,
        default: () => new Date().getFullYear()
    },
    showPriceHistory: {
        type: Boolean,
        default: true
    }
})

const showAllPrices = ref(false)

const currentPrice = computed(() => {
    if (!props.product.prices || props.product.prices.length === 0) {
        return 'No pricing'
    }
    
    const price = props.product.prices.find(p => p.year === props.selectedYear)
    return price ? formatPrice(price.price_per_kg) + '/kg' : 'No pricing'
})

const sortedPrices = computed(() => {
    return props.product.prices?.slice().sort((a, b) => b.year - a.year) || []
})

const categoryName = computed(() => {
    const code = props.product.product_code
    if (code.startsWith('FR')) return 'Citrusdal'
    if (code.startsWith('GT')) return 'Grahamstown'
    return 'Other'
})

const categoryIcon = computed(() => {
    const code = props.product.product_code
    if (code.startsWith('FR')) return '🍊'
    if (code.startsWith('GT')) return '🍋'
    return '🍎'
})

const categoryColorClass = computed(() => {
    const code = props.product.product_code
    if (code.startsWith('FR')) return 'bg-orange-100 text-orange-800'
    if (code.startsWith('GT')) return 'bg-green-100 text-green-800'
    return 'bg-gray-100 text-gray-800'
})

const categoryIconClass = computed(() => {
    const code = props.product.product_code
    if (code.startsWith('FR')) return 'bg-orange-500'
    if (code.startsWith('GT')) return 'bg-green-500'
    return 'bg-gray-500'
})

const isPremium = computed(() => {
    // Consider Grade A and Grade 1 products as premium
    return props.product.description.includes('Grade A') || 
           props.product.description.includes('Grade 1')
})

const priceChange = computed(() => {
    if (!props.product.prices || props.product.prices.length < 2) return null
    
    const currentYearPrice = props.product.prices.find(p => p.year === props.selectedYear)
    const previousYearPrice = props.product.prices.find(p => p.year === props.selectedYear - 1)
    
    if (!currentYearPrice || !previousYearPrice) return null
    
    const change = currentYearPrice.price_per_kg - previousYearPrice.price_per_kg
    const percentage = Math.abs((change / previousYearPrice.price_per_kg) * 100).toFixed(1)
    
    return {
        isIncrease: change > 0,
        percentage,
        previousYear: props.selectedYear - 1
    }
})

const formatPrice = (amount) => {
    return new Intl.NumberFormat('en-ZA', {
        style: 'currency',
        currency: 'ZAR',
        minimumFractionDigits: 2
    }).format(amount)
}

const togglePriceHistory = () => {
    showAllPrices.value = !showAllPrices.value
}
</script>