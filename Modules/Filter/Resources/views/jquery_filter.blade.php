@php namespace Modules\Filter\Views; @endphp
<script>
    var delay = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();

    $(function () {
        $('#filter').keyup(function () {
            delay(function () {
                $.post('{{ url('filter/ajax/' . $filter_method) }}',
                    {
                        filter_query: $('#filter').val()
                    }, function (data) {
                        @if(config('app.ip_debug'))
console.log(data);
@endif
                        $('#filter_results').html(data);
                    });
            }, 1000);
        });
    });
</script>
