/**
 * JS scripts required in only in admin area
 */

import './admin/apexcharts';
import flatpickr from "flatpickr";
import './admin/admincp_sidebar';
import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()

/**
 * Admincp - Datepicker apply daterange selector to elements
 * @type {HTMLElement}
 */
let datepicker_element = document.getElementById('datepickr-element');
if (datepicker_element) {
    flatpickr(datepicker_element, {
        dateFormat: "d.m.Y",
        mode: "range"
    });
}

/**
 * Admincp - Preview uploadable image on the page
 * @type {NodeListOf<Element>}
 */
let watchable_elements = document.querySelectorAll('.previewUploadableImage');
if (watchable_elements) {
    for (let watchable_element of watchable_elements) {
        watchable_element.addEventListener('change', () => {
            if (watchable_element.files && watchable_element.files[0]) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    if (watchable_element.dataset && watchable_element.dataset.previewInput) {
                        let image_input = document.getElementById(watchable_element.dataset.previewInput);
                        image_input.src = e.target.result;
                        image_input.alt = "Logo";
                    }

                    if (watchable_element.dataset && watchable_element.dataset.previewInputContainer) {
                        let image_input_container = document.getElementById(watchable_element.dataset.previewInputContainer);
                        image_input_container.classList.add("h-48");
                        image_input_container.classList.remove("hidden");
                    }
                };
                reader.readAsDataURL(watchable_element.files[0]);
            }
        })
    }
}
