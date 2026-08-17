<template>
    <AuthenticatedLayout>
        <div class="py-4">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Фиксируем высоту карточки, например 500px или 70vh -->
                <div class="overflow-hidden shadow-sm sm:rounded-lg flex flex-col">
                    <div class="p-1 flex-1 min-h-0">
                        <!-- flex-1 + min-h-0 позволяют внутренним колонкам скроллиться -->
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Левое меню с собственным скроллом -->
                            <aside class="w-full md:w-64 rounded-lg text-white shrink-0 self-start">
                                <!--ПРОФИЛЬ ЮЗЕРА-->
                                <div>
                                    <UserProfile :name="user.name"/>
                                </div>
                                <slot name="sidebar">
                                    <!--РОУТЫ ЮЗЕРА-->
                                    <div>
                                        <UserRoute v-for="link in usr.links" :link="link"/>
                                    </div>
                                </slot>

                            </aside>
                            <!-- Правый контент со своим скроллом -->
                            <main class="flex-1 rounded-md p-1 min-w-0 bg-white overflow-y-auto p-4">
                                <slot name="content" />
                            </main>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link} from "@inertiajs/vue3";
import { usePage } from '@inertiajs/vue3';
import UserProfile from "@/Components/UserProfile/UserProfile.vue";
import UserRoute from "@/Components/UserRoute.vue";
export default {
    name: "UserLayout",
    components: {UserRoute, UserProfile, AuthenticatedLayout, Link},
    data() {
      return {
          usr: {
              links: [
                  {
                      title: 'Конференции',
                      name: 'conferences.index',
                      image: '<i class="fa-solid fa-book"></i>'
                  },
                  {
                      title: 'Салоны',
                      name: 'salons.index',
                      image: '<i class="fa-solid fa-book-open"></i>'
                  },
                  {
                      title: 'Регионы',
                      name: 'regions.index',
                      image: '<i class="fa-solid fa-layer-group"></i>'
                  },
              ]
          },
      }
    },
    computed: {
        user() {
            const page = usePage();
            return page.props.auth.user || {};
        }
    }
}
</script>

<style scoped>

</style>
