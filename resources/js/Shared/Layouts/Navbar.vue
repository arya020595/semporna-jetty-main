<script setup>
import { computed } from "vue";
import { usePage, Link } from "@inertiajs/vue3";

const user = computed(() => usePage().props.authUser);

const appBaseUrl = usePage().props.appBaseUrl;
</script>

<template>
    <nav class="sb-topnav navbar navbar-expand bg-white shadow-sm">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3 d-flex align-items-center" href="#">
            <img
                alt="Sabah Logo"
                :src="appBaseUrl + '/assets/images/Coat_of_arms_of_Sabah.jpg'"
                class="brand-image"
            />
            <div class="has-line">&nbsp;</div>
            <img
                alt="Semporna Logo"
                :src="appBaseUrl + '/assets/images/semporna-logo.png'"
                class="brand-image secondary"
            />
        </a>
        <!-- Sidebar Toggle-->
        <button
            class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0"
            id="sidebarToggle"
            href="#!"
        >
            <i class="fas fa-bars"></i>
        </button>
        <!-- Navbar Search-->
        <form
            class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0"
        ></form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-lg-4 align-items-center">
            <li class="nav-item dropdown">
                <a
                    class="nav-link d-flex align-items-center justify-content-center"
                    id="navbarDropdown"
                    href="#"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <span v-if="user.picture" class="profile-picture">
                        <img :src="user.picture" class="preview-picture" />
                    </span>
                    <i v-else class="fas fa-user fa-fw"></i>
                    <span class="ms-2 d-none d-lg-block">{{ user.name }}</span>
                </a>
                <ul
                    class="dropdown-menu dropdown-menu-end"
                    aria-labelledby="navbarDropdown"
                >
                    <li>
                        <Link class="dropdown-item" href="/profile">
                            User Account
                        </Link>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li>
                        <Link
                            class="dropdown-item"
                            href="/logout"
                            method="post"
                            as="button"
                        >
                            Logout
                        </Link>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<style scoped>
.has-line {
    position: relative;
}

.has-line::after {
    content: "";
    position: absolute;
    border-right: 1px solid #000;
    top: 0px;
    bottom: 0px;
}
</style>
