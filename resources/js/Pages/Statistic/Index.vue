<script>
import UserLayout from "@/Layouts/UserLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import axios from "axios";

export default {
    name: "Index",

    components: {
        PrimaryButton,
        UserLayout
    },

    props: {
        statistics: {
            type: Array,
            default: () => []
        }
    },

    data() {
        return {
            current: '',
            last: '',

            results: [],
            allSalons: [],

            loading: false,

            // Какая секция открыта
            openSection: {}
        };
    },

    mounted() {
        this.setStatistics();
        this.getStatistics();
    },

    methods: {

        /**
         * Даты по умолчанию — сегодняшний день
         */
        setStatistics() {
            const formattedDate = new Date()
                .toISOString()
                .split('T')[0];

            this.current = formattedDate;
            this.last = formattedDate;
        },

        /**
         * Получение статистики
         */
        async getStatistics() {
            this.loading = true;

            try {
                const { data } = await axios.get(
                    route('conferences.statistics.show', {
                        current: this.current,
                        last: this.last
                    })
                );

                this.results = data.statistics || [];
                this.allSalons = data.allSalons || [];

                // После нового поиска закрываем все секции
                this.openSection = {};

            } catch (error) {
                console.error(
                    'Ошибка получения статистики:',
                    error
                );
            } finally {
                this.loading = false;
            }
        },

        /**
         * Открыть / закрыть секцию
         *
         * Одновременно открывается только одна.
         */
        toggleSection(conferenceId, section) {
            const key = `${conferenceId}_${section}`;

            if (this.openSection[key]) {
                this.openSection = {};
            } else {
                this.openSection = {
                    [key]: true
                };
            }
        },

        /**
         * Проверка открыта ли секция
         */
        isSectionOpen(conferenceId, section) {
            return !!this.openSection[
                `${conferenceId}_${section}`
                ];
        },

        /**
         * Группировка салонов
         */
        groupSalons(items) {
            return Object.values(
                items.reduce((acc, salon) => {

                    const key = `${salon.region}_${salon.name}`;

                    if (!acc[key]) {
                        acc[key] = {
                            region: salon.region,
                            name: salon.name,
                            count: 0,
                            items: []
                        };
                    }

                    acc[key].count++;
                    acc[key].items.push(salon);

                    return acc;

                }, {})
            );
        },

        /**
         * Статистика конкретной конференции
         */
        getConferenceStats(conference) {
            const deadline = new Date(
                conference.deadline_at
            );

            const salons = conference.salons || [];

            /*
             * Успели:
             * время посещения <= дедлайна
             */
            const visited = salons.filter(salon => {
                return new Date(
                    salon.pivot.created_at
                ) <= deadline;
            });

            /*
             * Опоздали:
             * время посещения > дедлайна
             */
            const late = salons.filter(salon => {
                return new Date(
                    salon.pivot.created_at
                ) > deadline;
            });

            /*
             * Все салоны, которые вообще посещали конференцию.
             */
            const visitedSalonIds = new Set(
                salons.map(salon => salon.id)
            );

            /*
             * Не заходили:
             * салон отсутствует среди посетивших.
             */
            const notVisited = this.allSalons.filter(
                salon => !visitedSalonIds.has(salon.id)
            );

            return {
                visitedCount: visited.length,
                lateCount: late.length,
                notVisitedCount: notVisited.length,

                visitedGroups: this.groupSalons(visited),
                lateGroups: this.groupSalons(late),
                notVisitedGroups: this.groupSalons(notVisited),

                visited,
                late,
                notVisited
            };
        }
    }
};
</script>


