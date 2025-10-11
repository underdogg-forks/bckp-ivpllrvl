@php
$alert_class = 'alert';
$alert_class .= isset($without_margin) ? ' no-margin' : '';
$types = ['success', 'info', 'warning', 'error'];
$classes = ['success', 'info', 'warning', 'danger'];
$icons = ['info-circle', 'info-circle', 'exclamation-circle', 'warning'];
@endphp

{{-- Validation errors --}}
@if(function_exists('validation_errors') && validation_errors())
    {!! validation_errors('<div class="' . $alert_class . ' alert-danger">', '</div>') !!}
@endif

{{-- Flash alert messages --}}
@foreach($types as $index => $type)
    @if($this->session->flashdata('alert_' . $type))
        <div class="{{ $alert_class }} alert-{{ $classes[$index] }} alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa fa-fw fa-lg fa-{{ $icons[$index] }}"></i>
            <span>{{ $this->session->flashdata('alert_' . $type) }}</span>
        </div>
    @endif
@endforeach
