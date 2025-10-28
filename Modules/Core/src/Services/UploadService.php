<?php

namespace Modules\Core\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use src\Models\Upload;
use const Modules\Upload\Services\UPLOADS_CFILES_FOLDER;

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
    public function defaultOrderBy(): void
    {
        // Eloquent handles ordering in queries, not in the service
    }

    /**
     * @originalName create
     *
     * @originalFile Upload.php
     */
    public function create(array $data = []): Upload
    {
        return Upload::create($data);
    }

    /**
     * @originalName getQuoteUploads
     *
     * @originalFile Upload.php
     */
    public function getQuoteUploads(int $quoteId): array
    {
        $quote   = \Modules\Quotes\Models\Quote::findOrFail($quoteId);
        $uploads = Upload::query()->where('url_key', $quote->quote_url_key)->get(['file_name_new', 'file_name_original']);
        $names   = [];
        foreach ($uploads as $upload) {
            $names[] = [
                'path'     => UPLOADS_CFILES_FOLDER . $upload->file_name_new,
                'filename' => $upload->file_name_original,
            ];
        }

        return $names;
    }

    /**
     * @originalName getInvoiceUploads
     *
     * @originalFile Upload.php
     */
    public function getInvoiceUploads(int $invoiceId): array
    {
        $invoice = \Modules\Invoices\Models\Invoice::findOrFail($invoiceId);
        $uploads = Upload::query()->where('url_key', $invoice->invoice_url_key)->get(['file_name_new', 'file_name_original']);
        $names   = [];
        foreach ($uploads as $upload) {
            $names[] = [
                'path'     => UPLOADS_CFILES_FOLDER . $upload->file_name_new,
                'filename' => $upload->file_name_original,
            ];
        }

        return $names;
    }

    /**
     * @originalName getFiles
     *
     * @originalFile Upload.php
     */
    public function getFiles(string $url_key): array
    {
        $result  = [];
        $uploads = Upload::query()->where('url_key', $url_key)->get();
        foreach ($uploads as $upload) {
            $size = @filesize(UPLOADS_CFILES_FOLDER . $upload->file_name_new);
            if ($size === false) {
                $this->deleteFile($url_key, $upload->file_name_original);
                continue;
            }
            $result[] = [
                'name' => $upload->file_name_original,
                'size' => $size,
            ];
        }

        return $result;
    }

    /**
     * @originalName deleteFile
     *
     * @originalFile Upload.php
     */
    public function deleteFile(string $url_key, string $filename): void
    {
        Upload::query()->where('url_key', $url_key)
            ->where('file_name_original', $filename)
            ->delete();
    }

    /**
     * @originalName byClient
     *
     * @originalFile Upload.php
     */
    public function byClient(int $client_id)
    {
        return Upload::query()->where('client_id', $client_id);
    }
}
