<?php

$receipts = $data['receipts'];
$staff = $data['staff'];
$invoices = $data['invoices'];
$created_by = $data['created_by'];
$receipt_currency = !empty($receipts->receipt_currency_symbol)
    ? $receipts->receipt_currency_symbol
    : (!empty($receipts->receipt_currency_code) ? $receipts->receipt_currency_code : '');

$receipt_number = $receipts->receipt_id;

$dimensions = $pdf->getPageDimensions();
$client_details = "";
// Tag - used in BULK pdf exporter
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

$info_right_column .= '<span style="font-weight:bold;font-size:27px;margin-bottom: 20px">Receipt</span><br />';
$pdf->Ln(3);
$info_right_column .= '<b>No #: </b>' . format_receipt_number($receipts->receipt_num);


// Dates
// $pdf->Cell(0, 0, _l('invoice_data_date') . ' ' . _d($invoice->date), 0, 1, ($swap == '1' ? 'L' : 'R'), 0, '', 0);

$info_right_column .= '<br/><b>Date: </b> ' . date_format_dmy($receipts->receipt_date);

// write the first column
$info_left_column .= pdf_logo_url();

$pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['lm'], 0, $info_left_column, 0, 'J', 0, 0, '', '', true, 0, true, true, 0);
// write the second column
$pdf->MultiCell(($dimensions['wk'] / 2) - $dimensions['rm'], 0, $info_right_column, 0, 'R', 0, 1, '', '', true, 0, true, false, 0);
$pdf->ln(1);
// Get Y position for the separation
$y = $pdf->getY();

//  Bill to
//  $client_details = '<b>' . _l('invoice_bill_to') . '</b><br />';
$client_details .= '<div style="margin-top: 20px;"><strong>Client:</strong> <br/>';
$client_details .= '<span style="font-weight: normal;">' . format_customer_info($receipts, 'receipt', 'billing') . '</span>';
$client_details .= '</div>';

$pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['rm'], '', '', ($swap == '1' ? $y : ''), $client_details, 0, 1, false, true, ($swap == '1' ? 'J' : 'R'), true);

// The Table
$pdf->Ln(3);
$item_width = 38;
// If show item taxes is disabled in PDF we should increase the item width table heading
$item_width = get_option('show_tax_per_item') == 0 ? $item_width + 15 : $item_width;


if (!empty($receipts->receipt_note)) {
    $tblmemo = '    <table width="100%">
                        <tbody>
                        <tr>
                            <th align="left" width="10%"><strong>Memo: </strong></th>
                            <th align="left" style="text-decoration: underline;">' . $receipts->receipt_note . ' </th>
                        </tr>  
                        </tbody>
                        ';

    $tblmemo .= '        </table>';
    $pdf->writeHTML($tblmemo, true, false, false, false, '');
}

$cheque_date = "";
if (!empty($receipts->receipt_cheque_date) && $receipts->receipt_cheque_date != "0000-00-00") {
    $cheque_date = (!empty($receipts->receipt_cheque_num) ? ', ' : '') . date('d-m-Y', strtotime($receipts->receipt_cheque_date));
}

$cheque_info = trim($receipts->receipt_cheque_num . $cheque_date);
if (empty($cheque_info)) {
    $cheque_info = '-';
}

$pdf_bank_name = get_receipt_bank_name($receipts);
if (empty($pdf_bank_name)) {
    $pdf_bank_name = '-';
}

$tblD = '    <table width="100%" border="1" bgcolor="#fff" cellspacing="0" cellpadding="8" >
                        <thead>
                        <tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">
                        <th align="center"><strong>Payment Method</strong></th>
                         <th align="center"><strong>Check Number & Date</strong></th>
                          <th align="center"><strong>Bank</strong> </th>
                        </tr>
                        </thead>
                          <tbody>
                        <tr>
                            <th align="center">' . $receipts->receipt_type . '  </th>
                           <th align="center"> ' . $cheque_info . '</th>
                           <th align="center">' . $pdf_bank_name . ' </th>
                        </tr>  
                        </tbody>
                        ';

$tblD .= '        </table>';

$pdf->writeHTML($tblD, true, false, false, false, '');


$tblhtml = '    <table width="100%" border="1" bgcolor="#fff" cellspacing="0" cellpadding="8">
                        <tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">
                            <th align="center"><strong>Invoice Date</strong></th>
                            <th align="center"><strong>Invoice No.</strong></th> 
                            <th align="center"><strong>Invoice Description</strong></th> 
                            <th align="center"><strong>Invoice Amount</strong></th> 
                            <th align="center"><strong>Amount Received</strong></th>';
$tblhtml .= '   </tr>';

$tblhtml .= '        <tbody>';
$total = 0;
if (count($invoices) > 0) {

    foreach ($invoices as $item) {

        $total = $item->amount + $total;

        $tblhtml .= '<tr>';
        $tblhtml .= '    <td align="center">' . date_format_dmy($item->date) . '</td>';
        $tblhtml .= '    <td align="center">' . format_invoice_number($item->invoiceid) . '</td>';
        $tblhtml .= '    <td align="center">' . $item->subject . '</td>';
        $tblhtml .= '    <td align="center">' . format_money($item->total_amount, $receipt_currency) . '</td>';
        $tblhtml .= '    <td align="center">' . format_money($item->amount, $receipt_currency) . '</td>';
        $tblhtml .= '</tr>';
    }

    $tblhtml .= '<tr>';
    $tblhtml .= '    <td align="center">&nbsp;</td>';
    $tblhtml .= '    <td align="center">&nbsp;</td>';
    $tblhtml .= '    <td align="center">&nbsp;</td>';
    $tblhtml .= '    <td align="center"><strong>Total</strong></td>';
    $tblhtml .= '    <td align="center">' . format_money($total, $receipt_currency) . '</td>';
    $tblhtml .= '</tr>';

}
$tblhtml .= '        </tbody>';
$tblhtml .= '    </table>';
$tblhtml .= '<span style="font-size: 10px; color: red;">* This is computer generated receipt, and doesn\'t need signature.</span>';

$pdf->writeHTML($tblhtml, true, false, false, false, '');
$pdf->ln(10);
$admin_details = "";
//  Bill to
//  $client_details = '<b>' . _l('invoice_bill_to') . '</b><br />';
$admin_details .= '</b><br /></b><br /></b><br />';
$admin_details .= '<div style="margin-top: 50px;">';
$admin_details = '    <table  style="margin-top: 50px;" width="100%">
                        <tbody>
                        <tr>
                            <th align="left" width="50%"><strong>Customer A/C Manager</strong><br/> ' . $staff->firstname . " " . $staff->lastname . ' </th>
                           <th align="right" width="50%"><strong>Received By</strong><br/> ' . $created_by->firstname . " " . $created_by->lastname . ' </th>
                          </tr>   
                        </tbody> ';

$admin_details .= '        </table>';
$admin_details .= '</div>';

$pdf->writeHTML($admin_details, true, false, false, true, '');
