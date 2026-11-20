import IMask from "imask";

let body = document.body;

if (body && body.dataset && body.dataset.hasOwnProperty("config")) {
    let config = JSON.parse(body.dataset.config);

    if (config && config.apply_phone_mask) {
        let mask = config.phone_extension;

        // Mask for Ukraine phone numbers
        if (mask === "+380") {
            mask = "+{38}({\\0}00)";
        }

        if (config.phone_length > 0) {
            for (let i = 0; i < config.phone_length; i++) {
                mask += "0";
            }
        }

        if (mask) {
            let maskable_elements = document.querySelectorAll('.applyPhoneMask');
            for (let maskable_element of maskable_elements) {
                IMask(maskable_element, {
                    mask: mask,
                    lazy: false,
                });
            }
        }
    }

}
