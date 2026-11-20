import noUiSlider from "nouislider";
// import "nouislider/dist/nouislider.css";

// Plus and minus buttons for the amount slider
let plusButton = document.querySelector("#plusButton");
let minusButton = document.querySelector("#minusButton");

let amountSlider = document.getElementById("amountSlider");
if (amountSlider) {
    let amountInput = document.getElementById("amount"),
        percent = 0.003,
        start = 3000,
        step = 50,
        step_after_1000 = 100,
        min = 50,
        max = 25000;
    //Default hidden input value
    amountInput.value = start;
    noUiSlider.create(amountSlider, {
        connect: "lower",
        start: start,
        // step: step,
        range: {
            min: min,
            max: max,
        },
        animate: true,
        animationDuration: 600,
    });

    plusButton.addEventListener("click", function () {
        let value = parseInt(amountSlider.noUiSlider.get());

        if (value > 999) {
            amountSlider.noUiSlider.set(value + step_after_1000);
        } else {
            amountSlider.noUiSlider.set(value + step);
        }
    });

    minusButton.addEventListener("click", function () {
        let value = parseInt(amountSlider.noUiSlider.get());

        if (value > 1000) {
            amountSlider.noUiSlider.set(value - step_after_1000);
        } else {
            amountSlider.noUiSlider.set(value - step);
        }
    });

    amountSlider.noUiSlider.on("update", function (values, handle) {
        let sliderAmount = values[handle];

        if (sliderAmount > 999) {
            sliderAmount = Math.round(sliderAmount / 100) * 100;
        } else {
            sliderAmount = Math.round(sliderAmount / 50) * 50;
        }

        // Loan amount
        if (document.querySelector("#slider_loan_amount")) {
            document.querySelector("#slider_loan_amount").innerHTML =
                parseInt(sliderAmount);
        }

        // Percentage amount
        if (document.querySelector("#slider_percentage_amount")) {
            document.querySelector("#slider_percentage_amount").innerHTML =
                parseInt(sliderAmount * percent);
        }

        // Final amount
        if (document.querySelector("#slider_final_amount")) {
            document.querySelector("#slider_final_amount").innerHTML =
                parseInt(sliderAmount) + parseInt(sliderAmount * percent);
        }

        amountInput.value = parseInt(sliderAmount);
    });
}
