@props([
    'title',
    'description',
])

<div class="card workflow-hero mb-4">
    <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h5 class="mb-2">{{ $title }}</h5>
            <p class="text-muted mb-0">{{ $description }}</p>
        </div>
        @isset($actions)
            <div class="d-flex flex-wrap gap-2">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
