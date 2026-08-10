<template>
    <div>
        <div class="p-2 hover:bg-neutral-800 mb-2 bg-neutral-900 flex items-center size-15 w-full">
            <div>
                <div class="avatar rounded-full border border-neutral-900 flex-shrink-0 grid place-items-center text-slate-900 font-bold"
                     :style="{ background: avatarColor(name) }">
                    <p class="author">{{ name ? name[0].toUpperCase() : '' }}</p>
                </div>
            </div>

            <div class="ml-2">
                <p class="text-neutral-300">{{name}}</p>
            </div>
        </div>
    </div>
</template>

<script>
import {Link} from "@inertiajs/vue3";
import {avatarColor} from "@/utils/avatarColor.js";
export default {
    name: "UserProfile",
    components: {Link},
    props: {
        name: {
            type: String,
            default: ''
        }
    },
    methods: {
        avatarColor,
    },
}
</script>

<style scoped>
.avatar {
    width: 60px;
    height: 60px;
    border-radius: 100%;
}

.avatar_img {
    width: 60px;
    height: 60px;
}

.author {
    color: black;
    font-size: 30px;
    margin-right: 0;
    font-weight: bold;
}
</style>
