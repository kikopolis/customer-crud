<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router';
</script>

<template>
    <header class="flex">
        <div class="wrapper mt-10 mx-auto">
            <nav>
                <RouterLink
                        class="text-blue-900 hover:text-blue-400 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium
                        text-sm px-5 py-2.5 text-center me-2 mb-2 underline transition-all duration-300"
                        to="/"
                >
                    Customers
                </RouterLink>
            </nav>
        </div>
    </header>

    <RouterView/>
</template>
