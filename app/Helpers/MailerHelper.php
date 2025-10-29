<?php

namespace App\Helpers;

class MailerHelper
{
    /**
     * @originalName mailer_configured
     *
     * @originalFile mailer_helper.php
     */
    public static function mailerConfigured(): bool
    { // TODO: Replace with Laravel patterns

        return get_setting('email_send_method') == 'phpmail' || get_setting('email_send_method') == 'sendmail' || get_setting('email_send_method') == 'smtp' && get_setting('smtp_server_address');
    }

    /**
     * @originalName email_invoice
     *
     * @originalFile mailer_helper.php
     */
    public static function emailInvoice(string $invoice_id, $invoice_template, array $from, $to, $subject, $body, $cc = null, $bcc = null, $attachments = null)
    { // TODO: Replace with Laravel patterns
        // TODO: Laravel autoloads helpers - ['mailer/phpmailer', 'template', 'invoice', 'pdf']);
        $db_invoice = $CI->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)->get()->row();
        if ($db_invoice->sumex_id == null) {
            $invoice = generate_invoice_pdf($invoice_id, false, $invoice_template);
        } else {
            $invoice = generate_invoice_sumex($invoice_id, false, $invoice_template, true);
        }
        // Need Specific eInvoice filename?
        if ( ! empty($_SERVER['CIIname'])) {
            // Use $options['CIIname' => '{{{tags}}}'] in your config (Helpers/XMLconfigs)
            // Or set $_SERVER['CIIname'] in your generator (libraries/XMLtemplates)
            $_SERVER['CIIname'] = parse_template($db_invoice, $_SERVER['CIIname']);
        }
        $message = parse_template($db_invoice, $body);
        $subject = parse_template($db_invoice, $subject);
        $cc      = parse_template($db_invoice, $cc);
        $bcc     = parse_template($db_invoice, $bcc);
        $from    = [parse_template($db_invoice, $from[0]), parse_template($db_invoice, $from[1])];
        // Check parsed emails before phpmail - since v1.6.3
        $errors = [];
        if ( ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'to_email';
        }
        if ( ! filter_var($from[0], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'from_email';
        }
        if ($cc && ! filter_var($cc, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'cc_email';
        }
        if ($bcc && ! filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'bcc_email';
        }
        check_mail_errors($errors, 'mailer/invoice/' . $invoice_id);
        $message = empty($message) ? ' ' : $message;

        return phpmail_send($from, $to, $subject, $message, $invoice, $cc, $bcc, $attachments);
    }

    /**
     * @originalName email_quote
     *
     * @originalFile mailer_helper.php
     */
    public static function emailQuote(string $quote_id, $quote_template, array $from, $to, $subject, $body, $cc = null, $bcc = null, $attachments = null)
    { // TODO: Replace with Laravel patterns
        // TODO: Laravel autoloads helpers - ['mailer/phpmailer', 'template', 'pdf']);
        $quote    = generate_quote_pdf($quote_id, false, $quote_template);
        $db_quote = $CI->mdl_quotes->where('ip_quotes.quote_id', $quote_id)->get()->row();
        $message  = parse_template($db_quote, $body);
        $subject  = parse_template($db_quote, $subject);
        $cc       = parse_template($db_quote, $cc);
        $bcc      = parse_template($db_quote, $bcc);
        $from     = [parse_template($db_quote, $from[0]), parse_template($db_quote, $from[1])];
        // Check parsed emails before phpmail - since v1.6.3
        $errors = [];
        if ( ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'to_email';
        }
        if ( ! filter_var($from[0], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'from_email';
        }
        if ($cc && ! filter_var($cc, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'cc_email';
        }
        if ($bcc && ! filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'bcc_email';
        }
        check_mail_errors($errors, 'mailer/quote/' . $quote_id);
        $message = empty($message) ? ' ' : $message;

        return phpmail_send($from, $to, $subject, $message, $quote, $cc, $bcc, $attachments);
    }

    /**
     * @originalName email_quote_status
     *
     * @originalFile mailer_helper.php
     */
    public static function emailQuoteStatus(string $quote_id, $status)
    {
        ini_set('display_errors', 'on');
        error_reporting(E_ALL);
        if ( ! mailer_configured()) {
            return false;
        } // TODO: Replace with Laravel patterns
        // TODO: Laravel autoloads helpers - 'mailer/phpmailer');
        $quote      = $CI->mdl_quotes->where('ip_quotes.quote_id', $quote_id)->get()->row();
        $index      = env('REMOVE_INDEXPHP', true) ? '' : 'index.php';
        $base_url   = base_url('/' . $index . '/quotes/view/' . $quote_id);
        $user_email = $quote->user_email;
        $subject    = sprintf(trans('quote_status_email_subject'), $quote->client_name, mb_strtolower(lang($status)), $quote->quote_number);
        $body       = sprintf(nl2br(trans('quote_status_email_body')), $quote->client_name, mb_strtolower(lang($status)), $quote->quote_number, '<a href="' . $base_url . '">' . $base_url . '</a>');

        return phpmail_send($user_email, $user_email, $subject, $body);
    }

    /**
     * @originalName check_mail_errors
     *
     * @originalFile mailer_helper.php
     */
    public static function checkMailErrors(array $errors = [], $redirect = ''): void
    {
        if ($errors) { // TODO: Replace with Laravel patterns
            foreach ($errors as $i => $e) {
                $errors[$i] = strtr(trans('form_validation_valid_email'), ['{field}' => trans($e)]);
            }
            $CI->session->set_flashdata('alert_error', implode('<br>', $errors));
            $redirect = empty($redirect) ? empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'] : $redirect;
            redirect($redirect);
        }
    }
}
