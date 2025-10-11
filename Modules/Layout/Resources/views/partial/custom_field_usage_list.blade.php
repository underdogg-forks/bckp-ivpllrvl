@if($custom_field_usage)
    @php
        $url = [
            'invoice' => 'invoices/view/',
            'quote' => 'quotes/view/',
            'payment' => 'payments/form/',
            'user' => 'users/form/',
            'client' => 'clients/form/'
        ];
        $what = strtr($custom_field_table, ['ip_' => '', '_custom' => '']);
        $href = site_url($url[$what]);
    @endphp

<div id="used{{ $what }}" class="w-full px-4 md:w-1/2 col-md-offset-3">
    <div class="panel-group" id="accordion{{ $what }}" role="tablist" aria-multiselectable="true">
        <div class="bg-white dark:bg-gray-800 border border-cyan-200 dark:border-cyan-700 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 no-padding rounded" role="tab" id="heading{{ $what }}">
                <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100" role="button" data-toggle="collapse" aria-expanded="true"
                    style="padding:1rem 8px"
                    data-parent="#accordion{{ $what }}" href="#collapse{{ $what }}" aria-controls="collapse{{ $what }}">
                    <i class="more-less fa float-right fa-chevron-down"></i>
                    @lang('custom_used_in')
                </h5>
            </div>
            <div id="collapse{{ $what }}" class="panel-collapse collapse" role="tabpanel"
                 aria-labelledby="heading{{ $what }}">
                <div class="p-6">
                    @php
                        $need_model = false;
                        $links = [];
                        if (in_array($what, ['invoice', 'quote'])) {
                            $need_model = true;
                            $model = 'mdl_' . $what . 's';
                            $CI = get_instance();
                            $CI->load->model($what . 's/' . $model);
                        }
                    @endphp
                    @foreach($custom_field_usage as $obj)
                        @php
                            $fid = $what . '_id';
                            $id = $obj->{$fid};
                            $display = $id;
                            if ($need_model) {
                                $display = '#' . $CI->{$model}->getById($id)->{$what . '_number'};
                            }
                            $links[] = anchor($href . $id, trans($what) . '&nbsp;' . $display);
                        @endphp
                    @endforeach
                    {!! !empty($links) ? implode(', ', $links) : '' !!}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleIcon(e) {
        $(e.target)
            .prev('.panel-heading')
            .find('.more-less')
            .toggleClass('fa-chevron-down fa-chevron-up');
    }

    $('.panel-group').on('hidden.bs.collapse', toggleIcon);
    $('.panel-group').on('shown.bs.collapse', toggleIcon);
</script>
@endif
