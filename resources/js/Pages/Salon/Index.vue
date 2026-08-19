<script>
import UserLayout from "@/Layouts/UserLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

export default {
    name: "Index",
    components: {SecondaryButton, InputLabel, TextInput, PrimaryButton, UserLayout},
    props: {
        regions: {
            type: Array,
            default: () => []
        },
        salons: Array,
        default: () => []
    },
    data() {
        return {
            name: '',
            showNameInput: false,
            region: null
        }
    },
    methods: {
        toggleShowNameInput() {
            this.showNameInput = !this.showNameInput
        },
        storeSalon() {
            this.$inertia.post(route('salons.store'), {
                name: this.name,
                region: this.region.name
            })
            this.name = '';
            this.showNameInput = !this.showNameInput
        },
        deleteSalon(salon){
            this.$inertia.delete(route('salons.destroy', {salon: salon.id}))
        }
    }
}
</script>

<template>
    <UserLayout>
        <template #content>

            <!-- Заголовок -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">

                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">
                        Салоны
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Управление салонами и их регионами
                    </p>
                </div>

                <PrimaryButton @click.prevent="toggleShowNameInput">
                    {{ showNameInput ? 'Отмена' : '+ Добавить салон' }}
                </PrimaryButton>

            </div>


            <!-- Форма создания -->
            <div
                v-show="showNameInput"
                class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
            >

                <div class="border-b border-gray-100 px-5 py-4">

                    <h2 class="font-semibold text-gray-800">
                        Новый салон
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Выберите регион и укажите название салона
                    </p>

                </div>


                <div class="p-5">

                    <InputLabel>
                        Регион и название салона
                    </InputLabel>


                    <div class="mt-1 grid grid-cols-1 gap-3 sm:grid-cols-3">

                        <select
                            v-model="region"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:col-span-1"
                        >

                            <option
                                :value="null"
                                disabled
                            >
                                Выберите регион
                            </option>

                            <option
                                v-for="item in regions"
                                :key="item.id"
                                :value="item"
                            >
                                {{ item.name }}
                            </option>

                        </select>


                        <TextInput
                            v-model="name"
                            class="sm:col-span-2"
                            placeholder="Название салона"
                        />

                    </div>


                    <div class="mt-4 flex justify-end">

                        <SecondaryButton
                            v-if="name.trim().length >= 3 && region"
                            @click.prevent="storeSalon"
                        >
                            Сохранить
                        </SecondaryButton>

                    </div>

                </div>

            </div>


            <!-- Список салонов -->
            <div
                v-show="!showNameInput"
                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
            >

                <!-- Заголовок -->
                <div
                    class="hidden sm:grid grid-cols-[180px_1fr_auto] gap-4 border-b border-gray-200 bg-gray-50 px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500"
                >

                    <span>
                        Регион
                    </span>

                    <span>
                        Салон
                    </span>

                    <span>
                        Действия
                    </span>

                </div>


                <!-- Салоны -->
                <div
                    v-for="salon in salons"
                    :key="salon.id"
                    class="grid grid-cols-1 gap-3 border-b border-gray-100 px-5 py-4 last:border-b-0 sm:grid-cols-[180px_1fr_auto] sm:items-center"
                >

                    <!-- Регион -->
                    <div>

                        <span
                            class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700"
                        >
                            {{ salon.region }}
                        </span>

                    </div>


                    <!-- Название -->
                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600"
                        >
                            🏢
                        </div>

                        <div>

                            <p class="font-medium text-gray-800">
                                {{ salon.name }}
                            </p>

                            <p class="text-xs text-gray-400">
                                Салон #{{ salon.id }}
                            </p>

                        </div>

                    </div>


                    <!-- Удаление -->
                    <div class="flex justify-end">

                        <button
                            @click.prevent="deleteSalon(salon)"
                            class="rounded-md px-3 py-1.5 text-sm text-red-500 transition hover:bg-red-50 hover:text-red-700"
                        >
                            Удалить
                        </button>

                    </div>

                </div>


                <!-- Пусто -->
                <div
                    v-if="!salons.length"
                    class="px-5 py-10 text-center"
                >

                    <div class="mb-3 text-4xl">
                        🏢
                    </div>

                    <p class="font-medium text-gray-700">
                        Салонов пока нет
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        Добавьте первый салон
                    </p>

                </div>

            </div>

        </template>
    </UserLayout>
</template>

<style scoped>

</style>
