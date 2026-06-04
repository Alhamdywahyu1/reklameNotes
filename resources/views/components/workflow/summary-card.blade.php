@props([
    'number',
    'label',
    'helper',
    'numberClass' => '',
])

<div {{ $attributes->class(['card', 'workflow-summary-card']) }}>
    <div class="card-body">
        <div class="number {{ $numberClass }}">{{ $number }}</div>
        <div class="label">{{ $label }}</div>
        <p class="helper">{{ $helper }}</p>
    </div>
</div>
