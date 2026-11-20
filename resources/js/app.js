import "./public/header_navbar";
import "./public/header_profile";
import "./public/button_scroll_to";

import "./public/landing-form_soso";
import "./public/landing-form-slider";
import "./public/landing-form-phonemask";
import "./public/landing-form-emailmask";

import "./public/applicant-form-sliders";
import "./public/applicant-form-personal-code-mask";
import "./public/applicant-form-zipcode-mask";
import "./public/applicant-form-type-dropdown";
import "./public/applicant-form-address-autocomplete";

import "./public/cookies_notification";

import Alpine from "alpinejs";
import "./public/loan-widget";
window.Alpine = Alpine;
Alpine.start();

import Swiper from "swiper";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";

document.addEventListener("DOMContentLoaded", () => {
    const el = document.querySelector(".reviews-swiper");
    if (!el) return;

    new Swiper(el, {
        modules: [Autoplay, Pagination],
        slidesPerView: 1,
        spaceBetween: 16,
        grabCursor: true,
        loop: true,
        speed: 600,

        autoplay: {
            delay: 2000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },

        pagination: {
            el: ".reviews-swiper .swiper-pagination",
            clickable: true,
        },

        breakpoints: {
            640: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
            1280: { slidesPerView: 5 },
        },
        on: {
            init(swiper) {
                swiper.el.classList.remove("invisible");
            },
        },
    });
});
