<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Receipts_model extends CRM_Model
{

    /**
     * Receipts_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $data
     * @return bool
     */
    public function insert($data)
    {

        $checque_date = '';
        $cheque_num = '';
        $bank_name = '';
        $deposit_bank = '';

        $owner = $this->session->userdata['staff_user_id'];

        if (isset($data['cheque_date'])) {
            $checque_date = date('Y-m-d', strtotime($data['cheque_date']));
        }
        if (isset($data['adjustment'])) {
        } else {
            $data['adjustment'] = 0;
        }

        if (isset($data['cheque_num'])) {
            $cheque_num = $data['cheque_num'];
        }

        if (isset($data['bank_name'])) {
            $bank_name = $data['bank_name'];
        }
        if (isset($data['bank'])) {
            $deposit_bank = $data['bank'];
        }
        if (empty($bank_name) && !empty($deposit_bank)) {
            $bank_name = get_receipt_bank_name($deposit_bank);
        }

        if (isset($data['owner']) && $data['owner'] != "") {
            $owner = $data['owner'];
        }

        $total = $data['amount'];

        $table = [
            'receipt_client_id' => $data['client_id'],
            'reciept_owner' => $owner,
            'receipt_num' => $this->makeReceiptNumber(),
            'receipt_created_by' => $this->session->userdata['staff_user_id'],
            'receipt_amount' => $data['amount'],
            'receipt_type' => $data['type'],
            'receipt_date' => date('Y-m-d', strtotime($data['date'])),
            'receipt_cheque_date' => $checque_date,
            'receipt_cheque_num' => $cheque_num,
            'receipt_bank' => $bank_name,
            'deposit_bank' => $deposit_bank,
            'receipt_slip_no' => $data['slip_no'],
            'receipt_currency' => $data['currency'],
            'receipt_status' => $data['status'],
            'receipt_note' => $data['note'],
            'receipt_transaction_no' => $data['trxn_no'],
            'receipt_created_at' => date('Y-m-d H:i:s'),
            'adjustment' => $data['adjustment']
        ];

        $this->db->insert('tblreciepts', $table);
        $insertId = $this->db->insert_id();

        if (!$insertId) {
            return false;
        }

        return $insertId;
    }

    /**
     * @param $data
     * @return bool
     */
    public function update($post)
    {
        $this->db->trans_start();

        $data = $post['data'];

        $cheque_num = '';
        $bank_name = '';
        $deposit_bank = '';
        $checque_date = '';
        $receipt_amount = $post['data']['amount'];
        $client = $post['data']['client_id'];
        $paid = 0;

        $owner = $this->session->userdata['staff_user_id'];

        if (isset($data['cheque_date'])) {
            $checque_date = date('Y-m-d', strtotime($data['cheque_date']));
        }
        if (isset($data['adjustment'])) {
        } else {
            $data['adjustment'] = 0;
        }

        if (isset($data['cheque_num'])) {
            $cheque_num = $data['cheque_num'];
        }

        if (isset($data['bank_name'])) {
            $bank_name = $data['bank_name'];
        }
        if (isset($data['bank'])) {
            $deposit_bank = $data['bank'];
        }
        if (empty($bank_name) && !empty($deposit_bank)) {
            $bank_name = get_receipt_bank_name($deposit_bank);
        }

        if (isset($data['owner']) && $data['owner'] != "") {
            $owner = $data['owner'];
        }

        if (isset($data['owner']) && $data['owner'] != "") {
            $owner = $data['owner'];
        }


        $table = [
            'receipt_client_id' => $data['client_id'],
            'reciept_owner' => $owner,
            'receipt_num' => $data['receipt_num'],
            'receipt_created_by' => $this->session->userdata['staff_user_id'],
            'receipt_amount' => $data['amount'],
            'receipt_type' => $data['type'],
            'receipt_date' => date('Y-m-d', strtotime($data['date'])),
            'receipt_cheque_date' => $checque_date,
            'receipt_cheque_num' => $cheque_num,
            'receipt_bank' => $bank_name,
            'deposit_bank' => $deposit_bank,
            'receipt_slip_no' => $data['slip_no'],
            'receipt_currency' => $data['currency'],
            'receipt_status' => $data['status'],
            'receipt_note' => $data['note'],
            'adjustment' => $data['adjustment'],
        ];

        $this->db->where('receipt_id', $data['receipt_id']);
        $update = $this->db->update('tblreciepts', $table);

        if ($update) {

            if (isset($post['invoice'])) {
                foreach ($post['invoice'] as $inovice) {

                    $paymentmode = get_receipt_invoice_payment_mode_id(
                        $data['type'],
                        isset($inovice['paymentmode']) ? $inovice['paymentmode'] : 1
                    );

                    $paymentRecord = [
                        'paymentmode' => $paymentmode,
                        'paymentmethod' => $inovice['paymentmethod'],
                        'invoiceid' => $inovice['invoiceid'],
                        'amount' => $inovice['amount'],
                        'discount' => $inovice['discount'],
                        'date' => date('Y-m-d'),
                        'daterecorded' => date('Y-m-d'),
                        'adjustment' => $data['adjustment'],
                    ];

                    $this->db->where('id', $inovice['paymentrecordid']);
                    $paymentUpdate = $this->db->update('tblinvoicepaymentrecords', $paymentRecord);

                    if (!$paymentUpdate) {
                        return false;
                    }

                    if(isset($inovice['change_to_tax_invoice'])){

                        if($inovice['change_to_tax_invoice']) {

                            $update_data['number'] = make_next_invoice_num();
                            $update_data['prefix'] = get_option('invoice_prefix');
                            $update_data['type'] = 'invoice';

                            $this->db->where('id',  $inovice['invoiceid']);
                            $this->db->update('tblinvoices', $update_data);

                        }
                    }
                }
            }
        } else {
            return false;
        }

        $withdraw = 0;
        $advance = 0;
        if (isset($data['use_advance']) && is_numeric($data['use_advance'])) {
            $withdraw = (float)$data['use_advance'];
        }
        if (isset($data['add_advance']) && is_numeric($data['add_advance'])) {
            $advance = (float)$data['add_advance'];
        }

        $owner = $this->session->userdata['staff_user_id'];

        $table = [
            'receipt_id' => $post['data']['receipt_id'],
            'client_id' => $client,
            'amount' => $advance,
            'withdraw' => $withdraw,
            'remaining_advance' => ($advance - $withdraw),
            'date' => date('Y-m-d H:i:s'),
            'created_by' => $owner,
        ];

        $this->load->model('cashadvance_model');

        if (isset($data['previous_advance_entry']) && ($data['previous_advance_entry'] > 0)) {
            if (!$this->cashadvance_model->updateData($data['previous_advance_entry'], $table)) {
                return false;
            }
        } elseif ($advance > 0 || $withdraw > 0) {
            $this->db->insert('tblcashadvance', $table);
        }

        $this->db->trans_complete();

        return true;
    }

    /**
     * Get all  items
     * @param  mixed $id invoiceid
     * @return array
     */
    public function get($id = '', $where = array())
    {
        if($id){
            $this->db->select(', tblclients.company as client_name, tblclients.phonenumber as client_phone ');
        }else {
            $this->db->select('verify_date,receipt_created_by,reciept_owner,receipt_status,receipt_note,receipt_cheque_date,receipt_amount,receipt_type,receipt_slip_no,receipt_id,receipt_client_id,receipt_num,receipt_date, tblclients.company as client_name, tblclients.phonenumber as client_phone,tblreciepts.adjustment,tblreciepts.zoho_id');
        }
        $this->db->from('tblreciepts');
        $this->db->join('tblclients', 'tblclients.userid = tblreciepts.receipt_client_id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblreciepts.reciept_owner', 'left');
        $this->db->where($where);
        if (!is_admin()) {
            //$where['reciept_owner'] = $this->session->userdata['staff_user_id'];
            $this->db->or_where('reciept_owner', $this->session->userdata['staff_user_id']);
        }

        $this->db->order_by('receipt_date', 'DESC');


        return $this->db->get()->result();
    }

    public function get_zoho($id = '', $where = array())
    {

            $this->db->distinct('tblreciepts.receipt_id');
            $this->db->select('tblreciepts.receipt_id,tblreciepts.verify_date,tblreciepts.receipt_created_by,tblreciepts.reciept_owner,tblreciepts.receipt_status,tblreciepts.receipt_note,tblreciepts.receipt_cheque_date,tblreciepts.receipt_amount,tblreciepts.receipt_type,tblreciepts.receipt_bank,tblreciepts.deposit_bank,tblreciepts.receipt_currency,tblreciepts.receipt_slip_no,tblreciepts.receipt_id,tblreciepts.receipt_client_id,tblreciepts.receipt_num,tblreciepts.receipt_date,tblreciepts.zoho_id,tblclients.company as client_name, tblclients.phonenumber as client_phone,tblinvoicepaymentrecords.date as paydate,tblcurrencies.name as receipt_currency_code');

        $this->db->from('tblreciepts');
        $this->db->join('tblclients', 'tblclients.userid = tblreciepts.receipt_client_id', 'left');
        $this->db->join('tblinvoicepaymentrecords', 'tblinvoicepaymentrecords.receipt_id = tblreciepts.receipt_id', 'left');
        $this->db->join('tblcurrencies', 'tblcurrencies.id = tblreciepts.receipt_currency', 'left');
       // $this->db->join('tblcontacts', 'tblcontacts.userid = tblclients.userid', 'left');
      // $this->db->join('tblstaff', 'tblstaff.staffid = tblreciepts.reciept_owner', 'left');
        $this->db->where($where);
       // $this->db->limit(1);


        $this->db->order_by('receipt_date', 'DESC');


        return $this->db->get()->result_array();
    }
    public function get_zoho_recipt_invoices($id = '', $where = array())
    {
       /* $id=1627;*/

        $this->db->select('tblinvoicepaymentrecords.invoiceid,tblinvoicepaymentrecords.amount as applied_amount,tblinvoices.zoho_id,tblinvoices.prefix,tblinvoices.number,tblinvoices.total,tblinvoices.date,tblinvoices.currency,tblcurrencies.name as currency_code');
        $this->db->from('tblinvoicepaymentrecords');
        $this->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid', 'left');
        $this->db->join('tblcurrencies', 'tblcurrencies.id = tblinvoices.currency', 'left');
      //  $this->db->order_by('tblinvoicepaymentrecords.id', 'asc');
        $this->db->where('tblinvoicepaymentrecords.receipt_id', $id);
        $invoices = $this->db->get()->result_array();
        if (!$invoices) {
            return false;
        }


        return $invoices;
    }

    public function get_invoice_previous_payment($id = '',$receipt_id='')
    {

        $query = $this->db->query('SELECT SUM(amount) AS inv_payment_total FROM tblinvoicepaymentrecords WHERE invoiceid='.$id.' AND receipt_id!='.$receipt_id);
        $invoices_total = $query->result_array();
        if (!$invoices_total) {
            return false;
        }
        return $invoices_total[0]['inv_payment_total'];
    }


    /**
     * Get all  items
     * @param  mixed $id invoiceid
     * @return array
     */
    public function getbyId($id)
    {
        $this->db->select('tblreciepts.*, tblcurrencies.name as receipt_currency_code, tblcurrencies.symbol as receipt_currency_symbol');
        $this->db->from('tblreciepts');
        $this->db->join('tblcurrencies', 'tblcurrencies.id = tblreciepts.receipt_currency', 'left');
        $this->db->where(['receipt_id' => $id]);
        $receipt = $this->db->get()->row();

        if ($receipt) {
            if (empty($receipt->receipt_bank) && !empty($receipt->deposit_bank)) {
                $receipt->receipt_bank = get_receipt_bank_name($receipt);
            }
            if (function_exists('get_safe_currency_symbol')) {
                $receipt->receipt_currency_symbol = get_safe_currency_symbol(
                    $receipt->receipt_currency_code,
                    $receipt->receipt_currency_symbol
                );
            }
        }

        return $receipt;
    }

    /**
     * @param string $id
     * @param array $where
     * @return mixed
     */
    public function getTotalandDue($id = '')
    {
        $this->db->select('tblreciepts.receipt_num, tblreciepts.receipt_id, SUM(tblinvoices.total) as total_payable, SUM(tblinvoicepaymentrecords.amount) as total_paid');
        $this->db->from('tblreciepts');
        $this->db->join('tblinvoicepaymentrecords', 'tblinvoicepaymentrecords.receipt_id = tblreciepts.receipt_num', 'left');
        $this->db->join('tblinvoices', 'tblinvoices.number  = tblinvoicepaymentrecords.invoiceid', 'left');
        $this->db->where(['receipt_num' => $id]);
        $this->db->order_by('receipt_num', 'DESC');


        return $this->db->get()->result();
    }

    /**
     * @param $id
     * @param $status
     */
    public function updateStatus($id, $status)
    {
        $data = ['receipt_status' => $status];
        $date = date('Y-m-d H:i:s');
        $staff = $this->session->userdata['staff_user_id'];

        if ($status == 'handover') {
            $data['receipt_handover_by'] = $staff;
            $data['receipt_handover_on'] = $date;
        }

        if ($status == 'deposited') {
            $data['receipt_deposit_by'] = $staff;
            $data['receipt_deposit_on'] = $date;
        }

        if ($status == 'verified') {
            $data['receipt_verified_by'] = $staff;
            $data['receipt_verified_on'] = $date;
        }


        $this->db->where('receipt_id', $id);
        return $this->db->update('tblreciepts', $data);
    }

    public function updateStatusVerify($id, $status, $verify_date)
    {
        $data = ['receipt_status' => $status];
        $date = date('Y-m-d H:i:s');
        $staff = $this->session->userdata['staff_user_id'];
        $verify_date = date("Y-m-d", strtotime($verify_date));


        if ($status == 'verified') {
            $data['receipt_verified_by'] = $staff;
            $data['receipt_verified_on'] = $date;
            $data['verify_date'] = $verify_date;
        }


        $this->db->where('receipt_id', $id);
        return $this->db->update('tblreciepts', $data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function staffNameById($id)
    {
        $this->db->select('firstname, lastname');
        $this->db->from('tblstaff');
        $this->db->where(['staffid' => $id]);
        return $query = $this->db->get()->row();
    }

    /**
     * @return mixed
     */
    public function getLastReceiptNum()
    {
        $this->db->select('receipt_num');
        $this->db->from('tblreciepts');
        $this->db->limit(1);
        $this->db->order_by('receipt_num', 'DESC');
        return $query = $this->db->get()->row();
    }

    /**
     * @return bool|int
     */
    public function makeReceiptNumber()
    {

        $lastReceipt = $this->getLastReceiptNum();

        if ($lastReceipt <> null) {
            return $lastReceipt->receipt_num + 1;
        }
        return false;
    }

    /**
     * @return bool|int
     */
    public function getInvoicesReceiptNumber()
    {

        $lastReceipt = $this->getLastReceiptNum();

        if ($lastReceipt <> null) {
            return $lastReceipt->receipt_num + 1;
        }
        return false;
    }

    /**
     * @param $id
     */
    public function delete_receipt($id)
    {
        $this->db->where('receipt_id', $id);
        return $this->db->delete('tblreciepts');
    }

    /**
     * @param $id
     */
    public function delete_receipt_payments($id)
    {
        $this->db->where('receipt_id', $id);
        return $this->db->delete('tblinvoicepaymentrecords');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getInvoicesTotal($id)
    {
        $this->db->select('SUM(total) as total,date as invoice_date, 	id as invoice_id');
        $this->db->from('tblinvoices');
        $this->db->where('id', $id);
        return $query = $this->db->get()->row();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getAllStaff()
    {
        $this->db->select('staffid, firstname, lastname');
        $this->db->from(' tblstaff');
        return $query = $this->db->get()->result_array();
    }

    public function getCashAdvanceByReceipt($receipt_id)
    {
        $this->db->select('*');
        $this->db->from(' tblcashadvance');
        $this->db->where('receipt_id', $receipt_id);
        return $query = $this->db->get()->row();
    }

    /**
     * Send invoice to client
     * @param  mixed $id invoiceid
     * @param  string $template email template to sent
     * @param  boolean $attachpdf attach invoice pdf or not
     * @return boolean
     */
    public function send_receipt_to_client($id, $template = '', $attachpdf = true, $cc = '', $subject, $manually = false)
    {

        $this->load->model('emails_model');

        $this->emails_model->set_rel_id($id);
        $this->emails_model->set_rel_type('invoice');

        $receipt = $this->getbyId($id);

        if ($template == '') {
            if ($receipt->receipt_sent == 0) {
                $template = 'receipt-send-to-client';
            } else {
                $template = 'receipt-already-send';
            }
            $template = do_action('after_invoice_sent_template_statement', $template);
        }

        $receipt_number = format_receipt_number($receipt->receipt_num);

        $emails_sent = array();
        $send = false;
        // Manually is used when sending the invoice via add/edit area button Save & Send
        if (!DEFINED('CRON') && $manually === false) {
            $sent_to = $this->input->post('sent_to');
        } else {
            $sent_to = array();
            $contacts = $this->clients_model->get_contacts($receipt->receipt_client_id);
            foreach ($contacts as $contact) {
                if (has_contact_permission('receipts', $contact['id'])) {
                    array_push($sent_to, $contact['id']);
                }
            }
        }

        if (is_array($sent_to) && count($sent_to) > 0) {

            // $status_updated = update_invoice_status($receipt->receipt_id, true, true);

            if ($attachpdf) {

                $dataReceipts = $this->getbyId($id);

                $dataReceipts->clientid = $dataReceipts->receipt_client_id;

                $client = $this->clients_model->get($dataReceipts->receipt_client_id);

                $dataReceipts->client = $client;
                $dataReceipts->billing_street = $client->billing_street;
                $dataReceipts->billing_city = $client->billing_city;
                $dataReceipts->billing_state = $client->billing_state;
                $dataReceipts->billing_zip = $client->billing_zip;
                $dataReceipts->billing_country = $client->billing_country;
                $_pdf_receipt['receipts'] = $dataReceipts;

                // pre_array($_pdf_receipt);

                $invoices = $this->invoices_model->get_all_receipts_invoices($id);

                foreach ($invoices as $invoice) {

                    $invoice->total_amount = $this->receipts_model->getInvoicesTotal($invoice->invoiceid)->total;
                    $inv_data = $this->invoices_model->get($invoice->invoiceid);
                    $invoice->subject = $inv_data->subject;
                    $_pdf_receipt['invoices'][] = $invoice;

                }


                $_pdf_receipt['staff'] = $this->staff_model->get($dataReceipts->reciept_owner);

                $_pdf_receipt['created_by'] = $this->staff_model->get($dataReceipts->receipt_created_by);

                $pdf = receipt_pdf($_pdf_receipt);
                $attach = $pdf->Output($receipt_number . '.pdf', 'S');
            }


            $i = 0;
            foreach ($sent_to as $contact_id) {
                if ($contact_id != '') {
                    if ($attachpdf) {
                        $this->emails_model->add_attachment(array(
                            'attachment' => $attach,
                            'filename' => $receipt_number . '.pdf',
                            'type' => 'application/pdf'
                        ));
                    }

                    $contact = $this->clients_model->get_contact($contact_id);
                    $merge_fields = array();
                    $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($receipt->receipt_client_id, $contact_id));

                    $merge_fields = array_merge($merge_fields, get_receipt_merge_fields($receipt->receipt_id));

                    // Send cc only for the first contact
                    if (!empty($cc) && $i > 0) {
                        $cc = '';
                    }

                    if ($this->emails_model->send_email_template($template, $contact->email, $merge_fields, '', $cc, $subject)) {
                        $send = true;
                        array_push($emails_sent, $contact->email);
                    }
                }
                $i++;
            }
        } else {
            return false;
        }

        if ($send) {
            $this->set_receipt_sent($id, false, $emails_sent, true);
            return 1;
        }

        return false;
    }

    /**
     * Set invoice to sent when email is successfuly sended to client
     * @param mixed $id invoiceid
     * @param  mixed $manually is staff manually marking this invoice as sent
     * @return  boolean
     */
    public function set_receipt_sent($id, $manually = false, $emails_sent = array(), $is_status_updated = false)
    {
        $this->db->where('receipt_id', $id);
        $this->db->update('tblreciepts', array(
            'receipt_sent' => 1,
            'datesend' => date('Y-m-d H:i:s')
        ));
        $marked = false;
        if ($this->db->affected_rows() > 0) {
            $marked = true;
        }
        if (DEFINED('CRON')) {
            $additional_activity_data = serialize(array(
                '<custom_data>' . implode(', ', $emails_sent) . '</custom_data>'
            ));
            $description = 'invoice_activity_sent_to_client_cron';
        } else {
            if ($manually == false) {
                $additional_activity_data = serialize(array(
                    '<custom_data>' . implode(', ', $emails_sent) . '</custom_data>'
                ));
                $description = 'invoice_activity_sent_to_client';
            } else {
                $additional_activity_data = serialize(array());
                $description = 'invoice_activity_marked_as_sent';
            }
        }

        if ($is_status_updated == false) {
            update_invoice_status($id, true);
        }
        return $marked;
    }
}
