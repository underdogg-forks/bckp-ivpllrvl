@php

A PHP Error was encountered

Severity:    {{ $severity, "\n" }}
Message:     {{ $message, "\n" }}
Filename:    {{ $filepath, "\n" }}
Line Number: {{ $line }}

@if(defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE)
    Backtrace:
    @foreach(debug_backtrace() as $error)
        @if(isset($error['file']) && ! str_starts_with($error['file'], realpath(BASEPATH)))
            File: {{ $error['file'], "\n" }}
            Line: {{ $error['line'], "\n" }}
            Function: {{ $error['function'], "\n\n" }}
        @php endif
    @php endforeach

<?php endif;
