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
        regions: Array,
        default: []
    },
    data() {
        return {
            name: '',
            showNameInput: false
        }
    },
    methods: {
        toggleShowNameInput() {
            this.showNameInput = !this.showNameInput
        },
        storeRegion() {
            this.$inertia.post(route('regions.store'), {
                name: this.name
            })
            this.name = '';
            this.showNameInput = !this.showNameInput
        },
        deleteRegion(region){
            this.$inertia.delete(route('regions.destroy', {region: region}))
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
                        Регионы
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Управление регионами, к которым относятся салоны
                    </p>
                </div>

                <PrimaryButton @click.prevent="toggleShowNameInput">
                    {{ showNameInput ? 'Отмена' : '+ Добавить регион' }}
                </PrimaryButton>
            </div>


            <!-- Форма -->
            <div
                v-show="showNameInput"
                class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
            >
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="font-semibold text-gray-800">
                        Новый регион
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Укажите название региона
                    </p>
                </div>

                <div class="p-5">

                    <InputLabel>
                        Название региона
                    </InputLabel>

                    <TextInput
                        v-model="name"
                        class="mt-1 w-full"
                        placeholder="Например: Москва"
                    />

                    <div class="mt-4 flex justify-end">
                        <SecondaryButton
                            :disabled="!name.trim()"
                            @click.prevent="storeRegion"
                        >
                            Сохранить
                        </SecondaryButton>
                    </div>

                </div>
            </div>


            <!-- Список -->
            <div
                v-show="!showNameInput"
                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
            >

                <!-- Заголовок таблицы -->
                <div
                    class="hidden sm:grid grid-cols-[1fr_auto] gap-4 border-b border-gray-200 bg-gray-50 px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500"
                >
                    <span>
                        Регион
                    </span>

                    <span>
                        Действия
                    </span>
                </div>


                <!-- Регионы -->
                <div
                    v-for="region in regions"
                    :key="region.id"
                    class="grid grid-cols-1 gap-3 border-b border-gray-100 px-5 py-4 last:border-b-0 sm:grid-cols-[1fr_auto] sm:items-center"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600"
                        >
                            📍
                        </div>

                        <div>
                            <p class="font-medium text-gray-800">
                                {{ region.name }}
                            </p>

                            <p class="text-xs text-gray-400">
                                Регион #{{ region.id }}
                            </p>
                        </div>

                    </div>


                    <div class="flex justify-end">

                        <button
                            @click.prevent="deleteRegion(region)"
                            class="rounded-md px-3 py-1.5 text-sm text-red-500 transition hover:bg-red-50 hover:text-red-700"
                        >
                            Удалить
                        </button>

                    </div>

                </div>


                <!-- Пусто -->
                <div
                    v-if="!regions.length"
                    class="px-5 py-10 text-center"
                >
                    <div class="mb-3 text-4xl">
                        📍
                    </div>

                    <p class="font-medium text-gray-700">
                        Регионов пока нет
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        Добавьте первый регион
                    </p>
                </div>

            </div>

        </template>
    </UserLayout>
</template>

<style scoped>

</style>
