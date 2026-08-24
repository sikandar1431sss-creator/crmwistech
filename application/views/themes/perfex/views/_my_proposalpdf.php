<?php

$dimensions = $pdf->getPageDimensions();
if ($tag != '') {
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetDrawColor(245, 245, 245);
    $pdf->SetXY(0, 0);
    $pdf->SetFont($font_name, 'B', 15);
    $pdf->SetTextColor(0);
    $pdf->SetLineWidth(0.75);
    $pdf->StartTransform();
    $pdf->Rotate(-35, 109, 235);
    $pdf->Cell(100, 1, mb_strtoupper($tag, 'UTF-8'), 'TB', 0, 'C', '1');
    $pdf->StopTransform();
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->setX(10);
    $pdf->setY(10);
}

$info_right_column = '';
$info_left_column = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('proposal_pdf_heading') . '</span><br />';
$info_right_column .= '<b># ' . $number . '</b>';

if (get_option('show_status_on_pdf_ei') == 1) {
    $info_right_column .= '<br /><span style="color:rgb(' . invoice_status_color_pdf($status) . ');text-transform:uppercase;">' . format_invoice_status($status, '', false) . '</span>';
}

$info_right_column .= '<br/><b> ' . _l('proposal_date') . ':</b> ' . _d(date_format_dmy($proposal->date)) . '</1b>';
if (!empty($proposal->open_till)) {
    $open_till = "<b>" . _l('proposal_open_till') . ': </b>' . _d(date_format_dmy($proposal->open_till));
    $info_right_column .= "<br/>" . $open_till;
}

// write the first column
$info_left_column .= pdf_logo_url();

$client_details = "";

if ($proposal->rel_type == "customer") {
    $client_details = 'Attn: <b>' . $proposal->proposal_to . '</b>';
    $client_details .= '<br /><b>' . get_company_name($proposal->rel_id) . '</b>';
}

if ($proposal->rel_type == "lead") {
    $client_details = 'Attn: <b>' . get_lead_name($proposal->rel_id) . '</b>';
    $client_details .= '<br /><b>' . get_lead_company($proposal->rel_id) . '</b>';
}

$info_left_column .= '<br /><div style="">';
$info_left_column .= $client_details;
$info_left_column .= '</div>';

$pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['lm'], 0, $info_left_column, 0, 'J', 0, 0, '', '', true, 0, true, true, 0);
// write the second column
$pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['rm'], 0, $info_right_column, 0, 'R', 0, 1, '', '', true, 0, true, false, 0);
$pdf->ln(6);
// Get Y position for the separation
$y = $pdf->getY();

$pdf->ln(2);

if (!empty($proposal->subject)) {
    $pdf->Ln(1);
    $pdf->writeHTMLCell('', '', '', '', "<b>Subject: </b><span style='font-size: large;' ><u>" . $proposal->subject . "</u></span>", 0, 1, false, true, 'L', true);
}


if (!empty($proposal->customNote_header)) {
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $proposal->customNote_header, 0, 1, false, true, 'L', true);
}

$custom_fields_data = '';

$pdf_custom_fields = get_custom_fields('proposal', array('show_on_pdf' => 1));
foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($proposal->id, $field['id'], 'proposal');
    if ($value == '') {
        continue;
    }
    $custom_fields_data .= $field['name'] . ': ' . $value . '<br />';
}

// Add new line if found custom fields so the custom field can go on the next line
$custom_fields_data = $custom_fields_data != '' ? '<br />' . $custom_fields_data : $custom_fields_data;

$item_width = 56;
if ($proposal->discount_type == "percentage") {
    $percentage_sign = '%';
} else {
    $percentage_sign = '';
}
// If show item taxes is disabled in PDF we should increase the item width table heading
$item_width = get_option('show_tax_per_item') == 0 ? $item_width + 10 : $item_width;

// The same language keys from estimates are used here
$qty_heading = _l('estimate_table_quantity_heading');
if ($proposal->show_quantity_as == 2) {
    $qty_heading = _l('estimate_table_hours_heading');
} else if ($proposal->show_quantity_as == 3) {
    $qty_heading = _l('estimate_table_quantity_heading') . '/' . _l('estimate_table_hours_heading');
}
$pdf->Ln(3);
// Header

