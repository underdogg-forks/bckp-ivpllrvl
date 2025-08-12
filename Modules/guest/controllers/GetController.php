<?php

namespace Modules\Guest\Controllers;

use Modules\Core\Controllers\BaseController;

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
class GetController extends BaseController
{
    public $targetPath = UPLOADS_CFILES_FOLDER;

    // UPLOADS_FOLDER . 'customer_files/'
    public $ctype_default = 'application/octet-stream';

    public $content_types = [];

    /**
     * UploadController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('upload/mdl_uploads');
        $this->content_types = $this->mdl_uploads->content_types;
    }

    /**
     * @originalName showFiles
     *
     * @originalFile GetController.php
     */
    public function showFiles($url_key = null): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($url_key && ! $result = $this->mdl_uploads->getFiles($url_key)) {
            exit('{}');
        }
        echo json_encode($result);
        exit;
    }

    /**
     * @originalName getFile
     *
     * @originalFile GetController.php
     */
    public function getFile($filename): void
    {
        $filename = urldecode($filename);
        if ( ! file_exists($this->targetPath . $filename)) {
            $ref = isset($_SERVER['HTTP_REFERER']) ? ', Referer:' . $_SERVER['HTTP_REFERER'] : '';
            $this->respondMessage(404, 'upload_error_file_not_found', $this->targetPath . $filename . $ref);
        }
        $path_parts = pathinfo($this->targetPath . $filename);
        $file_ext   = mb_strtolower($path_parts['extension'] ?? '');
        $ctype      = $this->content_types[$file_ext] ?? $this->ctype_default;
        $file_size  = filesize($this->targetPath . $filename);
        header('Expires: -1');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Type: ' . $ctype);
        header('Content-Length: ' . $file_size);
        readfile($this->targetPath . $filename);
    }

    /**
     * @originalName respondMessage
     *
     * @originalFile GetController.php
     */
    private function respondMessage(int $httpCode, string $messageKey, string $dynamicLogValue = ''): void
    {
        log_message('debug', 'guest/get: ' . trans($messageKey) . ': (status ' . $httpCode . ') ' . $dynamicLogValue);
        http_response_code($httpCode);
        _trans($messageKey);
        exit;
    }
}
