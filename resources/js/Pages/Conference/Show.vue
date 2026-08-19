<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import axios from "axios";

export default {
    components: {
        PrimaryButton
    },

    props: {
        conference: {
            type: Object,
            required: true
        },

        salons: {
            type: Array,
            default: () => []
        }
    },

    data() {
        return {
            searchQuery: '',
            selectedSalon: null,
            isOpen: false,

            visitorToken: null,
            tokenExpiresAt: null,

            loading: false,
            result: null
        };
    },

    mounted() {
        this.restoreVisitor();
    },

    computed: {
        filteredSalons() {
            const query = this.searchQuery.toLowerCase().trim();

            if (!query) {
                return this.salons;
            }

            return this.salons.filter(salon =>
                salon.name.toLowerCase().includes(query)
            );
        },

        storageKey() {
            return `conference_${this.conference.id}`;
        }
    },

    methods: {

        /**
         * Восстановление посетителя из localStorage
         */
        restoreVisitor() {
            const saved = localStorage.getItem(this.storageKey);

            if (!saved) {
                return;
            }

            try {
                const data = JSON.parse(saved);

                // Проверяем срок действия токена
                if (
                    !data.expires_at ||
                    data.expires_at <= Date.now()
                ) {
                    localStorage.removeItem(this.storageKey);
                    return;
                }

                this.visitorToken = data.token;
                this.tokenExpiresAt = data.expires_at;

                // Восстанавливаем салон
                const salon = this.salons.find(
                    salon => salon.id === data.salon_id
                );

                if (salon) {
                    this.selectedSalon = salon;
                    this.searchQuery = salon.name;
                } else {
                    // Если салон уже удалили
                    localStorage.removeItem(this.storageKey);
                    this.visitorToken = null;
                    this.tokenExpiresAt = null;
                }

            } catch (error) {
                console.error(
                    'Ошибка восстановления посетителя:',
                    error
                );

                localStorage.removeItem(this.storageKey);
            }
        },

        /**
         * Открыть / закрыть список
         */
        toggleDropdown() {
            this.isOpen = !this.isOpen;

            if (this.isOpen) {
                if (this.selectedSalon) {
                    this.searchQuery = this.selectedSalon.name;
                } else {
                    this.searchQuery = '';
                }
            }
        },

        /**
         * Выбор салона
         */
        selectSalon(salon) {
            this.selectedSalon = salon;
            this.searchQuery = salon.name;
            this.isOpen = false;
        },

        /**
         * Очистить выбор
         */
        clearSelection() {
            this.selectedSalon = null;
            this.searchQuery = '';
            this.isOpen = true;
        },

        /**
         * Закрыть список
         */
        closeDropdown() {
            setTimeout(() => {
                this.isOpen = false;
            }, 200);
        },
        preparationQueryString() {

            clearTimeout(this.debounceTimer);

            const query = this.searchQuery.trim().toLowerCase();

            if (query.length < 2) return;
            this.debounceTimer = setTimeout(() => {
                this.searchToDatabase(query);
            }, 500);
        },
        async searchToDatabase(query) {
            try {
                const { data } = await axios.get('/salons/search', {
                    params: { query: query }
                });
                if (typeof data.res !== "object") {
                    this.message = data.res;
                } else {
                    this.result = data.res;
                }
            } catch (error) {
                this.error = error
            }
        },

        /**
         * Вход в конференцию
         */
        async enterConference() {
            if (!this.selectedSalon || this.loading) {
                return;
            }

            this.loading = true;

            try {
                const response = await axios.post(
                    route('conferences.salon.store'),
                    {
                        conference_id: this.conference.id,
                        salon_id: this.selectedSalon.id,
                        visitor_token: this.visitorToken
                    }
                );

                const token = response.data.token;

                // 12 часов
                const expiresAt = Date.now() + (12 * 60 * 60 * 1000);

                localStorage.setItem(
                    this.storageKey,
                    JSON.stringify({
                        token: token,
                        salon_id: this.selectedSalon.id,
                        expires_at: expiresAt
                    })
                );

                this.visitorToken = token;
                this.tokenExpiresAt = expiresAt;

                // Переходим в Zoom
                window.location.href = response.data.link;

            } catch (error) {
                console.error('Ошибка регистрации посещения:', error);

                if (error.response) {
                    console.error('Статус:', error.response.status);
                    console.error('Ответ:', error.response.data);
                }
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">

        <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">

            <!-- Конференция -->
            <div class="mb-6 text-center">

                <h2 class="text-xl font-semibold text-gray-800">
                    {{ conference.title }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Дата начала: {{ conference.scheduled_date }}
                </p>

            </div>

            <!-- Вход -->
            <div v-show="selectedSalon" class="text-center mb-6">

                <PrimaryButton
                    :disabled="!selectedSalon || loading"
                    @click="enterConference"
                >
                    {{ loading
                    ? 'Переходим...'
                    : 'Вход в конференцию'
                    }}
                </PrimaryButton>

            </div>

            <!-- Салон -->
            <div class="relative">

                <label
                    class="block text-sm font-medium text-gray-700 mb-2 text-center"
                >
                    Выберите салон
                </label>

                <div class="relative">

                    <input
                        type="text"
                        v-model="searchQuery"
                        @focus="isOpen = true"
                        @blur="closeDropdown"
                        @input="isOpen = true"
                        :placeholder="
                            selectedSalon
                                ? ''
                                : 'Выберите салон'
                        "
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-3 pr-10 py-2"
                    />

                    <button
                        v-if="selectedSalon"
                        @click.prevent.stop="clearSelection"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                        type="button"
                    >
                        ✕
                    </button>

                </div>

                <!-- Список -->
                <div
                    v-if="isOpen"
                    class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
                >

                    <div
                        v-if="filteredSalons.length === 0"
                        class="px-4 py-2 text-sm text-gray-500"
                    >
                        Ничего не найдено
                    </div>

                    <div
                        v-for="salon in filteredSalons"
                        :key="salon.id"
                        @click="selectSalon(salon)"
                        class="px-4 py-2 cursor-pointer hover:bg-indigo-50 transition"
                        :class="{
                            'bg-indigo-50':
                                selectedSalon &&
                                selectedSalon.id === salon.id
                        }"
                    >
                        {{ salon.region }} | {{ salon.name }}
                    </div>

                </div>

            </div>

            <!-- Информация о восстановленном посещении -->
            <div
                v-if="visitorToken && selectedSalon"
                class="mt-4 text-center text-sm text-gray-500"
            >
                Салон:
                <span class="font-medium text-gray-700">
                    {{ selectedSalon.region }} | {{ selectedSalon.name }}
                </span>
            </div>

        </div>

    </div>
</template>
