<script setup>
import { Head } from '@inertiajs/vue3';
import Navbar from "@/Components/Main/Admin/Layout/Nav/Navbar.vue";
import MainFooter from "@/Components/Main/Admin/Layout/Footer/MainFooter.vue";
</script>


<template>
    <div class="flex flex-col min-h-screen">
        <Head>
            <meta head-key="viewport" name="viewport"
                  content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
        </Head>

        <Navbar/>

        <main class="xs:px-0 lg:p-6 mx-4 lg:ml-64 flex-1">
            <div class="2xl:max-w-[1500px] max-w-full mx-auto">
                <slot/>
            </div>
        </main>

        <MainFooter class="xs:px-0 lg:p-6 mx-4 lg:ml-64"/>
    </div>
</template>

