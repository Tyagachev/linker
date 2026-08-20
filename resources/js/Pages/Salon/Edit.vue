<script>
import UserLayout from "@/Layouts/UserLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

export default {
    name: "Edit",
    components: {TextInput, SecondaryButton, InputLabel, UserLayout},
    props: {
        salon: {
            type: Object,
            default:() => {}
        },
        regions: {
            type: Array,
            default: () => []
        },
    },
    data() {
        return {
            name: this.salon.name,
            region: this.regions.find(r => r.name === this.salon.region) || null
        }
    },
    methods: {
        updateSalon() {
            this.$inertia.put(route('salons.update', {salon: this.salon.id}), {
                name: this.name,
                region: this.region.name
            })
        }
    }
}
</script>

<template>
    <UserLayout>
        <template #content>
            <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm" >

                <div class="border-b border-gray-100 px-5 py-4">

                    <h2 class="font-semibold text-gray-800">
                        Редактирование салона
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
                            @click.prevent="updateSalon"
                        >
                            Сохранить
                        </SecondaryButton>

                    </div>

                </div>

            </div>

        </template>
    </UserLayout>
</template>

<style scoped>

</style>
