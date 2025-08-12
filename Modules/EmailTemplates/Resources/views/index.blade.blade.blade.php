@php namespace Modules\Emailtemplates\Views; @endphp
<div id="headerbar">
    <h1 class="headerbar-title">@@lang('email_templates')</h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="{{ url('email_templates/form') }}">
            <i class="fa fa-plus"></i> @@lang('new')
        </a>
    </div>

    <div class="headerbar-item pull-right">
        {{ pager(site_url('email_templates/index'), 'mdl_email_templates') }}
    </div>
</div>

<div id="content" class="table-content">

    {{ $this->layout->loadView('layout/alerts') }}

    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>@@lang('title')</th>
            <th>@@lang('type')</th>
            <th>@@lang('options')</th>
        </tr>
        </thead>

        <tbody>
        @php foreach ($email_templates as $email_template) {
    @endphp
            <tr>
                <td>@php
    _htmlsc($email_template->email_template_title);
    @endphp</td>
                <td>{{ lang($email_template->email_template_type) }}</td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#"><i
                                    class="fa fa-cog"></i> @@lang('options')</a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ url('email_templates/form/' . $email_template->email_template_id) }}">
                                    <i class="fa fa-edit fa-margin"></i> @@lang('edit')
                                </a>
                            </li>
                            <li>
                                <form action="{{ url('email_templates/delete/' . $email_template->email_template_id) }}"
                                      method="POST">
                                    @php
    _csrf_field();
    @endphp
                                    <button type="submit" class="dropdown-button"
                                            onclick="return confirm('@@lang('delete_record_warning')');">
                                        <i class="fa fa-trash-o fa-margin"></i> @@lang('delete')
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php
} @endphp
        </tbody>

    </table>

</div>
<?php 
