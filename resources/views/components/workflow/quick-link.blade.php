@props([
    'href',
    'icon',
    'title',
    'description',
    'iconClass' => '',
])

<a href="{{ $href }}" {{ $attributes->class(['workflow-quick-link']) }}>
    <div class="d-flex align-items-center gap-3">
        <span class="icon-wrap {{ $iconClass }}"><i class="bi {{ $icon }}"></i></span>
        <div>
            <div class="link-title">{{ $title }}</div>
            <p class="link-text">{{ $description }}</p>
        </div>
    </div>
    <i class="bi bi-chevron-right text-muted"></i>
</a>