<template>
    <UserLayout>

        <template #content>

            <!-- Заголовок -->
            <div class="mb-6">

                <h1 class="text-2xl font-semibold text-gray-800">
                    Статистика конференций
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Выберите период, за который необходимо сформировать отчёт
                </p>

            </div>


            <!-- Фильтр -->
            <div
                class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
            >

                <div
                    class="flex flex-col gap-4 md:flex-row md:items-end"
                >

                    <!-- С -->
                    <div class="flex-1">

                        <label
                            for="current"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            С даты
                        </label>

                        <input
                            id="current"
                            v-model="current"
                            type="date"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />

                    </div>


                    <!-- По -->
                    <div class="flex-1">

                        <label
                            for="last"
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            По дату
                        </label>

                        <input
                            id="last"
                            v-model="last"
                            type="date"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />

                    </div>


                    <!-- Кнопка -->
                    <div>

                        <PrimaryButton
                            @click="getStatistics"
                            :disabled="loading"
                            class="w-full justify-center md:w-auto"
                        >
                            <span v-if="loading">
                                Загрузка...
                            </span>

                            <span v-else>
                                Показать
                            </span>
                        </PrimaryButton>

                    </div>

                </div>

            </div>


            <!-- Нет результатов -->
            <div
                v-if="!loading && results.length === 0"
                class="rounded-xl border border-gray-200 bg-white p-10 text-center shadow-sm"
            >

                <div class="mb-3 text-4xl">
                    📊
                </div>

                <h3 class="font-medium text-gray-800">
                    Конференций не найдено
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    За выбранный период конференций нет.
                </p>

            </div>


            <!-- Конференции -->
            <div
                v-for="conf in results"
                :key="conf.id"
                class="mb-5 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
            >

                <!-- Заголовок конференции -->
                <div
                    class="border-b border-gray-100 bg-gray-50 px-5 py-4"
                >

                    <div
                        class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
                    >

                        <div>

                            <h2 class="text-lg font-semibold text-gray-800">
                                {{ conf.title }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">
                                Дата начала:
                                <span class="font-medium text-gray-700">
                                    {{ conf.scheduled_date }}
                                </span>
                            </p>
                            <div v-if="conf.comment">
                                <p class="mt-1 text-sm text-gray-500">
                                    Комментарий: <span class="text-orange-500">{{ conf.comment }}</span>
                                </p>
                            </div>

                        </div>


                        <!-- Дедлайн -->
                        <div
                            class="rounded-lg border border-orange-100 bg-orange-50 px-3 py-2"
                        >

                            <p class="text-xs text-orange-600">
                                Успеть до
                            </p>

                            <p class="font-semibold text-orange-700">
                                {{ conf.dead }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- Статистика -->
                <div class="divide-y divide-gray-100">


                    <!-- ===================================== -->
                    <!-- УСПЕЛИ -->
                    <!-- ===================================== -->

                    <div>

                        <button
                            type="button"
                            @click="toggleSection(conf.id, 'visited')"
                            class="flex w-full items-center justify-between px-5 py-4 text-left transition hover:bg-green-50"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600"
                                >
                                    ✓
                                </div>

                                <div>

                                    <p class="font-medium text-gray-800">
                                        Успели до дедлайна
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Посетили конференцию вовремя
                                    </p>

                                </div>

                            </div>


                            <div class="flex items-center gap-3">

                                <span
                                    class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700"
                                >
                                    {{ getConferenceStats(conf).visitedCount }}
                                </span>

                                <span
                                    class="text-sm text-gray-400 transition-transform duration-200"
                                    :class="{
                                        'rotate-180':
                                            isSectionOpen(
                                                conf.id,
                                                'visited'
                                            )
                                    }"
                                >
                                    ▼
                                </span>

                            </div>

                        </button>


                        <!-- Таблица -->
                        <div
                            v-if="isSectionOpen(conf.id, 'visited')"
                            class="bg-gray-50 px-5 py-4"
                        >

                            <div
                                v-if="getConferenceStats(conf).visitedGroups.length"
                                class="overflow-hidden rounded-lg border border-gray-200 bg-white"
                            >

                                <table class="w-full text-sm">

                                    <thead class="bg-gray-50">
                                    <tr>

                                        <th
                                            class="px-4 py-3 text-left font-medium text-gray-500"
                                        >
                                            Регион
                                        </th>

                                        <th
                                            class="px-4 py-3 text-left font-medium text-gray-500"
                                        >
                                            Салон
                                        </th>

                                        <th
                                            class="px-4 py-3 text-right font-medium text-gray-500"
                                        >
                                            Количество
                                        </th>

                                    </tr>
                                    </thead>


                                    <tbody class="divide-y divide-gray-100">

                                    <tr
                                        v-for="group in getConferenceStats(conf).visitedGroups"
                                        :key="group.region + group.name"
                                        class="transition hover:bg-green-50"
                                    >

                                        <td class="px-4 py-3 text-gray-600">
                                            {{ group.region }}
                                        </td>

                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ group.name }}
                                        </td>

                                        <td
                                            class="px-4 py-3 text-right font-semibold text-green-600"
                                        >
                                            {{ group.count }}
                                        </td>

                                    </tr>

                                    </tbody>

                                </table>

                            </div>


                            <div
                                v-else
                                class="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-center text-sm text-gray-500"
                            >
                                Никто не успел до дедлайна.
                            </div>

                        </div>

                    </div>


                    <!-- ===================================== -->
                    <!-- ОПОЗДАЛИ -->
                    <!-- ===================================== -->

                    <div>

                        <button
                            type="button"
                            @click="toggleSection(conf.id, 'late')"
                            class="flex w-full items-center justify-between px-5 py-4 text-left transition hover:bg-orange-50"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600"
                                >
                                    !
                                </div>

                                <div>

                                    <p class="font-medium text-gray-800">
                                        Опоздали
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Посетили конференцию после дедлайна
                                    </p>

                                </div>

                            </div>


                            <div class="flex items-center gap-3">

                                <span
                                    class="rounded-full bg-orange-100 px-3 py-1 text-sm font-semibold text-orange-700"
                                >
                                    {{ getConferenceStats(conf).lateCount }}
                                </span>

                                <span
                                    class="text-sm text-gray-400 transition-transform duration-200"
                                    :class="{
                                        'rotate-180':
                                            isSectionOpen(
                                                conf.id,
                                                'late'
                                            )
                                    }"
                                >
                                    ▼
                                </span>

                            </div>

                        </button>


                        <div
                            v-if="isSectionOpen(conf.id, 'late')"
                            class="bg-gray-50 px-5 py-4"
                        >

                            <div
                                v-if="getConferenceStats(conf).lateGroups.length"
                                class="overflow-hidden rounded-lg border border-gray-200 bg-white"
                            >

                                <table class="w-full text-sm">

                                    <thead class="bg-gray-50">
                                    <tr>

                                        <th
                                            class="px-4 py-3 text-left font-medium text-gray-500"
                                        >
                                            Регион
                                        </th>

                                        <th
                                            class="px-4 py-3 text-left font-medium text-gray-500"
                                        >
                                            Салон
                                        </th>

                                        <th
                                            class="px-4 py-3 text-right font-medium text-gray-500"
                                        >
                                            Количество
                                        </th>

                                    </tr>
                                    </thead>


                                    <tbody class="divide-y divide-gray-100">

                                    <tr
                                        v-for="group in getConferenceStats(conf).lateGroups"
                                        :key="group.region + group.name"
                                        class="transition hover:bg-orange-50"
                                    >

                                        <td class="px-4 py-3 text-gray-600">
                                            {{ group.region }}
                                        </td>

                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ group.name }}
                                        </td>

                                        <td
                                            class="px-4 py-3 text-right font-semibold text-orange-600"
                                        >
                                            {{ group.count }}
                                        </td>

                                    </tr>

                                    </tbody>

                                </table>

                            </div>


                            <div
                                v-else
                                class="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-center text-sm text-gray-500"
                            >
                                Опоздавших нет.
                            </div>

                        </div>

                    </div>


                    <!-- ===================================== -->
                    <!-- НЕ ЗАХОДИЛИ -->
                    <!-- ===================================== -->

                    <div>

                        <button
                            type="button"
                            @click="toggleSection(conf.id, 'notVisited')"
                            class="flex w-full items-center justify-between px-5 py-4 text-left transition hover:bg-red-50"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600"
                                >
                                    ×
                                </div>

                                <div>

                                    <p class="font-medium text-gray-800">
                                        Не заходили
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Посещения не зафиксированы
                                    </p>

                                </div>

                            </div>


                            <div class="flex items-center gap-3">

                                <span
                                    class="rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-700"
                                >
                                    {{ getConferenceStats(conf).notVisitedCount }}
                                </span>

                                <span
                                    class="text-sm text-gray-400 transition-transform duration-200"
                                    :class="{
                                        'rotate-180':
                                            isSectionOpen(
                                                conf.id,
                                                'notVisited'
                                            )
                                    }"
                                >
                                    ▼
                                </span>

                            </div>

                        </button>


                        <div
                            v-if="isSectionOpen(conf.id, 'notVisited')"
                            class="bg-gray-50 px-5 py-4"
                        >

                            <div
                                v-if="getConferenceStats(conf).notVisitedGroups.length"
                                class="overflow-hidden rounded-lg border border-gray-200 bg-white"
                            >

                                <table class="w-full text-sm">

                                    <thead class="bg-gray-50">
                                    <tr>

                                        <th
                                            class="px-4 py-3 text-left font-medium text-gray-500"
                                        >
                                            Регион
                                        </th>

                                        <th
                                            class="px-4 py-3 text-left font-medium text-gray-500"
                                        >
                                            Салон
                                        </th>

                                        <th
                                            class="px-4 py-3 text-right font-medium text-gray-500"
                                        >
                                            Количество
                                        </th>

                                    </tr>
                                    </thead>


                                    <tbody class="divide-y divide-gray-100">

                                    <tr
                                        v-for="group in getConferenceStats(conf).notVisitedGroups"
                                        :key="group.region + group.name"
                                        class="transition hover:bg-red-50"
                                    >

                                        <td class="px-4 py-3 text-gray-600">
                                            {{ group.region }}
                                        </td>

                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ group.name }}
                                        </td>

                                        <td
                                            class="px-4 py-3 text-right font-semibold text-red-600"
                                        >
                                            {{ group.count }}
                                        </td>

                                    </tr>

                                    </tbody>

                                </table>

                            </div>


                            <div
                                v-else
                                class="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-center text-sm text-gray-500"
                            >
                                Все салоны посетили конференцию.
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </template>

    </UserLayout>
</template>

<style scoped>

</style>
