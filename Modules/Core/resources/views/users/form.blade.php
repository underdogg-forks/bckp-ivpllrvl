@php
    // Fix item table head when numerous (>= 12) items (overflowing in 2nd page)
    $add_table_and_head_for_sums = 1;

    // Example condition idea (retain for reference)
    // $only_one = ! $invoice->client_einvoicing_active && get_setting('pdf_watermark');
@endphp

<form method="post" action="{{ $this->uri->uri_string() }}" class="form-horizontal">

    <div class="mb-4">
        <label for="user_name">{{ trans('user_name') }}</label>
        <input
            type="text"
            id="user_name"
            name="user_name"
            value="{{ $this->mdl_users->form_value('user_name', true) }}"
            class="form-control"
        >
    </div>

    <div class="mb-4">
        <label for="user_email">{{ trans('user_email') }}</label>
        <input
            type="email"
            id="user_email"
            name="user_email"
            value="{{ $this->mdl_users->form_value('user_email', true) }}"
            class="form-control"
        >
    </div>

    <div class="mb-4">
        <label for="user_type">{{ trans('user_type') }}</label>
        <select
            id="user_type"
            name="user_type"
            class="form-control"
            x-data
            x-on:change="updateEInvoicingRequirement($event.target.value)"
        >
            <option value="0" {{ $this->mdl_users->form_value('user_type') == '0' ? 'selected' : '' }}>
                {{ trans('standard_user') }}
            </option>
            <option value="1" {{ $this->mdl_users->form_value('user_type') == '1' ? 'selected' : '' }}>
                {{ trans('administrator') }}
            </option>
        </select>
    </div>

    <div class="mb-4" @if(!empty($einvoicingReq))
        {{ $einvoicingReq }}
        @endif>
        <label for="user_einvoicing">{{ trans('user_einvoicing') }}</label>
        <input
            type="checkbox"
            id="user_einvoicing"
            name="user_einvoicing"
            value="1"
            {{ $this->mdl_users->form_value('user_einvoicing') ? 'checked' : '' }}
        >
        <span>{{ trans('enable_einvoicing') }}</span>
    </div>

    <div class="mb-4">
        <button type="submit" class="btn btn-primary">
            {{ trans('save') }}
        </button>
        <a href="{{ site_url('users') }}" class="btn btn-secondary">
            {{ trans('cancel') }}
        </a>
    </div>
</form>

<script>
    function updateEInvoicingRequirement(userType) {
        const einvoicingField = document.getElementById('user_einvoicing');
        if (!einvoicingField) return;

        if (userType === '1') {
            einvoicingField.checked = true;
            einvoicingField.disabled = true;
        } else {
            einvoicingField.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const userType = document.getElementById('user_type')?.value;
        if (userType) {
            updateEInvoicingRequirement(userType);
        }
    });
</script>
