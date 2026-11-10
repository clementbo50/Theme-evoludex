/**
 * Theme configuration admin page.
 *
 * Behaviors:
 *  - Color inputs: mirror the selected hex value in a text element.
 *  - Image selects: live preview of the selected image.
 *  - Logo size range: display the current value.
 *  - Form: disable the save button during submission.
 */

(() => {
    'use strict';

    // Write the input's hex value into the element referenced by data-color-hex.
    const syncColor = (input) => {
        const targetId = input.getAttribute('data-color-hex');

        if (!targetId) {
            return;
        }

        const target = document.getElementById(targetId);

        if (target) {
            target.textContent = input.value.toLowerCase();
        }
    };

    // Initial render for values already present on page load
    document.querySelectorAll('input[type="color"][data-color-hex]').forEach(syncColor);

    document.addEventListener('input', (event) => {
        if (event.target.matches('input[type="color"][data-color-hex]')) {
            syncColor(event.target);
        }
    });

    // Prefix the library file name with the image base path.
    const resolveImageUrl = (base, value) => {
        if (!value) {
            return '';
        }

        return base.replace(/\/$/, '') + '/' + value;
    };

    document.querySelectorAll('select[data-image-preview]').forEach((select) => {
        const preview = document.getElementById(select.getAttribute('data-image-preview'));

        if (!preview) {
            return;
        }
        // Element hidden when no image is selected.
        const wrapper = preview.closest('[data-image-preview-wrap]') || preview.parentElement;

        if (!wrapper) {
            return;
        }

        const refreshPreview = () => {
            const source = resolveImageUrl(select.getAttribute('data-base') || '', select.value);

            if (source) {
                preview.setAttribute('src', source);
                wrapper.classList.remove('d-none');
            } else {
                preview.removeAttribute('src');
                wrapper.classList.add('d-none');
            }
        };

        select.addEventListener('change', refreshPreview);
        refreshPreview();
    });

    const logoSize = document.getElementById('navbar-logo-size');
    const logoSizeOutput = document.getElementById('navbar-logo-size-value');

    if (logoSize && logoSizeOutput) {
        const refreshLogoSize = () => {
            logoSizeOutput.textContent = logoSize.value;
        };

        logoSize.addEventListener('input', refreshLogoSize);
        refreshLogoSize();
    }

    const form = document.getElementById('themeConfigForm');
    const saveButton = document.querySelector('[data-config-save]');
    const saveSpinner = document.querySelector('[data-config-save-spinner]');

    if (form && saveButton) {
        // Prevent double submission and show the spinner.
        form.addEventListener('submit', () => {
            saveButton.disabled = true;

            if (saveSpinner) {
                saveSpinner.hidden = false;
            }
        });
         // Restore the button when the page is shown again (bfcache / back button).
        window.addEventListener('pageshow', () => {
            saveButton.disabled = false;

            if (saveSpinner) {
                saveSpinner.hidden = true;
            }
        });
    }
})();
