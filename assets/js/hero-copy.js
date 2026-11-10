(() => {
    'use strict';

    document.querySelectorAll('.poke-copy').forEach((button) => {
        const original = button.textContent;
        const tip = button.getAttribute('data-bs-title') || '';
        const copied = button.getAttribute('data-copied') || original;
        let timer = null;

        const tooltip = window.bootstrap ? new window.bootstrap.Tooltip(button) : null;

        const showTip = (text) => {
            if (!tooltip) {
                return;
            }

            tooltip.setContent({ '.tooltip-inner': text });
            tooltip.show();
        };

        const selectText = () => {
            const range = document.createRange();
            range.selectNodeContents(button);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        };

        const done = () => {
            button.textContent = copied;
            showTip(copied);

            if (timer) {
                clearTimeout(timer);
            }

            timer = setTimeout(() => {
                button.textContent = original;

                if (tooltip) {
                    tooltip.setContent({ '.tooltip-inner': tip });
                    tooltip.hide();
                }
            }, 2000);
        };

        button.addEventListener('click', () => {
            const text = button.getAttribute('data-copy') || original;

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(done).catch(selectText);
            } else {
                selectText();
            }
        });
    });
})();
