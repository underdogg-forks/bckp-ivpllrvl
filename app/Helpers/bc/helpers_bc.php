<?php

// Utility to convert "echoing void method" → string result
use App\Helpers\ClientHelper;
use App\Helpers\CountryHelper;
use App\Helpers\CustomValuesHelper;
use App\Helpers\DateHelper;
use App\Helpers\DiacriticsHelper;
use App\Helpers\DropzoneHelper;
use App\Helpers\EchoHelper;
use App\Helpers\EInvoiceHelper;
use App\Helpers\InvoiceHelper;
use App\Helpers\JsonErrorHelper;
use App\Helpers\MailerHelper;
use App\Helpers\MpdfHelper;
use App\Helpers\NumberHelper;
use App\Helpers\OrphanHelper;
use App\Helpers\PagerHelper;
use App\Helpers\PaymentsHelper;
use App\Helpers\PdfHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\SettingsHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\TransHelper;
use App\Helpers\UserHelper;

if ( ! function_exists('__bc_capture_static')) {
    /**
     * @param callable          $callable [ClassName::class, 'method']
     * @param array<int, mixed> $args
     *
     * @return string
     */
    function __bc_capture_static(callable $callable, array $args): string
    {
        ob_start();
        $callable(...$args);

        return (string) ob_get_clean();
    }
}

// Utility to call side-effect methods and not “use” their void result
if ( ! function_exists('__bc_just_call')) {
    /**
     * @param callable          $callable
     * @param array<int, mixed> $args
     *
     * @return void
     */
    function __bc_just_call(callable $callable, array $args): void
    {
        $callable(...$args);
    }
}