$items_html = '<table width="100%"  bgcolor="#fff" cellspacing="0" cellpadding="8" border="1">
<tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">
    <th width="6%;" align="center">#</th>
    <th width="' . $item_width . '%" align="left">' . _l('estimate_table_item_heading') . '</th>
    <th width="6%" align="center">' . $qty_heading . '</th>
    <th width="11%" align="center">' . _l('estimate_table_rate_heading') . '</th>';
if (get_option('show_tax_per_item') == 1) {
    $items_html .= '<th width="10%" align="right">' . _l('estimate_table_tax_heading') . '</th>';
}
$items_html .= '<th width="11%" align="right">' . _l('estimate_table_amount_heading') . '</th>
</tr>';

// Items
$items_html .= '<tbody>';

$items_data = get_table_items_and_taxes($proposal->items, 'proposal');

$taxes = $items_data['taxes'];
$items_html .= $items_data['html'];

$items_html .= '</tbody>';
$items_html .= '</table>';
$items_html .= '<br /><br />';
$items_html .= '';
$items_html .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px" border="1">';
$items_html .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_subtotal') . '</strong></td>
    <td align="right" width="15%">' . format_money($proposal->subtotal, $proposal->symbol) . '</td>
</tr>';
if (is_sale_discount_applied($proposal)) {
    $items_html .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('estimate_discount');
    if (is_sale_discount($proposal, 'percent')) {
        $items_html .= '(' . _format_number($proposal->discount_percent, true) . '%)';
    }
    $items_html .= '</strong>';
    $items_html .= '</td>';
    $items_html .= '<td align="right" width="15%">-' . format_money($proposal->discount_total, $proposal->symbol) . '</td>
    </tr>';
}
foreach ($taxes as $tax) {
    $total = array_sum($tax['total']);
    if ($proposal->discount_percent != 0 && $proposal->discount_type == 'before_tax') {
        $total_tax_calculated = ($total * $proposal->discount_percent) / 100;
        $total = ($total - $total_tax_calculated);
    }
    // The tax is in format TAXNAME|20
    $_tax_name = explode('|', $tax['tax_name']);
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . $_tax_name[0] . '(' . _format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . format_money($total, $proposal->symbol) . '</td>
</tr>';
}

if ((int)$proposal->adjustment != 0) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_adjustment') . '</strong></td>
    <td align="right" width="15%">' . format_money($proposal->adjustment, $proposal->symbol) . '</td>
</tr>';
}
$items_html .= '
<tr style="">
    <td align="right" width="85%"><strong>' . _l('estimate_total') . '</strong></td>
    <td align="right" width="15%">' . format_money($proposal->total, $proposal->symbol) . '</td>
</tr>';
$items_html .= '</table>';

/*if (get_option('total_to_words_enabled') == 1) {
    $items_html .= '<br /><br /><br />';
    $items_html .= '<strong style="text-align:center;">' . _l('num_word') . ': ' . $CI->numberword->convert($proposal->total, $proposal->currency_name) . '</strong>';
}*/

$proposal->content = str_replace('{proposal_items}', $items_html, $proposal->content);
// Get the proposals css
$css = file_get_contents(FCPATH . 'assets/css/proposals.css');
// Theese lines should aways at the end of the document left side. Dont indent these lines
$html = <<<EOF
<style>
    $css
</style>
<div style="width:675px !important;">
$proposal->content
</div>
EOF;

$pdf->writeHTML($html, true, false, true, false, '');

if (!empty($proposal->customNote_footer)) {
    $pdf->Ln(4);
    $pdf->writeHTMLCell('', '', '', '', $proposal->customNote_footer, 0, 1, false, true, 'L', true);
}

if ($proposal->assigned) {
    $staff = get_staff_info_signature($proposal->assigned);
} else {
    $staff = get_staff_info_signature($proposal->addedfrom);
}

if ($staff <> null) {
    if ($staff->email_signature <> null) {
        $toolcopy = '';
        if (!empty($staff->email_signature_image) && $staff->email_signature_image <> null) {
            $toolcopy .= pdf_email_signature($staff->staffid, $staff->email_signature_image) . "<br/>";
        }

        $cimg = get_wisdom_stamp_link();

        $toolcopy .= "________________<br/>";
        $toolcopy .= $staff->email_signature;
        $pdf->ln(10);
        $pdf->Image($cimg, '30', '', 35, '', '', '', 'L', false, '', '5', false, false, 0);
        $pdf->writeHTML($toolcopy, true, 0, true, 0);
    }
}

$CI = &get_instance();
$CI->load->library('pdf');


$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 0, false, "L", true);
