<script>
import UserLayout from "@/Layouts/UserLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
    name: "Index",

    components: {
        SecondaryButton,
        InputLabel,
        TextInput,
        PrimaryButton,
        UserLayout,
        Link
    },

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

            // Только дата конференции
            scheduledDate: '',

            // Время, до которого нужно успеть
            deadlineTime: '11:05',

            showNameInput: false,

            copiedStates: {}
        };
    },

    mounted() {
        this.title = 'Конференция ' + this.confNumber;
    },

    methods: {
        toggleShowNameInput() {
            this.showNameInput = !this.showNameInput;

            if (this.showNameInput && !this.scheduledDate) {
                this.setDefaultScheduledDate();
            }
        },

        setDefaultScheduledDate() {
            const now = new Date();

            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');

            this.scheduledDate = `${year}-${month}-${day}`;
        },

        getDeadlineAt() {
            if (!this.scheduledDate || !this.deadlineTime) {
                return null;
            }

            return `${this.scheduledDate} ${this.deadlineTime}:00`;
        },

        storeConference() {
            this.$inertia.post(route('conferences.store'), {
                title: this.title,
                link: this.link,
                active: this.active,
                scheduledDate: this.scheduledDate,
                deadlineAt: this.getDeadlineAt(),
            });

            this.resetForm();
        },

        resetForm() {
            this.title = '';
            this.link = '';
            this.active = true;
            this.scheduledDate = '';
            this.deadlineTime = '11:05';
            this.showNameInput = false;
        },

        getInternalLink(conference) {
            return `${window.location.origin}/conferences/${conference.id}`;
        },

        async copyLink(conference) {
            const link = this.getInternalLink(conference);

            try {
                await navigator.clipboard.writeText(link);

                this.copiedStates = {
                    ...this.copiedStates,
                    [conference.id]: true
                };

                setTimeout(() => {
                    this.copiedStates = {
                        ...this.copiedStates,
                        [conference.id]: false
                    };
                }, 2000);

            } catch (err) {
                console.error('Ошибка копирования:', err);
            }
        },

        deleteConference(conference) {
            this.$inertia.delete(
                route('conferences.destroy', {
                    conference: conference.id
                })
            );
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

            <h1 class="text-black">
                Список конференций
            </h1>

            <PrimaryButton @click.prevent="toggleShowNameInput">
                {{ showNameInput ? 'Отмена' : 'Добавить конференцию' }}
            </PrimaryButton>

            <div v-show="showNameInput">

                <div class="mt-4">

                    <InputLabel>
                        Название конференции
                    </InputLabel>

                    <TextInput
                        v-model="title"
                        class="w-full"
                    />

                    <!-- Дата конференции -->
                    <div class="mt-4">
                        <InputLabel>
                            Дата конференции
                        </InputLabel>

                        <input
                            v-model="scheduledDate"
                            type="date"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <!-- Крайний срок -->
                    <div class="mt-4">
                        <InputLabel>
                            Успеть до
                        </InputLabel>

                        <div class="flex items-center gap-2">
                            <input
                                v-model="deadlineTime"
                                type="time"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            <span class="text-sm text-gray-500">
                                в день конференции
                            </span>
                        </div>

                        <p class="mt-1 text-sm text-gray-500">
                            По умолчанию — 11:05.
                        </p>
                    </div>

                    <!-- Ссылка -->
                    <div class="mt-4">
                        <InputLabel>
                            Ссылка на конференцию
                        </InputLabel>

                        <TextInput
                            v-model="link"
                            class="w-full"
                            placeholder="https://zoom.us/..."
                        />
                    </div>

                    <SecondaryButton
                        v-if="
                            title?.length >= 3 &&
                            link?.length &&
                            scheduledDate &&
                            deadlineTime
                        "
                        @click.prevent="storeConference"
                        class="mt-4"
                    >
                        Сохранить
                    </SecondaryButton>

                </div>
            </div>

            <!-- Список конференций -->
            <div
                v-show="!showNameInput"
                v-for="conference in conferences"
                :key="conference.id"
            >
                <div class="p-2 flex justify-between mt-2 rounded-md border border-gray-300">

                    <div>

                        <p class="text-sm text-black">
                            Название: {{ conference.title }}
                        </p>

                        <p class="text-sm text-black">
                            Ссылка на конференцию:
                            <span class="text-blue-500">
                                {{ conference.link }}
                            </span>
                        </p>

                        <p class="text-sm text-black">
                            Дата конференции:
                            <span class="font-medium">
                                {{ conference.scheduled_date }}
                            </span>
                        </p>

                        <p class="text-sm text-black">
                            Успеть до:
                            <span class="font-medium text-orange-600">
                                {{ conference.deadline_at }}
                            </span>
                        </p>

                        <p class="text-sm text-black">
                            Статус:
                            <span
                                :class="[
                                    conference.active
                                        ? 'text-green-600'
                                        : 'text-orange-500'
                                ]"
                            >
                                {{
                                    conference.active
                                        ? 'Ссылка активна'
                                        : 'Ссылка не активна'
                                }}
                            </span>
                        </p>

                        <p class="text-sm text-black">
                            Дата создания:
                            {{ conference.created_at }}
                        </p>

                        <p class="text-sm text-black flex items-center gap-2">
                            <span>Внутренняя ссылка:</span>

                            <span class="text-blue-600 font-mono">
                                {{ getInternalLink(conference) }}
                            </span>

                            <button
                                @click="copyLink(conference)"
                                class="px-2 py-0.5 text-xs bg-gray-200 hover:bg-gray-300 rounded transition"
                                :class="
                                    copiedStates[conference.id]
                                        ? 'bg-green-200 text-green-800'
                                        : ''
                                "
                            >
                                {{
                                    copiedStates[conference.id]
                                        ? 'Скопировано!'
                                        : 'Копировать'
                                }}
                            </button>
                        </p>

                    </div>

                    <div>
                        <a
                            @click.prevent="deleteConference(conference)"
                            class="cursor-pointer block text-sm text-red-400 hover:text-red-500"
                        >
                            Удалить
                        </a>
                    </div>

                </div>
            </div>

        </template>
    </UserLayout>
</template>

<style scoped>
</style>
