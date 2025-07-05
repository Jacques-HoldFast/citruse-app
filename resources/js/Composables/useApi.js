// resources/js/Composables/useApi.js
import axios from 'axios'
import { ref } from 'vue'

export function useApi() {
    const loading = ref(false)
    const error = ref(null)

    // Set up axios defaults
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
    axios.defaults.headers.common['Accept'] = 'application/json'
    axios.defaults.headers.common['Content-Type'] = 'application/json'

    // Add CSRF token if available
    const csrfToken = document.querySelector('meta[name="csrf-token"]')
    if (csrfToken) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content')
    }

    const apiCall = async (method, url, data = null, config = {}) => {
        loading.value = true
        error.value = null

        try {
            const response = await axios({
                method,
                url: `/api${url}`,
                data,
                ...config
            })

            loading.value = false
            return response.data
        } catch (err) {
            loading.value = false
            error.value = err.response?.data?.message || err.message || 'An error occurred'

            // Log error for debugging
            console.error('API Error:', {
                method,
                url: `/api${url}`,
                error: err.response?.data || err.message,
                status: err.response?.status
            })

            throw err
        }
    }

    // API methods
    const api = {
        // User & Auth
        getUser: () => apiCall('GET', '/user'),
        getDashboard: () => apiCall('GET', '/user/dashboard'),
        updateProfile: (data) => apiCall('PATCH', '/user/profile', data),

        // Products
        getProducts: (params = {}) => apiCall('GET', '/products', null, { params }),
        getProduct: (id) => apiCall('GET', `/products/${id}`),
        getProductCatalog: () => apiCall('GET', '/products/catalog'),
        getProductPricing: (year) => apiCall('GET', '/products/pricing', null, { params: { year } }),
        createProduct: (data) => apiCall('POST', '/products', data),
        updateProduct: (id, data) => apiCall('PATCH', `/products/${id}`, data),
        updateProductPricing: (id, data) => apiCall('PATCH', `/products/${id}/pricing`, data),
        deleteProduct: (id) => apiCall('DELETE', `/products/${id}`),

        // Suppliers
        getSuppliers: (params = {}) => apiCall('GET', '/suppliers', null, { params }),
        getSupplier: (id) => apiCall('GET', `/suppliers/${id}`),
        createSupplier: (data) => apiCall('POST', '/suppliers', data),
        updateSupplier: (id, data) => apiCall('PATCH', `/suppliers/${id}`, data),
        deleteSupplier: (id) => apiCall('DELETE', `/suppliers/${id}`),
        getSupplierPerformance: (id) => apiCall('GET', `/suppliers/${id}/performance`),

        // Distributors
        getDistributors: (params = {}) => apiCall('GET', '/distributors', null, { params }),
        getDistributor: (id) => apiCall('GET', `/distributors/${id}`),
        createDistributor: (data) => apiCall('POST', '/distributors', data),
        updateDistributor: (id, data) => apiCall('PATCH', `/distributors/${id}`, data),
        deleteDistributor: (id) => apiCall('DELETE', `/distributors/${id}`),

        // Purchase Orders
        getPurchaseOrders: (params = {}) => apiCall('GET', '/purchase-orders', null, { params }),
        getPurchaseOrder: (id) => apiCall('GET', `/purchase-orders/${id}`),
        createPurchaseOrder: (data) => apiCall('POST', '/purchase-orders', data),
        updatePurchaseOrder: (id, data) => apiCall('PATCH', `/purchase-orders/${id}`, data),
        deletePurchaseOrder: (id) => apiCall('DELETE', `/purchase-orders/${id}`),
        updateLineItem: (id, data) => apiCall('PATCH', `/purchase-order-items/${id}`, data),
        getPipelineForecast: (months = 6) => apiCall('GET', '/purchase-orders/reports/pipeline-forecast', null, { params: { months } }),

        // Users (System Admin only)
        getUsers: (params = {}) => apiCall('GET', '/users', null, { params }),
        createUser: (data) => apiCall('POST', '/users', data),
        updateUser: (id, data) => apiCall('PATCH', `/users/${id}`, data),
        deleteUser: (id) => apiCall('DELETE', `/users/${id}`),

        // Test endpoints (remove these after testing)
        testBasic: () => apiCall('GET', '/test'),
        testAuth: () => apiCall('GET', '/test-auth'),
        testData: () => apiCall('GET', '/test-data'),
    }

    // Helper function to handle API responses with pagination
    const handlePaginatedResponse = (response) => {
        if (response.data && Array.isArray(response.data)) {
            return {
                data: response.data,
                meta: response.meta || {},
                links: response.links || {}
            }
        }
        return response
    }

    // Helper function to build query params
    const buildParams = (filters = {}) => {
        const params = {}
        Object.keys(filters).forEach(key => {
            if (filters[key] !== null && filters[key] !== undefined && filters[key] !== '') {
                params[key] = filters[key]
            }
        })
        return params
    }

    return {
        loading,
        error,
        api,
        handlePaginatedResponse,
        buildParams
    }
}