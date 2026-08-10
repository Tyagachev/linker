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
        salons: Array,
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
        storeSalon() {
            this.$inertia.post(route('salons.store'), {
                name: this.name
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
            <div v-show="showNameInput">
                <div class="mt-4">
                    <InputLabel>Название салона (от 3х символов)</InputLabel>
                    <TextInput class="w-full required" v-model="name">saaas</TextInput>
                    <SecondaryButton v-if="name.length > 3" @click.prevent="storeSalon" class="mt-2">Сохранить</SecondaryButton>
                </div>
            </div>
            <div v-for="salon in salons">
                <div class="p-1 flex justify-between mt-2 rounded-md border border-gray-300">
                    <div>
                        <p class="text-sm text-black">{{ salon.name }}</p>
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
