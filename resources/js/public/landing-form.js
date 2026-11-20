if (document.querySelector("#landing_form")) {

    let body = document.body,
        config = {};

    if (body && body.dataset && body.dataset.hasOwnProperty("config")) {
        config = JSON.parse(body.dataset.config);
    }
    // Form input required attributes
    const emailInput = document
        .querySelector("#landing_form")
        .querySelector("#email");

    const phoneInput = document
        .querySelector("#landing_form")
        .querySelector("#phone");

    if (emailInput) {
        emailInput.addEventListener("input", () => {
            if (phoneInput) {
                if (emailInput.value.length > 0) {
                    phoneInput.required = false;
                } else {
                    phoneInput.required = true;
                    document.getElementById("subscribe").required = false;
                }
            }
        });
    }

    if (phoneInput) {
        phoneInput.addEventListener("input", () => {
            if (emailInput) {
                if (phoneInput.value.length > 0) {
                    emailInput.required = false;
                } else {
                    emailInput.required = true;
                }
            }
        });
    }

    // Form steps
    const landingForm = document.querySelector("#landing_form"); // Landing form <div> wrapper
    const registrationForm = landingForm.querySelector("#registration_form"); // Step 2 <form>
    const phoneVerificationForm = landingForm.querySelector(
        "#phone_verification_form"
    );
    const emailNotification = landingForm.querySelector("#email_notification");
    const phoneNotification = landingForm.querySelector("#phone_notification");
    const emailVerificationContainer = landingForm.querySelector(
        "#email_verification_container"
    );
    const phoneVerificationContainer = landingForm.querySelector(
        "#phone_verification_container"
    );
    const emailOfferNotification = landingForm.querySelector(
        "#email_offer_notification"
    );
    const phoneOfferNotification = landingForm.querySelector(
        "#phone_offer_notification"
    );
    const sliderButtons = landingForm.querySelectorAll("[id^='slider_button_']");
    const landingFormSteps = landingForm.querySelectorAll(
        "[id^='landing_form_step_']"
    );
    const backButton = landingForm.querySelector("#back_button");
    const sendPhoneVerificationAgainButton = landingForm.querySelector(
        "#phone_verification_send_verification_again"
    );

    // Fingerprint
    async function getFingerprint() {
        // Initialize the agent at application startup.
        const fpPromise = new Promise((resolve, reject) => {
            const script = document.createElement("script");
            script.onload = resolve;
            script.onerror = reject;
            script.async = true;
            script.src = "/js/fp.min.js";
            document.head.appendChild(script);
        }).then(() =>
            FingerprintJS.load({
                token: "JAQdy9AySNSaanRzPPo3",
                region: "eu",
                endpoint: "https://metrics.goodaff.com",
            })
        );

        const cookieExpiryDate = new Date(
            Date.now() + 30 * 1000 * 60 * 60 * 24
        ).toUTCString(); // 30 days

        const fingerprintJSCookieExists = document.cookie
            .split(";")
            .some((cookie) => cookie.trim().startsWith("fingerprint="));

        if (!fingerprintJSCookieExists) {
            await fpPromise
                .then((fp) => fp.get())
                .then((result) => {
                    if (result.visitorId) {
                        document.cookie =
                            "fingerprint=" +
                            result.visitorId +
                            ";expires=" +
                            cookieExpiryDate +
                            ";path=/";
                        console.log("Fingerprint set: " + result.visitorId);
                        return;
                    }
                })
                .catch((error) => {
                    console.error(error);
                    return;
                });
        } else {
            console.log("Fingerprint already exists");
            return;
        }
    }

    // Registration form
    if (registrationForm) {
        registrationForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            let SHOULD_VERIFY_PHONE = config && config.require_phone,
                SHOULD_VERIFY_EMAIL = config && config.require_email;

            // fallback for Soso.lv
            if (SHOULD_VERIFY_PHONE !== true && SHOULD_VERIFY_EMAIL !== true) {
                SHOULD_VERIFY_PHONE = true;
                SHOULD_VERIFY_EMAIL = false;
            }

            // Disable the form submit button
            landingForm.querySelector("#slider_button_2").disabled = true;

            await getFingerprint();

            if (registrationForm.reportValidity()) {
                const formData = new FormData(registrationForm);
                formData.append("locale", document.querySelector("#locale").value);

                // Experiment parameter
                // const urlParams = new URLSearchParams(window.location.search);
                // const exp = urlParams.get("exp");
                // formData.append("exp", exp != null ? exp : "");

                fetch("/api/landing-form", {
                    method: "POST",
                    body: formData,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        // Facebook Pixel
                        if (window.hasOwnProperty("fbq") && window.fbq) {
                            fbq("track", "CompleteRegistration", {
                                currency: "EUR",
                                value: 0.0,
                            });
                            console.log("CompleteRegistration");
                        }
                        console.log(`Success: ${data.message}`);
                        document
                            .querySelector("#landing_form_step_2")
                            .classList.add("hidden");
                        document
                            .querySelector("#landing_form_step_3")
                            .classList.remove("hidden");

                        if (SHOULD_VERIFY_EMAIL === true) {
                            if (formData.get("email")) {
                                if (data.verified.email == true) {
                                    // Unhide email notification
                                    emailNotification.classList.remove("hidden");

                                    // Hide all the other notifications
                                    emailVerificationContainer.classList.add("hidden");
                                    emailOfferNotification.classList.add("hidden");

                                    landingForm.querySelector(
                                        "#email_notification_email"
                                    ).innerHTML = formData.get("email");
                                } else {
                                    // Unhide email verification container
                                    emailVerificationContainer.classList.remove("hidden");

                                    // Hide all the other notifications
                                    emailNotification.classList.add("hidden");
                                    emailOfferNotification.classList.add("hidden");

                                    landingForm.querySelector(
                                        "#email_verification_email"
                                    ).innerHTML = formData.get("email");
                                }
                            } else if (emailInput) {
                                emailOfferNotification.classList.remove("hidden");
                            } else {
                                emailOfferNotification.parentNode.classList.add("hidden");
                            }
                        }


                        if (SHOULD_VERIFY_PHONE === true) {
                            if (formData.get("phone")) {
                                if (data.verified.phone == true) {
                                    // Unhide phone notification
                                    phoneNotification.classList.remove("hidden");
                                    phoneNotification.querySelector(
                                        "#phone_notification_phone"
                                    ).innerHTML = `${formData.get("phone")}`;

                                    // Hide the 'verified' text
                                    phoneNotification
                                        .querySelector("#phone_notification_recurring_user")
                                        .classList.add("hidden");

                                    // Hide all the other notifications
                                    phoneVerificationContainer.classList.add("hidden");
                                    if (phoneOfferNotification) {
                                        phoneOfferNotification.classList.add("hidden");
                                    }
                                } else {
                                    // Unhide phone verification container
                                    phoneVerificationContainer.classList.remove("hidden");

                                    // Hide all the other notifications
                                    phoneNotification.classList.add("hidden");
                                    if (phoneOfferNotification) {
                                        phoneOfferNotification.classList.add("hidden");
                                    }
                                    landingForm.querySelector(
                                        "#phone_verification_phone"
                                    ).innerHTML = `${formData.get("phone")}`;

                                    // Unhide the 'verified' text
                                    phoneNotification
                                        .querySelector("#phone_notification_recurring_user")
                                        .classList.remove("hidden");
                                }
                            } else if (phoneInput) {
                                phoneOfferNotification.classList.remove("hidden");
                            } else if (phoneOfferNotification.parentNode) {
                                phoneOfferNotification.parentNode.classList.add("hidden");
                            }
                        }

                        // Enable the form submit button
                        landingForm.querySelector("#slider_button_2").disabled = false;
                    })
                    .catch((error) => {
                        // Enable the form submit button
                        landingForm.querySelector("#slider_button_2").disabled = false;

                        console.log(`Error: ${error.message}`);
                    });
            } else {
                // Enable the form submit button
                landingForm.querySelector("#slider_button_2").disabled = false;

                return false;
            }
        });
    }

    // Phone verification form
    if (phoneVerificationForm) {
        phoneVerificationForm.addEventListener("submit", (e) => {
            e.preventDefault();

            if (phoneVerificationForm.reportValidity()) {
                let formData = new FormData();
                formData.append(
                    "phone_number",
                    registrationForm.querySelector("#phone").value
                );
                formData.append(
                    "verification_code",
                    phoneVerificationForm.querySelector("#phone_verification_input")
                        .value
                );
                formData.append("locale", document.querySelector("#locale").value);

                fetch("/api/landing-form/verification/sms", {
                    method: "POST",
                    body: formData,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        console.log(`Success: ${data.message}`);

                        // Show the alert box if there is an error
                        if (data.message.includes("invalid")) {
                            phoneVerificationForm
                                .querySelector("#phone_verification_alert")
                                .classList.remove("hidden");
                            return;
                        }

                        phoneVerificationForm
                            .querySelector("#phone_verification_alert")
                            .classList.add("hidden");

                        // Hide the verification input
                        document
                            .querySelector("#phone_verification_container")
                            .classList.add("hidden");
                        phoneNotification.classList.remove("hidden");

                        // Display the phone number
                        phoneNotification.querySelector(
                            "#phone_notification_phone"
                        ).innerHTML = document.querySelector("#phone").value;
                    })
                    .catch((error) => {
                        console.log(`Error: ${error.message}`);
                    });
            } else {
                return false;
            }
        });
    }

    // Send the SMS verification again
    if (sendPhoneVerificationAgainButton) {
        sendPhoneVerificationAgainButton.addEventListener("click", () => {
            console.log("Sending the verification code again..");

            let formData = new FormData();
            formData.append(
                "phone_number",
                registrationForm.querySelector("#phone").value
            );
            formData.append("locale", document.querySelector("#locale").value);

            fetch("/api/landing-form/verification/sms/send-again", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    console.log(`Success: ${data.message}`);
                })
                .catch((error) => {
                    console.log(`Error: ${error.message}`);
                });

            // Disable the button, so the user can't send a verification SMS again
            sendPhoneVerificationAgainButton.disabled = true;

            // Enable the button after disableTime time amount
            const disableTime = 60;

            setTimeout(() => {
                sendPhoneVerificationAgainButton.disabled = false;
            }, disableTime * 1000);

            // Countdown timer
            const sendAgainTimer = landingForm.querySelector(
                "#phone_verification_send_verification_again_timer"
            );

            let timerValue = disableTime - 1;
            let timer = setInterval(() => {
                sendAgainTimer.innerHTML = `${timerValue}...`;

                if (--timerValue < 0) {
                    sendAgainTimer.innerHTML = "";
                    clearInterval(timer);
                }
            }, 1000);
        });
    }

    // Toggle the step visibility
    sliderButtons.forEach((sliderButton) => {
        sliderButton.addEventListener("click", () => {
            const buttonId = parseInt(sliderButton.id.split("_")[2]);

            landingFormSteps.forEach((landingFormStep) => {
                if (landingFormStep.id == `landing_form_step_${buttonId}`) {
                    // Hide the button if it's the first step
                    if (buttonId + 1 == 1) {
                        backButton.parentNode.classList.add("hidden");
                    } else {
                        backButton.parentNode.classList.remove("hidden");
                    }

                    if (buttonId != 2) {
                        // Hide the current step and show the next step
                        const nextLandingFormStep =
                            landingFormStep.nextElementSibling;
                        landingFormStep.classList.add("hidden");
                        nextLandingFormStep.classList.remove("hidden");
                    }
                }
            });
        });
    });

    // Back button
    backButton.addEventListener("click", () => {
        landingFormSteps.forEach((landingFormStep) => {
            // Show the previous step
            if (!landingFormStep.classList.contains("hidden")) {
                landingFormStep.classList.add("hidden");
                landingFormStep.previousElementSibling.classList.remove("hidden");

                // Hide the button if it's the first step
                const stepId = parseInt(landingFormStep.id.split("_")[3]);

                if (stepId - 1 == 1) {
                    backButton.parentNode.classList.add("hidden");
                } else {
                    backButton.parentNode.classList.remove("hidden");
                }
            }
        });
    });
}
