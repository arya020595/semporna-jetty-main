<script setup>
import { computed, ref } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
import SidenavWithSubmenu from "./SidenavWithSubmenu.vue";

const menus = computed(() => usePage().props.menus);

const activeMenu = ref(null);

const appBaseUrl = usePage().props.appBaseUrl;
</script>

<template>
    <div id="layoutSidenav_nav">
        <nav
            class="sb-sidenav accordion sb-sidenav-dark bg-stb"
            id="sidenavAccordion"
        >
            <div class="sb-sidenav-menu pt-4">
                <div class="nav">
                    <template v-for="menu in menus" :key="menu.id">
                        <!-- MENU TYPE LINK -->
                        <Link
                            v-if="menu.type == 0"
                            class="nav-link"
                            :class="{
                                active: $page.url.startsWith('/' + menu.code),
                            }"
                            :href="appBaseUrl + '/' + menu.code"
                            preserve-state
                        >
                            <div class="sb-nav-link-icon">
                                <span class="material-icons">{{
                                    menu.icon
                                }}</span>
                            </div>
                            {{ menu.name }}
                        </Link>

                        <!-- MENU TYPE HEADER -->
                        <div
                            v-if="menu.type == 1"
                            class="sb-sidenav-menu-heading"
                        >
                            {{ menu.name }}
                        </div>

                        <!-- MENU TYPE WITH CHILDREN -->
                        <SidenavWithSubmenu
                            v-if="menu.type == 2"
                            :menu="menu"
                        />
                    </template>
                </div>
            </div>
        </nav>
    </div>
</template>

<style scoped>
.task-notif {
    margin-left: auto;
}

.bg-stb {
    background: linear-gradient(
            92deg,
            rgba(40, 40, 40, 0.355),
            rgba(39, 39, 39, 0.225)
        ),
        url(/assets/images/semporna-menu.jpeg);
    background-size: cover; /* Scale the image to cover the entire area */
    background-position: left; /* Center the image */
    background-repeat: no-repeat;
}
</style>
