document.addEventListener("alpine:init", () => {
    Alpine.data("cookieModal", () => ({
        open: true,
        tab: "consent",
        openSection: null,
        mobileDetails: false,
        consent: {
            analytics: false,
            marketing: false,
            functional: false,
        },

        init() {
            const existing = this.getConsentCookie();
            if (existing) {
                this.consent = existing;
                this.applyConsent();
                this.open = false;
            }
        },

        toggle(section) {
            this.openSection = this.openSection === section ? null : section;
        },

        save() {
            const expires = new Date(Date.now() + 365 * 864e5).toUTCString();
            document.cookie = `cookies_consent=${encodeURIComponent(
                JSON.stringify(this.consent)
            )}; expires=${expires}; path=/; SameSite=Lax`;

            fetch("/cookie-consent", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify(this.consent),
            });

            this.applyConsent();
            this.open = false;
        },

        acceptAll() {
            this.consent.analytics = true;
            this.consent.marketing = true;
            this.consent.functional = true;
            this.save();
        },

        acceptSelected() {
            this.save();
        },

        rejectAll() {
            this.consent.analytics = false;
            this.consent.marketing = false;
            this.consent.functional = false;
            this.save();
        },

        getConsentCookie() {
            const match = document.cookie.match(/(^| )cookies_consent=([^;]+)/);
            if (!match) return null;

            try {
                return JSON.parse(decodeURIComponent(match[2]));
            } catch {
                return null;
            }
        },

        applyConsent() {
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }

            gtag("consent", "update", {
                analytics_storage: this.consent.analytics
                    ? "granted"
                    : "denied",
                ad_storage: this.consent.marketing ? "granted" : "denied",
                ad_user_data: this.consent.marketing ? "granted" : "denied",
                ad_personalization: this.consent.marketing
                    ? "granted"
                    : "denied",
            });

            // Facebook Pixel (optional hard block)
            if (this.consent.marketing && window.fbq) {
                fbq("consent", "grant");
            }
            if (!this.consent.marketing && window.fbq) {
                fbq("consent", "revoke");
            }
        },
    }));
});

document.addEventListener("click", (e) => {
    const a = e.target.closest("#open-cookie-settings, #open-cookie-details");
    if (!a) return;

    e.preventDefault();

    const tab = a.id === "open-cookie-details" ? "details" : "consent";
    window.dispatchEvent(
        new CustomEvent("open-cookie-modal", { detail: { tab } })
    );
});
