import noUiSlider from "nouislider";

const amountSlider = document.getElementById("applicantAmountSlider");

const locale = amountSlider?.dataset.locale || "en";

function formatCurrency(value) {
    return new Intl.NumberFormat(locale).format(value).replace(/[.,]/g, " ");
}

window.formatCurrency = formatCurrency;

function parseCurrency(value) {
    return parseInt(value.replace(/[^0-9]/g, "")) || 0;
}

function resizeAmountInput() {
    const display = document.getElementById("amount_display");
    if (!display) return;

    const canvas =
        resizeAmountInput._canvas ||
        (resizeAmountInput._canvas = document.createElement("canvas"));
    const ctx = canvas.getContext("2d");
    ctx.font = getComputedStyle(display).font;
    const textWidth = ctx.measureText(display.value).width;

    display.style.width = textWidth + 42 + "px";
}

function resizeTermInput() {
    const input = document.getElementById("term_months_input");
    if (!input) return;

    const canvas =
        resizeTermInput._canvas ||
        (resizeTermInput._canvas = document.createElement("canvas"));
    const ctx = canvas.getContext("2d");
    ctx.font = getComputedStyle(input).font;
    const textWidth = ctx.measureText(input.value).width;

    input.style.width = textWidth + 68 + "px";
}

function applicantForm_updatePercentageAndFinalAmount() {
    const amountSlider = document.getElementById("applicantAmountSlider");
    const amountInput = document.getElementById("amount");
    const termInput =
        document.getElementById("term_months") ||
        document.getElementById("term_months_input");

    if (!amountSlider || !amountInput || !termInput) return;

    const percent = amountSlider.dataset.percent
        ? parseFloat(amountSlider.dataset.percent)
        : 0.3;

    const loan_amount = parseFloat(amountInput.value || 0);
    const term_months = parseInt(termInput.value || 1);

    const total_percent = (percent / 100) * term_months * loan_amount;
    const payment_total = loan_amount + total_percent;
    const payment_monthly = payment_total / term_months;

    const pm = document.getElementById("payment_monthly_amount");
    const pt = document.getElementById("payment_total_amount");

    if (pm) pm.innerText = payment_monthly.toFixed(2);
    if (pt) pt.innerText = payment_total.toFixed();
}

window.initAmountSlider = function () {
    if (!amountSlider) return;

    const amountInput = document.getElementById("amount");
    const amountDisplay = document.getElementById("amount_display");

    let start = amountSlider.dataset.start
            ? parseInt(amountSlider.dataset.start)
            : 1000,
        step = amountSlider.dataset.step
            ? parseInt(amountSlider.dataset.step)
            : 100,
        min = amountSlider.dataset.min
            ? parseInt(amountSlider.dataset.min)
            : 50,
        max = amountSlider.dataset.max
            ? parseInt(amountSlider.dataset.max)
            : 25000;

    amountInput.value = start;
    amountDisplay.value = formatCurrency(start);
    resizeAmountInput();

    if (amountSlider.noUiSlider) {
        amountSlider.noUiSlider.destroy();
    }

    noUiSlider.create(amountSlider, {
        connect: "lower",
        start: start,
        step: step,
        range: { min: min, max: max },
        animate: true,
        animationDuration: 600,
        handleAttributes: [{ "aria-label": "Amount slider" }],
    });

    amountSlider.noUiSlider.on("update", function (values, handle) {
        const sliderAmount = parseInt(values[handle]);
        amountInput.value = sliderAmount;
        amountDisplay.value = formatCurrency(sliderAmount);
        resizeAmountInput();
        applicantForm_updatePercentageAndFinalAmount();
    });

    // While typing — strip formatting, keep raw number
    amountDisplay.addEventListener("input", function () {
        const maxLen = String(max).length;
        if (this.value.replace(/[^0-9]/g, "").length > maxLen) {
            this.value = this.value.slice(0, -1);
        }
        const raw = parseCurrency(this.value);
        amountInput.value = raw;
        resizeAmountInput();
        applicantForm_updatePercentageAndFinalAmount();
    });

    // On blur — clamp, reformat and sync slider
    amountDisplay.addEventListener("blur", function () {
        let parsed = parseCurrency(this.value);
        if (isNaN(parsed) || parsed === 0) parsed = start;
        parsed = Math.min(Math.max(parsed, min), max);

        amountInput.value = parsed;
        this.value = formatCurrency(parsed);
        resizeAmountInput();
        amountSlider.noUiSlider.set(parsed);
        applicantForm_updatePercentageAndFinalAmount();
    });

    amountDisplay.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            this.blur();
        }
    });

    document.getElementById("amount_display").style.visibility = "visible";
};

window.initTermSlider = function () {
    const termMonthsSlider = document.getElementById(
        "applicantTermMonthsSlider"
    );
    if (!termMonthsSlider) return;

    const termMonthsInput = document.getElementById("term_months_input");
    const termHidden = document.getElementById("term_months");
    const minTermMonths = parseInt(termMonthsSlider.dataset.min);
    const maxTermMonths = parseInt(termMonthsSlider.dataset.max);
    const step = 1;
    const start = parseInt(termMonthsSlider.dataset.start) || 16;

    termMonthsInput.value = start;
    if (termHidden) termHidden.value = start;
    resizeTermInput();

    if (termMonthsSlider.noUiSlider) {
        termMonthsSlider.noUiSlider.destroy();
    }

    noUiSlider.create(termMonthsSlider, {
        connect: "lower",
        start: start,
        step: step,
        range: { min: minTermMonths, max: maxTermMonths },
        animate: true,
        animationDuration: 600,
        handleAttributes: [{ "aria-label": "Term slider" }],
    });

    termMonthsSlider.noUiSlider.on("update", function (values, handle) {
        const sliderAmount = parseInt(values[handle]);
        termMonthsInput.value = sliderAmount;
        if (termHidden) termHidden.value = sliderAmount;
        resizeTermInput();
        applicantForm_updatePercentageAndFinalAmount();
    });

    termMonthsInput.addEventListener("input", function () {
        const maxLen = String(maxTermMonths).length;
        if (this.value.length > maxLen) {
            this.value = this.value.slice(0, maxLen);
        }
        resizeTermInput();
    });

    termMonthsInput.addEventListener("blur", function () {
        let inputValue = parseInt(termMonthsInput.value);
        if (isNaN(inputValue)) inputValue = start;
        inputValue = Math.min(
            Math.max(inputValue, minTermMonths),
            maxTermMonths
        );

        termMonthsInput.value = inputValue;
        if (termHidden) termHidden.value = inputValue;
        resizeTermInput();
        termMonthsSlider.noUiSlider.set(inputValue);
        applicantForm_updatePercentageAndFinalAmount();
    });

    termMonthsInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            this.blur();
        }
    });

    document.getElementById("term_months_input").style.visibility = "visible";
};

document.fonts.ready.then(() => {
    window.initAmountSlider();
    window.initTermSlider();
});

document.addEventListener("alpine:init", () => {
    setTimeout(() => {
        const amountSlider = document.getElementById("applicantAmountSlider");
        if (amountSlider && amountSlider.noUiSlider) {
            amountSlider.noUiSlider.on("update", function (values) {
                const newAmount = parseInt(values[0]);
                document.getElementById("amount").value = newAmount;
                window.dispatchEvent(
                    new CustomEvent("amount-updated", { detail: newAmount })
                );
            });
        }
    }, 100);
});
