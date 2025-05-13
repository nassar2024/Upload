<template>
    <div class="min-h-screen flex flex-col lg:flex-row bg-gradient-to-br from-blue-900 to-blue-600 text-white relative">
        <!-- Particle Background -->
        <div id="tsparticles" class="absolute inset-0 z-0"></div>

        <!-- Promotional Section -->
        <div class="w-full lg:w-1/2 p-8 flex flex-col justify-between relative z-10">
            <div>
                <a href="https://www.yoprint.com/" target="_blank" rel="noopener noreferrer">
                    <img src="/images/logo.png" alt="Logo" class="h-24 w-24 rounded-full object-cover logo-hover" />
                </a>
            </div>
            <div class="mt-8 lg:mt-0">
                <h1 class="text-4xl font-bold text-blue-400 leading-tight">
                    An All-in-One Print Shop Management Software
                </h1>
                <p class="mt-4 text-blue-400">
                    Get all the tools you need to get organized, grow sales, track inventory, and increase customer satisfaction.
                </p>
                <div class="mt-8 flex space-x-4">
                    <div class="flex-1 bg-white/20 p-4 rounded-lg">
                        <p class="text-2xl font-bold text-blue-400">7k+</p>
                        <p class="text-sm text-blue-300">Active Users</p>
                    </div>
                    <div class="flex-1 bg-white/20 p-4 rounded-lg">
                        <p class="text-2xl font-bold text-blue-400">98%</p>
                        <p class="text-sm text-blue-300">Satisfaction</p>
                    </div>
                    <div class="flex-1 bg-white/20 p-4 rounded-lg">
                        <p class="text-2xl font-bold text-blue-400">24/7</p>
                        <p class="text-sm text-blue-300">Support</p>
                    </div>
                </div>
            </div>
            <div class="mt-8 text-sm text-blue-300">
                © 2025 YoPrint. All rights reserved.
            </div>
        </div>

        <!-- Form Section -->
        <div class="w-full lg:w-1/2 flex items-center justify-center relative z-10 p-8">
            <div class="w-full max-w-md space-y-6 bg-white p-6 rounded-lg shadow-lg">
                <div>
                    <h3 class="text-2xl font-bold">Welcome Back</h3>
                    <p class="mt-1 text-sm text-blue-400">Please sign in to continue</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-blue-400">Email address</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Enter your email"
                        />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-blue-400">Password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Enter your password"
                        />
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input
                                id="remember_me"
                                v-model="form.remember"
                                type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            />
                            <label for="remember_me" class="ml-2 block text-sm text-blue-400">Remember me</label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-medium text-blue-400 hover:text-blue-300">Forgot password?</a>
                        </div>
                    </div>

                    <div>
                        <button
                            type="button"
                            @click="login"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Sign in
                        </button>
                    </div>
                </div>

                <div class="text-center text-sm text-blue-400">
                    New to YoPrint? <router-link to="/register" class="font-medium text-blue-400 hover:text-blue-300">Create an account</router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Login',
    data() {
        return {
            form: {
                email: '',
                password: '',
                remember: false,
            },
            errors: {},
        };
    },
    mounted() {
        const particleOptions = {
            background: {
                color: {
                    value: "transparent",
                },
            },
            fpsLimit: 60,
            particles: {
                number: {
                    value: 150, // Increased from 80 to 150 for more particles
                    density: {
                        enable: true,
                        value_area: 800,
                    },
                },
                color: {
                    value: "#60A5FA",
                },
                shape: {
                    type: "circle",
                },
                opacity: {
                    value: 0.5,
                    random: true,
                },
                size: {
                    value: 3,
                    random: true,
                },
                links: {
                    enable: true,
                    distance: 150,
                    color: "#60A5FA",
                    opacity: 0.4,
                    width: 1,
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: "none",
                    random: true,
                    straight: false,
                    outModes: {
                        default: "out",
                    },
                },
            },
            interactivity: {
                events: {
                    onHover: {
                        enable: true,
                        mode: "grab",
                    },
                    onClick: {
                        enable: true,
                        mode: "push",
                    },
                },
                modes: {
                    grab: {
                        distance: 200,
                        links: {
                            opacity: 1,
                        },
                    },
                    push: {
                        quantity: 4,
                    },
                },
            },
            detectRetina: true,
        };

        tsParticles.load({
            id: "tsparticles",
            options: particleOptions,
        });
    },
    methods: {
        async login() {
            try {
                const response = await this.$axios.post('/api/login', this.form);
                localStorage.setItem('token', response.data.token);
                this.$router.push({ name: 'dashboard' });
            } catch (error) {
                console.error('Login error:', error);
                this.errors = error.response?.data?.errors || { message: 'Login failed. Please check your credentials.' };
            }
        },
    },
};
</script>

<style scoped>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}

.btn-primary {
    transition: transform 0.2s ease, background-color 0.2s ease;
    background: linear-gradient(to right, #60A5FA, #2563EB);
}

.btn-primary:hover {
    transform: scale(1.05);
}

.input-focus {
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.input-focus:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

.text-light-blue {
    color: rgba(147, 197, 253, 0.9);
}

.text-light-blue-faded {
    color: rgba(147, 197, 253, 0.7);
}
</style>