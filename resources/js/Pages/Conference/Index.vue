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
        },
        activeTab: {
            type: String,
            default: 'upcoming'
        }
    },

    data() {
        return {
            title: '',
            comment: '',
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
        changeTab(tab) {
            this.$inertia.get(
                route('conferences.index'),
                {
                    tab: tab
                },
                {
                    preserveState: true,
                    preserveScroll: true
                }
            );
        },
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
                comment: this.comment,
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

            <!-- Заголовок страницы -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">
                        Конференции
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        Управление конференциями
                    </p>
                </div>

                <PrimaryButton
                    v-if="activeTab === 'upcoming'"
                    @click.prevent="toggleShowNameInput"
                >
                    Добавить конференцию
                </PrimaryButton>
            </div>

            <!-- Вкладки -->
            <div v-show="!showNameInput" class="border-b border-gray-200 mb-6">
                <nav class="flex gap-6">

                    <button
                        @click="changeTab('upcoming')"
                        class="relative pb-3 text-sm font-medium transition"
                        :class="activeTab === 'upcoming'
                ? 'text-indigo-600'
                : 'text-gray-500 hover:text-gray-700'"
                    >
                        Предстоящие

                        <span
                            v-if="activeTab === 'upcoming'"
                            class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 rounded-full"
                        ></span>
                    </button>

                    <button
                        @click="changeTab('past')"
                        class="relative pb-3 text-sm font-medium transition"
                        :class="activeTab === 'past'
                ? 'text-indigo-600'
                : 'text-gray-500 hover:text-gray-700'"
                    >
                        Прошедшие

                        <span
                            v-if="activeTab === 'past'"
                            class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 rounded-full"
                        ></span>
                    </button>

                </nav>
            </div>


            <!-- Форма создания -->
            <div
                v-show="showNameInput && activeTab === 'upcoming'"
                class="mb-6 rounded-xl border border-gray-200 bg-white shadow-sm"
            >

                <div class="border-b border-gray-200 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Новая конференция
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Укажите дату конференции и время, до которого необходимо успеть подключиться.
                    </p>
                </div>


                <div class="p-5">

                    <!-- Название -->
                    <div>
                        <InputLabel>
                            Название конференции
                        </InputLabel>

                        <TextInput
                            v-model="title"
                            class="w-full mt-1"
                            placeholder="Например: Конференция 15"
                        />
                    </div>

                    <div class="mt-3">
                        <InputLabel>
                            Внутренний комментарий (не виден участникам)
                        </InputLabel>

                        <TextInput
                            v-model="comment"
                            class="w-full mt-1"
                            placeholder="Какой-то комментарий по желанию"
                        />
                    </div>


                    <!-- Дата + дедлайн -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">

                        <div>
                            <InputLabel>
                                Дата конференции
                            </InputLabel>

                            <input
                                v-model="scheduledDate"
                                type="date"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>


                        <div>
                            <InputLabel>
                                Успеть до
                            </InputLabel>

                            <input
                                v-model="deadlineTime"
                                type="time"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            <p class="mt-1 text-xs text-gray-500">
                                По умолчанию — 11:05
                            </p>
                        </div>

                    </div>


                    <!-- Ссылка -->
                    <div class="mt-4">

                        <InputLabel>
                            Ссылка на конференцию
                        </InputLabel>

                        <TextInput
                            v-model="link"
                            class="w-full mt-1"
                            placeholder="https://zoom.us/..."
                        />

                    </div>


                    <!-- Кнопка -->
                    <div class="mt-5 flex justify-end">

                        <SecondaryButton
                            v-if="
                                title?.length >= 3 &&
                                link?.length &&
                                scheduledDate &&
                                deadlineTime
                            "
                            @click.prevent="storeConference"
                        >
                            Создать конференцию
                        </SecondaryButton>

                    </div>

                </div>

            </div>


            <!-- Пустой список -->
            <div
                v-if="!showNameInput && conferences.length === 0"
                class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-10 text-center"
            >

                <div class="text-4xl mb-3">
                    📅
                </div>

                <h2 class="text-lg font-medium text-gray-700">
                    Конференций пока нет
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Создайте первую конференцию, чтобы начать.
                </p>

            </div>


            <!-- Список конференций -->
            <div
                v-show="!showNameInput"
                class="space-y-4"
            >

                <div
                    v-for="conference in conferences"
                    :key="conference.id"
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md"
                >

                    <!-- Верх карточки -->
                    <div class="border-b border-gray-100 px-5 py-4">

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                            <div>

                                <div class="flex items-center gap-2">

                                    <h2 class="text-lg font-semibold text-gray-800">
                                        {{ conference.title }}
                                    </h2>

                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-medium"
                                        :class="
                                            conference.active
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-orange-100 text-orange-700'
                                        "
                                    >
                                        {{
                                            conference.active
                                                ? 'Ссылка активна'
                                                : 'Ссылка неактивна'
                                        }}
                                    </span>

                                </div>

                                <p class="mt-1 text-xs text-gray-400">
                                    Создана {{ conference.created_at }}
                                </p>
                                <div v-if="conference.comment">
                                    <p class="mt-1 text-sm text-gray-500">
                                        Комментарий: <span class="text-orange-500">{{ conference.comment }}</span>
                                    </p>
                                </div>
                            </div>


                            <button
                                @click="deleteConference(conference)"
                                class="self-start text-sm text-red-400 hover:text-red-600 transition"
                            >
                                Удалить
                            </button>

                        </div>

                    </div>


                    <!-- Основная информация -->
                    <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2 lg:grid-cols-3">

                        <!-- Дата -->
                        <div class="rounded-lg bg-gray-50 p-4">

                            <div class="flex items-center gap-2">

                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                    📅
                                </div>

                                <div>

                                    <p class="text-xs text-gray-500">
                                        Дата конференции
                                    </p>

                                    <p class="mt-0.5 font-semibold text-gray-800">
                                        {{ conference.scheduled_date }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- Дедлайн -->
                        <div class="rounded-lg bg-orange-50 p-4">

                            <div class="flex items-center gap-2">

                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-100 text-orange-600">
                                    ⏰
                                </div>

                                <div>

                                    <p class="text-xs text-gray-500">
                                        Успеть до
                                    </p>

                                    <p class="mt-0.5 font-semibold text-orange-700">
                                        {{ conference.deadline_at }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- Zoom -->
                        <div class="rounded-lg bg-blue-50 p-4">

                            <div class="flex items-center gap-2">

                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                    🔗
                                </div>

                                <div class="min-w-0">

                                    <p class="text-xs text-gray-500">
                                        Конференция
                                    </p>

                                    <a
                                        :href="conference.link"
                                        target="_blank"
                                        class="mt-0.5 block truncate font-medium text-blue-600 hover:text-blue-800"
                                    >
                                        Перейти в Zoom
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- Внутренняя ссылка -->
                    <div class="border-t border-gray-100 bg-gray-50 px-5 py-4">

                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">

                            <div class="min-w-0">

                                <p class="text-xs font-medium text-gray-500">
                                    Ссылка для участников
                                </p>

                                <p class="mt-1 truncate font-mono text-sm text-gray-700">
                                    {{ getInternalLink(conference) }}
                                </p>

                            </div>


                            <button
                                @click="copyLink(conference)"
                                class="shrink-0 rounded-md border px-4 py-2 text-sm font-medium transition"
                                :class="
                                    copiedStates[conference.id]
                                        ? 'border-green-200 bg-green-100 text-green-700'
                                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100'
                                "
                            >
                                {{
                                    copiedStates[conference.id]
                                        ? '✓ Скопировано'
                                        : 'Копировать ссылку'
                                }}
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </template>
    </UserLayout>
</template>

<style scoped>
</style>
