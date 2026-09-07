<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const activeMenuCode = computed(() => usePage().props.activePublicMenuCode);
</script>
<template>
    <nav class="navbar navbar-expand-lg navbar-light my-3">
        <div class="container flex-column flex-md-row">
            <a
                class="navbar-brand public-navbar-brand text-center text-md-start m-0"
                href="#"
            >
                <img
                    src="/assets/images/lkm-logo.png"
                    class="brand-image"
                    alt="Brand Image"
                />
                <span class="d-block d-md-inline-block"
                    >COCOA GERMPLASM DATABASE</span
                >
            </a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div
                class="collapse navbar-collapse justify-content-end w-100"
                tabindex="-1"
                id="navbarNavDropdown"
            >
                <ul class="navbar-nav public-navbar">
                    <li class="nav-item">
                        <a class="nav-link" href="/">HOME</a>
                    </li>
                    <li
                        class="nav-item"
                        :class="{
                            active: activeMenuCode == 'search',
                        }"
                    >
                        <a class="nav-link" href="/search">SEARCH</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/page/about-us">ABOUT US</a>
                    </li>
                    <li
                        class="nav-item"
                        :class="{
                            active: activeMenuCode == 'inquiries',
                        }"
                    >
                        <a class="nav-link" href="/inquiries">INQUIRIES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">LOGIN</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<style scoped>
.public-navbar .nav-item {
    font-family: Inter;
    font-weight: bold;
    font-size: 0.9rem;
}

.public-navbar .nav-link {
    font-size: 16px;
    padding: 13px 20px;
    color: #000;
    position: relative;
    font-weight: 600;
}

.public-navbar .nav-item.active .nav-link,
.public-navbar .nav-link:hover {
    color: #fff;
    background-color: #55595c;
}

@media screen and (min-width: 992px) {
    .public-navbar .nav-item.active .nav-link,
    .public-navbar .nav-link:hover {
        color: #61ce70;
        background-color: #fff;
    }

    .public-navbar .nav-item.active .nav-link:after,
    .public-navbar .nav-link:hover:after {
        position: absolute;
        content: "";
        height: 3px;
        width: 100%;
        left: 0;
        background-color: #61ce70;
        z-index: 2;
        bottom: 0;
    }
}
.public-navbar-brand {
    font-family: Inter;
    font-weight: 600;
    font-size: 22px;
    letter-spacing: -0rem;
}

@media screen and (max-width: 600px) {
    .public-navbar-brand {
        font-size: 0.9rem;
    }
}
</style>
