<div class="alert alert-{{ $type }}">
    @if ($type === 'error')
        <i class="far fa-exclamation-triangle"></i>
    @elseif ($type === 'info')
        <i class="far fa-info-circle"></i>
    @else
        <i class="far fa-exclamation-circle"></i>
    @endif

    <span class="alert-content">
        {{ $content }}
    </span>
</div>
