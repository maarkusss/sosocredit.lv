if (document.querySelector("#landing_form")) {

    let body = document.body,
        config = {};

    if (body && body.dataset && body.dataset.hasOwnProperty("config")) {
        config = JSON.parse(body.dataset.config);
    }

    const emailInput = document
        .querySelector("#landing_form")
        .querySelector("#email");

    const phoneInput = document
        .querySelector("#landing_form")
        .querySelector("#phone");

    // Form steps
    const landingForm = document.querySelector("#landing_form"); // Landing form <div> wrapper
    const registrationForm = landingForm.querySelector("#registration_form"); // Step 2 <form>
    const registrationFormErrors = landingForm.querySelector("#registration_form_errors"); // Step 2 <div>
    const phoneFormErrors = landingForm.querySelector("#phone_form_errors"); // Step 3 <div>
    const loadingSpinner = landingForm.querySelector(".loading-spinner"); // Step 2 <div>
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
    const backButtonV2 = landingForm.querySelector("#back_button_v2");
    const sendPhoneVerificationAgainButton = landingForm.querySelector(
        "#phone_verification_send_verification_again"
    );

    // Registration form
    if (registrationForm) {
        registrationForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            let SHOULD_VERIFY_PHONE = config && config.verify_phone,
                SHOULD_VERIFY_EMAIL = config && config.verify_email;

            // fallback for Soso.lv
            if (SHOULD_VERIFY_PHONE !== true && SHOULD_VERIFY_EMAIL !== true) {
                SHOULD_VERIFY_PHONE = true;
                SHOULD_VERIFY_EMAIL = false;
            }

            registrationFormErrors.classList.add('hidden');
            registrationFormErrors.innerHTML = "";

            // Disable the form submit button
            landingForm.querySelector("#slider_button_2").disabled = true;

            if (loadingSpinner) {
                loadingSpinner.classList.remove('hidden');
            }

            if (registrationForm.reportValidity()) {
                const formData = new FormData(registrationForm);
                formData.append("locale", document.querySelector("#locale").value);

                fetch("/api/landing-form", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    },
                })
                    .then(function(response) {
                        if (
                            response.ok ||
                            (response.status === 400 || (response.status > 420 && response.status < 430))
                        ) {
                            return response.json();
                        }

                        throw new Error('Something went wrong.');
                    })
                    .then((data) => {

                        if (data.hasOwnProperty("errors") && data.errors) {
                            // 422 status from API with errors

                            for (let error_key in data.errors) {
                                // registrationFormErrors.insertAdjacentHTML('beforeend', '<p>' + data.errors[error_key][0] + '</p>');
                                registrationFormErrors.insertAdjacentHTML('beforeend', '<p>' + data.errors[error_key]['error_messages'][0] + '</p>');
                            }
                            registrationFormErrors.classList.remove('hidden');

                        } else {
                            // 2xx successful response

                            // Facebook Pixel
                            if (window.hasOwnProperty("fbq") && window.fbq) {
                                fbq("track", "CompleteRegistration", {
                                    currency: "EUR",
                                    value: 0.0,
                                });
                                console.log("CompleteRegistration");
                            }
                            console.log(`Success: ${data.message}`);

                            if (data.hasOwnProperty("redirect_url") && data.hasOwnProperty("redirect_timeout")) {
                                setTimeout(() => {
                                    window.location.href = data.redirect_url;
                                }, data.redirect_timeout);
                                return;
                            }

                            document
                                .querySelector("#landing_form_step_2")
                                .classList.add("hidden");
                            document
                                .querySelector("#landing_form_step_3")
                                .classList.remove("hidden");

                            // if (SHOULD_VERIFY_EMAIL === true) {
                            //     if (formData.get("email")) {
                            //         if (data.verified.email == true) {
                            //             // Unhide email notification
                            //             emailNotification.classList.remove("hidden");
                            //
                            //             // Hide all the other notifications
                            //             emailVerificationContainer.classList.add("hidden");
                            //             emailOfferNotification.classList.add("hidden");
                            //
                            //             landingForm.querySelector(
                            //                 "#email_notification_email"
                            //             ).innerHTML = formData.get("email");
                            //         } else {
                            //             // Unhide email verification container
                            //             emailVerificationContainer.classList.remove("hidden");
                            //
                            //             // Hide all the other notifications
                            //             emailNotification.classList.add("hidden");
                            //             emailOfferNotification.classList.add("hidden");
                            //
                            //             landingForm.querySelector(
                            //                 "#email_verification_email"
                            //             ).innerHTML = formData.get("email");
                            //         }
                            //     } else if (emailInput) {
                            //         emailOfferNotification.classList.remove("hidden");
                            //     } else {
                            //         emailOfferNotification.parentNode.classList.add("hidden");
                            //     }
                            // }


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

                            // Enable Go Back v2 button
                            if (backButtonV2) {
                                backButtonV2.parentNode.classList.remove('hidden');
                            }
                        }

                        // Enable the form submit button
                        landingForm.querySelector("#slider_button_2").disabled = false;
                        if (loadingSpinner) {
                            loadingSpinner.classList.add('hidden');
                        }

                    })
                    .catch((error) => {
                        // Enable the form submit button
                        landingForm.querySelector("#slider_button_2").disabled = false;
                        if (loadingSpinner) {
                            loadingSpinner.classList.add('hidden');
                        }

                        console.log(`Error: ${error.message}`);
                    });
            } else {
                // Enable the form submit button
                landingForm.querySelector("#slider_button_2").disabled = false;
                if (loadingSpinner) {
                    loadingSpinner.classList.add('hidden');
                }

                return false;
            }
        });
    }

    // Phone verification form
    if (phoneVerificationForm) {
        phoneVerificationForm.addEventListener("submit", (e) => {
            e.preventDefault();

            phoneFormErrors.classList.add('hidden');
            phoneFormErrors.innerHTML = "";

            // Disable the form submit button
            landingForm.querySelector("#confirm_phone_button").disabled = true;
            goodday_triggerLoadingSpinners(landingForm, 'show');

            if (phoneVerificationForm.reportValidity()) {
                let formData = new FormData();
                formData.append(
                    "phone",
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
                    headers: {
                        'Accept': 'application/json'
                    },
                })
                    .then(function(response) {
                        if (
                            response.ok ||
                            (response.status === 400 || (response.status > 420 && response.status < 430))
                        ) {
                            return response.json();
                        }

                        throw new Error('Something went wrong.');
                    })
                    .then((data) => {
                        console.log(`Success: ${data.message}`);

                        // Enable the form submit button
                        landingForm.querySelector("#confirm_phone_button").disabled = false;
                        goodday_triggerLoadingSpinners(landingForm,'hide');

                        if (data.hasOwnProperty("errors") && data.errors) {
                            // 422 status from API with errors

                            for (let error_key in data.errors) {
                                phoneFormErrors.insertAdjacentHTML('beforeend', '<p>' + data.errors[error_key][0] + '</p>');
                                // phoneFormErrors.insertAdjacentHTML('beforeend', '<p>' + data.errors[error_key]['error_messages'][0] + '</p>');
                            }
                            phoneFormErrors.classList.remove('hidden');

                        } else {

                            // Hide the verification input
                            document
                                .querySelector("#phone_verification_container")
                                .classList.add("hidden");
                            phoneNotification.classList.remove("hidden");

                            // Display the phone number
                            phoneNotification.querySelector(
                                "#phone_notification_phone"
                            ).innerHTML = document.querySelector("#phone").value;

                        }

                        // // Show the alert box if there is an error
                        // if (data.message.includes("invalid")) {
                        //     phoneVerificationForm
                        //         .querySelector("#phone_verification_alert")
                        //         .classList.remove("hidden");
                        //     return;
                        // }
                        //
                        // phoneVerificationForm
                        //     .querySelector("#phone_verification_alert")
                        //     .classList.add("hidden");
                    })
                    .catch((error) => {
                        // Enable the form submit button
                        landingForm.querySelector("#confirm_phone_button").disabled = false;
                        goodday_triggerLoadingSpinners(landingForm, 'hide');

                        console.log(`Error: ${error.message}`);
                    });
            } else {
                // Enable the form submit button
                landingForm.querySelector("#confirm_phone_button").disabled = false;
                goodday_triggerLoadingSpinners(landingForm, 'hide');

                return false;
            }
        });
    }

    // Send the SMS verification again
    if (sendPhoneVerificationAgainButton) {
        sendPhoneVerificationAgainButton.addEventListener("click", () => {
            console.log("Sending the verification code again..");

            phoneFormErrors.classList.add('hidden');
            phoneFormErrors.innerHTML = "";

            let formData = new FormData();
            formData.append(
                "phone",
                registrationForm.querySelector("#phone").value
            );
            formData.append("locale", document.querySelector("#locale").value);

            fetch("/api/landing-form/verification/sms/send-again", {
                method: "POST",
                body: formData,
                headers: {
                    'Accept': 'application/json'
                },
            })
                .then(function(response) {
                    if (
                        response.ok ||
                        (response.status === 400 || (response.status > 420 && response.status < 430))
                    ) {
                        return response.json();
                    }

                    throw new Error('Something went wrong.');
                })
                .then((data) => {
                    if (data.hasOwnProperty("errors") && data.errors) {
                        // 422 status from API with errors

                        for (let error_key in data.errors) {
                            phoneFormErrors.insertAdjacentHTML('beforeend', '<p>' + data.errors[error_key][0] + '</p>');
                        }
                        phoneFormErrors.classList.remove('hidden');

                    } else {

                        console.log(`Success: ${data.message}`);
                        goodday_disableButtonForOneMinute(sendPhoneVerificationAgainButton, landingForm);

                    }

                })
                .catch((error) => {
                    console.log(`Error: ${error.message}`);
                });
        });
    }

    // Toggle the step visibility
    sliderButtons.forEach((sliderButton) => {
        sliderButton.addEventListener("click", () => {
            const buttonId = parseInt(sliderButton.id.split("_")[2]);

            landingFormSteps.forEach((landingFormStep) => {
                if (landingFormStep.id == `landing_form_step_${buttonId}`) {

                    if (backButton) {
                        // Hide the button if it's the first step
                        if (buttonId + 1 == 1) {
                            backButton.parentNode.classList.add("hidden");
                        } else {
                            backButton.parentNode.classList.remove("hidden");
                        }
                    }

                    if (backButtonV2) {
                        // Hide the button v2 if it's the second step
                        if (buttonId == 2) {
                            backButtonV2.parentNode.classList.add("hidden");
                        } else {
                            backButtonV2.parentNode.classList.remove("hidden");
                        }
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
    if (backButton) {
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

    // Back button v2
    if (backButtonV2) {
        backButtonV2.addEventListener("click", () => {
            landingFormSteps.forEach((landingFormStep) => {
                // Show the previous step
                if (!landingFormStep.classList.contains("hidden")) {
                    landingFormStep.classList.add("hidden");
                    landingFormStep.previousElementSibling.classList.remove("hidden");

                    // Hide the button if it's the first step
                    const stepId = parseInt(landingFormStep.id.split("_")[3]);

                    if (stepId - 1 < 3) {
                        backButtonV2.parentNode.classList.add("hidden");
                    } else {
                        backButtonV2.parentNode.classList.remove("hidden");
                    }
                }
            });
        });
    }
}

function goodday_disableButtonForOneMinute(sendPhoneVerificationAgainButton, landingForm)
{
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
}

function goodday_triggerLoadingSpinners(landingForm, action)
{
    let loadingSpinners = landingForm.querySelectorAll(".loading-spinner");

    if (action === 'show') {
        for (let loadingSpinner of loadingSpinners) {
            loadingSpinner.classList.remove('hidden');
        }
    } else {
        for (let loadingSpinner of loadingSpinners) {
            loadingSpinner.classList.add('hidden');
        }
    }
}

