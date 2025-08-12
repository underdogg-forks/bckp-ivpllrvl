@php  @endphp

<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

    <h4>A PHP Error was encountered</h4>

    <p>Severity: {{ $severity }}</p>
    <p>Message: {{ $message }}</p>
    <p>Filename: {{ $filepath }}</p>
    <p>Line Number: {{ $line }}</p>
@php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE) { @endphp
    <p>Backtrace:</p>
@php foreach (debug_backtrace() as $error) {
            if (isset($error['file']) && ! str_starts_with($error['file'], realpath(BASEPATH))) { @endphp
    <p style="margin-left:10px">
        File: @php echo $error['file'] @endphp<br/>
        Line: @php echo $error['line'] @endphp<br/>
        Function: @php echo $error['function'] @endphp
    </p>

@php }
        }
} @endphp

</div>
