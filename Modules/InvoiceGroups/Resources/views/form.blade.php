
<form method="post">

    @csrf

    <div id="headerbar">
        <h1 class="headerbar-title">@lang('invoice_group_form')</h1>
        @include('layout.header_buttons')
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                @include('layout.alerts')

                <div class="form-group">
                    <label class="control-label" for="invoice_group_name">
                        @lang('name')
                    </label>
                    <input type="text" name="invoice_group_name" id="invoice_group_name" class="form-control"
                           value="{{ $this->mdl_invoice_groups->form_value('invoice_group_name', true) }}" required>
                </div>

                <div class="form-group">
                    <label class="control-label" for="invoice_group_identifier_format">
                        @lang('identifier_format')
                    </label>
                    <input type="text" class="form-control taggable"
                           name="invoice_group_identifier_format" id="invoice_group_identifier_format"
                           value="{{ $this->mdl_invoice_groups->form_value('invoice_group_identifier_format', true) }}"
                           placeholder="INV-{{{id}}}" required>
                </div>

                <div class="form-group">
                    <label class="control-label" for="invoice_group_next_id">
                        @lang('next_id')
                    </label>
                    <input type="number" name="invoice_group_next_id" id="invoice_group_next_id" class="form-control"
                           value="{{ $this->mdl_invoice_groups->form_value('invoice_group_next_id') }}" required>
                </div>

                <div class="form-group">
                    <label class="control-label" for="invoice_group_left_pad">
                        @lang('left_pad')
                    </label>
                    <input type="number" name="invoice_group_left_pad" id="invoice_group_left_pad" class="form-control"
                           value="{{ $this->mdl_invoice_groups->form_value('invoice_group_left_pad') }}" required>
                </div>

                <hr>

                <div class="form-group no-margin">

                    <label for="tags_client">@lang('identifier_format_template_tags')</label>

                    <p class="small">@lang('identifier_format_template_tags_instructions')</p>

                    <select id="tags_client" class="tag-select form-control">
                        <option value="{{{id}}}">
                            @lang('id')
                        </option>
                        <option value="{{{year}}}">
                            @lang('current_year')
                        </option>
                        <option value="{{{yy}}}">
                            @lang('current_yy')
                        </option>
                        <option value="{{{month}}}">
                            @lang('current_month')
                        </option>
                        <option value="{{{day}}}">
                            @lang('current_day')
                        </option>
                    </select>

                </div>

            </div>
        </div>

    </div>

</form>
