import IMask from "imask";

let maskableElements = document.querySelectorAll(".applyZipcodeMask");

if (maskableElements.length > 0) {
    maskableElements.forEach((el) => {
        IMask(el, {
            mask: [
                {
                    mask: "LV-0000",
                },
            ],
            lazy: false,
        });
    });
}
