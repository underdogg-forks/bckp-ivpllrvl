<?php

namespace Modules\Core\Models;

use AllowDynamicProperties;
use AllowDynamicProperties;

#[AllowDynamicProperties]
class FormValidationModel extends MyModel
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
