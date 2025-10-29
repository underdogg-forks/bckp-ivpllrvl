<?php

namespace Modules\Core\Models;

use Illuminate\Support\Facades\DB;

use AllowDynamicProperties;

use function App\Models\validation_errors;

/**
 * CodeIgniter CRUD Model 2
 * A base model providing CRUD, pagination and validation.
 *
 * Install this file as application/core/Modules\Core\Models\MY_Model.php
 *
 * @author        Jesse Terry
 * @copyright     Copyright (c) 2012-2013, Jesse Terry
 *
 * @see          http://developer13.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
#[AllowDynamicProperties]
class MyModel
{
    public $table;

    public $primary_key;

    /** @var int */
    public $default_limit = 15;

    public $page_links;

    public $query;

    /** @var array */
    public $form_values = [];

    public $validation_errors;

    public $total_rows;

    public $date_created_field;

    public $date_modified_field;

    /** @var array */
    public $native_methods = ['select', 'select_max', 'select_min', 'select_avg', 'select_sum', 'join', 'where', 'or_where', 'where_in', 'or_where_in', 'where_not_in', 'or_where_not_in', 'like', 'or_like', 'not_like', 'or_not_like', 'group_by', 'distinct', 'having', 'or_having', 'order_by', 'limit'];

    /** @var int */
    public $total_pages = 0;

    /** @var int */
    public $current_page;

    /** @var int */
    public $next_page;

    /** @var int */
    public $previous_page;

    /** @var int */
    public $offset;

    /** @var int */
    public $next_offset;

    /** @var int */
    public $previous_offset;

    /** @var int */
    public $last_offset;

    /** @var int */
    public $id;

    /** @var array */
    public $filter = [];

    /** @var string */
    protected $default_validation_rules = 'validation_rules';

    /** @var array */
    protected $validation_rules;

    /**
     * @originalName __call
     *
     * @originalFile MyModel.php
     */
    public function call($name, $arguments)
    {
        if (mb_substr($name, 0, 7) === 'filter_') {
            $this->filter[] = [mb_substr($name, 7), $arguments];
        } else {
            call_user_func_array([$this->db, $name], $arguments);
        }

        return $this;
    }

    /**
     * @originalName get
     *
     * @originalFile MyModel.php
     */
    public function get($include_defaults = true)
    {
        if ($include_defaults) {
            $this->setDefaults();
        }
        $this->runFilters();
        $this->query  = DB::get($this->table);
        $this->filter = [];

        return $this;
    }

    /**
     * @originalName paginate
     *
     * @originalFile MyModel.php
     */
    public function paginate($base_url, $offset = 0, $uri_segment = 3)
    {
// TODO: Laravel autoloads helpers - $this->load->helper('url');
// TODO: Use Laravel services/facades - $this->load->library('pagination');
        $this->offset       = $offset;
        $default_list_limit = $this->mdl_settings->setting('default_list_limit');
        $per_page           = empty($default_list_limit) ? $this->default_limit : $default_list_limit;
        $this->setDefaults();
        $this->runFilters();
        DB::limit($per_page, $this->offset);
        $this->query           = DB::get($this->table);
        $this->total_rows      = DB::query('SELECT FOUND_ROWS() AS num_rows')->row()->num_rows;
        $this->total_pages     = ceil($this->total_rows / $per_page);
        $this->previous_offset = $this->offset - $per_page;
        $this->next_offset     = $this->offset + $per_page;
        $config                = ['base_url' => $base_url, 'total_rows' => $this->total_rows, 'per_page' => $per_page];
        $this->last_offset     = $this->total_pages * $per_page - $per_page;
        if (config('pagination_style')) {
            $config = array_merge($config, config('pagination_style'));
        }
        $this->pagination->initialize($config);
        $this->page_links = $this->pagination->create_links();
    }

    /**
     * @originalName save
     *
     * @originalFile MyModel.php
     */
    public function save($id = null, $db_array = null)
    {
        if ( ! $db_array) {
            $db_array = $this->dbArray();
        }
        $datetime = date('Y-m-d H:i:s');
        if ( ! $id) {
            if ($this->date_created_field) {
                if (is_array($db_array)) {
                    $db_array[$this->date_created_field] = $datetime;
                    if ($this->date_modified_field) {
                        $db_array[$this->date_modified_field] = $datetime;
                    }
                } else {
                    $db_array->{$this->date_created_field} = $datetime;
                    if ($this->date_modified_field) {
                        $db_array->{$this->date_modified_field} = $datetime;
                    }
                }
            } elseif ($this->date_modified_field) {
                if (is_array($db_array)) {
                    $db_array[$this->date_modified_field] = $datetime;
                } else {
                    $db_array->{$this->date_modified_field} = $datetime;
                }
            }
            DB::insert($this->table, $db_array);

            return DB::insert_id();
        }
        if ($this->date_modified_field) {
            if (is_array($db_array)) {
                $db_array[$this->date_modified_field] = $datetime;
            } else {
                $db_array->{$this->date_modified_field} = $datetime;
            }
        }
        DB::where($this->primary_key, $id);
        DB::update($this->table, $db_array);

        return $id;
    }

    /**
     * @originalName db_array
     *
     * @originalFile MyModel.php
     */
    public function dbArray()
    {
        $db_array         = [];
        $validation_rules = $this->{$this->validation_rules}();
        foreach (request()->input() as $key => $value) {
            if (array_key_exists($key, $validation_rules)) {
                $db_array[$key] = $value;
            }
        }

        return $db_array;
    }

    /**
     * @originalName delete
     *
     * @originalFile MyModel.php
     */
    public function delete($id)
    {
        DB::where($this->primary_key, $id);
        DB::delete($this->table);
    }

    /**
     * @originalName result
     *
     * @originalFile MyModel.php
     */
    public function result()
    {
        return $this->query->result();
    }

    /**
     * @originalName row
     *
     * @originalFile MyModel.php
     */
    public function row()
    {
        return $this->query->row();
    }

    /**
     * @originalName result_array
     *
     * @originalFile MyModel.php
     */
    public function resultArray()
    {
        return $this->query->resultArray();
    }

    /**
     * @originalName row_array
     *
     * @originalFile MyModel.php
     */
    public function rowArray()
    {
        return $this->query->rowArray();
    }

    /**
     * @originalName num_rows
     *
     * @originalFile MyModel.php
     */
    public function numRows()
    {
        return $this->query->numRows();
    }

    /**
     * @originalName prep_form
     *
     * @originalFile MyModel.php
     */
    public function prepForm($id = null)
    {
        if ( ! $_POST && $id) {
            $row = $this->getById($id);
            if ($row) {
                foreach ($row as $key => $value) {
                    $this->form_values[$key] = $value;
                }

                return true;
            }

            return false;
        }
        if ( ! $id) {
            return true;
        }
    }

    /**
     * @originalName get_by_id
     *
     * @originalFile MyModel.php
     */
    public function getById($id)
    {
        return $this->where($this->primary_key, $id)->get()->row();
    }

    /**
     * @originalName run_validation
     *
     * @originalFile MyModel.php
     */
    public function runValidation($validation_rules = null)
    {
        if ( ! $validation_rules) {
            $validation_rules = $this->default_validation_rules;
        }
        foreach (array_keys($_POST) as $key) {
            $this->form_values[$key] = request()->input($key);
        }
        if (method_exists($this, $validation_rules)) {
            $this->validation_rules = $validation_rules;
// TODO: Use Laravel services/facades - $this->load->library('form_validation');
            // TODO: Move to Form Request - $this->form_validation->set_rules($this->{$validation_rules}());
            $run                     = // TODO: Move to Form Request - $this->form_validation->run();
            $this->validation_errors = validation_errors();

            return $run;
        }
    }

    /**
     * @originalName form_value
     *
     * @originalFile MyModel.php
     */
    public function formValue($key, $escape = false)
    {
        $value = $this->form_values[$key] ?? '';

        return $escape ? htmlspecialchars($value) : $value;
    }

    /**
     * @originalName set_form_value
     *
     * @originalFile MyModel.php
     */
    public function setFormValue($key, $value)
    {
        $this->form_values[$key] = $value;
    }

    /**
     * @originalName set_id
     *
     * @originalFile MyModel.php
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @originalName set_defaults
     *
     * @originalFile MyModel.php
     */
    private function setDefaults($exclude = [])
    {
        $native_methods = $this->native_methods;
        foreach ($exclude as $unset_method) {
            unset($native_methods[array_search($unset_method, $native_methods, true)]);
        }
        foreach ($native_methods as $native_method) {
            $native_method = 'default_' . $native_method;
            if (method_exists($this, $native_method)) {
                $this->{$native_method}();
            }
        }
    }

    /**
     * @originalName run_filters
     *
     * @originalFile MyModel.php
     */
    private function runFilters()
    {
        foreach ($this->filter as $filter) {
            call_user_func_array([$this->db, $filter[0]], $filter[1]);
        }
        /*
         * Clear the filter array since this should only be run once per model
         * execution
         */
        $this->filter = [];
    }
}
