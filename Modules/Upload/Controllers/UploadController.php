<?php

namespace Modules\Upload\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\Upload\Services\UploadService;

#[AllowDynamicProperties]
class UploadController extends AdminController
{
    public $targetPath = UPLOADS_CFILES_FOLDER;

    // UPLOADS_FOLDER . 'customer_files/';
    public $ctype_default = 'application/octet-stream';

    public $content_types = [];

    /**
     * UploadController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('upload/mdl_uploads');
        $this->content_types = (new UploadService())->content_types;
    }

    /**
     * @originalName uploadFile
     *
     * @originalFile UploadController.php
     */
    public function uploadFile(int $customerId, string $url_key): void
    {
        if (empty($_FILES['file']['name'])) {
            $this->respondMessage(400, 'upload_error_no_file');
        }
        $filename = $this->sanitizeFileName($_FILES['file']['name']);
        $filePath = $this->getTargetFilePath($url_key, $filename);
        if (file_exists($filePath)) {
            $this->respondMessage(409, 'upload_error_duplicate_file', $filename);
        }
        $tempFile = $_FILES['file']['tmp_name'];
        $this->validateMimeType(mime_content_type($tempFile));
        $this->moveUploadedFile($tempFile, $filePath, $filename);
        $this->saveFileMetadata($customerId, $url_key, $filename);
        $this->respondMessage(200, 'upload_file_uploaded_successfully', $filename);
    }

    /**
     * @originalName createDir
     *
     * @originalFile UploadController.php
     */
    public function createDir($path, $chmod = '0755'): bool
    {
        if ( ! is_dir($path) && ! is_link($path)) {
            return mkdir($path, $chmod);
        }

        return true;
    }

    /**
     * @originalName showFiles
     *
     * @originalFile UploadController.php
     */
    public function showFiles($url_key = null): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($url_key && ! $result = (new UploadService())->getFiles($url_key)) {
            exit('{}');
        }
        exit(json_encode($result));
    }

    /**
     * @originalName deleteFile
     *
     * @originalFile UploadController.php
     */
    public function deleteFile(string $url_key): void
    {
        $filename  = urldecode($this->input->post('name'));
        $finalPath = $this->targetPath . $url_key . '_' . $filename;
        if (realpath($this->targetPath) === mb_substr(realpath($finalPath), 0, mb_strlen(realpath($this->targetPath))) && ( ! file_exists($finalPath) || @unlink($finalPath))) {
            (new UploadService())->deleteFile($url_key, $filename);
            $this->respondMessage(200, 'upload_file_deleted_successfully', $filename);
        }
        $ref = isset($_SERVER['HTTP_REFERER']) ? ', Referer:' . $_SERVER['HTTP_REFERER'] : '';
        $this->respondMessage(410, 'upload_error_file_delete', $finalPath . $ref);
    }

    /**
     * @originalName getFile
     *
     * @originalFile UploadController.php
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
     * @originalName sanitizeFileName
     *
     * @originalFile UploadController.php
     */
    private function sanitizeFileName(string $filename): string
    {
        // Clean filename (same in dropzone script)
        return preg_replace("/[^\\p{L}\\p{N}\\s\\-_'’.]/u", '', mb_trim($filename));
    }

    /**
     * @originalName getTargetFilePath
     *
     * @originalFile UploadController.php
     */
    private function getTargetFilePath(string $url_key, string $filename): string
    {
        return $this->targetPath . $url_key . '_' . $filename;
    }

    /**
     * @originalName validateMimeType
     *
     * @originalFile UploadController.php
     */
    private function validateMimeType(string $mimeType): void
    {
        $allowedTypes = array_values($this->content_types);
        if ( ! in_array($mimeType, $allowedTypes, true)) {
            $this->respondMessage(415, 'upload_error_unsupported_file_type', $mimeType);
        }
    }

    /**
     * @originalName saveFileMetadata
     *
     * @originalFile UploadController.php
     */
    private function saveFileMetadata(int $customerId, string $url_key, string $filename): void
    {
        $data = ['client_id' => $customerId, 'url_key' => $url_key, 'file_name_original' => $filename, 'file_name_new' => $url_key . '_' . $filename];
        if ( ! (new UploadService())->create($data)) {
            $this->respondMessage(500, 'upload_error_database', $filename);
        }
    }

    /**
     * @originalName moveUploadedFile
     *
     * @originalFile UploadController.php
     */
    private function moveUploadedFile(string $tempFile, string $filePath, string $filename): void
    {
        // Create the target dir (if unexist)
        $this->createDir($this->targetPath);
        // Checks to ensure that the target dir is writable
        if ( ! is_writable($this->targetPath)) {
            $this->respondMessage(410, 'upload_error_folder_not_writable', $this->targetPath);
        } elseif ( ! move_uploaded_file($tempFile, $filePath)) {
            $this->respondMessage(400, 'upload_error_invalid_move_uploaded_file', $filename);
        }
    }

    /**
     * @originalName respondMessage
     *
     * @originalFile UploadController.php
     */
    private function respondMessage(int $httpCode, string $messageKey, string $dynamicLogValue = ''): void
    {
        log_message('debug', trans($messageKey) . ': (status ' . $httpCode . ') ' . $dynamicLogValue);
        http_response_code($httpCode);
        _trans($messageKey);
        if ($httpCode == 410) {
            echo PHP_EOL . PHP_EOL . '"' . basename(UPLOADS_FOLDER) . DIRECTORY_SEPARATOR . basename($this->targetPath) . '"';
        }
        exit;
    }
}
