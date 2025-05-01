document.addEventListener("DOMContentLoaded", function () {
    // ============ TÉLÉPHONE ============ //
    const phoneInput = document.querySelector("#registration_form_telephone");
    if (phoneInput) {
        window.intlTelInput(phoneInput, {
            initialCountry: "auto",
            preferredCountries: ["tn", "fr", "dz", "ma"],
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                return "ex: " + selectedCountryPlaceholder;
            },
            geoIpLookup: function(callback) {
                fetch('https://ipapi.co/json')
                    .then(res => res.json())
                    .then(data => callback(data.country_code))
                    .catch(() => callback("tn"));
            }
        });
    }

    // ============ CARTE ============ //
    const map = L.map('map').setView([33.8869, 9.5375], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const marker = L.marker([33.8869, 9.5375], { 
        draggable: true 
    }).addTo(map);

    // Ajout du contrôle de géocodage
    const geocoder = L.Control.geocoder({
        defaultMarkGeocode: false,
        position: 'topright',
        placeholder: 'Rechercher une adresse...',
        errorMessage: 'Adresse non trouvée'
    }).addTo(map);

    geocoder.on('markgeocode', function(e) {
        const latlng = e.geocode.center;
        marker.setLatLng(latlng);
        map.setView(latlng, 16);
        updateAddress(latlng.lat, latlng.lng);
    });

    // Ajout du contrôle de localisation
    const lc = L.control.locate({
        position: 'topleft',
        strings: {
            title: "Me localiser"
        },
        locateOptions: {
            maxZoom: 16
        }
    }).addTo(map);

    // Gestion de la localisation
    map.on('locationfound', function(e) {
        marker.setLatLng(e.latlng);
        updateAddress(e.latlng.lat, e.latlng.lng);
        map.setView(e.latlng, 16);
    });

    // Gestion des clics sur la carte
    map.on("click", function (e) {
        marker.setLatLng(e.latlng);
        updateAddress(e.latlng.lat, e.latlng.lng);
    });

    // Gestion du glisser-déposer du marqueur
    marker.on("dragend", function () {
        const pos = marker.getLatLng();
        updateAddress(pos.lat, pos.lng);
    });

    // Bouton de localisation manuelle
    document.getElementById('locate-me')?.addEventListener('click', function() {
        lc.start();
    });

    // Bouton de recherche
    document.getElementById('search-place')?.addEventListener('click', function() {
        const input = geocoder.getContainer().querySelector('input');
        input.focus();
    });

    // Fonction pour mettre à jour l'adresse
    function updateAddress(lat, lon) {
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`)
            .then(res => res.json())
            .then(data => {
                const adresse = data.display_name;
                const adresseField = document.getElementById("registration_form_adresse");
                if (adresseField) {
                    adresseField.value = adresse;
                }
            })
            .catch(error => {
                console.error("Erreur lors de la récupération de l'adresse:", error);
            });
    }

    // Initialisation avec l'adresse par défaut
    updateAddress(33.8869, 9.5375);

    // ============ MOT DE PASSE ============ //
    const passwordInput = document.querySelector('#registration_form_plainPassword');
    const strengthBar = document.querySelector('.password-strength');
    
    if (passwordInput && strengthBar) {
        passwordInput.addEventListener('input', function () {
            const result = zxcvbn(this.value);
            const strength = result.score * 25;
            const colors = ['#dc3545', '#ff6b6b', '#ffd93d', '#6cbf6c', '#4caf50'];
            strengthBar.style.setProperty('--strength', `${strength}%`);
            strengthBar.style.setProperty('--strength-color', colors[result.score]);
        });
    }
});