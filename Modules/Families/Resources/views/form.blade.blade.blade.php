@php namespace Modules\Families\Views;

$is_update = $this->mdl_families->form_value('is_update'); @endphp
<form method="post">

    @php _csrf_field(); @endphp

    <div id="headerbar">
        <h1 class="headerbar-title">@php _trans($is_update ? 'family' : 'add_family'); @endphp</h1>
        @php $this->layout->loadView('layout/header_buttons'); @endphp
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                @php $this->layout->loadView('layout/alerts'); @endphp

                <input class="hidden" name="is_update" type="hidden" value="{{ $is_update ? '1' : '0' }}">

                <div class="form-group">
                    <label for="family_name">
                        @@lang('family_name')
                    </label>
                    <input type="text" name="family_name" id="family_name" class="form-control"
                           value="{{ $this->mdl_families->form_value('family_name', true) }}" required>
                </div>

            </div>
        </div>

    </div>

</form>
