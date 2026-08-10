<script>
import UserLayout from "@/Layouts/UserLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
    name: "Index",
    components: { SecondaryButton, InputLabel, TextInput, PrimaryButton, UserLayout, Link },
    props: {
        conferences: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            title: '',
            link: '',
            active: true,
            showNameInput: false,
            copiedStates: false, // объект для хранения состояния копирования по id конференции
        };
    },
    mounted() {
        this.title = 'Конференция ' + this.confNumber;
    },
    methods: {
        toggleShowNameInput() {
            this.showNameInput = !this.showNameInput;
        },
        storeConference() {
            this.$inertia.post(route('conferences.store'), {
                title: this.title,
                link: this.link,
                active: this.active,
            });
            this.title = '';
            this.link = '';
            this.showNameInput = !this.showNameInput;
        },
        // Метод для генерации внутренней ссылки для конкретной конференции
        getInternalLink(conference) {
            return `${window.location.origin}/conferences/${conference.id}`;
        },
        // Метод копирования для конкретной конференции
        async copyLink(conference) {
            const link = this.getInternalLink(conference);
                await navigator.clipboard.writeText(link);
            // Устанавливаем состояние copied для этой конференции
            if(link) {
                this.copiedStates = true
            }
            setTimeout(() => {
                this.copiedStates = false
                }, 2000);
        },
        deleteConference(conference) {
            this.$inertia.delete(route('conferences.destroy', { conference: conference.id }));
        }
    },
    computed: {
        confNumber() {
            return this.conferences.length + 1;
        }
    }
};
</script>

<template>
    <UserLayout>
        <template #content>

            <h1 class="text-black">Список конференций</h1>
            <PrimaryButton @click.prevent="toggleShowNameInput">Добавить конференцию</PrimaryButton>
            <div v-show="showNameInput">
                <div class="mt-4">
                    <InputLabel>Название конференции (от 3х символов)</InputLabel>
                    <TextInput class="w-full required" v-model="title"></TextInput>
                    <InputLabel>Ссылка на конференцию</InputLabel>
                    <TextInput class="w-full required" v-model="link"></TextInput>
                    <SecondaryButton v-if="title?.length && link?.length" @click.prevent="storeConference" class="mt-2">Сохранить
                    </SecondaryButton>
                </div>
            </div>
            <div v-for="conference in conferences" :key="conference.id">
                <div class="p-1 flex justify-between mt-2 rounded-md border border-gray-300">
                    <div>
                        <p class="text-sm text-black">Название: {{ conference.title }}</p>
                        <p class="text-sm text-black">Ссылка на конференцию: <span class="text-blue-500">{{ conference.link }}</span></p>
                        <p class="text-sm text-black">Статус: <span :class="[conference.active ? 'text-green-600' : 'text-orange-500']">{{ conference.active ? 'Ссылка активна' : 'Ссылка не активна' }}</span></p>
                        <p class="text-sm text-black">Дата создания: {{ conference.created_at }}</p>
                        <p class="text-sm text-black flex items-center gap-2">
                            <span>Внутренняя ссылка:</span>
                            <span class="text-blue-600 font-mono">{{ getInternalLink(conference) }}</span>
                            <button
                                @click="copyLink(conference)"
                                class="px-2 py-0.5 text-xs bg-gray-200 hover:bg-gray-300 rounded transition"
                                :class="copiedStates ? 'bg-green-200 text-green-800' : ''"
                            >
                                {{ copiedStates ? 'Скопировано!' : 'Копировать' }}
                            </button>
                        </p>
                    </div>
                    <div>
                        <a @click.prevent="deleteConference(conference)" class="block text-sm text-red-400 hover:text-red-500">Удалить</a>
                    </div>
                </div>
            </div>
        </template>
    </UserLayout>
</template>

<style scoped>

</style>
