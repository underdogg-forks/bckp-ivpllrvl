<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

#[AllowDynamicProperties]
class FormValidationModel extends BaseModel
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
