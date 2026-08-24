<?php

//#######################
## For Navigation Menu
#######################

add_action('after_render_single_aside_menu', 'my_custom_menu_items');
/**
 * @param $order
 */
function my_custom_menu_items($order)
{
    if ($order == 2) {
        echo '<li>';
        echo '    <a href="' . base_url() . 'admin/renewals" aria-expanded="false"><i class="fa fa-registered menu-icon"></i>Renewals</a>';
        echo '</li>';
    }

    if ($order == 3) {

        echo '<li>';
        echo '    <a href="#" aria-expanded="false"><i class="fa fa-registered menu-icon"></i>Receipts<span class="fa arrow"></span></a>';
        echo '    <ul class="nav nav-second-level collapse" aria-expanded="false">';


        if (is_admin() || has_permission('receipts', '', 'view') || has_permission('receipts', '', 'view_own')) {
            echo '        <li><a href="' . base_url() . 'admin/receipts/">All Receipts</a></li>';
        }


        if (is_admin() || has_permission('receipts', '', 'create')) {
            echo '        <li><a href="' . base_url() . 'admin/receipts/create">Create New</a></li>';
        }

        if (is_admin() || has_permission('receipt_handover', '', 'edit')) {
            echo '        <li><a href="' . base_url() . 'admin/receipts/index/handover">Handover</a></li>';

        }

        if (is_admin() || has_permission('receipt_deposit', '', 'edit')) {
            echo '        <li><a href="' . base_url() . 'admin/receipts/index/deposited">Deposit</a></li>';
        }

        if (is_admin() || has_permission('receipt_verify', '', 'edit')) {
            echo '        <li><a href="' . base_url() . 'admin/receipts/index/verified">Verify</a></li>';
        }

        echo '    </ul>';
        echo '</li>';

    }

    if ($order == 7) {

        echo '<li>';
        echo '    <a href="#" aria-expanded="false"><i class="fa fa-file-text-o menu-icon"></i>Custom Notes<span class="fa arrow"></span></a>';
        echo '    <ul class="nav nav-second-level collapse" aria-expanded="false">
                    <li><a href="' . base_url() . 'admin/custom_notes/">View All</a></li> 
                    <li><a href="' . base_url() . 'admin/custom_notes/create">Create New</a></li> 
                  </ul>';
        echo '</li>';

    }

    if ($order == 9) {
        echo '<li>';
        echo '    <a href="' . base_url() . 'admin/sync_invoices/create" aria-expanded="false"><i class="fa fa-file-archive-o menu-icon"></i>Sync Invoices</a>';
        echo '</li>';
    }
}

/**
 * @param $date
 * @return false|string
 */
function date_format_dmy($date)
{
    return date("d-m-Y", strtotime($date));
}

/**
 * @param $data
 */
function pre_array($data)
{
    echo '<pre>';
    print_r($data);
    die();
}

/**
 * @return bool|int
 */
function make_next_invoice_num()
{
    $CI = &get_instance();
    $CI->db->select('number');
    $CI->db->from('tblinvoices');
    $CI->db->limit(1);
    $CI->db->order_by('number', 'DESC');
    $number = $CI->db->get()->row();

    if ($number <> null) {
        return $number->number + 1;
    }

    return false;
}

/**
 * @return mixed
 */
function make_next_performa_invoice_number()
{
    $CI = &get_instance();
    $CI->db->select('number');
    $CI->db->from('tblinvoices');
    $CI->db->where(['type' => 'performa']);
    $CI->db->limit(1);
    $CI->db->order_by('number', 'DESC');
    $number = $CI->db->get()->row()->number;

    if(!isset($number)){
        $p_number = 1;
    }

    if ($number <> null) {
        $p_number = $number + 1;
    }

    return $p_number;
}

/**
 * @param $id
 * @return mixed
 */
function getInvoiceTerms($id)
{
    $CI = &get_instance();
    $CI->db->select('cn_title');
    $CI->db->from('tblcustomnotes');
    $CI->db->where(['cn_id' => $id]);
    $CI->db->limit(1);
    return $CI->db->get()->row()->cn_title;
}

/**
 *
 */
function getInvoiceType($id){

    $CI = &get_instance();
    $CI->db->select('type');
    $CI->db->from('tblinvoices');
    $CI->db->where(['id' => $id]);
    $CI->db->limit(1);
    $CI->db->order_by('number', 'DESC');
    $type = $CI->db->get()->row()->type;

    if ($type <> null) {
        return $type;
    }

    return false;
}

/**
 * @param $id
 * @return bool
 */
function get_lead_name($id){

    $CI = &get_instance();
    $CI->db->select('name');
    $CI->db->from('tblleads');
    $CI->db->where(['id' => $id]);
    $CI->db->limit(1);
    $name = $CI->db->get()->row()->name;

    if ($name <> null) {
        return $name;
    }

    return false;
}


/**
 * @param $id
 * @return bool
 */
function get_lead_company($id){

    $CI = &get_instance();
    $CI->db->select('company');
    $CI->db->from('tblleads');
    $CI->db->where(['id' => $id]);
    $CI->db->limit(1);
    $name = $CI->db->get()->row()->company;

    if ($name <> null) {
        return $name;
    }

    return false;
}

    /**
     * @param $id
     * @return bool
     */
function get_customer_name($id){

    $CI = &get_instance();
    $CI->db->select('company');
    $CI->db->from('tblleads');
    $CI->db->where(['id' => $id]);
    $CI->db->limit(1);
    $name = $CI->db->get()->row()->company;

    if ($name <> null) {
        return $name;
    }

    return false;
}


/**
 * @param string $type
 * @param string $position
 * @return mixed
 */
