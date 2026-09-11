<script>
    document.addEventListener('DOMContentLoaded', function () {
        const captureDetailsField = document.getElementById(
            'capture-details-field'
        );

        const captureDetails = document.getElementById(
            'capture_details'
        );

        const sightingDetailsField = document.getElementById(
            'sighting-details-field'
        );

        const sightingDetails = document.getElementById(
            'sighting_details'
        );

        const ammunitionUseField = document.getElementById(
            'ammunition-use-field'
        );

        const ammunitionCountFields = document.getElementById(
            'ammunition-count-fields'
        );

        const ammunitionInputs = [
            'sabot_count',
            'slug_count',
            'bs_count',
            'shot_count',
        ].map(function (id) {
            return document.getElementById(id);
        });

        function selectedValue(name) {
            const selected = document.querySelector(
                'input[name="' + name + '"]:checked'
            );

            return selected ? selected.value : null;
        }

        function selectRadio(name, value) {
            const radio = document.querySelector(
                'input[name="'
                + name
                + '"][value="'
                + value
                + '"]'
            );

            if (radio) {
                radio.checked = true;
            }
        }

        function toggleCaptureDetails() {
            const hasCapture =
                selectedValue('has_capture') === '1';

            captureDetailsField.hidden = !hasCapture;
            captureDetails.disabled = !hasCapture;
        }

        function toggleSightingDetails() {
            const hasSighting =
                selectedValue('has_sighting') === '1';

            sightingDetailsField.hidden = !hasSighting;
            sightingDetails.disabled = !hasSighting;
        }

        function toggleGunFields() {
            const hasGun =
                selectedValue('has_gun') === '1';

            if (!hasGun) {
                selectRadio('has_used_ammunition', '0');
            }

            ammunitionUseField.hidden = !hasGun;

            toggleAmmunitionCounts();
        }

        function toggleAmmunitionCounts() {
            const hasGun =
                selectedValue('has_gun') === '1';

            const hasUsedAmmunition =
                selectedValue('has_used_ammunition') === '1';

            const shouldShow =
                hasGun && hasUsedAmmunition;

            ammunitionCountFields.hidden = !shouldShow;

            ammunitionInputs.forEach(function (input) {
                if (!input) {
                    return;
                }

                input.disabled = !shouldShow;

                if (!shouldShow) {
                    input.value = 0;
                }

                updateCounterButtons(input);
            });
        }

        document
            .querySelectorAll('input[name="has_capture"]')
            .forEach(function (input) {
                input.addEventListener(
                    'change',
                    toggleCaptureDetails
                );
            });

        document
            .querySelectorAll('input[name="has_sighting"]')
            .forEach(function (input) {
                input.addEventListener(
                    'change',
                    toggleSightingDetails
                );
            });

        document
            .querySelectorAll('input[name="has_gun"]')
            .forEach(function (input) {
                input.addEventListener(
                    'change',
                    toggleGunFields
                );
            });

        document
            .querySelectorAll(
                'input[name="has_used_ammunition"]'
            )
            .forEach(function (input) {
                input.addEventListener(
                    'change',
                    toggleAmmunitionCounts
                );
            });

        toggleCaptureDetails();
        toggleSightingDetails();
        toggleGunFields();
    });
    const counterButtons = document.querySelectorAll(
        '.counter-button'
    );

    function updateCounterButtons(input) {
        const counter = input.closest('.counter');

        if (!counter) {
            return;
        }

        const decreaseButton = counter.querySelector(
            '[data-counter-action="decrease"]'
        );

        const increaseButton = counter.querySelector(
            '[data-counter-action="increase"]'
        );

        const value = Number(input.value);

        decreaseButton.disabled =
            input.disabled || value <= 0;

        increaseButton.disabled =
            input.disabled || value >= 99;
    }

    counterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = button.dataset.counterTarget;
            const action = button.dataset.counterAction;
            const input = document.getElementById(targetId);

            if (!input || input.disabled) {
                return;
            }

            let value = Number(input.value);

            if (!Number.isInteger(value)) {
                value = 0;
            }

            if (action === 'increase') {
                value = Math.min(99, value + 1);
            }

            if (action === 'decrease') {
                value = Math.max(0, value - 1);
            }

            input.value = value;

            updateCounterButtons(input);
        });
    });

    ammunitionInputs.forEach(function (input) {
        if (input) {
            updateCounterButtons(input);
        }
    });
</script>