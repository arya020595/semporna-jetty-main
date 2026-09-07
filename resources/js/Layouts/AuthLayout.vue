<script setup>
import { usePage } from "@inertiajs/vue3";

const appBaseUrl = usePage().props.appBaseUrl;
</script>

<template>
    <div class="min-vh-100 d-flex flex-lg-row-reverse flex-column-reverse">
        <div id="left-container" class="d-flex justify-content-center mx-3">
            <div class="my-lg-auto row justify-content-center flex-grow-1">
                <div class="col-12 col-md-8">
                    <div class="text-center mb-5">
                        <div
                            class="circle-wrapper mx-auto d-flex justify-content-center align-items-center"
                        >
                            <img
                                :src="
                                    appBaseUrl +
                                    '/assets/images/Coat_of_arms_of_Sabah.jpg'
                                "
                                class="me-3 logo-sabah"
                                alt="STB Logo"
                            />
                            <img
                                id="logo"
                                :src="
                                    appBaseUrl +
                                    '/assets/images/semporna-logo.png'
                                "
                                class="logo-semporna"
                                alt="Semporna Logo"
                            />
                        </div>
                        <div id="logo-title" class="text-center"></div>
                    </div>

                    <slot />
                </div>
            </div>
        </div>

        <div id="right-container" class="d-flex flex-column">
            <div class="overlay d-flex align-items-center">
                <div class="row w-100 justify-content-center">
                    <div class="col-10">
                        <div class="d-none d-lg-block">
                            <div class="logo-feel-sabah text-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
#left-container {
    flex-grow: 2;
    z-index: 2;
}

@media only screen and (min-width: 1000px) {
    #left-container {
        max-width: 562px;
        margin-top: 25px;
    }
}

@media only screen and (max-width: 990px) {
    #left-container {
        margin-top: -50px;
    }
}

.logo-sabah {
    height: 140px;
}

.logo-semporna {
    height: 150px;
}

.circle-wrapper {
    border-radius: 19px;
    height: 170px;
    width: 270px;
    background: white;
}

#left-container #logo-title {
    font-weight: bold;
}

#right-container {
    background-image: url("/assets/images/semporna-backdrop.jpg");
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    flex-grow: 3;
    min-height: 200px;
    z-index: 0;
}

#right-container .overlay {
    background: linear-gradient(
        360deg,
        rgba(0, 0, 0, 0) 0%,
        rgba(255, 255, 255, 0) 122.37%
    );
    flex-grow: 1;
}

#right-container h3 {
    font-size: 3rem;
    line-height: 3.8rem;
    color: #282828;
    font-weight: 900;
    z-index: 100;
}

#right-container .logo-feel-sabah {
    z-index: 100;
}

#right-container .logo-feel-sabah img {
    width: 90%;
    max-height: 200px;
}

#right-container h4 {
    margin-left: 100px;
    font-size: 2rem;
    line-height: 2.8rem;
    color: #fff;
    z-index: 100;
}
</style>
