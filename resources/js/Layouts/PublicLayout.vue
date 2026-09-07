<script setup>
import { usePage } from "@inertiajs/vue3";

const appBaseUrl = usePage().props.appBaseUrl;
</script>

<template>
    <section id="header" class="text-center">
        <img
            alt="logo-stb"
            class="logo"
            :src="appBaseUrl + '/assets/images/feel-sabah.svg'"
        />
        <h3>Sabah Tourism Board</h3>
    </section>
    <section id="content">
        <div class="container">
            <main class="mb-5">
                <slot />
            </main>
        </div>
    </section>
    <section id="footer" class="text-center">
        <div class="container">
            <div>
                <img
                    alt="logo-stb"
                    class="footer-logo"
                    :src="appBaseUrl + '/assets/images/stb-logo.jpg'"
                />
                <small class="d-block"
                    >Copyright © 2024, Sabah Tourism Board</small
                >
            </div>
        </div>
    </section>
</template>

<style scoped>
#header {
    background: linear-gradient(rgb(255 255 255 / 10%), rgb(255 255 255)),
        url(/assets/images/kinabatangan-4.jpg);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    padding: 80px 0px;
}

#header .logo {
    max-width: 500px;
}

#content {
    margin-top: -1px;
    background: white;
}

#footer {
    height: 200px;
}

#footer .footer-logo {
    height: 150px;
}
</style>
