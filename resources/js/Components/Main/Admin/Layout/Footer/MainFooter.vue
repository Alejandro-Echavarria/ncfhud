<script setup>
import { Link, usePage } from '@inertiajs/vue3';

// const appVersion = import.meta.env.VITE_APP_VERSION || '1.0.0'; // Define la versión aquí si no la tomas del entorno
const page = usePage();
const appVersion = page.props.entity?.version || '1.0.0';
</script>

<template>
    <footer class="mt-16 mx-4">
        <div class="mx-auto h-auto py-4 border-t border-gray-200">
            <div class="md:flex md:justify-between">
                <div class="mb-6 md:mb-0">
                    <Link href="/" title="MAET" class="flex-shrink-0">
                        <div class="flex items-center h-auto w-full">
                            <div class="items-center">
                                <p class="font-bold dark:text-gray-200 text-gray-700">
                                <span>
                                    MAET / NCF Hud
                                </span>
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <div class="mt-6 lg:mt-8 sm:flex sm:items-center sm:justify-between">
                <div class="text-center sm:text-left">
                    <span class="text-xs text-gray-400">
                        Powered by <a href="https://maet.dev" class="hover:underline">MAET DEV</a> | v{{ appVersion }}
                    </span>
                    <br/>
                    <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                        © {{ new Date().getFullYear() }}
                        <a href="https://maet.dev" class="hover:underline" title="MAET">
                            MAET DEV
                        </a>. All Rights Reserved.
                    </span>

                </div>

                <div class="flex mt-4 space-x-6 sm:justify-center sm:mt-0">
                    <a href="https://do.linkedin.com/in/mechavarria-maet?trk=people-guest_people_search-card&original_referer=https%3A%2F%2Fwww.linkedin.com%2F"
                       class="text-gray-500 hover:text-gray-700 dark:hover:text-white" target="_blank"
                       rel="noreferrer noopener nofollow" title="Linkedin">
                        <svg xmlns="https://www.w3.org/2000/svg" fill="currentColor" width="20" height="20"
                             viewBox="0 0 24 24">
                            <path
                                d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                        <span class="sr-only">Linkedin account</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>
