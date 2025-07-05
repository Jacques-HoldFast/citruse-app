<template>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium mb-4">API Test</h3>
        
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-4">
            <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-sm text-gray-600">Loading...</p>
        </div>
        
        <!-- Error State -->
        <div v-else-if="error" class="bg-red-50 border border-red-200 rounded p-4 mb-4">
            <p class="text-red-700 text-sm">{{ error }}</p>
        </div>
        
        <!-- Test Buttons -->
        <div class="space-y-2 mb-4">
            <button @click="testBasicApi" 
                    class="mr-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Test Basic API
            </button>
            <button @click="testAuthApi" 
                    class="mr-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Test Auth API
            </button>
            <button @click="testDataApi" 
                    class="mr-2 px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                Test Data API
            </button>
            <button @click="testUserApi" 
                    class="mr-2 px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                Get User Info
            </button>
        </div>
        
        <!-- Results -->
        <div v-if="result" class="bg-gray-50 rounded p-4">
            <h4 class="font-medium mb-2">Result:</h4>
            <pre class="text-sm text-gray-700 overflow-x-auto">{{ JSON.stringify(result, null, 2) }}</pre>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useApi } from '@/Composables/useApi'

const { loading, error, api } = useApi()
const result = ref(null)

const testBasicApi = async () => {
    try {
        result.value = await api.testBasic()
    } catch (err) {
        console.error('Basic API test failed:', err)
    }
}

const testAuthApi = async () => {
    try {
        result.value = await api.testAuth()
    } catch (err) {
        console.error('Auth API test failed:', err)
    }
}

const testDataApi = async () => {
    try {
        result.value = await api.testData()
    } catch (err) {
        console.error('Data API test failed:', err)
    }
}

const testUserApi = async () => {
    try {
        result.value = await api.getUser()
    } catch (err) {
        console.error('User API test failed:', err)
    }
}
</script>