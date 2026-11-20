document.addEventListener("DOMContentLoaded", function () {
    const daySelect = document.querySelector("select[name='dob_day']");
    const monthSelect = document.querySelector("select[name='dob_month']");
    const yearSelect = document.querySelector("select[name='dob_year']");

    function validateDay() {
        const val = parseInt(daySelect.value, 10);
        if (!val || val < 1 || val > 31) {
            daySelect.classList.add("input-error");
        } else {
            daySelect.classList.remove("input-error");
        }
    }

    function validateMonth() {
        const val = parseInt(monthSelect.value, 10);
        if (!val || val < 1 || val > 12) {
            monthSelect.classList.add("input-error");
        } else {
            monthSelect.classList.remove("input-error");
        }
    }

    function validateYear() {
        const val = parseInt(yearSelect.value, 10);
        const currentYear = new Date().getFullYear();
        const minYear = currentYear - 100;

        if (!val || val < minYear || val > currentYear) {
            yearSelect.classList.add("input-error");
        } else {
            yearSelect.classList.remove("input-error");
        }
    }

    // Validation on change
    daySelect.addEventListener("change", validateDay);
    monthSelect.addEventListener("change", validateMonth);
    yearSelect.addEventListener("change", validateYear);
});
