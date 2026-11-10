/**
 * The full Discord section and the small stats tile both read the same
 * invite, so they share one request per invite code and one session cache
 * entry instead of calling the API twice on the same page.
 */
(() => {
    'use strict';

    const invitePattern = /^[A-Za-z0-9-]{2,32}$/;
    const cachePrefix = 'evoludex.discord.v1.';
    const cacheTtl = 5 * 60 * 1000;
    const requests = new Map();

    const isCount = (value) => Number.isInteger(value) && value >= 0;
    const cleanString = (value) => (typeof value === 'string' ? value.trim() : '');
    const formatNumber = (value) => new Intl.NumberFormat(document.documentElement.lang || undefined).format(value);
    const fillTemplate = (template, value) => template.replace(':count', value);

    const requestKey = (code) => cachePrefix + encodeURIComponent(code);

    const readCache = (key) => {
        try {
            const raw = window.sessionStorage.getItem(key);

            if (!raw) {
                return null;
            }

            const parsed = JSON.parse(raw);

            if (
                !parsed
                || typeof parsed !== 'object'
                || parsed.state !== 'ok'
                || typeof parsed.expiresAt !== 'number'
                || parsed.expiresAt <= Date.now()
            ) {
                window.sessionStorage.removeItem(key);

                return null;
            }

            return parsed;
        } catch {
            return null;
        }
    };

    const writeCache = (key, result) => {
        try {
            window.sessionStorage.setItem(key, JSON.stringify({
                ...result,
                expiresAt: Date.now() + cacheTtl,
            }));
        } catch {
            return;
        }
    };

    const readErrorCode = async (response) => {
        try {
            const error = await response.json();

            return error && Number.isInteger(error.code) ? error.code : null;
        } catch {
            return null;
        }
    };

    const cleanTraits = (raw) => {
        if (!Array.isArray(raw)) {
            return [];
        }

        return raw
            .map((trait) => {
                if (typeof trait === 'string') {
                    return { label: trait, position: Number.MAX_SAFE_INTEGER };
                }

                if (!trait || typeof trait !== 'object') {
                    return null;
                }

                const label = cleanString(trait.label) || cleanString(trait.name);

                if (label === '') {
                    return null;
                }

                const position = typeof trait.position === 'number' && Number.isFinite(trait.position)
                    ? trait.position
                    : Number.MAX_SAFE_INTEGER;

                return { label, position };
            })
            .filter((trait) => trait !== null)
            .sort((first, second) => first.position - second.position || first.label.localeCompare(second.label))
            .slice(0, 5)
            .map((trait) => trait.label);
    };

    const pickCount = (...values) => {
        for (const value of values) {
            if (isCount(value)) {
                return value;
            }
        }

        return null;
    };

    const buildIconUrl = (guildId, hash) => {
        if (guildId === '' || hash === '') {
            return null;
        }

        return 'https://cdn.discordapp.com/icons/' + encodeURIComponent(guildId) + '/' + encodeURIComponent(hash) + '.png?size=128';
    };

    const normalize = (data) => {
        if (!data || typeof data !== 'object') {
            return null;
        }

        const profile = data.profile && typeof data.profile === 'object' ? data.profile : {};
        const guild = data.guild && typeof data.guild === 'object' ? data.guild : null;
        const guildId = guild ? cleanString(guild.id) : '';
        const hash = cleanString(profile.icon_hash) || (guild ? cleanString(guild.icon) : '');

        return {
            name: cleanString(profile.name),
            traits: cleanTraits(profile.traits),
            memberCount: pickCount(profile.member_count, data.approximate_member_count),
            onlineCount: pickCount(profile.online_count, data.approximate_presence_count),
            iconUrl: buildIconUrl(guildId, hash),
            guildId,
        };
    };

    /**
     * Return the invite result, from the session cache when it is still
     * fresh, from the network otherwise. Concurrent callers for the same
     * code await the same promise, so the API is called once.
     *
     * Result shapes:
     *   { state: 'ok', name, traits, memberCount, onlineCount, iconUrl, guildId }
     *   { state: 'offline', errorCode }
     *   { state: 'unavailable' }
     */
    const requestInvite = (code) => {
        const key = requestKey(code);

        if (requests.has(key)) {
            return requests.get(key);
        }

        const pending = (async () => {
            const cached = readCache(key);

            if (cached) {
                return cached;
            }

            try {
                const response = await fetch(
                    'https://discord.com/api/v10/invites/' + encodeURIComponent(code) + '?with_counts=true',
                    { headers: { Accept: 'application/json' } },
                );

                if (response.status === 404) {
                    return { state: 'offline', errorCode: await readErrorCode(response) };
                }

                if (response.status !== 200) {
                    return { state: 'unavailable' };
                }

                const payload = normalize(await response.json());

                if (!payload) {
                    return { state: 'unavailable' };
                }

                const result = { state: 'ok', ...payload };
                writeCache(key, result);

                return result;
            } catch {
                return { state: 'unavailable' };
            }
        })();

        requests.set(key, pending);

        return pending;
    };

    const initSection = () => {
        const card = document.querySelector('[data-discord]');

        if (!card) {
            return;
        }

        const fallback = card.getAttribute('data-fallback') || '\u2014';
        const code = card.getAttribute('data-invite') || '';

        const elements = {
            avatar: card.querySelector('[data-discord-avatar]'),
            server: card.querySelector('[data-discord-server]'),
            badge: card.querySelector('[data-discord-badge]'),
            badgeText: card.querySelector('[data-discord-badge-text]'),
            badgeDetail: card.querySelector('[data-discord-badge-detail]'),
            membersValue: card.querySelector('[data-discord-members-value]'),
            membersLabel: card.querySelector('[data-discord-members-label]'),
            onlineValue: card.querySelector('[data-discord-online-value]'),
            onlineLabel: card.querySelector('[data-discord-online-label]'),
            traits: card.querySelector('[data-discord-traits]'),
            traitsFallback: card.querySelector('[data-discord-traits-fallback]'),
            cta: card.querySelector('[data-discord-cta]'),
        };

        const labels = {
            membersUnavailable: card.getAttribute('data-label-members') || '',
            onlineUnavailable: card.getAttribute('data-label-online') || '',
            countMembers: card.getAttribute('data-count-members') || ':count',
            countOnline: card.getAttribute('data-count-online') || ':count',
            badge: {
                checking: card.getAttribute('data-badge-checking') || '',
                online: card.getAttribute('data-badge-online') || '',
                offline: card.getAttribute('data-badge-offline') || '',
                unavailable: card.getAttribute('data-badge-unavailable') || '',
            },
            detail: {
                checking: card.getAttribute('data-detail-checking') || '',
                online: card.getAttribute('data-detail-online') || '',
                offline: card.getAttribute('data-detail-offline') || '',
                unavailable: card.getAttribute('data-detail-unavailable') || '',
                expired: card.getAttribute('data-detail-expired') || '',
                unknown: card.getAttribute('data-detail-unknown') || '',
            },
        };

        const badgeVariantByState = {
            unavailable: 'text-bg-secondary',
            checking: 'text-bg-warning',
            online: 'text-bg-success',
            offline: 'text-bg-danger',
        };
        const badgeVariants = Object.values(badgeVariantByState);

        const setBadge = (state, text, detail) => {
            elements.badge.dataset.state = state;
            elements.badge.classList.remove(...badgeVariants);
            elements.badge.classList.add(badgeVariantByState[state] || badgeVariantByState.unavailable);
            elements.badgeText.textContent = text;
            elements.badgeDetail.textContent = detail;
        };

        const setCount = (valueElement, labelElement, value, template, unavailableLabel) => {
            if (!isCount(value)) {
                valueElement.textContent = fallback;
                labelElement.textContent = unavailableLabel;

                return;
            }

            const formatted = formatNumber(value);
            valueElement.textContent = formatted;
            labelElement.textContent = fillTemplate(template, formatted);
        };

        const setName = (name) => {
            const value = cleanString(name);

            if (value === '') {
                elements.server.textContent = '';
                elements.server.hidden = true;

                return;
            }

            elements.server.textContent = value;
            elements.server.hidden = false;
        };

        const showFallbackIcon = () => {
            const icon = document.createElement('i');
            icon.className = 'bi bi-discord';

            elements.avatar.replaceChildren(icon);
        };

        const isSafeIconUrl = (url) => typeof url === 'string' && url.startsWith('https://cdn.discordapp.com/icons/');

        const setIcon = (url) => {
            if (!isSafeIconUrl(url)) {
                showFallbackIcon();

                return;
            }

            const image = document.createElement('img');
            image.className = 'd-block w-100 h-100 object-fit-cover';
            image.alt = '';
            image.loading = 'lazy';
            image.decoding = 'async';
            image.referrerPolicy = 'no-referrer';
            image.addEventListener('error', showFallbackIcon, { once: true });
            image.src = url;

            elements.avatar.replaceChildren(image);
        };

        const setTraits = (traits) => {
            const names = Array.isArray(traits)
                ? traits.filter((name) => typeof name === 'string' && name.trim() !== '').slice(0, 5)
                : [];

            elements.traits.replaceChildren();

            if (names.length === 0) {
                elements.traits.classList.add('d-none');

                if (elements.traitsFallback) {
                    elements.traitsFallback.textContent = fallback;
                    elements.traitsFallback.classList.remove('d-none');
                }

                return;
            }

            const fragment = document.createDocumentFragment();

            for (const name of names) {
                const item = document.createElement('li');
                item.className = 'badge bg-discord';
                item.textContent = name.trim();

                fragment.appendChild(item);
            }

            elements.traits.appendChild(fragment);
            elements.traits.classList.remove('d-none');

            if (elements.traitsFallback) {
                elements.traitsFallback.classList.add('d-none');
            }
        };

        const activateCta = () => {
            if (elements.cta.tagName !== 'A') {
                return;
            }

            elements.cta.classList.remove('is-disabled');
            elements.cta.removeAttribute('aria-disabled');
            elements.cta.removeAttribute('aria-describedby');
            elements.cta.removeAttribute('tabindex');

            if (!elements.cta.getAttribute('href') && invitePattern.test(code)) {
                elements.cta.setAttribute('href', 'https://discord.gg/' + encodeURIComponent(code));
            }
        };

        const disableCta = () => {
            if (elements.cta.tagName === 'A') {
                elements.cta.removeAttribute('href');
                elements.cta.setAttribute('aria-disabled', 'true');
                elements.cta.setAttribute('tabindex', '-1');
                elements.cta.setAttribute('aria-describedby', 'discord-cta-help');
                elements.cta.classList.add('is-disabled');

                return;
            }

            elements.cta.disabled = true;
            elements.cta.classList.add('is-disabled');
        };

        const renderData = (data) => {
            setName(data.name);
            setIcon(data.iconUrl);
            setTraits(data.traits);
            setCount(elements.membersValue, elements.membersLabel, data.memberCount, labels.countMembers, labels.membersUnavailable);
            setCount(elements.onlineValue, elements.onlineLabel, data.onlineCount, labels.countOnline, labels.onlineUnavailable);
        };

        const showOffline = (detailKey) => {
            setBadge('offline', labels.badge.offline, labels.detail[detailKey] || labels.detail.offline);
            setName('');
            setIcon(null);
            setTraits([]);
            setCount(elements.membersValue, elements.membersLabel, null, labels.countMembers, labels.membersUnavailable);
            setCount(elements.onlineValue, elements.onlineLabel, null, labels.countOnline, labels.onlineUnavailable);
            disableCta();
        };

        const showUnavailable = (detailKey) => {
            setBadge('unavailable', labels.badge.unavailable, labels.detail[detailKey] || labels.detail.unavailable);
        };

        const render = (result) => {
            if (result.state === 'ok') {
                if (result.guildId === '') {
                    showUnavailable('unavailable');

                    return;
                }

                renderData(result);
                setBadge('online', labels.badge.online, labels.detail.online);
                activateCta();

                return;
            }

            if (result.state === 'offline') {
                showOffline(result.errorCode === 50270 ? 'expired' : (result.errorCode === 10006 ? 'unknown' : 'offline'));

                return;
            }

            showUnavailable('unavailable');
        };

        const init = async () => {
            setBadge('checking', labels.badge.checking, labels.detail.checking);

            if (!invitePattern.test(code)) {
                setBadge('offline', labels.badge.offline, labels.detail.offline);
                disableCta();

                return;
            }

            render(await requestInvite(code));
        };

        init();
    };

    const initStats = () => {
        const card = document.querySelector('[data-stats-discord]');

        if (!card) {
            return;
        }

        const valueElement = card.querySelector('[data-stats-discord-value]');
        const labelElement = card.querySelector('[data-stats-discord-label]');

        if (!valueElement || !labelElement) {
            return;
        }

        const mode = card.getAttribute('data-mode') === 'active' ? 'active' : 'registered';
        const code = card.getAttribute('data-invite') || '';
        const field = mode === 'active' ? 'onlineCount' : 'memberCount';
        const fallback = card.getAttribute('data-fallback') || '\u2014';
        const unavailableLabel = card.getAttribute('data-label-unavailable') || '';
        const expiredLabel = card.getAttribute('data-label-expired') || unavailableLabel;
        const unknownLabel = card.getAttribute('data-label-unknown') || unavailableLabel;
        const successLabel = card.getAttribute('data-label-success') || ':count';

        const showUnavailable = (label = unavailableLabel) => {
            valueElement.textContent = fallback;
            labelElement.textContent = label;
        };

        const renderValue = (value) => {
            const formatted = formatNumber(value);

            valueElement.textContent = formatted;
            labelElement.textContent = fillTemplate(successLabel, formatted);
        };

        const render = (result) => {
            if (result.state === 'ok') {
                const value = result[field];

                if (!isCount(value)) {
                    showUnavailable();

                    return;
                }

                renderValue(value);

                return;
            }

            if (result.state === 'offline') {
                if (result.errorCode === 50270) {
                    showUnavailable(expiredLabel);

                    return;
                }

                if (result.errorCode === 10006) {
                    showUnavailable(unknownLabel);

                    return;
                }
            }

            showUnavailable();
        };

        const init = async () => {
            if (!invitePattern.test(code)) {
                showUnavailable();

                return;
            }

            render(await requestInvite(code));
        };

        init();
    };

    initSection();
    initStats();
})();
