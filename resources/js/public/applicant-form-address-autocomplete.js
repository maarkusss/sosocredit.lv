function checkAutocompleteAvailability(callback) {
    try {
        const sessionToken = new google.maps.places.AutocompleteSessionToken();
        const service = new google.maps.places.AutocompleteService();

        // Test is needed, to check if google maps scripts are loaded correctly, and if yes then init autocomplete
        service.getPlacePredictions(
            {
                input: 'Test',
                sessionToken: sessionToken,
                componentRestrictions: { country: 'lv' },
                types: ['address']
            },
            (predictions, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    callback(true, sessionToken); // API key works, return token
                } else {
                    console.warn('Autocomplete not available:', status);
                    callback(false);
                }
            }
        );
    } catch (err) {
        console.error('AutocompleteService could not be created', err);
        callback(false);
    }
}

function initAutocomplete() {
    const input = document.getElementById('address_street');

    if (!input) {
        return;
    }

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // stop the form from submitting
            e.stopPropagation(); // stop bubbling
        }
    });

    checkAutocompleteAvailability((isAvailable, sessionToken) => {
        if (!isAvailable) {
            console.warn('Autocomplete disabled, input works manually');
            return;
        }

        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['address'], // restrict to addresses only
            componentRestrictions: { country: 'lv' }, // restrict to Latvia (change if needed)
            sessionToken: sessionToken, // new session token
            fields: ['address_components', 'formatted_address'], // reduce data usage
        });

        // Optional: what to do when a place is selected
        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();

            if (!place || !place.address_components) {
                console.warn('No place data returned. Possibly quota exceeded.');
                return;
            }

            const components = place.address_components;

            // Helper function to get component by type
            function getComponent(type) {
                const comp = components.find((c) => c.types.includes(type));
                return comp ? comp.long_name : '';
            }

            // Extract the fields
            const street = getComponent('route') + ' ' + getComponent('street_number');
            const city = getComponent('locality') || getComponent('administrative_area_level_2');
            const postalCode = getComponent('postal_code');

            // Fill the input fields
            if (street) {
                document.getElementById('address_street').value = street;
            }

            if (city) {
                document.getElementById('address_city').value = city;
            }

            if (postalCode) {
                document.getElementById('address_zipcode').value = 'LV-'+postalCode;
            }
        });

        setTimeout(() => {
            input.setAttribute('autocomplete', 'try-disable-browser-default-autocomplete');
        });
    });
}

// Wait for the Google API script to load before initializing
window.addEventListener("load", () => {
    if (typeof google !== "undefined" && google && google.maps && google.maps.places) {
        initAutocomplete();
    } else {
        console.log("Google Maps JavaScript disabled.");
    }
});
