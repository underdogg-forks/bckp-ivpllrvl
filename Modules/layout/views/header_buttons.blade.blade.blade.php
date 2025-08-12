@php namespace Modules\Layout\Views; @endphp
<div class="headerbar-item pull-right">
    <div class="btn-group btn-group-sm">
@php
if (!isset($hide_submit_button)) {
    @endphp
        <button id="btn-submit" name="btn_submit" class="btn btn-success ajax-loader" value="1">
            <i class="fa fa-check"></i> @@lang('save')
        </button>
@php
}
if (!isset($hide_cancel_button)) {
    $attribute_cancel = empty($attribute_cancel) ? 'onclick="window.history.back()"' : $attribute_cancel;
    @endphp
        <button type="button" {{ $attribute_cancel }} id="btn-cancel" name="btn_cancel" class="btn btn-danger ajax-loader" value="1">
            <i class="fa fa-times"></i> @@lang('cancel')
        </button>
@php } @endphp
    </div>
</div>
<?php 