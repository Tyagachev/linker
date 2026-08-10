<template>
    <div class="bg-neutral-900">
        <nav class="space-y-2">
            <Link :class="['block px-2 py-2 hover:bg-neutral-800', route().current(link.name) ? 'bg-neutral-800 border-2 border-neutral-900 border-r-indigo-500' : '']" :href="route(link.name)">
                <span v-html="link.image"></span>
                <span class="px-2 text-sm text-neutral-300">{{link.title}}</span>
            </Link>
        </nav>
    </div>
</template>

<script>
import {Link} from "@inertiajs/vue3";

export default {
    name: "UserRoute",
    components: {Link},
    props: {
        link: {
            type: Object,
            default: {}
        }
    }
}
</script>

<style scoped>

</style>
