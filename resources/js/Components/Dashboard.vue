<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Toast Notifications -->
        <div class="fixed top-4 right-4 z-50 space-y-2">
            <div v-for="toast in toasts" :key="toast.id" class="bg-white border-l-4 p-4 rounded-lg shadow-md transition-all duration-300"
                 :class="{
                     'border-green-500': toast.type === 'success',
                     'border-red-500': toast.type === 'error'
                 }">
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-700">{{ toast.message }}</p>
                    <button @click="removeToast(toast.id)" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900 transition-colors duration-300 hover:text-blue-600">Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button @click="toggleNotifications" class="text-gray-600 hover:text-gray-900 focus:outline-none transition-transform duration-200 hover:scale-110">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span v-if="completedUploads.length" class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full transition-all duration-300">
                                {{ completedUploads.length }}
                            </span>
                        </button>
                        <transition name="fade">
                            <div v-if="showNotifications" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg py-2 z-10 border border-gray-200">
                                <div v-if="!completedUploads.length" class="px-4 py-2 text-sm text-gray-700">
                                    No completed uploads.
                                </div>
                                <div v-else>
                                    <div v-for="upload in completedUploads" :key="upload.id" class="px-4 py-2 text-sm text-gray-700 border-b hover:bg-gray-50 transition-colors duration-200">
                                        <p>{{ upload.filename }} completed at {{ upload.completed_at }}</p>
                                    </div>
                                    <button @click="clearNotifications" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors duration-200">
                                        Clear Notifications
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </div>
                    <div class="relative">
                        <button @click="toggleDropdown" class="flex items-center text-gray-600 hover:text-gray-900 focus:outline-none transition-transform duration-200 hover:scale-105">
                            <span>{{ user?.name || 'User' }}</span>
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <transition name="fade">
                            <div v-if="showDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-10 border border-gray-200">
                                <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">Logout</button>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button @click="currentTab = 'upload'; loadUploads()" :class="currentTab === 'upload' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-300">
                            Upload
                        </button>
                        <button @click="currentTab = 'products'; loadProducts()" :class="currentTab === 'products' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-300">
                            Products
                        </button>
                    </nav>
                </div>

                <!-- Tab Content with Transition -->
                <transition name="fade" mode="out-in">
                    <div :key="currentTab" class="mt-6">
                        <!-- Upload Tab -->
                        <div v-if="currentTab === 'upload'">
                            <!-- File Upload -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">Upload CSV File</label>
                                <input type="file" accept=".csv" @change="handleFileUpload" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <button @click="uploadFile" :disabled="isButtonDisabled" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-400 transition-colors duration-200 flex items-center space-x-2">
                                    <span v-if="isUploading" class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"></span>
                                    <span>{{ isUploading ? 'Uploading' : 'Upload' }}</span>
                                </button>
                            </div>

                            <!-- Upload History -->
                            <div class="mt-6">
                                <h2 class="text-lg font-medium text-gray-900">Upload History</h2>
                                <p v-if="!uploads.length" class="mt-2 text-gray-500">No uploads yet.</p>
                                <ul v-else class="mt-2 divide-y divide-gray-200">
                                    <li v-for="upload in uploads" :key="upload.id" class="py-4 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ upload.filename }}</p>
                                                <p class="text-sm text-gray-500">{{ upload.created_at }}</p>
                                            </div>
                                            <p class="text-sm text-gray-500">{{ upload.status }}</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Products Tab -->
                        <div v-if="currentTab === 'products'">
                            <h2 class="text-lg font-medium text-gray-900">Your Products</h2>
                         <button @click="clearProductCache" :disabled="isClearingCache" class="px-3 py-1 bg-blue-300 text-white rounded hover:bg-blue-400 transition-colors duration-200 flex items-center space-x-2">
                            <span v-if="isClearingCache" class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"></span>
                            <span>{{ isClearingCache ? 'Clearing' : 'Clear Cache' }}</span>
                          </button>
                            <div class="flex justify-between items-center mb-2">
                                <p v-if="isLoadingProducts" class="text-gray-500">Loading products...</p>
                                <p v-else-if="!products.length" class="text-gray-500">No products found.</p>
                            </div>
                            <div v-if="products.length" class="mt-2">
                                <div class="flex justify-end mb-2 space-x-2">
                                    <select v-model="perPage" @change="loadProducts()" class="border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200">
                                        <option v-for="option in [10, 25, 50, 100]" :key="option" :value="option" class="py-1">{{ option }} per page</option>
                                    </select>
                                </div>
                                <div class="overflow-x-auto overflow-y-auto max-h-96">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique Key</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Title</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Style</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="product in products" :key="product.id" class="hover:bg-gray-100 transition-colors duration-200">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ product.unique_key }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ product.product_title }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ product.piece_price }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-500">{{ product.product_description || 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.style || 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.color_name || 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.size || 'N/A' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4 flex justify-between items-center">
                                    <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1" class="px-4 py-2 bg-gray-300 text-white rounded hover:bg-gray-400 disabled:bg-gray-200 transition-all duration-200">
                                        Previous
                                    </button>
                                    <span class="text-sm text-gray-700">Page {{ currentPage }} of {{ lastPage }}</span>
                                    <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage" class="px-4 py-2 bg-gray-300 text-white rounded hover:bg-gray-400 disabled:bg-gray-200 transition-all duration-200">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </main>
    </div>
</template>

<script>
import axios from 'axios';
import Echo from 'laravel-echo';

