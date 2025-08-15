<?php

namespace Modules\Upload\Services;

use App\Services\BaseService;

use AllowDynamicProperties;
use Modules\Core\Models\ResponseModel;

#[AllowDynamicProperties]
class UploadService extends BaseService
{
    public $table = 'ip_uploads';

    public $primary_key = 'ip_uploads.upload_id';

    public $date_modified_field = 'uploaded_date';

    public $content_types = [
        'avif' => 'image/avif',
        'gif'  => 'image/gif',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg'  => 'image/svg+xml',
        'webp' => 'image/webp',
        'txt'  => 'text/plain',
        'xml'  => 'text/xml',
        'pdf'  => 'application/pdf',
        // file-audio
        'mp3'  => 'audio/mpeg',
        'oga'  => 'audio/ogg',
        'ogg'  => 'audio/ogg',
        'wav'  => 'audio/x-wav',
        'weba' => 'audio/webm',
        // file-document
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'odt'  => 'application/vnd.oasis.opendocument.text',
        // file-spreadsheet
        'xls'  => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
        // file-presentation
        'ppt'  => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'odp'  => 'application/vnd.oasis.opendocument.presentation',
    ];

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile Upload.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_uploads.upload_id ASC');
    }

    /**
     * @originalName create
     *
     * @originalFile Upload.php
     */
    public function create($db_array = null)
    {
        return parent::save(null, $db_array);
    }

    /**
     * @originalName getQuoteUploads
     *
     * @originalFile Upload.php
     */
    public function getQuoteUploads($id)
    {
        $this->load->model('quotes/mdl_quotes');
        $quote = $this->mdl_quotes->getById($id);
        $query = $this->db->query("SELECT file_name_new,file_name_original FROM ip_uploads WHERE url_key = '" . $quote->quote_url_key . "'");
        $names = [];
        if ($query->numRows() > 0) {
            foreach ($query->result() as $row) {
                $names[] = ['path' => UPLOADS_CFILES_FOLDER . $row->file_name_new, 'filename' => $row->file_name_original];
            }
        }

        return $names;
    }

    /**
     * @originalName getInvoiceUploads
     *
     * @originalFile Upload.php
     */
    public function getInvoiceUploads($id)
    {
        $this->load->model('invoices/mdl_invoices');
        $invoice = $this->mdl_invoices->getById($id);
        $query   = $this->db->query("SELECT file_name_new,file_name_original FROM ip_uploads WHERE url_key = '" . $invoice->invoice_url_key . "'");
        $names   = [];
        if ($query->numRows() > 0) {
            foreach ($query->result() as $row) {
                $names[] = ['path' => UPLOADS_CFILES_FOLDER . $row->file_name_new, 'filename' => $row->file_name_original];
            }
        }

        return $names;
    }

    /**
     * @originalName getFiles
     *
     * @originalFile Upload.php
     */
    public function getFiles($url_key)
    {
        $result = [];
        if ($url_key && $rows = $this->where('url_key', $url_key)->get()->result()) {
            foreach ($rows as $row) {
                $size = @filesize(UPLOADS_CFILES_FOLDER . $row->file_name_new);
                if ($size === false) {
                    // Probably Deleted, remove it
                    $this->deleteFile($url_key, $row->file_name_original);
                    continue;
                }
                $result[] = ['name' => $row->file_name_original, 'size' => $size];
            }
        }

        return $result;
    }

    /**
     * @originalName deleteFile
     *
     * @originalFile Upload.php
     */
    public function deleteFile($url_key, $filename)
    {
        $this->db->where(['url_key' => $url_key, 'file_name_original' => $filename])->delete('ip_uploads');
    }

    /**
     * @originalName byClient
     *
     * @originalFile Upload.php
     */
    public function byClient($client_id)
    {
        $this->filter_where('ip_uploads.client_id', $client_id);

        return $this;
    }
}
