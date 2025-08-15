<?php

namespace Modules\Core\Services;

use App\Services\BaseService;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class FormValidationModelService extends BaseService
{
    /**
     * Modules\Core\Models\Form_Validation_Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->form_validation->CI = & $this;
    }
}