export default {
    data() {
        return {
            user: null,
            showDropdown: false,
            showNotifications: false,
            completedUploads: [],
            file: null,
            uploads: [],
            isUploading: false,
            isLoadingUploads: false,
            currentTab: 'upload',
            products: [],
            isLoadingProducts: false,
            currentPage: 1,
            lastPage: 1,
            perPage: 10,
            isClearingCache: false,
            toasts: [],
        };
    },
    computed: {
        isButtonDisabled() {
            return !this.file || this.isUploading;
        }
    },
    watch: {
        file(newFile) {
            console.log('File changed:', newFile);
        }
    },
    async mounted() {
        try {
            const response = await axios.get('/api/user', {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`,
                },
            });
            this.user = response.data;
            console.log('User loaded:', this.user);
            await this.loadUploads();
            this.setupReverb();
        } catch (error) {
            console.error('Failed to load user:', error);
            this.$router.push({ name: 'login' });
        }
    },
    methods: {
        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
            if (this.showNotifications) this.showNotifications = false;
        },
        toggleNotifications() {
            this.showNotifications = !this.showNotifications;
            if (this.showDropdown) this.showDropdown = false;
        },
        clearNotifications() {
            this.completedUploads = [];
            this.showNotifications = false;
        },
        async logout() {
            localStorage.removeItem('token');
            this.$router.push('/login');
        },
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file && !file.name.endsWith('.csv')) {
                this.addToast('Please upload a valid CSV file', 'error');
                return;
            }
            if (file && file.size > 40 * 1024 * 1024) {
                this.addToast('File size must be less than 40MB', 'error');
                return;
            }
            this.file = file;
            console.log('Selected file:', this.file);
        },
        async uploadFile() {
            if (!this.file) return;
            this.isUploading = true;
            const formData = new FormData();
            formData.append('file', this.file);
            try {
                await axios.post('/api/uploads', formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                        'Content-Type': 'multipart/form-data',
                    },
                });
                this.file = null;
                await this.loadUploads();
            } catch (error) {
                console.error('Upload failed:', error.response?.data || error.message);
                this.addToast('Failed to upload file. Please try again.', 'error');
            } finally {
                this.isUploading = false;
            }
        },
        async loadUploads() {
            this.isLoadingUploads = true;
            try {
                const response = await axios.get('/api/uploads', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                    },
                });
                this.uploads = response.data.data;
                console.log('Uploads loaded:', this.uploads);
            } catch (error) {
                console.error('Failed to load uploads:', error.response?.data || error.message);
                this.addToast('Failed to load uploads.', 'error');
            } finally {
                this.isLoadingUploads = false;
            }
        },
        async loadProducts() {
            this.isLoadingProducts = true;
            try {
                const response = await axios.get('/api/products', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                    },
                    params: {
                        user_id: this.user.id,
                        page: this.currentPage,
                        per_page: this.perPage,
                    },
                });
                this.products = response.data.data;
                this.currentPage = response.data.current_page;
                this.lastPage = response.data.last_page;
                console.log('Products loaded:', this.products);
            } catch (error) {
                console.error('Failed to load products:', error.response?.data || error.message);
                this.addToast('Failed to load products.', 'error');
            } finally {
                this.isLoadingProducts = false;
            }
        },
        changePage(newPage) {
            if (newPage >= 1 && newPage <= this.lastPage) {
                this.currentPage = newPage;
                this.loadProducts();
            }
        },
        async clearProductCache() {
            this.isClearingCache = true;
            try {
                await axios.post('/api/clear-product-cache', {}, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                    },
                });
                await this.loadProducts();
                this.addToast('Product cache cleared successfully.', 'success');
            } catch (error) {
                console.error('Failed to clear cache:', error.response?.data || error.message);
                this.addToast('Failed to clear cache.', 'error');
            } finally {
                this.isClearingCache = false;
            }
        },
        setupReverb() {
            try {
                console.log('Subscribing to channel: uploads.' + this.user.id);
                window.Echo.channel(`uploads.${this.user.id}`)
                    .listen('.CsvProcessing', async (e) => {
                        console.log('Received CsvProcessing event:', e);
                        // Update upload status
                        this.uploads = this.uploads.map((upload) =>
                            upload.id === e.upload.id ? { ...upload, status: e.upload.status } : upload
                        );
                        if (e.upload.status === 'completed') {
                            // Reload uploads to ensure we have the latest data
                            await this.loadUploads();
                            const completedUpload = this.uploads.find(upload => upload.id === e.upload.id);
                            if (completedUpload && !this.completedUploads.some(u => u.id === completedUpload.id)) {
                                this.completedUploads.push({
                                    id: completedUpload.id,
                                    filename: completedUpload.filename,
                                    completed_at: new Date().toLocaleString(),
                                });
                                this.addToast(`Upload ${completedUpload.filename} completed successfully.`, 'success');
                            }
                        } else if (e.upload.status === 'failed') {
                            this.addToast('Upload failed. Please try again or check logs.', 'error');
                        }
                    })
                    .error((error) => {
                        console.error('Reverb channel error:', error);
                        this.addToast('Failed to connect to notifications.', 'error');
                    })
                    .subscribed(() => {
                        console.log('Successfully subscribed to Reverb channel: uploads.' + this.user.id);
                    });
            } catch (error) {
                console.error('Failed to setup Reverb:', error);
                this.addToast('Failed to setup notifications.', 'error');
            }
        },
        addToast(message, type) {
            const id = Date.now();
            this.toasts.push({ id, message, type });
            setTimeout(() => this.removeToast(id), 5000);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(toast => toast.id !== id);
        },
    },
};
</script>