if ( ! function_exists('format_client')) {
    function format_client(...$args)
    {
        return ClientHelper::formatClient(...$args);
    }
}
if ( ! function_exists('format_gender')) {
    function format_gender(...$args)
    {
        return ClientHelper::formatGender(...$args);
    }
}
if ( ! function_exists('get_country_list')) {
    function get_country_list(...$args)
    {
        return CountryHelper::getCountryList(...$args);
    }
}
if ( ! function_exists('get_country_name')) {
    function get_country_name(...$args)
    {
        return CountryHelper::getCountryName(...$args);
    }
}
if ( ! function_exists('format_date')) {
    function format_date(...$args)
    {
        return CustomValuesHelper::formatDate(...$args);
    }
}
if ( ! function_exists('format_text')) {
    function format_text(...$args)
    {
        return CustomValuesHelper::formatText(...$args);
    }
}
if ( ! function_exists('format_singlechoice')) {
    function format_singlechoice(...$args)
    {
        return CustomValuesHelper::formatSinglechoice(...$args);
    }
}
if ( ! function_exists('format_multiplechoice')) {
    function format_multiplechoice(...$args)
    {
        return CustomValuesHelper::formatMultiplechoice(...$args);
    }
}
if ( ! function_exists('format_boolean')) {
    function format_boolean(...$args)
    {
        return CustomValuesHelper::formatBoolean(...$args);
    }
}
if ( ! function_exists('format_avs')) {
    function format_avs(...$args)
    {
        return CustomValuesHelper::formatAvs(...$args);
    }
}
if ( ! function_exists('format_fallback')) {
    function format_fallback(...$args)
    {
        return CustomValuesHelper::formatFallback(...$args);
    }
}
if ( ! function_exists('print_field')) {
    function print_field(...$args)
    {
        return CustomValuesHelper::printField(...$args);
    }
}
if ( ! function_exists('date_formats')) {
    function date_formats(...$args)
    {
        return DateHelper::dateFormats(...$args);
    }
}
if ( ! function_exists('date_from_mysql')) {
    function date_from_mysql(...$args)
    {
        return DateHelper::dateFromMysql(...$args);
    }
}
if ( ! function_exists('date_from_timestamp')) {
    function date_from_timestamp(...$args)
    {
        return DateHelper::dateFromTimestamp(...$args);
    }
}
if ( ! function_exists('date_to_mysql')) {
    function date_to_mysql(...$args)
    {
        return DateHelper::dateToMysql(...$args);
    }
}
if ( ! function_exists('is_date')) {
    function is_date(...$args)
    {
        return DateHelper::isDate(...$args);
    }
}
if ( ! function_exists('date_format_setting')) {
    function date_format_setting(...$args)
    {
        return DateHelper::dateFormatSetting(...$args);
    }
}
if ( ! function_exists('date_format_datepicker')) {
    function date_format_datepicker(...$args)
    {
        return DateHelper::dateFormatDatepicker(...$args);
    }
}
if ( ! function_exists('increment_user_date')) {
    function increment_user_date(...$args)
    {
        return DateHelper::incrementUserDate(...$args);
    }
}
if ( ! function_exists('increment_date')) {
    function increment_date(...$args)
    {
        return DateHelper::incrementDate(...$args);
    }
}
if ( ! function_exists('diacritics_seems_utf8')) {
    function diacritics_seems_utf8(...$args)
    {
        return DiacriticsHelper::diacriticsSeemsUtf8(...$args);
    }
}
if ( ! function_exists('diacritics_remove_accents')) {
    function diacritics_remove_accents(...$args)
    {
        return DiacriticsHelper::diacriticsRemoveAccents(...$args);
    }
}
if ( ! function_exists('diacritics_remove_diacritics')) {
    function diacritics_remove_diacritics(...$args)
    {
        return DiacriticsHelper::diacriticsRemoveDiacritics(...$args);
    }
}
if ( ! function_exists('_dropzone_html')) {
    function _dropzone_html(...$args)
    {
        return DropzoneHelper::DropzoneHtml(...$args);
    }
}
if ( ! function_exists('_dropzone_script')) {
    function _dropzone_script(...$args)
    {
        return DropzoneHelper::DropzoneScript(...$args);
    }
}
if ( ! function_exists('generate_xml_invoice_file')) {
    function generate_xml_invoice_file(...$args)
    {
        return EInvoiceHelper::generateXmlInvoiceFile(...$args);
    }
}
if ( ! function_exists('include_rdf')) {
    function include_rdf(...$args)
    {
        return EInvoiceHelper::includeRdf(...$args);
    }
}
if ( ! function_exists('get_xml_template_files')) {
    function get_xml_template_files(...$args)
    {
        return EInvoiceHelper::getXmlTemplateFiles(...$args);
    }
}
if ( ! function_exists('get_xml_full_name')) {
    function get_xml_full_name(...$args)
    {
        return EInvoiceHelper::getXmlFullName(...$args);
    }
}
if ( ! function_exists('get_admin_active_users')) {
    function get_admin_active_users(...$args)
    {
        return EInvoiceHelper::getAdminActiveUsers(...$args);
    }
}
if ( ! function_exists('get_req_fields_einvoice')) {
    function get_req_fields_einvoice(...$args)
    {
        return EInvoiceHelper::getReqFieldsEinvoice(...$args);
    }
}
if ( ! function_exists('get_einvoice_usage')) {
    function get_einvoice_usage(...$args)
    {
        return EInvoiceHelper::getEinvoiceUsage(...$args);
    }
}
if ( ! function_exists('get_items_tax_usages')) {
    function get_items_tax_usages(...$args)
    {
        return EInvoiceHelper::getItemsTaxUsages(...$args);
    }
}
if ( ! function_exists('items_tax_usages_bad')) {
    function items_tax_usages_bad(...$args)
    {
        return EInvoiceHelper::itemsTaxUsagesBad(...$args);
    }
}
if ( ! function_exists('htmlsc')) {
    function htmlsc(...$args)
    {
        return EchoHelper::htmlsc(...$args);
    }
}
if ( ! function_exists('_htmlsc')) {
    function _htmlsc(...$args)
    {
        return EchoHelper::Htmlsc(...$args);
    }
}
if ( ! function_exists('_htmle')) {
    function _htmle(...$args)
    {
        return EchoHelper::Htmle(...$args);
    }
}
if ( ! function_exists('_trans')) {
    function _trans(...$args)
    {
        return EchoHelper::Trans(...$args);
    }
}
if ( ! function_exists('_auto_link')) {
    function _auto_link(...$args)
    {
        return EchoHelper::AutoLink(...$args);
    }
}
if ( ! function_exists('_csrf_field')) {
    function _csrf_field(...$args)
    {
        return EchoHelper::CsrfField(...$args);
    }
}
if ( ! function_exists('_theme_asset')) {
    function _theme_asset(...$args)
    {
        return EchoHelper::ThemeAsset(...$args);
    }
}
if ( ! function_exists('_core_asset')) {
    function _core_asset(...$args)
    {
        return EchoHelper::CoreAsset(...$args);
    }
}
if ( ! function_exists('invoice_logo')) {
    function invoice_logo(...$args)
    {
        return InvoiceHelper::invoiceLogo(...$args);
    }
}
if ( ! function_exists('invoice_logo_pdf')) {
    function invoice_logo_pdf(...$args)
    {
        return InvoiceHelper::invoiceLogoPdf(...$args);
    }
}
if ( ! function_exists('invoice_genCodeline')) {
    function invoice_genCodeline(...$args)
    {
        return InvoiceHelper::invoiceGenCodeline(...$args);
    }
}
if ( ! function_exists('invoice_recMod10')) {
    function invoice_recMod10(...$args)
    {
        return InvoiceHelper::invoiceRecMod10(...$args);
    }
}
if ( ! function_exists('invoice_qrcode')) {
    function invoice_qrcode(...$args)
    {
        return InvoiceHelper::invoiceQrcode(...$args);
    }
}
if ( ! function_exists('json_errors')) {
    function json_errors(...$args)
    {
        return JsonErrorHelper::jsonErrors(...$args);
    }
}
if ( ! function_exists('mailer_configured')) {
    function mailer_configured(...$args)
    {
        return MailerHelper::mailerConfigured(...$args);
    }
}
if ( ! function_exists('email_invoice')) {
    function email_invoice(...$args)
    {
        return MailerHelper::emailInvoice(...$args);
    }
}
if ( ! function_exists('email_quote')) {
    function email_quote(...$args)
    {
        return MailerHelper::emailQuote(...$args);
    }
}
if ( ! function_exists('email_quote_status')) {
    function email_quote_status(...$args)
    {
        return MailerHelper::emailQuoteStatus(...$args);
    }
}
if ( ! function_exists('check_mail_errors')) {
    function check_mail_errors(...$args)
    {
        return MailerHelper::checkMailErrors(...$args);
    }
}
if ( ! function_exists('pdf_create')) {
    function pdf_create(...$args)
    {
        return MpdfHelper::pdfCreate(...$args);
    }
}
if ( ! function_exists('format_currency')) {
    function format_currency(...$args)
    {
        return NumberHelper::formatCurrency(...$args);
    }
}
if ( ! function_exists('format_amount')) {
    function format_amount(...$args)
    {
        return NumberHelper::formatAmount(...$args);
    }
}
if ( ! function_exists('format_quantity')) {
    function format_quantity(...$args)
    {
        return NumberHelper::formatQuantity(...$args);
    }
}
if ( ! function_exists('standardize_amount')) {
    function standardize_amount(...$args)
    {
        return NumberHelper::standardizeAmount(...$args);
    }
}
if ( ! function_exists('delete_orphans')) {
    function delete_orphans(...$args)
    {
        return OrphanHelper::deleteOrphans(...$args);
    }
}
if ( ! function_exists('pager')) {
    function pager(...$args)
    {
        return PagerHelper::pager(...$args);
    }
}
if ( ! function_exists('get_currencies')) {
    function get_currencies(...$args)
    {
        return PaymentsHelper::getCurrencies(...$args);
    }
}
if ( ! function_exists('discount_global_print_in_pdf')) {
    function discount_global_print_in_pdf(...$args)
    {
        return PdfHelper::discountGlobalPrintInPdf(...$args);
    }
}
if ( ! function_exists('generate_invoice_pdf')) {
    function generate_invoice_pdf(...$args)
    {
        return PdfHelper::generateInvoicePdf(...$args);
    }
}
if ( ! function_exists('generate_invoice_sumex')) {
    function generate_invoice_sumex(...$args)
    {
        return PdfHelper::generateInvoiceSumex(...$args);
    }
}
if ( ! function_exists('generate_quote_pdf')) {
    function generate_quote_pdf(...$args)
    {
        return PdfHelper::generateQuotePdf(...$args);
    }
}
if ( ! function_exists('redirect_to')) {
    function redirect_to(...$args)
    {
        return RedirectHelper::redirectTo(...$args);
    }
}
if ( ! function_exists('redirect_to_set')) {
    function redirect_to_set(...$args)
    {
        return RedirectHelper::redirectToSet(...$args);
    }
}
if ( ! function_exists('get_setting')) {
    function get_setting(...$args)
    {
        return SettingsHelper::getSetting(...$args);
    }
}
if ( ! function_exists('get_gateway_settings')) {
    function get_gateway_settings(...$args)
    {
        return SettingsHelper::getGatewaySettings(...$args);
    }
}
if ( ! function_exists('check_select')) {
    function check_select(...$args)
    {
        return SettingsHelper::checkSelect(...$args);
    }
}
if ( ! function_exists('parse_template')) {
    function parse_template(...$args)
    {
        return TemplateHelper::parseTemplate(...$args);
    }
}
if ( ! function_exists('get_invoice_status')) {
    function get_invoice_status(...$args)
    {
        return TemplateHelper::getInvoiceStatus(...$args);
    }
}
if ( ! function_exists('select_pdf_invoice_template')) {
    function select_pdf_invoice_template(...$args)
    {
        return TemplateHelper::selectPdfInvoiceTemplate(...$args);
    }
}
if ( ! function_exists('select_email_invoice_template')) {
    function select_email_invoice_template(...$args)
    {
        return TemplateHelper::selectEmailInvoiceTemplate(...$args);
    }
}
if ( ! function_exists('trans')) {
    function trans(...$args)
    {
        return TransHelper::trans(...$args);
    }
}
if ( ! function_exists('set_language')) {
    function set_language(...$args)
    {
        return TransHelper::setLanguage(...$args);
    }
}
if ( ! function_exists('get_available_languages')) {
    function get_available_languages(...$args)
    {
        return TransHelper::getAvailableLanguages(...$args);
    }
}
if ( ! function_exists('format_user')) {
    function format_user(...$args)
    {
        return UserHelper::formatUser(...$args);
    }
}
