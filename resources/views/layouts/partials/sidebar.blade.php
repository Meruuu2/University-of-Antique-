@php
    $portalTitle = $portalTitle ?? 'Portal';
    $portalSubtitle = $portalSubtitle ?? null;
    $sections = $sections ?? [];
    $footer = $footer ?? null;
@endphp

<aside class="fms-sidebar" aria-label="Main navigation">
    <div class="fms-sidebar-header">
        <p class="fms-sidebar-title">{{ $portalTitle }}</p>
        @if ($portalSubtitle)
            <p class="fms-sidebar-subtitle">{{ $portalSubtitle }}</p>
        @endif
    </div>

    <nav class="fms-sidebar-nav">
        @foreach ($sections as $section)
            <div class="fms-nav-group">
                @if (!empty($section['heading']))
                    <p class="fms-nav-group-title">{{ $section['heading'] }}</p>
                @endif
                <ul class="fms-nav-list">
                    @foreach ($section['links'] as $link)
                        @php
                            $active = $link['active'] ?? false;
                            if (!$active && !empty($link['route'])) {
                                $active = request()->routeIs($link['route']);
                            }
                            if (!$active && !empty($link['routes'])) {
                                $active = request()->routeIs($link['routes']);
                            }
                        @endphp
                        <li>
                            <a
                                href="{{ $link['url'] ?? (isset($link['route']) ? route($link['route']) : '#') }}"
                                class="fms-nav-link {{ $active ? 'fms-nav-link-active' : '' }}"
                                @if($active) aria-current="page" @endif
                            >
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    @if ($footer)
        <div class="fms-sidebar-footer">
            {!! $footer !!}
        </div>
    @endif
</aside>
