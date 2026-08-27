<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Statement_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get customer statement formatted
     * @param  mixed $customer_id customer id
     * @param  string $from        date from
     * @param  string $to          date to
     * @return array
     */
    public function get_statement($customer_id, $from, $to)
    {
        $sql = 'SELECT
        tblinvoices.id as invoice_id,
        hash,
        tblinvoices.date as date,
        tblinvoices.duedate,
        tblinvoices.status as invoice_status,
        concat(tblinvoices.date, \' \', RIGHT(tblinvoices.datecreated,LOCATE(\' \',tblinvoices.datecreated) - 3)) as tmp_date,
        tblinvoices.duedate as duedate,
        tblinvoices.adminnote as adminnote,
        tblinvoices.total as invoice_amount
        FROM tblinvoices WHERE clientid =' . $customer_id;

        if ($from == $to) {
            $sqlDate = 'date="' . $from . '"';
        } else {
            $sqlDate = '(date BETWEEN "' . $from . '" AND "' . $to . '")';
        }

        $sql .= ' AND ' . $sqlDate;

        $invoices = $this->db->query($sql . '
            AND status != 6
            AND status != 5
            ORDER By date DESC')->result_array();

        // Credit notes
        $sql_credit_notes = 'SELECT
        tblcreditnotes.id as credit_note_id,
        tblcreditnotes.date as date,
        concat(tblcreditnotes.date, \' \', RIGHT(tblcreditnotes.datecreated,LOCATE(\' \',tblcreditnotes.datecreated) - 3)) as tmp_date,
        tblcreditnotes.total as credit_note_amount
        FROM tblcreditnotes WHERE clientid =' . $customer_id . ' AND status != 3';

        $sql_credit_notes .= ' AND ' . $sqlDate;

        $credit_notes = $this->db->query($sql_credit_notes)->result_array();

        // Credits applied
        $sql_credits_applied = 'SELECT
        tblcredits.id as credit_id,
        invoice_id as credit_invoice_id,
        tblcredits.credit_id as credit_applied_credit_note_id,
        tblcredits.date as date,
        concat(tblcredits.date, \' \', RIGHT(tblcredits.date_applied,LOCATE(\' \',tblcredits.date_applied) - 3)) as tmp_date,
        tblcredits.amount as credit_amount
        FROM tblcredits
        JOIN tblcreditnotes ON tblcreditnotes.id = tblcredits.credit_id
        ';

        $sql_credits_applied .= '
        WHERE clientid =' . $customer_id;

        $sqlDateCreditsAplied = str_replace('date', 'tblcredits.date', $sqlDate);

        $sql_credits_applied .= ' AND ' . $sqlDateCreditsAplied;
        $credits_applied = $this->db->query($sql_credits_applied)->result_array();

        $sqlDatePayments = str_replace('date', 'tblinvoicepaymentrecords.date', $sqlDate);
        $sqlDateReceipts = str_replace('date', 'tblreciepts.receipt_date', $sqlDate);

        // Receipts
        $sql_receipts = 'SELECT
        tblreciepts.*,
        tblreciepts.receipt_date as date
        FROM tblreciepts
        WHERE ' . $sqlDateReceipts . ' AND tblreciepts.receipt_client_id = ' . $customer_id . '
        ORDER by tblreciepts.receipt_date DESC';

        $receipts = $this->db->query($sql_receipts)->result_array();

        // Standalone invoice payments not attached to any receipt
        $sql_standalone_payments = 'SELECT
        tblinvoicepaymentrecords.id as payment_id,
        tblinvoicepaymentrecords.date as date,
        tblinvoicepaymentrecords.invoiceid as payment_invoice_id,
        tblinvoicepaymentrecords.amount as payment_total,
        tblinvoicepaymentrecords.note as payment_note,
        tblinvoices.number as invoice_number
        FROM tblinvoicepaymentrecords
        JOIN tblinvoices ON tblinvoices.id = tblinvoicepaymentrecords.invoiceid
        WHERE tblinvoices.clientid = ' . $customer_id . '
        AND ' . $sqlDatePayments . '
        AND (tblinvoicepaymentrecords.receipt_id = 0 OR tblinvoicepaymentrecords.receipt_id NOT IN (SELECT receipt_id FROM tblreciepts WHERE receipt_client_id = ' . $customer_id . '))
        ORDER by tblinvoicepaymentrecords.date DESC';

        $standalone_payments = $this->db->query($sql_standalone_payments)->result_array();

        // merge results
        $merged = array_merge($invoices, $receipts, $standalone_payments, $credit_notes, $credits_applied);

        // sort by date and priority (Invoices -> Credit Notes -> Receipts -> Payments -> Credits Applied)
        usort($merged, function ($a, $b) {
            $time_a = strtotime($a['date']);
            $time_b = strtotime($b['date']);
            if ($time_a !== $time_b) {
                return ($time_a < $time_b) ? -1 : 1;
            }
            $type_order_a = isset($a['invoice_id']) ? 1 : (isset($a['credit_note_id']) ? 2 : (isset($a['receipt_id']) ? 3 : (isset($a['payment_id']) ? 4 : 5)));
            $type_order_b = isset($b['invoice_id']) ? 1 : (isset($b['credit_note_id']) ? 2 : (isset($b['receipt_id']) ? 3 : (isset($b['payment_id']) ? 4 : 5)));
            if ($type_order_a !== $type_order_b) {
                return ($type_order_a < $type_order_b) ? -1 : 1;
            }
            $id_a = isset($a['invoice_id']) ? $a['invoice_id'] : (isset($a['receipt_id']) ? $a['receipt_id'] : (isset($a['payment_id']) ? $a['payment_id'] : (isset($a['credit_note_id']) ? $a['credit_note_id'] : (isset($a['credit_id']) ? $a['credit_id'] : 0))));
            $id_b = isset($b['invoice_id']) ? $b['invoice_id'] : (isset($b['receipt_id']) ? $b['receipt_id'] : (isset($b['payment_id']) ? $b['payment_id'] : (isset($b['credit_note_id']) ? $b['credit_note_id'] : (isset($b['credit_id']) ? $b['credit_id'] : 0))));
            if ($id_a != $id_b) {
                return ($id_a < $id_b) ? -1 : 1;
            }
            return 0;
        });

        // Define final result variable
        $result = array();
        // Store in result array key
        $result['result'] = $merged;

        // Invoiced amount during the period
        $row_invoiced = $this->db->query('SELECT
        SUM(tblinvoices.total) as invoiced_amount
        FROM tblinvoices
        WHERE clientid = ' . $customer_id . '
        AND ' . $sqlDate . ' AND status != 5 and status != 6')->row();

        $result['invoiced_amount'] = ($row_invoiced && isset($row_invoiced->invoiced_amount) && $row_invoiced->invoiced_amount !== null) ? $row_invoiced->invoiced_amount : 0;

        $row_credit_notes = $this->db->query('SELECT
        SUM(tblcreditnotes.total) as credit_notes_amount
        FROM tblcreditnotes
        WHERE clientid = ' . $customer_id . '
        AND ' . $sqlDate . ' AND status != 3')->row();

        $result['credit_notes_amount'] = ($row_credit_notes && isset($row_credit_notes->credit_notes_amount) && $row_credit_notes->credit_notes_amount !== null) ? $row_credit_notes->credit_notes_amount : 0;

        $result['invoiced_amount'] = $result['invoiced_amount'] - $result['credit_notes_amount'];

        // Amount paid during the period (Receipts + Standalone payments)
        $row_receipts = $this->db->query('SELECT
        COALESCE(SUM(tblreciepts.receipt_amount),0) as receipts_paid
        FROM tblreciepts
        WHERE tblreciepts.receipt_client_id = ' . $customer_id . '
        AND ' . $sqlDateReceipts)->row();
        $receipts_amount_paid = ($row_receipts && isset($row_receipts->receipts_paid)) ? $row_receipts->receipts_paid : 0;

        $row_standalone = $this->db->query('SELECT
        COALESCE(SUM(tblinvoicepaymentrecords.amount),0) as standalone_paid
        FROM tblinvoicepaymentrecords
        JOIN tblinvoices ON tblinvoices.id = tblinvoicepaymentrecords.invoiceid
        WHERE tblinvoices.clientid = ' . $customer_id . '
        AND ' . $sqlDatePayments . '
        AND (tblinvoicepaymentrecords.receipt_id = 0 OR tblinvoicepaymentrecords.receipt_id NOT IN (SELECT receipt_id FROM tblreciepts WHERE receipt_client_id = ' . $customer_id . '))')->row();
        $standalone_amount_paid = ($row_standalone && isset($row_standalone->standalone_paid)) ? $row_standalone->standalone_paid : 0;

        $result['amount_paid'] = $receipts_amount_paid + $standalone_amount_paid;

        // Beginning balance is all invoices amount before the FROM date - payments/receipts received before FROM date
        $row_inv_before = $this->db->query('SELECT
            COALESCE(SUM(tblinvoices.total), 0) as total
            FROM tblinvoices
            WHERE clientid = ' . $customer_id . '
            AND date < "' . $from . '"
            AND status != 6
            AND status != 5')->row();
        $invoices_before = ($row_inv_before && isset($row_inv_before->total)) ? $row_inv_before->total : 0;

        $row_rec_before = $this->db->query('SELECT
            COALESCE(SUM(tblreciepts.receipt_amount), 0) as total
            FROM tblreciepts
            WHERE receipt_client_id = ' . $customer_id . '
            AND receipt_date < "' . $from . '"')->row();
        $receipts_before = ($row_rec_before && isset($row_rec_before->total)) ? $row_rec_before->total : 0;

        $row_stand_before = $this->db->query('SELECT
            COALESCE(SUM(tblinvoicepaymentrecords.amount), 0) as total
            FROM tblinvoicepaymentrecords
            JOIN tblinvoices ON tblinvoices.id = tblinvoicepaymentrecords.invoiceid
            WHERE tblinvoices.clientid = ' . $customer_id . '
            AND tblinvoicepaymentrecords.date < "' . $from . '"
            AND (tblinvoicepaymentrecords.receipt_id = 0 OR tblinvoicepaymentrecords.receipt_id NOT IN (SELECT receipt_id FROM tblreciepts WHERE receipt_client_id = ' . $customer_id . '))')->row();
        $standalone_before = ($row_stand_before && isset($row_stand_before->total)) ? $row_stand_before->total : 0;

        $row_cn_before = $this->db->query('SELECT
            COALESCE(SUM(tblcreditnotes.total), 0) as total
            FROM tblcreditnotes
            WHERE clientid = ' . $customer_id . '
            AND date < "' . $from . '"
            AND status != 3')->row();
        $credit_notes_before = ($row_cn_before && isset($row_cn_before->total)) ? $row_cn_before->total : 0;

        $result['beginning_balance'] = $invoices_before - ($receipts_before + $standalone_before + $credit_notes_before);

        $dec = get_decimal_places();

        if (function_exists('bcsub')) {
            $result['balance_due'] = bcsub($result['invoiced_amount'], $result['amount_paid'], $dec);
            $result['balance_due'] = bcadd($result['balance_due'], $result['beginning_balance'], $dec);
        } else {
            $result['balance_due'] = number_format($result['invoiced_amount'] - $result['amount_paid'], $dec, '.', '');
            $result['balance_due'] = $result['balance_due'] + number_format($result['beginning_balance'], $dec, '.', '');
        }

        $result['client_id'] = $customer_id;
        $result['client']    = $this->clients_model->get($customer_id);
        $result['from']      = $from;
        $result['to']        = $to;

        $customer_currency = $this->clients_model->get_customer_default_currency($customer_id);
        $this->load->model('currencies_model');

        if ($customer_currency != 0) {
            $currency = $this->currencies_model->get($customer_currency);
        } else {
            $currency = $this->currencies_model->get_base_currency();
        }

        $result['currency'] = $currency;

        return $result;
    }

    /**
     * Send customer statement to email
     * @param  mixed $customer_id customer id
     * @param  array $send_to     array of contact emails to send
     * @param  string $from        date from
     * @param  string $to          date to
     * @param  string $cc          email CC
     * @return boolean
     */
    public function send_statement_to_email($customer_id, $send_to, $from, $to, $cc = '')
    {
        $sent = false;
        if (is_array($send_to) && count($send_to) > 0) {
            $this->load->model('emails_model');

            $statement = $this->get_statement($customer_id, to_sql_date($from), to_sql_date($to));

            $pdf = statement_pdf($statement);

            $pdf_file_name = slug_it(_l('customer_statement') . '-' . $statement['client']->company);

            $attach = $pdf->Output($pdf_file_name . '.pdf', 'S');

            $i = 0;
            foreach ($send_to as $contact_id) {
                if ($contact_id != '') {
                    $this->emails_model->add_attachment(array(
                            'attachment' => $attach,
                            'filename'   => $pdf_file_name . '.pdf',
                            'type'       => 'application/pdf',
                    ));

                    $contact      = $this->clients_model->get_contact($contact_id);
                    $merge_fields = array();
                    $merge_fields = array_merge(
                        $merge_fields,
                        get_client_contact_merge_fields(
                            $statement['client']->userid,
                            $contact_id
                        )
                    );

                    $merge_fields = array_merge($merge_fields, get_statement_merge_fields($statement));

                    // Send cc only for the first contact
                    if (!empty($cc) && $i > 0) {
                        $cc = '';
                    }
                    if ($this->emails_model->send_email_template('client-statement', $contact->email, $merge_fields, '', $cc)) {
                        $sent = true;
                    }
                }
                $i++;
            }

            if ($sent) {
                return true;
            }
        }

        return false;
    }
}
