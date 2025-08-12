<?php

if ( ! function_exists('format_client')) {
    function format_client(...$args)
    {
        return \App\Helpers\ClientHelper::formatClient(...$args);
    }
}
if ( ! function_exists('format_gender')) {
    function format_gender(...$args)
    {
        return \App\Helpers\ClientHelper::formatGender(...$args);
    }
}
if ( ! function_exists('get_country_list')) {
    function get_country_list(...$args)
    {
        return \App\Helpers\CountryHelper::getCountryList(...$args);
    }
}
if ( ! function_exists('get_country_name')) {
    function get_country_name(...$args)
    {
        return \App\Helpers\CountryHelper::getCountryName(...$args);
    }
}
if ( ! function_exists('format_date')) {
    function format_date(...$args)
    {
        return \App\Helpers\CustomValuesHelper::formatDate(...$args);
    }
}
if ( ! function_exists('format_text')) {
    function format_text(...$args)
    {
        return \App\Helpers\CustomValuesHelper::formatText(...$args);
    }
}
if ( ! function_exists('format_singlechoice')) {
    function format_singlechoice(...$args)
    {
        return \App\Helpers\CustomValuesHelper::formatSinglechoice(...$args);
    }
}
if ( ! function_exists('format_multiplechoice')) {
    function format_multiplechoice(...$args)
    {
        return \App\Helpers\CustomValuesHelper::formatMultiplechoice(...$args);
    }
}
if ( ! function_exists('format_boolean')) {
    function format_boolean(...$args)
    {
        return \App\Helpers\CustomValuesHelper::formatBoolean(...$args);
    }
}
if ( ! function_exists('format_avs')) {
    function format_avs(...$args)
    {
        return \App\Helpers\CustomValuesHelper::formatAvs(...$args);
    }
}
if ( ! function_exists('format_fallback')) {
    function format_fallback(...$args)
    {
        return \App\Helpers\CustomValuesHelper::formatFallback(...$args);
    }
}
if ( ! function_exists('print_field')) {
    function print_field(...$args)
    {
        return \App\Helpers\CustomValuesHelper::printField(...$args);
    }
}
if ( ! function_exists('date_formats')) {
    function date_formats(...$args)
    {
        return \App\Helpers\DateHelper::dateFormats(...$args);
    }
}
if ( ! function_exists('date_from_mysql')) {
    function date_from_mysql(...$args)
    {
        return \App\Helpers\DateHelper::dateFromMysql(...$args);
    }
}
if ( ! function_exists('date_from_timestamp')) {
    function date_from_timestamp(...$args)
    {
        return \App\Helpers\DateHelper::dateFromTimestamp(...$args);
    }
}
if ( ! function_exists('date_to_mysql')) {
    function date_to_mysql(...$args)
    {
        return \App\Helpers\DateHelper::dateToMysql(...$args);
    }
}
if ( ! function_exists('is_date')) {
    function is_date(...$args)
    {
        return \App\Helpers\DateHelper::isDate(...$args);
    }
}
if ( ! function_exists('date_format_setting')) {
    function date_format_setting(...$args)
    {
        return \App\Helpers\DateHelper::dateFormatSetting(...$args);
    }
}
if ( ! function_exists('date_format_datepicker')) {
    function date_format_datepicker(...$args)
    {
        return \App\Helpers\DateHelper::dateFormatDatepicker(...$args);
    }
}
if ( ! function_exists('increment_user_date')) {
    function increment_user_date(...$args)
    {
        return \App\Helpers\DateHelper::incrementUserDate(...$args);
    }
}
if ( ! function_exists('increment_date')) {
    function increment_date(...$args)
    {
        return \App\Helpers\DateHelper::incrementDate(...$args);
    }
}
if ( ! function_exists('diacritics_seems_utf8')) {
    function diacritics_seems_utf8(...$args)
    {
        return \App\Helpers\DiacriticsHelper::diacriticsSeemsUtf8(...$args);
    }
}
if ( ! function_exists('diacritics_remove_accents')) {
    function diacritics_remove_accents(...$args)
    {
        return \App\Helpers\DiacriticsHelper::diacriticsRemoveAccents(...$args);
    }
}
if ( ! function_exists('diacritics_remove_diacritics')) {
    function diacritics_remove_diacritics(...$args)
    {
        return \App\Helpers\DiacriticsHelper::diacriticsRemoveDiacritics(...$args);
    }
}
if ( ! function_exists('_dropzone_html')) {
    function _dropzone_html(...$args)
    {
        return \App\Helpers\DropzoneHelper::DropzoneHtml(...$args);
    }
}
if ( ! function_exists('_dropzone_script')) {
    function _dropzone_script(...$args)
    {
        return \App\Helpers\DropzoneHelper::DropzoneScript(...$args);
    }
}
if ( ! function_exists('generate_xml_invoice_file')) {
    function generate_xml_invoice_file(...$args)
    {
        return \App\Helpers\EInvoiceHelper::generateXmlInvoiceFile(...$args);
    }
}
if ( ! function_exists('include_rdf')) {
    function include_rdf(...$args)
    {
        return \App\Helpers\EInvoiceHelper::includeRdf(...$args);
    }
}
if ( ! function_exists('get_xml_template_files')) {
    function get_xml_template_files(...$args)
    {
        return \App\Helpers\EInvoiceHelper::getXmlTemplateFiles(...$args);
    }
}
if ( ! function_exists('get_xml_full_name')) {
    function get_xml_full_name(...$args)
    {
        return \App\Helpers\EInvoiceHelper::getXmlFullName(...$args);
    }
}
if ( ! function_exists('get_admin_active_users')) {
    function get_admin_active_users(...$args)
    {
        return \App\Helpers\EInvoiceHelper::getAdminActiveUsers(...$args);
    }
}
if ( ! function_exists('get_req_fields_einvoice')) {
    function get_req_fields_einvoice(...$args)
    {
        return \App\Helpers\EInvoiceHelper::getReqFieldsEinvoice(...$args);
    }
}
if ( ! function_exists('get_einvoice_usage')) {
    function get_einvoice_usage(...$args)
    {
        return \App\Helpers\EInvoiceHelper::getEinvoiceUsage(...$args);
    }
}
if ( ! function_exists('get_items_tax_usages')) {
    function get_items_tax_usages(...$args)
    {
        return \App\Helpers\EInvoiceHelper::getItemsTaxUsages(...$args);
    }
}
if ( ! function_exists('items_tax_usages_bad')) {
    function items_tax_usages_bad(...$args)
    {
        return \App\Helpers\EInvoiceHelper::itemsTaxUsagesBad(...$args);
    }
}
if ( ! function_exists('htmlsc')) {
    function htmlsc(...$args)
    {
        return \App\Helpers\EchoHelper::htmlsc(...$args);
    }
}
if ( ! function_exists('_htmlsc')) {
    function _htmlsc(...$args)
    {
        return \App\Helpers\EchoHelper::Htmlsc(...$args);
    }
}
if ( ! function_exists('_htmle')) {
    function _htmle(...$args)
    {
        return \App\Helpers\EchoHelper::Htmle(...$args);
    }
}
if ( ! function_exists('_trans')) {
    function _trans(...$args)
    {
        return \App\Helpers\EchoHelper::Trans(...$args);
    }
}
if ( ! function_exists('_auto_link')) {
    function _auto_link(...$args)
    {
        return \App\Helpers\EchoHelper::AutoLink(...$args);
    }
}
if ( ! function_exists('_csrf_field')) {
    function _csrf_field(...$args)
    {
        return \App\Helpers\EchoHelper::CsrfField(...$args);
    }
}
if ( ! function_exists('_theme_asset')) {
    function _theme_asset(...$args)
    {
        return \App\Helpers\EchoHelper::ThemeAsset(...$args);
    }
}
if ( ! function_exists('_core_asset')) {
    function _core_asset(...$args)
    {
        return \App\Helpers\EchoHelper::CoreAsset(...$args);
    }
}
if ( ! function_exists('invoice_logo')) {
    function invoice_logo(...$args)
    {
        return \App\Helpers\InvoiceHelper::invoiceLogo(...$args);
    }
}
if ( ! function_exists('invoice_logo_pdf')) {
    function invoice_logo_pdf(...$args)
    {
        return \App\Helpers\InvoiceHelper::invoiceLogoPdf(...$args);
    }
}
if ( ! function_exists('invoice_genCodeline')) {
    function invoice_genCodeline(...$args)
    {
        return \App\Helpers\InvoiceHelper::invoiceGenCodeline(...$args);
    }
}
if ( ! function_exists('invoice_recMod10')) {
    function invoice_recMod10(...$args)
    {
        return \App\Helpers\InvoiceHelper::invoiceRecMod10(...$args);
    }
}
if ( ! function_exists('invoice_qrcode')) {
    function invoice_qrcode(...$args)
    {
        return \App\Helpers\InvoiceHelper::invoiceQrcode(...$args);
    }
}
if ( ! function_exists('json_errors')) {
    function json_errors(...$args)
    {
        return \App\Helpers\JsonErrorHelper::jsonErrors(...$args);
    }
}
if ( ! function_exists('mailer_configured')) {
    function mailer_configured(...$args)
    {
        return \App\Helpers\MailerHelper::mailerConfigured(...$args);
    }
}
if ( ! function_exists('email_invoice')) {
    function email_invoice(...$args)
    {
        return \App\Helpers\MailerHelper::emailInvoice(...$args);
    }
}
if ( ! function_exists('email_quote')) {
    function email_quote(...$args)
    {
        return \App\Helpers\MailerHelper::emailQuote(...$args);
    }
}
if ( ! function_exists('email_quote_status')) {
    function email_quote_status(...$args)
    {
        return \App\Helpers\MailerHelper::emailQuoteStatus(...$args);
    }
}
if ( ! function_exists('check_mail_errors')) {
    function check_mail_errors(...$args)
    {
        return \App\Helpers\MailerHelper::checkMailErrors(...$args);
    }
}
if ( ! function_exists('pdf_create')) {
    function pdf_create(...$args)
    {
        return \App\Helpers\MpdfHelper::pdfCreate(...$args);
    }
}
if ( ! function_exists('format_currency')) {
    function format_currency(...$args)
    {
        return \App\Helpers\NumberHelper::formatCurrency(...$args);
    }
}
if ( ! function_exists('format_amount')) {
    function format_amount(...$args)
    {
        return \App\Helpers\NumberHelper::formatAmount(...$args);
    }
}
if ( ! function_exists('format_quantity')) {
    function format_quantity(...$args)
    {
        return \App\Helpers\NumberHelper::formatQuantity(...$args);
    }
}
if ( ! function_exists('standardize_amount')) {
    function standardize_amount(...$args)
    {
        return \App\Helpers\NumberHelper::standardizeAmount(...$args);
    }
}
if ( ! function_exists('delete_orphans')) {
    function delete_orphans(...$args)
    {
        return \App\Helpers\OrphanHelper::deleteOrphans(...$args);
    }
}
if ( ! function_exists('pager')) {
    function pager(...$args)
    {
        return \App\Helpers\PagerHelper::pager(...$args);
    }
}
if ( ! function_exists('get_currencies')) {
    function get_currencies(...$args)
    {
        return \App\Helpers\PaymentsHelper::getCurrencies(...$args);
    }
}
if ( ! function_exists('discount_global_print_in_pdf')) {
    function discount_global_print_in_pdf(...$args)
    {
        return \App\Helpers\PdfHelper::discountGlobalPrintInPdf(...$args);
    }
}
if ( ! function_exists('generate_invoice_pdf')) {
    function generate_invoice_pdf(...$args)
    {
        return \App\Helpers\PdfHelper::generateInvoicePdf(...$args);
    }
}
if ( ! function_exists('generate_invoice_sumex')) {
    function generate_invoice_sumex(...$args)
    {
        return \App\Helpers\PdfHelper::generateInvoiceSumex(...$args);
    }
}
if ( ! function_exists('generate_quote_pdf')) {
    function generate_quote_pdf(...$args)
    {
        return \App\Helpers\PdfHelper::generateQuotePdf(...$args);
    }
}
if ( ! function_exists('redirect_to')) {
    function redirect_to(...$args)
    {
        return \App\Helpers\RedirectHelper::redirectTo(...$args);
    }
}
if ( ! function_exists('redirect_to_set')) {
    function redirect_to_set(...$args)
    {
        return \App\Helpers\RedirectHelper::redirectToSet(...$args);
    }
}
if ( ! function_exists('get_setting')) {
    function get_setting(...$args)
    {
        return \App\Helpers\SettingsHelper::getSetting(...$args);
    }
}
if ( ! function_exists('get_gateway_settings')) {
    function get_gateway_settings(...$args)
    {
        return \App\Helpers\SettingsHelper::getGatewaySettings(...$args);
    }
}
if ( ! function_exists('check_select')) {
    function check_select(...$args)
    {
        return \App\Helpers\SettingsHelper::checkSelect(...$args);
    }
}
if ( ! function_exists('parse_template')) {
    function parse_template(...$args)
    {
        return \App\Helpers\TemplateHelper::parseTemplate(...$args);
    }
}
if ( ! function_exists('get_invoice_status')) {
    function get_invoice_status(...$args)
    {
        return \App\Helpers\TemplateHelper::getInvoiceStatus(...$args);
    }
}
if ( ! function_exists('select_pdf_invoice_template')) {
    function select_pdf_invoice_template(...$args)
    {
        return \App\Helpers\TemplateHelper::selectPdfInvoiceTemplate(...$args);
    }
}
if ( ! function_exists('select_email_invoice_template')) {
    function select_email_invoice_template(...$args)
    {
        return \App\Helpers\TemplateHelper::selectEmailInvoiceTemplate(...$args);
    }
}
if ( ! function_exists('trans')) {
    function trans(...$args)
    {
        return \App\Helpers\TransHelper::trans(...$args);
    }
}
if ( ! function_exists('set_language')) {
    function set_language(...$args)
    {
        return \App\Helpers\TransHelper::setLanguage(...$args);
    }
}
if ( ! function_exists('get_available_languages')) {
    function get_available_languages(...$args)
    {
        return \App\Helpers\TransHelper::getAvailableLanguages(...$args);
    }
}
if ( ! function_exists('format_user')) {
    function format_user(...$args)
    {
        return \App\Helpers\UserHelper::formatUser(...$args);
    }
}
