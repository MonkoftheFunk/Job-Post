<script setup>
import {Head, useForm, Link} from '@inertiajs/vue3';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import Header from "@/Components/Header.vue";

const form = useForm({
    email: '',
    name: '',
    password: '',
    password_confirmation: '',
    title: '',
    company: '',
    logo: '',
    location: '',
    link: '',
    tags: '',
    content: '',
    is_highlighted: false,
})
</script>

<template>
    <Head title="Create Listing"/>
    <Header/>
    <section class="text-gray-600 body-font overflow-hidden">
        <div class="w-full md:w-1/2 py-24 mx-auto">
            <div class="mb-4">
                <h2 class="text-2xl font-medium text-gray-900 title-font">
                    Create a new listing
                </h2>
            </div>

            <form
                @submit.prevent="form.post(route('admin.listings.store'))"
                id="payment_form"
                enctype="multipart/form-data"
                class="bg-gray-100 p-4"
            >

                <div v-if="!$page.props.auth.user">
                    <div class="flex mb-4">
                        <div class="flex-1 mx-2">
                            <InputLabel value="Email Address"/>
                            <TextInput
                                v-model="form.email"
                                class="block mt-1 w-full"
                                id="email"
                                type="email"
                                name="email"
                                required
                                autofocus/>
                        </div>
                        <div class="flex-1 mx-2">
                            <InputLabel value="Full Name"/>
                            <TextInput
                                v-model="form.name"
                                class="block mt-1 w-full"
                                id="name"
                                type="text"
                                name="name"
                                required/>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        <div class="flex-1 mx-2">
                            <InputLabel value="Password"/>
                            <TextInput
                                v-model="form.password"
                                class="block mt-1 w-full"
                                id="password"
                                type="password"
                                name="password"
                                required/>
                        </div>
                        <div class="flex-1 mx-2">
                            <InputLabel value="Confirm Password"/>
                            <TextInput
                                v-model="form.password_confirmation"
                                class="block mt-1 w-full"
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required/>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mx-2">
                        <Link
                            :href="route('login')"
                            class="font-semibold text-gray-600 dark:text-gray-400 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                        >Log in
                        </Link>
                    </div>
                </div>
                <div class="mb-4 mx-2">
                    <InputLabel value="Job Title"/>
                    <TextInput
                        v-model="form.title"
                        id="title"
                        class="block mt-1 w-full"
                        type="text"
                        name="title"
                        required/>
                    <InputError class="mt-2" :message="form.errors.title"/>
                </div>
                <div class="mb-4 mx-2">
                    <InputLabel value="Company Name"/>
                    <TextInput
                        v-model="form.company"
                        id="company"
                        class="block mt-1 w-full"
                        type="text"
                        name="company"
                        required/>
                    <InputError class="mt-2" :message="form.errors.title"/>
                </div>
                <div class="mb-4 mx-2">
                    <InputLabel value="Company Logo"/>
                    <TextInput
                        v-model="form.logo"
                        id="logo"
                        class="block mt-1 w-full"
                        type="file"
                        name="logo"/>
                    <InputError class="mt-2" :message="form.errors.logo"/>
                </div>
                <div class="mb-4 mx-2">
                    <InputLabel value="Location (e.g. Remote, United States)"/>
                    <TextInput
                        v-model="form.location"
                        id="location"
                        class="block mt-1 w-full"
                        type="text"
                        name="location"
                        required/>
                    <InputError class="mt-2" :message="form.errors.location"/>
                </div>
                <div class="mb-4 mx-2">
                    <InputLabel value="Link To Apply"/>
                    <TextInput
                        v-model="form.link"
                        id="link"
                        class="block mt-1 w-full"
                        type="text"
                        name="link"
                        required/>
                    <InputError class="mt-2" :message="form.errors.link"/>
                </div>
                <div class="mb-4 mx-2">
                    <InputLabel value="Tags (separate by comma)"/>
                    <TextInput
                        v-model="form.tags"
                        id="tags"
                        class="block mt-1 w-full"
                        type="text"
                        name="tags"/>
                    <InputError class="mt-2" :message="form.errors.tags"/>
                </div>
                <div class="mb-4 mx-2">
                    <InputLabel value="Listing Content"/>
                    <textarea
                        v-model="form.content"
                        id="content"
                        rows="8"
                        class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full"
                        name="content"
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.content"/>
                </div>
                <div class="mb-4 mx-2">
                    <label for="is_highlighted" class="inline-flex items-center font-medium text-sm text-gray-700">
                        <input
                            v-model="form.is_highlighted"
                            type="checkbox"
                            id="is_highlighted"
                            name="is_highlighted"
                            value="Yes"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2">Highlight this post</span>
                        <InputError class="mt-2" :message="form.errors.is_highlighted"/>
                    </label>
                </div>
                <div class="mb-6 mx-2">
                    <div id="card-element"></div>
                </div>
                <div class="mb-2 mx-2">
                    <input
                        type="hidden"
                        id="payment_method_id"
                        name="payment_method_id"
                        value="">
                    <button
                        :disabled="form.processing"
                        type="submit" id="form_submit"
                        class="block w-full items-center bg-indigo-500 text-white border-0 py-2 focus:outline-none hover:bg-indigo-600 rounded text-base mt-4 md:mt-0">Continue
                    </button>
                </div>

            </form>
        </div>
    </section>
</template>
