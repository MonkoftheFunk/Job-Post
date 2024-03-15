<script setup>
import {Head, Link} from "@inertiajs/vue3"
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    listings: {
        type: Array,
        required: true
    }
})
</script>

<template>
    <Head title="Listings"/>
    <AuthenticatedLayout>
        <section class="text-gray-600 body-font overflow-hidden">
            <div class="w-full md:w-1/2 py-24 mx-auto">
                <div class="mb-4">
                    <h2 class="text-2xl font-medium text-gray-900 title-font">
                        Listings
                    </h2>
                </div>
                <div class="-my-6 p-4">
                    <a v-for="listing in listings"
                       :href="route('admin.listings.edit', {'slug': listing.slug})"
                       class="py-6 px-4 flex flex-wrap md:flex-nowrap border-b border-gray-100"
                       :class="[listing.is_highlighted ? 'bg-yellow-100 hover:bg-yellow-200' : 'bg-white hover:bg-gray-100']"
                    >
                        <div class="md:w-16 md:mb-0 mb-6 mr-4 flex-shrink-0 flex flex-col">
                            <img :src="listing.logoUri" :alt="listing.company + ' logo'" class="w-16 h-16 rounded-full object-cover">
                        </div>
                        <div class="md:w-1/2 mr-8 flex flex-col items-start justify-center">
                            <h2 class="text-xl font-bold text-gray-900 title-font mb-1">{{ listing.title }}</h2>
                            <p class="leading-relaxed text-gray-900">
                                {{ listing.company }} &mdash; <span class="text-gray-600">{{ listing.location }}</span>
                            </p>
                            <p class="leading-relaxed text-gray-900">
                                Applications {{ listing.clicksCount }}
                            </p>
                        </div>
                        <div v-if="listing.tags.length" class="md:flex-grow mr-8 flex items-center justify-start">
                            <span v-for="tag in listing.tags"
                                  class="inline-block ml-2 tracking-wide text-xs font-medium title-font py-0.5 px-1.5 border border-indigo-500 uppercase bg-white text-indigo-500">
                                   {{ tag.name }}
                            </span>
                        </div>
                        <span class="md:flex-grow flex items-center justify-end">
                        <span>{{ listing.sinceCreated }}</span>
                    </span>
                    </a>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
