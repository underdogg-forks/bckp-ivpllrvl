
<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>InvoicePlane</span></h1>
        <form method="post" class="space-y-4" action="{{ url($this->uri->uri_string());
?>">

            @csrf

            <legend>@lang('setup_prerequisites')</legend>

            <p>@lang('setup_prerequisites_message')</p>

@foreach($basics as $basic) {
    @if(isset($basic['warning']))
            <p><i class="fa fa-exclamation text-warning fa-margin"></i> {{ $basic['message'] }}</p>
@elseif($basic['success'] == 1)
            <p><i class="fa fa-check text-success fa-margin"></i> {{ $basic['message'] }}</p>
        @php
            } else {
                $errors = true;

        <p><i class="fa fa-close text-danger fa-margin"></i> {{ $basic['message'] }}</p>
        @php
            }
        }

        <br>

        @foreach($writables as $writable) {
    @if($writable['success'] === 1)
        <p><i class="fa fa-check text-success fa-margin"></i> {{ $writable['message'] }}</p>
        @else
        <p><i class="fa fa-close text-danger fa-margin"></i> {{ $writable['message'] }}</p>
        @php
            }
        }

        @if($errors)
        <a href="javascript:history.go(0)" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
            @php
                @lang('try_again')
                        </a>
            @else
            <input class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" type="submit" name="btn_continue"
                   value="@lang('continue')">@endforeach

            </form>

    </div>
</div>
