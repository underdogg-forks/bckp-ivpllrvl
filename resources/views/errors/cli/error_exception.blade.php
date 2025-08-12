@php  @endphp

An uncaught Exception was encountered

Type:        {{ get_class($exception), "\n" }}
Message:     {{ $message, "\n" }}
Filename:    {{ $exception->getFile(), "\n" }}
Line Number: {{ $exception->getLine() }}

@if(defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE)
    Backtrace:
    @foreach($exception->getTrace() as $error)
        @if(isset($error['file']) && ! str_starts_with($error['file'], realpath(BASEPATH)))
            File: {{ $error['file'], "\n" }}
            Line: {{ $error['line'], "\n" }}
            Function: {{ $error['function'], "\n\n" }}
        @php endif @endphp
    @php endforeach @endphp

<?php endif;
