
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>
        <form method="post" class="form-horizontal" action="{{ url($this->uri->uri_string());
?>">

            @csrf

            <legend>@lang('setup_prerequisites')</legend>

            <p>@lang('setup_prerequisites_message')</p>

@foreach($basics as $basic) {
    if (isset($basic['warning']))
            <p><i class="fa fa-exclamation text-warning fa-margin"></i> {{ $basic['message'] }}</p>
@elseif($basic['success'] == 1)
            <p><i class=" fa fa-check text-success fa-margin
        "></i> {{ $basic['message'] }}</p>
        @php
            } else {
                $errors = true;
        @endphp
        <p><i class="fa fa-close text-danger fa-margin"></i> {{ $basic['message'] }}</p>
        @php
            }
        } @endphp

        <br>

        @foreach($writables as $writable) {
    if ($writable['success'] === 1)
        <p><i class="fa fa-check text-success fa-margin"></i> {{ $writable['message'] }}</p>
        @else
        <p><i class="fa fa-close text-danger fa-margin"></i> {{ $writable['message'] }}</p>
        @php
            }
        } @endphp

        @if($errors)
        <a href="javascript:history.go(0)" class="btn btn-danger">
            @php
                @lang('try_again') }}
                        </a>
            @else
            <input class="btn btn-success" type="submit" name="btn_continue"
                   value="@lang('continue')">
            @endif

            </form>

    </div>
</div>
<?php
