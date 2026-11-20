const commonEmailDomains = [
    "inbox.lv",
    "gmail.com",
    "icloud.com",
    "tvnet.lv",
    "yahoo.com",
    "outlook.com",
    "hotmail.com",
    "mail.lv",
    "delfi.lv",
    "email.lv",
];

document.addEventListener("DOMContentLoaded", () => {
    const inputs = document.querySelectorAll(".applyEmailMask");

    inputs.forEach((input) => {
        const wrapper = input.closest(".applicant-form__input-wrap");
        if (!wrapper) return;

        let suggestionsBox = document.createElement("div");
        suggestionsBox.className = `
            email-suggestions
            absolute left-0 right-0 mt-1
            hidden
            bg-white
            border border-gray-200
            rounded-lg
            shadow-lg
            overflow-hidden
            z-50
        `;

        wrapper.classList.add("relative");
        wrapper.appendChild(suggestionsBox);

        input.addEventListener("input", () => {
            const value = input.value.trim();
            suggestionsBox.innerHTML = "";

            if (!value) {
                suggestionsBox.classList.add("hidden");
                return;
            }

            const [name, domainPart = ""] = value.split("@");

            if (!name) {
                suggestionsBox.classList.add("hidden");
                return;
            }

            const suggestions = commonEmailDomains
                .filter((domain) => domain.startsWith(domainPart.toLowerCase()))
                .map((domain) => `${name}@${domain}`);

            if (!suggestions.length) {
                suggestionsBox.classList.add("hidden");
                return;
            }

            suggestions.forEach((email) => {
                const item = document.createElement("div");
                item.textContent = email;
                item.className = `
                    px-3 py-2
                    text-sm
                    cursor-pointer
                    hover:bg-gray-100
                    transition
                `;

                item.addEventListener("mousedown", () => {
                    input.value = email;
                    suggestionsBox.classList.add("hidden");
                });

                suggestionsBox.appendChild(item);
            });

            suggestionsBox.classList.remove("hidden");
        });

        document.addEventListener("click", (e) => {
            if (!wrapper.contains(e.target)) {
                suggestionsBox.classList.add("hidden");
            }
        });
    });
});
