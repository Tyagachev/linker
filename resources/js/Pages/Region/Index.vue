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

            <h1 class="text-black">Регионы</h1>
            <PrimaryButton @click.prevent="toggleShowNameInput">Добавить регион</PrimaryButton>
            <div v-show="showNameInput">
                <div class="mt-4">
                    <InputLabel>Название региона</InputLabel>
                    <TextInput class="w-full required" v-model="name"></TextInput>
                    <SecondaryButton @click.prevent="storeRegion" class="mt-2">Сохранить</SecondaryButton>
                </div>
            </div>
            <div v-show="!showNameInput" v-for="region in regions">
                <div class="p-1 flex justify-between mt-2 rounded-md border border-gray-300">
                    <div>
                        <p class="text-sm text-black">{{ region.name }}</p>
                    </div>
                    <div>
                        <a @click.prevent="deleteRegion(region)" class="block text-sm text-red-400 hover:text-red-500">Удалить</a>
                    </div>
                </div>
            </div>
        </template>
    </UserLayout>
</template>

<style scoped>

</style>
