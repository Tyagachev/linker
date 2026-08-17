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

            <h1 class="text-black">Список салонов</h1>
            <PrimaryButton @click.prevent="toggleShowNameInput">Добавить салон</PrimaryButton>
            <div class="flex gap-2">

            </div>
            <div v-show="showNameInput" class="mt-4">
                <InputLabel>Название салона (от 3х символов)</InputLabel>

                <div class="flex gap-2">
                    <select
                        v-model="region"
                        class="w-1/3 rounded-md border-gray-300 shadow-sm"
                    >
                        <option :value="null" disabled>
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
                        class="flex-1"
                        placeholder="Название салона"
                    />
                </div>

                <SecondaryButton
                    v-if="name.length >= 3 && region"
                    @click.prevent="storeSalon"
                    class="mt-2"
                >
                    Сохранить
                </SecondaryButton>
            </div>
            <div v-show="!showNameInput" v-for="salon in salons">
                <div class="p-1 flex justify-between mt-2 rounded-md border border-gray-300">
                    <div class="flex">
                        <div>
                            <p class="text-sm text-black mx-3">{{ salon.region }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-black">{{ salon.name }}</p>
                        </div>

                    </div>
                    <div>
                        <a @click.prevent="deleteSalon(salon)" class="block text-sm text-red-400 hover:text-red-500">Удалить</a>
                    </div>
                </div>
            </div>
        </template>
    </UserLayout>
</template>

<style scoped>

</style>
