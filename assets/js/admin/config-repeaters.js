/**
 * Generic repeater: add / remove rows in a list.
 *
 * Driven by data-* attributes on the container:
 *   data-repeater           marks the container
 *   data-repeater-row       CSS selector of one row
 *   data-repeater-min       minimum rows (default 1)
 *   data-repeater-max       maximum rows (absent = unlimited)
 *   data-repeater-template  id of the <template> holding a blank row
 *   data-repeater-add       id of the "add" button
 *   data-repeater-defaults  optional JSON: default icons / colors
 *
 * The blank template uses the "__INDEX__" token, replaced on cloning.
 * Rows are renumbered after each change so field names stay sequential.
 */
(() => {
    'use strict';

    const containers = Array.from(document.querySelectorAll('[data-repeater]'));

    if (containers.length === 0) {
        return;
    }

    // Attributes (besides "name") whose trailing number follows the row index
    const renumberableAttributes = [
        'id',
        'for',
        'aria-describedby',
        'aria-labelledby',
        'aria-controls',
        'data-color-hex',
        'data-image-preview',
        'data-target',
    ];

    const elementsOf = (row) => [row, ...row.querySelectorAll('*')];

    // Replaces "__INDEX__" in every attribute of a freshly cloned row
    const replaceIndexToken = (row, index) => {
        const tokenValue = String(index);

        elementsOf(row).forEach((element) => {
            for (const attribute of element.getAttributeNames()) {
                const value = element.getAttribute(attribute);

                if (value.includes('__INDEX__')) {
                    element.setAttribute(attribute, value.split('__INDEX__').join(tokenValue));
                }
            }
        });
    };

    const selectorOf = (container) => container.dataset.repeaterRow;

    const rowsOf = (container) => Array.from(container.querySelectorAll(selectorOf(container)));

    const minOf = (container) => {
        const value = Number(container.dataset.repeaterMin);

        return Number.isFinite(value) && value > 0 ? value : 1;
    };

    const maxOf = (container) => {
        const value = Number(container.dataset.repeaterMax);

        return Number.isFinite(value) && value > 0 ? value : null;
    };

    // Builds a row from the <template>, or null if unavailable
    const createRow = (container, index) => {
        const template = document.getElementById(container.dataset.repeaterTemplate);

        if (!template) {
            return null;
        }

        const fragment = template.content.cloneNode(true);
        const row = fragment.firstElementChild;

        if (!row) {
            return null;
        }

        replaceIndexToken(row, index);
        row.dataset.repeaterIndex = String(index);

        return row;
    };

    // Makes indexes sequential again, e.g. hero[buttons][3][label] -> hero[buttons][1][label]
    const renumber = (container) => {
        const rows = rowsOf(container);

        rows.forEach((row, index) => {
            row.dataset.repeaterIndex = String(index);

            elementsOf(row).forEach((element) => {
                for (const attribute of element.getAttributeNames()) {
                    if (attribute === 'data-repeater-index') {
                        continue;
                    }

                    const value = element.getAttribute(attribute);

                    if (!value) {
                        continue;
                    }

                    let nextValue = value;

                    if (attribute === 'name') {
                        nextValue = value.replace(/(\[[^\]]+\])\[\d+\]/, `$1[${index}]`);
                    } else if (renumberableAttributes.includes(attribute)) {
                        nextValue = value.replace(/(\d+)(?=[-\s]|$)/g, String(index));
                    }

                    if (nextValue !== value) {
                        element.setAttribute(attribute, nextValue);
                    }
                }
            });
        });
    };

    // Cycles through default icons / colors (data-repeater-defaults) for a new row
    const applyDefaults = (container, row, index) => {
        const rawDefaults = container.dataset.repeaterDefaults;

        if (!rawDefaults) {
            return;
        }

        let defaults;

        try {
            defaults = JSON.parse(rawDefaults);
        } catch (error) {
            return;
        }

        const icons = Array.isArray(defaults.icons) ? defaults.icons : [];
        const colors = Array.isArray(defaults.colors) ? defaults.colors : [];
        const icon = row.querySelector('[data-discord-icon]');
        const color = row.querySelector('[data-discord-color]');

        if (icon && icons.length > 0 && typeof icons[index % icons.length] === 'string') {
            icon.value = icons[index % icons.length];
        }

        if (color && colors.length > 0 && typeof colors[index % colors.length] === 'string') {
            color.value = colors[index % colors.length];
            color.dispatchEvent(new Event('input', { bubbles: true }));
        }
    };

    // Hero rows: hide and disable value / new-tab columns for the "copy" type
    const toggleHeroRow = (row) => {
        if (!row) {
            return;
        }

        const typeSelect = row.querySelector('[data-hero-type]');

        if (!typeSelect) {
            return;
        }

        const isCopy = typeSelect.value === 'copy';
        const valueColumn = row.querySelector('.hero-button-value-col');
        const tabColumn = row.querySelector('.hero-button-tab-col');

        if (valueColumn) {
            valueColumn.classList.toggle('d-none', isCopy);
            valueColumn.querySelectorAll('input').forEach((input) => {
                input.disabled = isCopy;
            });
        }

        if (tabColumn) {
            tabColumn.classList.toggle('d-none', isCopy);
            tabColumn.querySelectorAll('input').forEach((input) => {
                input.disabled = isCopy;
            });
        }
    };

    const refreshAddButton = (container) => {
        const addButton = document.getElementById(container.dataset.repeaterAdd);

        if (!addButton) {
            return;
        }

        const max = maxOf(container);
        addButton.disabled = max !== null && rowsOf(container).length >= max;
    };

    // Keeps keyboard focus useful after a removal
    const focusAfterRemoval = (container, index) => {
        const rows = rowsOf(container);
        const nextRow = rows[Math.min(Math.max(index, 0), rows.length - 1)];
        const target = nextRow?.querySelector('[data-repeater-remove]')
            || document.getElementById(container.dataset.repeaterAdd);

        if (target) {
            target.focus();
        }
    };

    // At the minimum, a row cannot be deleted: replace it with a blank one
    const resetRow = (container, row) => {
        const rows = rowsOf(container);
        const index = Math.max(rows.indexOf(row), 0);
        const replacement = createRow(container, index);

        if (!replacement) {
            return;
        }

        container.replaceChild(replacement, row);
        renumber(container);
        applyDefaults(container, replacement, index);
        toggleHeroRow(replacement);
    };


    const initContainer = (container) => {
        const rowSelector = selectorOf(container);

        // Sync server-rendered rows and the add button with their initial state
        rowsOf(container).forEach((row) => toggleHeroRow(row));
        refreshAddButton(container);

        // Delegated: also covers rows added later
        container.addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-repeater-remove]');

            if (!removeButton || !container.contains(removeButton)) {
                return;
            }

            const row = removeButton.closest(rowSelector);

            if (!row) {
                return;
            }

            const removedIndex = rowsOf(container).indexOf(row);

            // Above the minimum: remove. At the minimum: reset to a blank row
            if (rowsOf(container).length > minOf(container)) {
                row.remove();
            } else {
                resetRow(container, row);
            }

            // Close the gap left in the indexes
            renumber(container);
            refreshAddButton(container);
            focusAfterRemoval(container, removedIndex);
        });

        container.addEventListener('change', (event) => {
            if (event.target.matches('[data-hero-type]')) {
                toggleHeroRow(event.target.closest(rowSelector));
            }
        });

        const addButton = document.getElementById(container.dataset.repeaterAdd);

        // Remove-only repeater: nothing left to bind
        if (!addButton) {
            return;
        }

        addButton.addEventListener('click', () => {
            const max = maxOf(container);

            // Safety net in case the button state is out of sync
            if (max !== null && rowsOf(container).length >= max) {
                refreshAddButton(container);
                return;
            }

            const rows = rowsOf(container);
            const index = rows.length;
            const row = createRow(container, index);

            if (!row) {
                return;
            }

            container.appendChild(row);
            applyDefaults(container, row, index);
            toggleHeroRow(row);
            refreshAddButton(container);
        });
    };

    containers.forEach(initContainer);
})();
