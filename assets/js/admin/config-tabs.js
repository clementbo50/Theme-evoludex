/**
 * Admin config page: tab navigation controller.
 *
 * Responsibilities:
 *  - Restore the active tab on load (priority: server errors > URL hash >
 *    localStorage > first tab).
 *  - Keep URL hash and localStorage in sync with the active tab.
 *  - React to hash changes (back/forward, manual edit).
 *  - Jump to the first invalid field from "error summary" links.
 *  - Work with the Bootstrap Tab plugin when available, and fall back to
 *    manual class/ARIA toggling when it is not (e.g. script failed to load).
 *
 * Expected markup:
 *  - Nav container:   .config-tabs, optional data-config-errors='["id1","id2"]'
 *  - Tab buttons:     [data-config-tab="<id>"] inside the nav
 *  - Panes:           #tab-<id>, children of #configTabContent
 *  - Error links:     [data-config-error-link="<id>"] anywhere on the page
 *
 * Wrapped in an IIFE so nothing leaks into the global scope.
 */
(() => {
    'use strict';

    const navigation = document.querySelector('.config-tabs');

    if (!navigation) {
        return;
    }

    const buttons = Array.from(navigation.querySelectorAll('[data-config-tab]'));

    if (buttons.length === 0) {
        return;
    }

    const storageKey = 'evoludex.config.activeTab';
    const homeButton = buttons[0];

    let currentId = null;

    /**
     * Checked at call time (not once at load) so a late-loaded Bootstrap is still detected.
     */
    const hasTabPlugin = () => typeof window.bootstrap !== 'undefined' && typeof window.bootstrap.Tab !== 'undefined';

    // Lookups return null (not undefined) when the id is unknown.
    const buttonFor = (id) => buttons.find((button) => button.dataset.configTab === id) || null;
    const paneFor = (id) => document.getElementById('tab-' + id);

    /**
     * Extracts the tab id from a "#tab-<id>" hash. The strict pattern also
     * ignores unrelated hashes and prevents arbitrary values from being used.
     */
    const idFromHash = () => {
        const match = window.location.hash.match(/^#tab-([a-z0-9-]+)$/i);

        return match ? match[1] : null;
    };

    const readStoredId = () => {
        try {
            return window.localStorage.getItem(storageKey);
        } catch (error) {
            return null;
        }
    };

    const storeId = (id) => {
        try {
            window.localStorage.setItem(storageKey, id);
        } catch (error) {
            // Storage can be unavailable, the page still works without it.
        }
    };

    /**
     * Updates the hash without adding a history entry, so switching tabs
     * does not pollute the back button. Path and query string are preserved.
     */
    const replaceHash = (id) => {
        if (typeof window.history.replaceState !== 'function') {
            return;
        }

        const url = window.location.pathname + window.location.search + '#tab-' + id;
        window.history.replaceState(null, '', url);
    };

    /**
     * Focuses the first visible, enabled invalid field of a pane.
     * offsetParent === null filters out hidden elements (display: none).
     */
    const focusFirstInvalid = (id) => {
        const pane = paneFor(id);

        if (!pane) {
            return;
        }

        const invalidField = Array.from(pane.querySelectorAll('.is-invalid')).find((field) => {
            return !field.disabled && field.offsetParent !== null;
        });

        if (invalidField) {
            invalidField.focus();
        }
    };

    /**
     * Fallback used when the Bootstrap Tab plugin is missing: reproduces what
     * the plugin does (active class, aria-selected, pane visibility).
     */
    const activateManually = (button) => {
        buttons.forEach((item) => {
            const isActive = item === button;

            item.classList.toggle('active', isActive);
            item.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        document.querySelectorAll('#configTabContent > .tab-pane').forEach((pane) => {
            const isActive = pane.id === 'tab-' + button.dataset.configTab;

            pane.classList.toggle('active', isActive);
            pane.classList.toggle('show', isActive);
        });
    };

    /**
     * Activates a tab by id.
     *
     * @param {string}  id
     * @param {object}  [options]
     * @param {boolean} [options.focus]      Focus the first invalid field afterwards
     * @param {boolean} [options.deferFocus] Delay the focus until the fade transition has ended
     * @returns {boolean} false when the id matches no tab
     */
    const activate = (id, options = {}) => {
        const button = buttonFor(id);

        if (!button) {
            return false;
        }

        currentId = id;

        if (hasTabPlugin()) {
            // Storage and hash are updated in the 'shown.bs.tab' listener below,
            // once the transition is complete.
            window.bootstrap.Tab.getOrCreateInstance(button).show();
        } else {
            activateManually(button);
            storeId(id);
            replaceHash(id);
        }

        if (options.focus) {
            if (options.deferFocus) {
                // A hidden field cannot receive focus: wait for the fade transition.
                // 250 ms is a time-based approximation, see notes below.
                window.setTimeout(() => focusFirstInvalid(id), 250);
            } else {
                focusFirstInvalid(id);
            }
        }

        return true;
    };

    /**
     * Tabs containing server-side validation errors, read from a JSON data
     * attribute. Parsing is defensive: invalid JSON or unknown ids are ignored.
     */
    const errorTabs = (() => {
        try {
            const parsed = JSON.parse(navigation.dataset.configErrors || '[]');

            return Array.isArray(parsed) ? parsed.filter((id) => buttonFor(id)) : [];
        } catch (error) {
            return [];
        }
    })();

    // With the plugin, persist state only after the tab is really shown.
    // This also covers tab switches triggered by Bootstrap itself (clicks, keyboard).
    if (hasTabPlugin()) {
        navigation.addEventListener('shown.bs.tab', (event) => {
            const id = event.target.dataset.configTab;

            if (!id) {
                return;
            }

            currentId = id;
            storeId(id);
            replaceHash(id);
        });
    }

    // Without the plugin, clicks must be handled manually (event delegation on the nav).
    navigation.addEventListener('click', (event) => {
        if (hasTabPlugin()) {
            return;
        }

        const button = event.target.closest('[data-config-tab]');

        if (!button) {
            return;
        }

        event.preventDefault();
        activate(button.dataset.configTab);
    });

    // "Error summary" links: switch to the tab, then focus the first invalid field.
    document.querySelectorAll('[data-config-error-link]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();

            const id = link.dataset.configErrorLink;
            const deferFocus = hasTabPlugin();

            // Redundant when the plugin is active (activate() already focuses
            // after the delay), and a second focus when it is not.
            if (activate(id, { focus: true, deferFocus })) {
                if (!deferFocus) {
                    focusFirstInvalid(id);
                }
            }
        });
    });

    // Back/forward or manual hash edit. The currentId check avoids a loop
    // with replaceHash (replaceState does not fire hashchange, but this
    // also skips needless re-activation).
    window.addEventListener('hashchange', () => {
        const id = idFromHash();

        if (id && buttonFor(id) && id !== currentId) {
            activate(id);
        }
    });


    // Validation errors take priority over any remembered tab.
    if (errorTabs.length > 0) {
        activate(errorTabs[0], { focus: true, deferFocus: true });
        return;
    }

    // Otherwise: URL hash (shareable link) > last visited tab > first tab.
    // Each candidate is checked against existing buttons, so stale values are ignored.
    const hashId = idFromHash();
    const storedId = readStoredId();
    const initialId = (hashId && buttonFor(hashId) ? hashId : null)
        || (storedId && buttonFor(storedId) ? storedId : null)
        || homeButton.dataset.configTab;

    activate(initialId);
})();
