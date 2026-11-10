{{--
    Admin config: tab panel with a standard section header.

    Renders a Bootstrap tab pane (role="tabpanel") containing a header
    (icon, title, optional description and action areas) followed by the
    tab content passed in the default slot.

    The pane id is "tab-{id}" and it is labelled by an element with id
    "tab-{id}-tab". That element is the tab button, which must be rendered
    elsewhere (in the nav) with matching id and data-bs-target="#tab-{id}".

    Slots:
      - default      Tab content
      - titleAction  Optional, displayed inline next to the title
      - actions      Optional, displayed at the right of the header

    @param string      $id          Tab identifier (used to build the pane id)
    @param string      $title       Section title
    @param string|null $description Short text under the title
    @param string      $icon        Bootstrap Icons class (e.g. "bi-brush-fill")
    @param bool        $active      Whether this pane is initially visible

    Extra attributes are merged onto the pane <div>.
--}}
@props([
    'id',
    'title',
    'description' => null,
    'icon' => 'bi-circle',
    'active' => false,
])

{{--
    "show" + "active" are both required by Bootstrap for the initial pane:
    "active" makes it display, "show" completes the "fade" transition.
    tabindex="0" lets keyboard users focus the panel when it has no focusable content.
--}}
<div {{ $attributes->class(['tab-pane', 'fade', 'show' => $active, 'active' => $active]) }} id="tab-{{ $id }}" role="tabpanel" aria-labelledby="tab-{{ $id }}-tab" tabindex="0">
    <div class="config-section-header d-flex align-items-start flex-wrap gap-3 mb-4">
        {{-- Decorative icon: hidden from assistive technologies, the title carries the meaning. --}}
        <span class="config-section-icon d-inline-flex align-items-center justify-content-center flex-shrink-0 lh-1 fs-4" aria-hidden="true">
            <i class="bi {{ $icon }}"></i>
        </span>

        <div class="flex-grow-1">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h2 class="h4 mb-1">{{ $title }}</h2>

                {{-- Optional named slot: only rendered when the caller provides <x-slot:titleAction>. --}}
                @isset($titleAction)
                    <div class="config-title-action d-inline-flex align-items-center">
                        {{ $titleAction }}
                    </div>
                @endisset
            </div>

            @if($description)
                <p class="text-body-secondary mb-0">{{ $description }}</p>
            @endif
        </div>

        {{-- Optional named slot pushed to the right on wider screens (ms-sm-auto). --}}
        @isset($actions)
            <div class="config-section-actions d-flex flex-wrap align-items-center gap-2 ms-sm-auto">
                {{ $actions }}
            </div>
        @endisset
    </div>

    {{-- Default slot: the fields of the tab. --}}
    {{ $slot }}
</div>
