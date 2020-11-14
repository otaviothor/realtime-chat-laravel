<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chat
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex" style="min-height: 400px; max-height: 400px;">
                    <!-- list user -->
                    <div class="w-3/12 bg-gray-200 bg-opacity-25 border-r border-gray-200 overflow-y-auto">
                        <ul>
                            <li
                                v-for="user in users"
                                :key="user.id"
                                :class="(userActive && userActive.id === user.id) ? 'bg-gray-200 bg-opacity-50' : ''"
                                @click="() => loadMessage(user.id)"
                                class="p-6 text-lg text-gray-600 leading-7 font-semibold border-b border-gray-200 hover:bg-gray-200 hover:bg-opacity-50 hover:cursor-pointer">
                                <p class="flex">
                                    {{ user.name }}
                                    <span
                                        v-if="user.notification"
                                        class="ml-2 w-2 h-2 bg-blue-300 rounded-full"></span>
                                </p>
                            </li>
                        </ul>
                    </div>

                    <!-- box message -->
                    <div class="w-9/12 flex flex-col justify-between">
                        <div class="w-full p-6 flex flex-col overflow-y-auto">
                            <div
                                v-for="message in messages"
                                :class="(message.from === $page.auth.user.id) ? 'text-right' : ''"
                                :key="message.id"
                                class="w-full mb-3 message">
                                <p
                                    :class="(message.from === $page.auth.user.id) ? 'messageFromMe' : 'messageToMe'"
                                    class="inline-block p-2 rounded-md messageFromMe" style="max-width: 75%;">
                                    {{message.content}}
                                </p>
                                <span class="block mt-1 text-xs text-gray-500">
                                    {{message.created_at | formatDate}}
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="userActive"
                            class="w-full bg-gray-200 bg-opacity-25 p-6 border-t border-gray-200">
                            <form
                                v-on:submit.prevent="sendMessage"
                            >
                                <div class="flex rounded-md overflow-hidden border border-gray-300">
                                    <input
                                        v-model="message"
                                        type="text"
                                        class="flex-1 px-4 py-2 text-sm focus:outline-none">
                                    <button
                                        type="submit"
                                        class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 focus:outline-none">Enviar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import Vue from 'vue';
    import AppLayout from '@/Layouts/AppLayout';
    import store from "../store";

    export default {
        components: {
            AppLayout
        },
        data() {
            return {
                users: [],
                messages: [],
                userActive: null,
                message: ''
            }
        },
        computed: {
            user() {
                return store.state.user
            }
        },
        methods: {
            scrollToButton: function () {
                if (this.messages.length) {
                    document.querySelectorAll('.message:last-child')[0].scrollIntoView();
                }
            },
            loadMessage: async function (userId) {
                await axios.get(`/api/users/${userId}`)
                    .then(response => {
                        this.userActive = response.data.user
                    });
                await axios.get(`/api/messages/${userId}`)
                    .then(response => {
                        this.messages = response.data.messages
                    });

                const user = this.users.filter((user) => {
                    if (user.id === userId) {
                        return user;
                    }
                })

                if (user) {
                    Vue.set(user[0], 'notification', false);
                }

                this.scrollToButton();
            },
            sendMessage: async function () {
                await axios.post('api/messages/store', {
                    'content': this.message,
                    'to': this.userActive.id
                })
                    .then(response => {
                        this.messages.push({
                            'from': this.user.id,
                            'to': this.userActive.id,
                            'content': this.message,
                            'created_at': new Date().toISOString(),
                            'updated_at': new Date().toISOString()
                        });
                        this.message = '';
                    });
                this.scrollToButton();
            }
        },
        mounted() {
            axios.get('api/users')
                .then(response => this.users = response.data.users);

            Echo.private(`user.${this.user.id}`).listen('.SendMessage', async (e) => {
                if (this.userActive && this.userActive.id === e.message.from) {
                    await this.messages.push(e.message);
                    this.scrollToButton();
                } else {
                    const user = this.users.filter((user) => {
                        if (user.id === e.message.from) {
                            return user;
                        }
                    })

                    if (user) {
                        Vue.set(user[0], 'notification', true);
                    }
                }
            })
        }
    }
</script>

<style>
    .messageFromMe {
        @apply bg-indigo-300 bg-opacity-25;
    }

    .messageToMe {
        @apply bg-gray-300 bg-opacity-25;
    }
</style>
