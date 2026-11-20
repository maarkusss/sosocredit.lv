import IMask from "imask";

let maskable_elements = document.querySelectorAll('.applyPersonalCodeMask');
if (maskable_elements) {
    let mask = "000000-00000";

    for (let maskable_element of maskable_elements) {
        IMask(maskable_element, {
            mask: mask,
            lazy: false,
        });
    }
}