function getByTypePosition($type = "", $position = "")
{
    $CI = &get_instance();
    $CI->db->select('*');
    $CI->db->from('tblcustomnotes');

    if ($type) {
        $CI->db->where(['cn_type' => $type]);
    }

    if ($position) {
        $CI->db->where(['cn_position' => $position]);
    }

    return $CI->db->get()->result();
}

/**
 * @param $description
 * @return string
 */
function getDescriptionFristLine($description)
{
    $array = array_filter(explode(PHP_EOL, strip_tags($description, '<br>')));

    if (count($array) > 0) {
        foreach ($array as $str) {
            if (!empty(trim($str)) && $str != " ") {

                $myArray = preg_split('/<br[^>]*>/i', $str);

                if (count($myArray) > 0) {
                    foreach ($myArray as $arr) {
                        if (!empty(trim($arr)) && $arr != " ") {
                            return $arr . '<br/>';
                        }
                    }
                    return $str . '<br/>';
                }
            }
        }
    }

    return $description;
}

/**
 * @param $id
 * @return string
 */
function format_receipt_number($id)
{
    $format = 1;
    $prefix = 'REC-';
    if ($format == 1) {
        // Number based
        return $prefix . str_pad($id, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
    } elseif ($format == 2) {
        return $prefix . date('Y', strtotime(date('Y-m-d'))) . '/' . str_pad($id, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
    }

}

/**
 * @param string $userid
 * @return string
 */
function get_staff_info_signature($userid = '')
{
    $_userid = get_staff_user_id();
    if ($userid !== '') {
        $_userid = $userid;
    }
    $CI =& get_instance();
    $CI->db->where('staffid', $_userid);
    $staff = $CI->db->select('staffid,email_signature,email_signature_image')->from('tblstaff')->get()->row();
    if ($staff) {
        return $staff;
    } else {
        return '';
    }
}

/**
 * Fetches custom pdf logo url for pdf or use the default logo uploaded for the company
 * Additional statements applied because this function wont work on all servers. All depends how the server is configured.
 * @return [type] [description]
 */
function pdf_email_signature($id, $signature)
{
    $cimg = base_url() . "uploads/staff_profile_images/" . $id . "/" . $signature;

    $width = 100;

    if ($cimg != '') {
        $logo_url = '<img   width="' . $width . 'px" src="' . $cimg . '">';
    }

    return do_action('pdf_logo_url', $logo_url);
}

/**
 * @return mixed
 */
function get_wisdom_stamp()
{

    $cimg = get_wisdom_stamp_link();
    $width = 150;

    if ($cimg != '') {
        $logo_url = '<img  width="' . $width . 'px" src="' . $cimg . '">';
    }

    return do_action('pdf_logo_url', $logo_url);
}

/**
 * @return string
 */
function get_wisdom_stamp_link()
{

    return base_url() . "uploads/company/wisdom-stamp.png";
}

/**
 * @return string
 */
function get_sql_select_client_city()
{
    return 'CASE city WHEN "" THEN (SELECT (city) FROM tblclients WHERE userid = tblinvoices.id) ELSE city END as city';

}

/**
 * @return string
 */
function get_sql_select_client_state()
{
    return 'CASE state WHEN "" THEN (SELECT (state) FROM tblclients WHERE userid = tblinvoices.id) ELSE state END as state';
}

/**
 * Prepare general invoice pdf
 * @param  object $invoice Invoice as object with all necessary fields
 * @return mixed object
 */
function receipt_pdf($data, $tag = '')
{
    $CI =& get_instance();
    load_pdf_language($data['receipts']->receipt_client_id);
    $CI->load->library('pdf');
    $receipt_number = format_receipt_number($data['receipts']->receipt_num);
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');

    if ($font_size == '') {
        $font_size = 10;
    }

    $CI->load->model('payment_modes_model');
    $payment_modes = $CI->payment_modes_model->get();

    // In case user want to include {invoice_number} in PDF offline mode description
    foreach ($payment_modes as $key => $mode) {
        if (isset($mode['description'])) {
            $payment_modes[$key]['description'] = str_replace('{invoice_number}', $receipt_number, $mode['description']);
        }
    }

    $formatArray = get_pdf_format('pdf_format_invoice');
    if (!file_exists(APPPATH . 'libraries/Invoice_pdf.php')) {
        $pdf = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'invoice');
    } else {
        include_once(APPPATH . 'libraries/Invoice_pdf.php');
        $pdf = new receipt_pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false, false);
    }

    if (defined('APP_PDF_MARGIN_LEFT') && defined('APP_PDF_MARGIN_TOP') && defined('APP_PDF_MARGIN_RIGHT')) {
        $pdf->SetMargins(APP_PDF_MARGIN_LEFT, APP_PDF_MARGIN_TOP, APP_PDF_MARGIN_RIGHT);
    }

    $pdf->SetTitle($receipt_number);

    $pdf->SetAutoPageBreak(true, (defined('APP_PDF_MARGIN_BOTTOM') ? APP_PDF_MARGIN_BOTTOM : PDF_MARGIN_BOTTOM));

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->setImageScale(1.53);
    $pdf->setJPEGQuality(100);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);

    if ($CI->input->get('print') == 'true') {
        // force print dialog
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }
    $data['hide_signature'] = true;

    $swap = get_option('swap_pdf_info');
    $CI->load->library('numberword', array(
        'clientid' => $data['receipts']->receipt_client_id
    ));

    $invoice = do_action('invoice_html_pdf_data', $data);

    if (file_exists(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_receipt_preview_template.php')) {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_receipt_preview_template.php');
    } else {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/receipt_preview_template.php');
    }

    if (ob_get_length() > 0 && ENVIRONMENT == 'production') {
        ob_end_clean();
    }

    return $pdf;
}