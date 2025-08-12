<style>

    .php-error-box {
        margin: 0 0 1em 0;
    }

    .php-error-box--title {
        background: #900;
        color: #fff;
        text-align: center;
        padding: 0.5em;
        border-radius: 4px 4px 0 0;
        font-size: 1.25em;
    }

    .php-error-box--list {
        border: 1px solid #900;
        border-top: 0;
        padding: 0.5em 1em;
        border-radius: 0 0 4px 4px;
        margin: 0;
        list-style: none;
    }

    .php-error-box--item {
        padding: 0.5em 0;
    }

    .php-error-box--item + .php-error-box--item {
        border-top: 1px solid #f9f9f9;
    }

</style>
<div class="php-error-box">
    <div class="php-error-box--title">A PHP Error was encountered</div>
    <ul class="php-error-box--list">
        <li class="php-error-box--item">Severity: {{ $severity }}</li>
        <li class="php-error-box--item">Message: {{ $message }}</li>
        <li class="php-error-box--item">Filename: {{ $filepath }}</li>
        <li class="php-error-box--item">Line Number: {{ $line }}</li>
    </ul>
</div>