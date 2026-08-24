<?php ob_start();
defined('BASEPATH') or exit('No direct script access allowed');

class Receipts extends Admin_controller
{
    private $zohoCurrenciesByCode = null;

    public function __construct()
    {
        ob_start();
        parent::__construct();
  
        $this->load->model('receipts_model');
        $this->load->model('invoices_model');
        $this->load->model('payments_model');
        $this->load->model('clients_model');
        $this->load->model('staff_model');
        $this->load->model('currencies_model');
        $this->load->model('projects_model');
        $this->load->helper('url');
        $this->load->model('cashadvance_model');
        $this->load->model('leads_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->model('customnotes_model');
        $this->load->library('zohoBooks');


    }

    /**
     * @param string $status
     */
    /* Get all invoices in case user go on index page */
    public function index($status = '')
    {
         
       
      //  ini_set('display_errors', 1);
//error_reporting(E_ALL);

        if (!has_permission('receipts', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('receipts');
        }

        $data['canVerify'] = false;
        $data['canDesposit'] = false;
        $data['canHandover'] = false;

        $data['staff'] = $this->receipts_model->getAllStaff();

        $view = 'list_receipts';

        $where = array();
        $data['change_status'] = false;
        $data['reciept_owner'] = '';
        $data['receipt_created_by'] = '';
        $data['receipt_date'] = '';
        $data['receipt_cheque_date'] = '';
        $data['receipt_status'] = '';

        if ($status == "") {
            $status = 'created';
        } elseif ($status == 'handover') {
            $data['change_status'] = 'handover';
            $data['lang_heading'] = 'receipt_handover_title';
            $where['receipt_status'] = 'created';
        } elseif ($status == 'deposited') {
            $data['change_status'] = 'deposited';
            $data['lang_heading'] = 'receipt_deposit_title';
            $where['receipt_status'] = 'handover';
        } elseif ($status == 'verified') {
            $data['change_status'] = 'verified';
            $where['receipt_status'] = 'deposited';
            $data['lang_heading'] = 'receipt_verify_title';
        }

        if ($this->input->get('agent_id', TRUE)) {
            $agent_id = $this->input->get('agent_id', TRUE);
            $where['reciept_owner'] = $agent_id;
        }

        if ($this->input->get('status', TRUE)) {
            $status = $this->input->get('status', TRUE);
            $where['receipt_status'] = $status;
        }

        if ($this->input->post()) {

            if (!is_admin()) {
                $where['reciept_owner'] = $this->session->userdata['staff_user_id'];
                $where['receipt_created_by'] = $this->session->userdata['staff_user_id'];
            } else {

                if ($this->input->post('owner') != "") {
                    $where['reciept_owner'] = $this->input->post('owner');
                    $data['reciept_owner'] = $this->input->post('owner');
                }

                if ($this->input->post('created_by') != "") {
                    $where['receipt_created_by'] = $this->input->post('created_by');
                    $data['receipt_created_by'] = $this->input->post('created_by');
                }
            }

            if ($this->input->post('date') != "") {
                // $where['receipt_date'] = $this->input->post('date');
                $where['receipt_date'] = date('Y-m-d', strtotime($this->input->post('date')));
                $data['receipt_date'] = date('Y-m-d', strtotime($this->input->post('date')));
            }

            if ($this->input->post('cheque_date') != "") {
                //$where['receipt_cheque_date'] = $this->input->post('cheque_date');
                $where['receipt_cheque_date'] = date('Y-m-d', strtotime($this->input->post('cheque_date')));
                $data['receipt_cheque_date'] = date('Y-m-d', strtotime($this->input->post('cheque_date')));
            }

            if ($this->input->post('status') != "") {
                if(isset($_POST['status']) && $_POST['status'] == 'posted'){
                    $where['TRIM(COALESCE(tblreciepts.zoho_id, "")) !='] = '';
                    $where['UPPER(TRIM(COALESCE(tblreciepts.zoho_id, ""))) !='] = 'NULL';
                    $where['tblreciepts.adjustment !='] = 1;
                }else if(isset($_POST['status']) && $_POST['status'] == 'not_posted'){
                    $where['TRIM(COALESCE(tblreciepts.zoho_id, "")) ='] = '';
                    $where['tblreciepts.adjustment !='] = 1;
                }else {
                    $where['receipt_status'] = $this->input->post('status');
                }
                $data['receipt_status'] = $this->input->post('status');
            }

        } else {

            if (!is_admin()) {
                $where['reciept_owner'] = $this->session->userdata['staff_user_id'];
                $where['receipt_created_by'] = $this->session->userdata['staff_user_id'];
            }
        }

        //Set Permissions
        if (is_admin() || has_permission('receipt_verify', '', 'edit')) {
            $data['canVerify'] = true;
        }

        if (is_admin() || has_permission('receipt_deposit', '', 'edit')) {
            $data['canDesposit'] = true;
        }

        if (is_admin() || has_permission('receipt_handover', '', 'edit')) {
            $data['canHandover'] = true;
        }
        $data['invoiceid'] = '';
        /* if (is_numeric($id)) {
             $data['invoiceid'] = $id;
         }*/
        $datatable = array();

        $receipts = $this->receipts_model->get('', $where);

        foreach ($receipts as $value) {

            $created_by = '';
            $receipt = $value;

            if ($receipt <> null) {

                $rec_id = $receipt->receipt_id;

                if ($receipt->reciept_owner <> null) {
                    $reciept_owner = $this->receipts_model->staffNameById($receipt->reciept_owner);
                }

                if ($receipt->receipt_created_by <> null) {
                    $created_by = $this->receipts_model->staffNameById($receipt->receipt_created_by);
                }
                $row = array();

                $row[] = '<a  class="cpointor" onclick="init_reciept(' . $receipt->receipt_id . '); return false;">' . $receipt->receipt_num . '</a>';
                $row[] = date('d-m-Y', strtotime($value->receipt_date));
                $row[] = '<a target="_blank" href="/admin/clients/client/' . $value->receipt_client_id . '">' . $value->client_name . '</a><br>' . $value->client_phone;
                $row[] = $value->receipt_slip_no;
                $row[] = $value->receipt_amount;
                $row[] = $value->receipt_type;
                $row[] = date('d-m-Y', strtotime($value->receipt_cheque_date));
                $row[] = $value->receipt_note;

                $status = '';
                $status .= '<div class="invoice_status_li-' . $rec_id . '"><span class="label ';
                if ($receipt->receipt_status == 'handover') {
                    $status .= 'label-success';
                } elseif ($receipt->receipt_status == 'deposited') {
                    $status .= 'label-info';
                } elseif ($receipt->receipt_status == 'verified') {
                    $status .= 'label-warning';
                } else {
                    $status .= 'label-default';
                }
                $status .= ' s-status">';
                $status .= $receipt->receipt_status;
                $status .= ' </span>';
                if ($receipt->receipt_status == 'verified') {
                    $verify_date = '';
                    if ($receipt->verify_date <> null) {
                        $verify_date = date('d-m-Y', strtotime($receipt->verify_date));
                    }
                    $status .= '<h5 style="padding-left: 7px;">' . $verify_date . '</h5> </div>';
                }
                $row[] = $status;
                $zoho_status='';
                if ($receipt->adjustment != 1 && $receipt->adjustment != 2 && $receipt->adjustment != 3) {
                    $receipt_zoho_id = trim((string)$receipt->zoho_id);
                    if ($receipt_zoho_id !== '' && strtoupper($receipt_zoho_id) !== 'NULL') {
                        $zoho_status = '<a id="zoho_disabled" class="pull-right btn btn-default btn-with-tooltip" data-toggle="tooltip"
                       title="Posted" data-placement="bottom"
                       style="margin-right: 5px;"><i class="fa fa-clipboard"> Posted</i>
                    </a>';
                    } else {
                        $zoho_status = '<a id="post_to_zoho" class="pull-right btn btn-success post_to_zoho btn-with-tooltip"
                         data_id="' . $rec_id . '" data-toggle="tooltip"
                         title="Post To Zoho" data-placement="bottom"
                         style="margin-right: 5px;"><i class="fa fa-clipboard"> Post</i>
                         </a>';

                    }
                }
                $row[] = $zoho_status;
                $adjustment = '';
                if($receipt->adjustment == 1){
                    $adjustment = '<span class="label label-default">Adjustment</span>';
                }else if($receipt->adjustment == 2){
                    $adjustment = '<span class="label label-info">Out of Book</span>';
                }else if($receipt->adjustment == 3){
                    $adjustment = '<span class="label label-warning">Bad Debts</span>';
                }
                $row[] = $adjustment;

                $actions = '';
                $actions .= '<div class="btn-group">
                            <button class="label label-default-light dropdown-toggle"
                             data-toggle="dropdown">
                            Action <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="' . base_url() . 'admin/receipts/#/' . $rec_id . '"
                                                           id="' . $rec_id . '"
                                                           class="text text-primary" target="_blank">Preview</a>
                                    </li>
                                    <li>
                                        <a href="' . base_url() . 'admin/receipts/update/' . $rec_id . '"
                                            id="' . $rec_id . '"
                                            class="text text-primary" target="_blank">Edit
                                         </a>
                                    </li>';
                if (is_admin()) {
                    $actions .= '<li><a href="#" id="' . $rec_id . '" class="delete text text-danger">Delete</a> </li>';
                }
                $actions .= '<li>
                                <a href="javascript:void(0)" id="' . $rec_id . '"
        
                                 class="status_change_status text text-danger">Status <span class="glyphicon glyphicon-chevron-right pull-right"></span></a>
        
                                  <ul class="dropdown_custom">
                                   <li id="handover_' . $rec_id . '"><a href="javascript:void(0)">Handover</a></li>
                                   <li id="deposited_' . $rec_id . '"><a href="javascript:void(0)">Deposited</a></li>
                                   <li id="verified_' . $rec_id . '"><a href="javascript:void(0)">Verified</a></li>
                                   <li id="created_' . $rec_id . '"><a href="javascript:void(0)">Created</a></li>
                                   </ul>
                                </li>

                     </ul>
                </div>';


                $row[] = $actions;


                if (is_admin()) {

                    $row[] = ($reciept_owner <> null) ? $reciept_owner->firstname . ' ' . $reciept_owner->lastname : '';
                    $row[] = ($created_by <> null) ? $created_by->firstname . ' ' . $created_by->lastname : '';
                }
                $datatable[] = $row;
            }
        }
        /* $output = array(
             "data" => $datatable,
         );*/


        $data['receipts_data1'] = json_encode($datatable);

        $data['receipts'] = $this->receipts_model->get('', $where);
        //echo $view;
       // die;
      // die('here9');
        $this->load->view('admin/receipts/' . $view, $data);
         

    }

    /* Get all invoices in case user go on index page */
    public function create()
    {

        if (!has_permission('receipts', '', 'create')) {
            access_denied('receipts');
        }

        $data['clients'] = $this->clients_model->get();
        $data['currencies'] = $this->currencies_model->get();
        $default_currency = $this->currencies_model->get_base_currency();
        $data['default_currency'] = $default_currency->id;
        $data['projects'] = $this->projects_model->get();
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['receipt_num'] = $this->receipts_model->makeReceiptNumber();
        $owner = $this->session->userdata['staff_user_id'];


        if ($this->input->post()) {

            $post = $this->input->post();
            $this->validateReceiptInvoiceCurrencySelection($post);
            $this->validateReceiptDepositBankCurrencySelection($post);
            // pre_array($post['data']['date']);
            // INSERT DATA

            $receipt_id = $this->receipts_model->insert($post['data']);


            $receipt_amount = $post['data']['amount'];
            $client = $post['data']['client_id'];

            $paid = 0;
            $advance = 0;
            $withdraw = 0;

            // IF insert successful
            if ($receipt_id) {
                // pre_array($post);
                if (isset($post['invoice'])) {

                    foreach ($post['invoice'] as $key => $inovice) {
                        $inv = $post['invoice'][$key];
                        $inv['do_not_send_email_template'] = 1;
                            $inv['paymentmode'] = get_receipt_invoice_payment_mode_id(
                            $post['data']['type'],
                            isset($inv['paymentmode']) ? $inv['paymentmode'] : 1
                        );
                        // IF invoice in paid and amount greater than 0
                        if ($inv['amount'] > 0) {

                            unset($inv['total']);
                            unset($inv['amount_due']);
                            // unset($inv['discount']);
                            $inv['receipt_id'] = $receipt_id;
                            $paid = $paid + $inv['amount'];
                            $changeToTax = false;

                            if (isset($inv['change_to_tax_invoice'])) {
                                $changeToTax = true;
                                unset($inv['change_to_tax_invoice']);
                            }
                            $inv['date'] = $post['data']['date'];
                            $inv['client_id'] = $post['data']['client_id'];
                            $inv['adjustment'] = $post['data']['adjustment'];

                            $payment = $this->payments_model->process_payment($inv, '');

                            if ($payment && $changeToTax) {
                                if($post['data']['adjustment'] == 1){
                                    $changeToTax = false;
                                }
                                if ($changeToTax) {

                                    $update_data['number'] = make_next_invoice_num();
                                    $update_data['prefix'] = get_option('invoice_prefix');
                                    $update_data['type'] = 'invoice';
                                  //  $update_data['date'] = date('Y-m-d');
                                    $update_data['date'] = date('Y-m-d', strtotime($post['data']['date']));
                                    $this->db->where('id', $inv['invoiceid']);
                                    $this->db->update('tblinvoices', $update_data);
                                    //$unv = $this->createInvoice($inv['invoiceid']);
                                    // $this->create_zoho($inv['invoiceid']);

                                }
                            }
                        }
                    }
                }

                //$this->createReceipt($receipt_id);
            }
            // pre_array($receipt_id);
            // Get total advance remaining
            $balance = $this->get_clients_advance_cash($client, false);

            // If client choose the advance to use
            if (isset($post['data']['use_advance']) && $post['data']['use_advance'] > 0) {
                $withdraw = $post['data']['use_advance'];
                $remaining = $balance - $withdraw;
                $this->AddCashAdnvance($receipt_id, $client, $advance, $withdraw, $remaining, $owner);
            }

            // If client choose to add advance to use later
            if (isset($post['data']['add_advance']) && $post['data']['add_advance'] > 0) {
                $advance = $post['data']['add_advance'];
                $remaining = $balance + $advance;
                $this->AddCashAdnvance($receipt_id, $client, $advance, $withdraw, $remaining, $owner);
            }

            // redirect('admin/receipts/details/' . $receipt_id, 'refresh');
            redirect('admin/receipts/#' . $receipt_id, 'refresh');
        }

        $this->load->view('admin/receipts/create', $data);
    }

    /**
     * @param string $id
     */
    public function update($id = '')
    {

        if (!has_permission('receipts', '', 'edit')) {
            access_denied('receipts');
        }

        if (!is_admin()) {
            redirect('admin/', 'refresh');
        }

        if ($this->input->post()) {

            $post = $this->input->post();
            $this->validateReceiptInvoiceCurrencySelection($post);
            $this->validateReceiptDepositBankCurrencySelection($post);
            $update = $this->receipts_model->update($post);

            // redirect('admin/receipts/details/' . $id, 'refresh');
            redirect('admin/receipts/#' . $id, 'refresh');
        }

        $data['clients'] = $this->clients_model->get();
        $data['currencies'] = $this->currencies_model->get();
        $default_currency = $this->currencies_model->get_base_currency();
        $data['default_currency'] = $default_currency->id;
        $data['projects'] = $this->projects_model->get();
        $data['staff'] = $this->staff_model->get('', 1);

        $where['tblreciepts'] = $id;
        $data['receipts'] = $this->receipts_model->getbyId($id);

        $data['receipt_id'] = $id;
        $data['invoices'] = $this->invoices_model->get_all_receipts_invoices($id);
        $data['cashAdvance'] = $this->receipts_model->getCashAdvanceByReceipt($id);

        $advance = $this->get_clients_advance_cash($data['receipts']->receipt_client_id, false);
        if ($advance > 0) {
            $data['receipts']->advance_amount = $advance;
        } else {
            $data['receipts']->advance_amount = 0;
        }

        $this->load->view('admin/receipts/update', $data);
    }

    /**
     * @param $id
     */
    public function details($id)
    {
        $template_name = "receipt-send-to-client";

        $dataReceipts = $this->receipts_model->getbyId($id);

        if ($dataReceipts == null) {
            redirect('admin/receipts/', 'refresh');
        }

        $dataReceipts->clientid = $dataReceipts->receipt_client_id;

        $client = $this->clients_model->get($dataReceipts->receipt_client_id);
        $data['cashAdvance'] = $this->receipts_model->getCashAdvanceByReceipt($id);

        $dataReceipts->client = $client;
        $dataReceipts->billing_street = $client->billing_street;
        $dataReceipts->billing_city = $client->billing_city;
        $dataReceipts->billing_state = $client->billing_state;
        $dataReceipts->billing_zip = $client->billing_zip;
        $dataReceipts->billing_country = $client->billing_country;

        $data['receipts'] = $dataReceipts;
        $data['invoices'] = $this->invoices_model->get_all_receipts_invoices($id);
        $data['staff'] = $this->staff_model->get($dataReceipts->reciept_owner);

        $contact = $this->clients_model->get_contact(get_primary_contact_user_id($dataReceipts->clientid));
        $email = '';
        if ($contact) {
            $email = $contact->email;
        }

        $data['template'] = get_email_template_for_sending($template_name, $email);

        $data['template_name'] = $template_name;

        $data['template_name'] = $template_name;
        $this->db->where('slug', $template_name);
        $this->db->where('language', 'english');
        $template_result = $this->db->get('tblemailtemplates')->row();

        $data['template_system_name'] = $template_result->name;
        $data['template_id'] = $template_result->emailtemplateid;

        $data['template_disabled'] = false;
        if (total_rows('tblemailtemplates', array('slug' => $data['template_name'], 'active' => 0)) > 0) {
            $data['template_disabled'] = true;
        }

        $this->load->view('admin/receipts/receipt_preview_template', $data);
    }

    /**
     * @param $receipt_id
     * @param $client
     * @param string $advance
     * @param string $withdraw
     * @param $remaining
     * @param $owner
     * @return mixed
     */
    public function AddCashAdnvance($receipt_id, $client, $advance = '', $withdraw = '', $remaining = '', $owner = '')
    {
        $owner = $this->session->userdata['staff_user_id'];

        $table = [
            'receipt_id' => $receipt_id,
            'client_id' => $client,
            'amount' => $advance,
            'withdraw' => $withdraw,
            'remaining_advance' => $remaining,
            'date' => date('Y-m-d H:i:s'),
            'created_by' => $owner,
        ];

        return $this->cashadvance_model->insert($table);
    }

    /**
     * @param $id
     */
    /* Generates invoice PDF and senting to email of $send_to_email = true is passed */
    public function pdf($id)
    {

        $dataReceipts = $this->receipts_model->getbyId($id);

        $dataReceipts->clientid = $dataReceipts->receipt_client_id;

        $client = $this->clients_model->get($dataReceipts->receipt_client_id);

        $dataReceipts->client = $client;
        $dataReceipts->billing_street = $client->billing_street;
        $dataReceipts->billing_city = $client->billing_city;
        $dataReceipts->billing_state = $client->billing_state;
        $dataReceipts->billing_zip = $client->billing_zip;
        $dataReceipts->billing_country = $client->billing_country;
        $_pdf_receipt['receipts'] = $dataReceipts;


        $invoices = $this->invoices_model->get_all_receipts_invoices($id);

        foreach ($invoices as $invoice) {

            $invoice->total_amount = $this->receipts_model->getInvoicesTotal($invoice->invoiceid)->total;
            $inv_data = $this->invoices_model->get($invoice->invoiceid);
            $invoice->subject = $inv_data->subject;
            $invoice->date = $inv_data->date;
            $_pdf_receipt['invoices'][] = $invoice;

        }
        $_pdf_receipt['staff'] = $this->staff_model->get($dataReceipts->reciept_owner);

        $_pdf_receipt['created_by'] = $this->staff_model->get($dataReceipts->receipt_created_by);
        ob_end_clean();

        try {
            $pdf = receipt_pdf($_pdf_receipt);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== FALSE) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf_name = format_receipt_number($dataReceipts->receipt_num);

        $pdf->Output($pdf_name . '.pdf', $type);
    }

    /**
     * @param $id
     */
    public function delete()
    {
        if ($this->input->post()) {
             $this->db->select('invoiceid');
            $this->db->from('tblinvoicepaymentrecords');
            $this->db->where(['receipt_id' => $this->input->post('receipt_id')]);
            $items = $this->db->get()->result_array();
    
            if (isset($items) && !empty($items)) {
                foreach ($items as $key => $val) {
                        $this->db->where('id', $val['invoiceid']);
                        $this->db->update('tblinvoices', array(
                            'status' => 1
                        ));
                   
                }
            }
            if ($this->receipts_model->delete_receipt_payments($this->input->post('receipt_id'))) {
                $this->receipts_model->delete_receipt($this->input->post('receipt_id'));
                redirect('admin/receipts/', 'refresh');
            }
        }
    }

    /**
     * @param string $status
     */
    public function updateStatus($status = '')
    {
        if ($this->input->post()) {

            if (count($this->input->post('receipt')) > 0) {
                foreach ($this->input->post('receipt') as $receipt) {
                    if (isset($receipt['status']) && isset($receipt['id'])) {
                        if ($receipt['id'] != "" && $receipt['status'] != "") {
                            $this->receipts_model->updateStatus($receipt['id'], $receipt['status']);
                        }
                    }
                }
                redirect('admin/receipts/', 'refresh');
            }

            if ($this->input->post('changeStatus')) {
                echo $this->receipts_model->updateStatus($this->input->post('id'), $this->input->post('status'));
            }
        }
    }

    /**
     * @param string $status
     */
    public function updateStatusVerify($status = '')
    {
        if ($this->input->post()) {

            if (count($this->input->post('receipt')) > 0) {
                foreach ($this->input->post('receipt') as $receipt) {
                    if (isset($receipt['status']) && isset($receipt['id'])) {
                        if ($receipt['id'] != "" && $receipt['status'] != "") {
                            $this->receipts_model->updateStatus($receipt['id'], $receipt['status']);
                        }
                    }
                }
                redirect('admin/receipts/', 'refresh');
            }

            if ($this->input->post('changeStatus')) {
                echo $this->receipts_model->updateStatusVerify($this->input->post('id'), $this->input->post('status'), $this->input->post('verify_date'));
            }
        }
    }

    /**
     * @param $client
     */
    public function clients_invoices($client)
    {
        if ($client) {
            $currency = $this->input->get('currency');
            $selected_currency_code = get_receipt_currency_code($currency);

            $data = $this->invoices_model->get_all_Customer_invoices($client, $currency);
            // $data_read = $this->invoices_model->get_all_Customer_proforma_invoices($client);
            $data_read = [];


            $total_due = 0;
            $total_payable = 0;
            $html = '';
            $html_total = '';

            if ($data <> null) {

                $i = 0;
                foreach ($data as $item) {

                    $statusAccepted = true;
                    /* $invoice_accepted = get_invoice_status($item['id']);

                     if ($invoice_accepted <> null) {
                         if ($invoice_accepted->invst_status <> 'accepted') {
                             $statusAccepted = false;
                         }
                     }*/

                    if ($item['status'] == 6) {
                        $disable = 'disabled="true"';
                    } else {
                        $disable = '';
                    }

                    // $amount_left = $item['total'] - $item['discount_total'];

                    $amount_due = get_invoice_total_left_to_pay($item['id'], $item['total']);

                    if ($amount_due > 0) {

                        // if ($item['type'] == "invoice") {

                        $invoice_currency_code = !empty($item['currency_code']) ? normalize_receipt_currency_code($item['currency_code']) : '';
                        $currency_mismatch = $selected_currency_code !== ''
                            && $invoice_currency_code !== ''
                            && $selected_currency_code !== $invoice_currency_code;
                        $amount_readonly = $currency_mismatch ? 'readonly="readonly"' : '';
                        $amount_help = $currency_mismatch
                            ? '<span class="display-block text-danger">Select ' . html_escape($invoice_currency_code) . ' receipt currency to pay this invoice.</span>'
                            : '';

                        if (!$currency_mismatch) {
                            $total_payable = $item['total'] + $total_payable;
                            $total_due = $amount_due + $total_due;
                        }

                        $to_pay = get_invoice_total_left_to_pay($item['id'], $item['total']);

                        // }

                        $html .= '<tr><input name="invoice[' . $i . '][paymentmode]" type="hidden" value="1"/>';

                        $html .= '<td>';

                        if ($item['type'] == "performa") {

                            if(is_admin()){
                                $html .= '<input  name="invoice[' . $i . '][change_to_tax_invoice]" type="checkbox" checked="checked" value="1" />';
                            }else{
                                $html .= '<input style="display:none;" name="invoice[' . $i . '][change_to_tax_invoice]" type="checkbox" checked="checked" value="1" />';
                            }
                        }
                        $html .= '</td>';

                        $html .= '<td><input name="invoice[' . $i . '][paymentmethod]" type="hidden" value="' . date("
                                             Y-m-d H:i:s") . '" />' . $item['date'] . '</td>';

                        if ($item['type'] == "invoice") {

                            $html .= '<td><input name="invoice[' . $i . '][invoiceid]" type="hidden"
                                             value="' . $item['id'] . '"/><a href="' . admin_url('/invoices/list_invoices#' . $item['id']) . '" target="_blank">' . format_invoice_number($item['id']) . '</a><span class="display-block text-muted">' . html_escape($item['currency_code']) . '</span></td>';
                        } else {
                            $html .= '<td><input name="invoice[' . $i . '][invoiceid]" type="hidden"
                                             value="' . $item['id'] . '"/><strike><a style="color:#cc6600;" href="' . admin_url('/invoices/list_invoices#' . $item['id']) . '" target="_blank">' . format_invoice_number($item['id']) . '</a></strike><span class="display-block text-muted">' . html_escape($item['currency_code']) . '</span></td>';
                        }

                        $html .= '<td><input ' . $disable . ' name="invoice[' . $i . '][total]" class="form-control" type="text"
                                             value="' . $item['total'] . '" style="width: 100px;"/></td>';
                        $html .= '<td><input id="amount_due_' . $item['id'] . '" ' . $disable . ' name="invoice[' . $i . '][amount_due]" class="form-control" type="text"
                                             value="' . $amount_due . '" style="width: 100px;"/></td>';
                        $html .= '<td><input ' . $disable . ' name="invoice[' . $i . '][discount]" class="form-control" type="text"
                                             value="0" min="0" max="' . $amount_due . '" style="width: 100px;"/></td>';
                        $html .= '<td><input ' . $disable . ' ' . $amount_readonly . ' name="invoice[' . $i . '][amount]" class="payment_amount form-control"
                                             id="_' . $item['id'] . '" type="text" value="0" data-currency-code="' . html_escape($invoice_currency_code) . '" style="width: 100px;"/>' . $amount_help . '</td>';
                        $html .= '</tr>';

                        $i++;
                    }

                }

                $html_total .= '<tr>';
                $html_total .= '<td>&nbsp;</td>';
                $html_total .= '<td>&nbsp;</td>';
                $selected_currency_symbol = $this->getCurrencySymbolById($currency);
                $html_total .= '<td><h5>Total Payable: ' . format_money($total_payable, $selected_currency_symbol) . '</h5></td>';
                $html_total .= '<td><h5>Total Due: ' . format_money($total_due, $selected_currency_symbol) . '</h5></td>';
                $html_total .= '<td>&nbsp;</td>';
                $html_total .= '<td><h5>Total Amount: <span id="amount_total">0</span></h5></td>';
                $html_total .= '</tr>';

            }

            if ($data <> null) {
                $html .= $html_total;
            }

            print_r(json_encode(['html' => $html, 'total_payable' => $total_payable, 'amount_due' => $total_due]));
        }
    }

    public function client_currency($client)
    {
        if (!$client) {
            echo json_encode(['success' => false]);
            return;
        }

        $client_currency_id = (int)$this->clients_model->get_customer_default_currency($client);
        $currency_source = 'client';
        $currency_id = $client_currency_id;

        if ($currency_id <= 0) {
            $currency_source = 'base';
            $currency_id = $this->getClientDefaultCurrencyId($client);
        }

        $currency = $currency_id > 0 ? $this->currencies_model->get($currency_id) : null;

        if (!$currency) {
            echo json_encode(['success' => false]);
            return;
        }

        echo json_encode([
            'success' => true,
            'id' => $currency->id,
            'name' => $currency->name,
            'symbol' => $currency->symbol,
            'source' => $currency_source,
            'message' => $currency_source === 'client'
                ? 'Currency is set from selected customer: ' . $currency->name . '.'
                : 'Customer currency is not defined. Base currency ' . $currency->name . ' is used.',
        ]);
    }

    /**
     * @param $client
     */
    public function get_clients_advance_cash($client, $output = true)
    {
        $amount = $this->cashadvance_model->get_total_advance_amount($client);

        if ($output) {
            echo $amount;
            return;
        }

        return $amount;
    }

    private function getCurrencySymbolById($currency_id)
    {
        $currency = $this->currencies_model->get($currency_id);

        if ($currency && !empty($currency->symbol)) {
            return $currency->symbol;
        }

        $base_currency = $this->currencies_model->get_base_currency();

        return $base_currency ? $base_currency->symbol : '';
    }

    private function validateReceiptInvoiceCurrencySelection($post)
    {
        if (!empty($post['data']['client_id']) && !empty($post['data']['currency'])) {
            $this->assertCurrencyMatchesClientDefault(
                $post['data']['client_id'],
                $post['data']['currency'],
                'Receipt'
            );
        }

        if (empty($post['invoice']) || empty($post['data']['currency'])) {
            return;
        }

        $receipt_currency_code = get_receipt_currency_code($post['data']['currency']);
        $paid_invoice_ids = [];

        foreach ($post['invoice'] as $invoice) {
            $amount = isset($invoice['amount']) ? (float)$invoice['amount'] : 0;

            if ($amount > 0 && !empty($invoice['invoiceid'])) {
                $paid_invoice_ids[] = (int)$invoice['invoiceid'];
            }
        }

        $paid_invoice_ids = array_values(array_unique($paid_invoice_ids));

        if (empty($paid_invoice_ids)) {
            return;
        }

        $invoices = $this->db
            ->select('tblinvoices.id, tblinvoices.prefix, tblinvoices.number, tblcurrencies.name as currency_code')
            ->from('tblinvoices')
            ->join('tblcurrencies', 'tblcurrencies.id = tblinvoices.currency', 'left')
            ->where_in('tblinvoices.id', $paid_invoice_ids)
            ->get()
            ->result_array();

        $invoice_currency_codes = [];
        $mismatched_labels = [];

        foreach ($invoices as $invoice) {
            $invoice_currency_code = !empty($invoice['currency_code'])
                ? normalize_receipt_currency_code($invoice['currency_code'])
                : '';

            if ($invoice_currency_code !== '') {
                $invoice_currency_codes[$invoice_currency_code] = true;
            }

            if (
                $receipt_currency_code !== ''
                && $invoice_currency_code !== ''
                && $receipt_currency_code !== $invoice_currency_code
            ) {
                $mismatched_labels[] = trim($invoice['prefix'] . $invoice['number']) . ' (' . $invoice_currency_code . ')';
            }
        }

        if (!empty($mismatched_labels)) {
            show_error(
                'Receipt currency is ' . $receipt_currency_code
                . ', but payment amount was entered for invoice(s): '
                . implode(', ', $mismatched_labels)
                . '. Please create a separate receipt for each currency.',
                400
            );
        }

        if (count($invoice_currency_codes) > 1) {
            show_error(
                'Please create a separate receipt for each currency. Selected paid invoices contain: '
                . implode(', ', array_keys($invoice_currency_codes)) . '.',
                400
            );
        }
    }

    private function validateReceiptDepositBankCurrencySelection($post)
    {
        if (empty($post['data']) || empty($post['data']['currency'])) {
            return;
        }

        $receipt_type = isset($post['data']['type']) ? strtolower(trim($post['data']['type'])) : '';

        if ($receipt_type === 'cash' || $receipt_type === 'stripe') {
            return;
        }

        if (empty($post['data']['bank'])) {
            return;
        }

        $bank = get_receipt_deposit_bank($post['data']['bank'], true);

        if (!$bank || empty($bank['currency_code'])) {
            return;
        }

        $receipt_currency_code = get_receipt_currency_code($post['data']['currency']);
        $bank_currency_code = normalize_receipt_currency_code($bank['currency_code']);

        if ($receipt_currency_code !== '' && $bank_currency_code !== '' && $receipt_currency_code !== $bank_currency_code) {
            show_error(
                'Selected bank currency is ' . $bank_currency_code
                . ', but receipt currency is ' . $receipt_currency_code
                . '. Please select a matching bank account or change the receipt currency.',
                400
            );
        }
    }

    private function getClientDefaultCurrencyId($client_id)
    {
        $client_currency = (int)$this->clients_model->get_customer_default_currency($client_id);

        if ($client_currency > 0) {
            return $client_currency;
        }

        $base_currency = $this->currencies_model->get_base_currency();

        return $base_currency ? (int)$base_currency->id : 0;
    }

    private function assertCurrencyMatchesClientDefault($client_id, $currency_id, $context)
    {
        $client_currency_id = $this->getClientDefaultCurrencyId($client_id);

        if ($client_currency_id <= 0 || (int)$currency_id === $client_currency_id) {
            return;
        }

        $client_currency_code = get_receipt_currency_code($client_currency_id);
        $selected_currency_code = get_receipt_currency_code($currency_id);

        show_error(
            $context . ' currency must match the selected customer currency. Customer currency is '
            . $client_currency_code . ', selected currency is ' . $selected_currency_code . '.',
            400
        );
    }

    /**
     *
     */
    /* Send invoiece to email */
    public function send_to_email($id)
    {
        ob_start();

        if (!has_permission('receipts', '', 'view') && !has_permission('receipts', '', 'view_own')) {
            access_denied('receipts');
        }
        $success = $this->receipts_model->send_receipt_to_client($id, '', $this->input->post('attach_pdf'), $this->input->post('cc'), $this->input->post('subject'));
        // pre_array($success);
        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('invoice_sent_to_client_success'));
        } else {
            set_alert('danger', _l('invoice_sent_to_client_fail'));
        }

        redirect(admin_url('receipts/#' . $id));
    }

    public function convert_performa_invoices_to_tax_invoice()
    {

        if ($this->input->post()) {

            $id = $this->input->post('invoice_id');

            $inv_data = $this->invoices_model->get($id);;
            $update_data['pinv_reference'] = $inv_data->number;
            $update_data['number'] = make_next_invoice_num();
            $update_data['prefix'] = get_option('invoice_prefix');
            $update_data['type'] = 'invoice';
            $update_data['date'] = date('Y-m-d');

            $this->db->where('id', $id);
            $update = $this->db->update('tblinvoices', $update_data);

            if ($update) {


                //$this->create_zoho($id);
                //$inv = $this->createInvoice($id);

                echo 1;
                return;
            }

            echo 0;
            return;

        }
    }

    public function get_ajax_client_contacts()
    {

        if ($this->input->post()) {

            $selected = array();
            $contacts = $this->clients_model->get_contacts($this->input->post('client'));

            foreach ($contacts as $contact) {
                if (has_contact_permission('invoices', $contact['id'])) {
                    array_push($selected, $contact['id']);
                }
            }

            if (count($selected) == 0) {
                echo '<p class="text-danger">' . _l('sending_email_contact_permissions_warning', _l('customer_permission_invoice')) . '</p><hr />';
            }

            echo render_select('SaveSend[sent_to][]', $contacts, array('id', 'email', 'firstname,lastname'), 'invoice_estimate_sent_to_email', $selected, array('multiple' => true), array(), 'form', '', false);

        }

    }

    /* Get all invoices in case user go on index page */
    public function create_zoho($id)
    {
        $this->load->library('form_validation');
        $data['staff'] = $this->staff_model->get('', 1);

        if ($id) {
            $errors = [];
            $res_data = "";

            // find invoice for given date
            $where['id ='] = $id;
            $where['type'] = 'invoice';

            //$invoices = $this->invoices_model->get('', $where);
            $invoice = $this->invoices_model->get($id);

            // Initialize Zoho API Class
            $zb = new ZohoBooks();


            if (empty($invoice->zoho_id)) {

                // Step 1: get or create client/customer in CRM
                $client_id = "";
                $client = $this->clients_model->get($invoice->clientid);

                if ($client <> null) {
                    if (!empty($client->vat)) {
                        $invoice->vat_reg_no = $client->vat;
                        $invoice->vat_treatment = "vat_registered";
                    } else {
                        $invoice->vat_reg_no = $client->vat;
                        $invoice->vat_treatment = "vat_not_registered";
                    }

                    if (!empty($client->zoho_id)) {
                        $client_id = $invoice->clientid = $client->zoho_id;
                    } else {
                        $client_id = $client->userid;
                    }
                }

                $customer = json_decode($zb->getContact($client_id));

                if ($customer == null || empty($customer)) {

                    $contact = $this->createContactData(
                        $invoice->clientid,
                        $this->getInvoiceCurrencyCode((array)$invoice)
                    );

                    if (!empty($contact) && count($contact) > 0 && $contact <> null) {

                        $contact = $zb->postContact(json_encode($contact));
                        $result = json_decode($contact);

                        if ($result <> null) {
                            if ($result->code == 0) {
                                $contact_id_zoho = $result->contact->contact_id;
                                // $this->clients_model->updateZohoId($client_id, $contact_id_zoho);
                                update_zoho_id('tblclients', 'userid', $client_id, 'zoho_id', $contact_id_zoho);
                                $invoice->clientid = $contact_id_zoho;
                            } else {
                                $client = $this->clients_model->get($invoice->clientid);

                                if ($client <> null && !empty($client)) {
                                    $invoice->clientid = $client->zoho_id;
                                }
                            }
                        }
                    }
                }


                $invoice_data = $this->postZohoInvoice($zb, $this->invoiceJson((array)$invoice));

                if (!empty($invoice_data) && $invoice_data <> null) {

                    if (isset($invoice_data->code)) {

                        if ($invoice_data->code == 0) {
                            $this->assertZohoInvoiceCurrencyMatches((array)$invoice, $invoice_data);
                            //items table
                            $this->db->where('rel_id', $invoice['id']);
                            $this->db->update('tblitems_in', array('zoho_id' => $invoice_data->invoice->invoice_id));
                            //invoice table
                            $this->db->where('id', $invoice['id']);
                            $this->db->update('tblinvoices', array('zoho_id' => $invoice_data->invoice->invoice_id));
                        }

                        /*  $res_data .= "<div class='alert alert-danger'>";
                          $res_data .= " Code:" . $invoice_data->code;
                          $res_data .= " Message:" . $invoice_data->message;
                          $res_data .= " Invoice Number:" . strip_tags($invoice['prefix'] . $invoice['number']);
                          $res_data .= "</div>";

                          $errors[]['code'] = $invoice_data->code;
                          $errors[]['message'] = $invoice_data->message;
                          $errors[]['invoice_id'] = strip_tags($invoice['prefix'] . $invoice['number']);*/
                    }
                }
            }

            //  sleep(20);


            //   print_r($res_data);
            //return;
            // $this->load->view('admin/sync_invoices/create', $data);

        }

        //$this->load->view('admin/sync_invoices/create', $data);
    }

    /**
     * @param $invoice_data
     * @return array
     */
    protected function invoiceJson($invoice_data)
    {

        $items = $this->invoices_model->get_invoice_items($invoice_data['id']);



        $line_items = [];
        $i = 0;
        $per_item_discount =0;
        if ($items <> null && count($items) > 0) {
            /*if($invoice_data['vat_treatment'] != 'vat_registered') {
                $total_items = count($items);
                if ($invoice_data['discount_total'] > 0) {
                    $per_item_discount = $invoice_data['discount_total'] / $total_items;
                    $per_item_discount = number_format((float)$per_item_discount, 2, '.', '');
                }
            }*/
            $total_items = count($items);
            $discount_type_flag = 0;
            if ($invoice_data['discount_total'] > 0) {
                $per_item_discount = $invoice_data['discount_total'] / $total_items;
                $per_item_discount = number_format((float)$per_item_discount, 2, '.', '');
                $discount_type_flag = 1;
            }

            foreach ($items as $item) {

                if ($item['zoho_id'] == "") {
                    $item_id_zoho = $this->getZohoItemId($item);
                } else {
                    $item_id_zoho = $item['zoho_id'];
                }

                $line_items[$i] = [
                    "item_id" => $item_id_zoho,
                    "project_id" => "",
                    "name" => strip_tags($item['description']),
                    "description" => strip_tags($item['long_description']),
                    "item_order" => strip_tags($item['item_order']),
                    "bcy_rate" => strip_tags($item['rate']),
                    "rate" => strip_tags($item['rate']),
                    "quantity" => strip_tags($item['qty']),
                    "unit" => strip_tags($item['unit']),
                    "discount_amount" => $per_item_discount,
                    "discount" => $per_item_discount,
                /*"discount_amount" => strip_tags($item['discount']),
                    "discount" => strip_tags($item['discount'])*/
                ];

                // get Item Tax
                $item_taxes = get_invoice_item_taxes($item['id']);

                if (count($item_taxes) > 0) {
                    foreach ($item_taxes as $taxes) {

                        if (strpos($taxes['taxname'], 'VAT|5.00') !== false) {
                            $line_items[$i]['tax_id'] = get_option('zoho_vat_id');
                            $line_items[$i]['tax_name'] = "VAT";
                            $line_items[$i]['tax_type'] = "tax";
                            $line_items[$i]['tax_percentage'] = $taxes['taxrate'];

                        }
                    }
                }
                $i++;
            }
        }

        if (count($invoice_data) > 0) {

            $sales_agent_name = "";
            $staff = $this->staff_model->get($invoice_data['sale_agent']);

            if ($staff != "" && $staff <> null) {

                $sales_agent_name = "";

                if ($staff->firstname <> null) {
                    $sales_agent_name = $staff->firstname;
                }

                if ($staff->lastname <> null) {
                    $sales_agent_name .= " " . $staff->lastname;
                }
            }
            $discount_type = 'entity_level';
            if($invoice_data['vat_treatment'] == 'vat_registered'){
                $discount_type = 'item_level';
            }else if($discount_type_flag == 1){
                $discount_type = 'item_level';
            }
            if($invoice_data['discount_type'] != "before_tax"){
                $discount_type = 'entity_level';
            }
           // $discount_type = 'item_level';
            $place_of_supply = "";
            $tax_treatment = "";

            if(strtotime($invoice_data['date']) >= strtotime('2018-01-01')){
                $place_of_supply =  $invoice_data['place_of_supply'];
                $tax_treatment = $invoice_data['vat_treatment'];
            }

            $invoice_currency_code = $this->getInvoiceCurrencyCode($invoice_data);
            $zoho_currency = $this->getZohoCurrencyByCode($invoice_currency_code);


            $invoice = [

                "customer_id" => $invoice_data['clientid'],
                "_crm_invoice_number" => $this->getCrmInvoiceNumber($invoice_data),
                "reference_number" => $invoice_data['id'],
                "date" => $invoice_data['date'],
                "due_date" => $invoice_data['date'],
                /* "due_date" => $invoice_data['duedate'],*/
               // "discount" => $invoice_data['discount_total'],
                "discount" => $invoice_data['discount_total'],
                // "is_discount_before_tax" => ($invoice_data['discount_calculation'] == "before_tax") ? true : false,
                "is_discount_before_tax" => ($invoice_data['discount_type'] == "before_tax") ? true : false,
                "discount_type" => $discount_type,
                "is_inclusive_tax" => false,
                "salesperson_name" => $sales_agent_name,
                "project_id" => $invoice_data['project_id'],
                "custom_body" => " ",
                "custom_subject" => " ",
                "notes" => strip_tags($invoice_data['clientnote']),
                "terms" => strip_tags($invoice_data['terms']),
                "shipping_charge" => 0,
                "adjustment" => 0,
                "adjustment_description" => " ",
                "reason" => " ",
                "expense_id" => " ",
                "line_items" => $line_items

            ];

            if ($invoice_currency_code !== '') {
                $invoice['currency_code'] = $invoice_currency_code;
            }

            if (!empty($zoho_currency['currency_id'])) {
                $invoice['currency_id'] = $zoho_currency['currency_id'];
            }

            if (!empty($zoho_currency['exchange_rate'])) {
                $invoice['exchange_rate'] = $zoho_currency['exchange_rate'];
            }

            if ($this->shouldSendZohoVatFields() && $place_of_supply !== '' && $tax_treatment !== '') {
                $invoice['place_of_supply'] = $place_of_supply;
                $invoice['tax_treatment'] = $tax_treatment;
            }

            // Zoho Books rejects manual invoice_number when auto-numbering is enabled.
            // CRM invoice number is still sent as reference_number above.
        }

        return $invoice;

    }

    protected function postZohoInvoice(ZohoBooks $zb, $invoice)
    {
        if (!is_array($invoice)) {
            $invoice = (array)$invoice;
        }

        $crm_invoice_number = isset($invoice['_crm_invoice_number'])
            ? trim((string)$invoice['_crm_invoice_number'])
            : '';
        unset($invoice['_crm_invoice_number']);

        $invoice_data = json_decode($zb->postInvoice(json_encode($invoice)));

        if ($this->isZohoAutoNumberInvoiceError($invoice_data) && isset($invoice['invoice_number'])) {
            unset($invoice['invoice_number']);
            $invoice_data = json_decode($zb->postInvoice(json_encode($invoice)));
        }

        if ($this->isZohoInvoiceNumberRequiredError($invoice_data) && $crm_invoice_number !== '') {
            $invoice['invoice_number'] = $crm_invoice_number;
            $invoice_data = json_decode($zb->postInvoice(json_encode($invoice)));

            if ($this->isZohoAutoNumberInvoiceError($invoice_data)) {
                unset($invoice['invoice_number']);
                $invoice_data = json_decode($zb->postInvoice(json_encode($invoice)));
            }
        }

        return $invoice_data;
    }

    protected function getCrmInvoiceNumber($invoice_data)
    {
        $prefix = isset($invoice_data['prefix']) ? $invoice_data['prefix'] : '';
        $number = isset($invoice_data['number']) ? $invoice_data['number'] : '';

        return trim(strip_tags($prefix . $number));
    }

    protected function isZohoAutoNumberInvoiceError($invoice_data)
    {
        if (empty($invoice_data) || empty($invoice_data->message)) {
            return false;
        }

        $message = strtolower($invoice_data->message);

        return strpos($message, 'auto-generated number') !== false
            || strpos($message, 'auto generated number') !== false;
    }

    protected function isZohoInvoiceNumberRequiredError($invoice_data)
    {
        if (empty($invoice_data) || empty($invoice_data->message)) {
            return false;
        }

        $message = strtolower($invoice_data->message);

        return strpos($message, 'invoice number field is blank') !== false
            || strpos($message, 'enter a valid invoice number') !== false;
    }

    /**
     * @param $item
     * @return mixed
     */
    public function getZohoItemId($item)
    {
        if (is_array($item) && count($item) > 0) {

            $data = [
                "name" => strip_tags($item['id']) . "-" . $item['description'],
                "rate" => $item['rate'],
                "description" => strip_tags($item['long_description']),
                "sku" => strip_tags($item['id']),
            ];

            $zb = new ZohoBooks();
            $response = $zb->postItems(json_encode($data));
            $do_item = json_decode($response);

            if ($do_item !== null && isset($do_item->code)) {
                if ((int)$do_item->code === 0 && isset($do_item->item->item_id)) {

                    $item_id = $do_item->item->item_id;
                    //### update zoho Id
                    $this->db->where('id', $item['id']);
                    $this->db->update('tblitems_in', array('zoho_id' => $item_id));

                    return $item_id;
                }

                echo isset($do_item->message)
                    ? 'Unable to create Zoho item: ' . $do_item->message
                    : 'Unable to create Zoho item.';
                exit;
            }

            echo 'Invalid response received while creating Zoho item: ' . $response;
            exit;
        }

        echo 'Unable to prepare Zoho item data.';
        exit;
    }

    /**
     * @param $client_id
     * @return array
     */
    protected function createContactData($client_id, $currency_code = '')
    {

        $client = $this->clients_model->get($client_id);
        $contact = [];

        if ($client <> null && !empty($client)) {

            $client_contacts = $this->clients_model->get_contacts($client_id);

            $primary_first_name = "";
            $primary_last_name = "";
            $primary_email = "";
            $tax_treatment = "vat_not_registered";

            $contacts = [];

            if (count($client_contacts) > 0) {

                foreach ($client_contacts as $contact) {

                    if ($contact['is_primary']) {
                        $primary_first_name = $contact['firstname'];
                        $primary_last_name = $contact['lastname'];
                        $primary_email = $contact['email'];
                    }

                    $contacts[] = [

                        "salutation" => substr($contact["title"],20),
                        "first_name" => $contact['firstname'],
                        "last_name" => $contact['lastname'],
                        "email" => $contact['email'],
                        "phone" => $contact['phonenumber'],
                        "mobile" => $contact['email'],
                        "designation" => $contact["title"],
                        "department" => "",
                        "skype" => "",
                       /* "is_primary_contact" => ($contact['is_primary']) ? 'true' : 'false',*/
                        "enable_portal" => ($contact['is_primary']) ? true : false
                    ];
                }
            }

            if (!empty($client->vat)) {
                $tax_treatment = "vat_registered";
            }

            $currency_code = normalize_receipt_currency_code($currency_code);
            $contact = [

                "contact_name" => $client->company,
                "company_name" => $client->company,
                "first_name" => $primary_first_name,
                "last_name" => $primary_last_name,
                "email" => $primary_email,
                "phone" => $client->phonenumber,
                "facebook" => "",
                "twitter" => "",
                "billing_address" => [
                    "attention" => $client->company,
                    "address" => strip_tags($client->address),
                    "street2" => "",
                    "state_code" => "",
                    "city" => $client->city,
                    "state" => $client->state,
                    "zip" => $client->zip,
                    "country" => (get_country($client->country) <> null) ? get_country($client->country)->long_name : "",
                    "fax" => $client->phonenumber,
                    "phone" => $client->phonenumber
                ],
                "shipping_address" => [
                    "attention" => $client->company,
                    "address" => strip_tags($client->address),
                    "street2" => "",
                    "state_code" => "",
                    "city" => $client->city,
                    "state" => $client->state,
                    "zip" => $client->zip,
                    "country" => (get_country($client->country) <> null) ? get_country($client->country)->long_name : "",
                    "fax" => $client->phonenumber,
                    "phone" => $client->phonenumber
                ],
                "contact_persons" => $contacts
            ];

            if ($currency_code !== '') {
                $contact['currency_code'] = $currency_code;

                $zoho_currency = $this->getZohoCurrencyByCode($currency_code);
                if (!empty($zoho_currency['currency_id'])) {
                    $contact['currency_id'] = $zoho_currency['currency_id'];
                }
            }

            if ($this->shouldSendZohoVatFields()) {
                $contact['tax_reg_no'] = $client->vat;
                $contact['tax_treatment'] = $tax_treatment;
            }

        }

        return $contact;
    }

    protected function shouldSendZohoVatFields()
    {
        return (int)get_option('zoho_enable_vat_fields') === 1;
    }

    protected function getOrCreateZohoContactId($client_id, ZohoBooks $zb, $currency_code = '')
    {
        $client = $this->clients_model->get($client_id);
        $currency_code = normalize_receipt_currency_code($currency_code);

        if (empty($client)) {
            echo 'Client not found.';
            exit;
        }

        if (!empty($client->zoho_id)) {
            $contact = json_decode(
                $zb->getContact(trim($client->zoho_id))
            );

            if (
                !empty($contact)
                && isset($contact->code)
                && (int)$contact->code === 0
                && isset($contact->contact->contact_id)
            ) {
                $zoho_contact_currency_code = $this->getZohoContactCurrencyCode($contact->contact, $currency_code);

                if (
                    $currency_code !== ''
                    && $zoho_contact_currency_code !== ''
                    && $zoho_contact_currency_code !== $currency_code
                ) {
                    $zoho_contact_name = !empty($contact->contact->contact_name)
                        ? $contact->contact->contact_name
                        : $client->company;

                    echo 'Unable to post to Zoho: CRM customer "' . $client->company
                        . '" is set to ' . $currency_code
                        . ', but its mapped Zoho customer "' . $zoho_contact_name
                        . '" is ' . $zoho_contact_currency_code
                        . '. Update the CRM customer Zoho mapping to a ' . $currency_code
                        . ' Zoho customer, or change the Zoho customer currency before it has transactions. Delete the wrongly created Zoho invoice and repost.';
                    exit;
                }

                return $contact->contact->contact_id;
            }
        }

        $contactData = $this->createContactData($client->userid, $currency_code);

        if (empty($contactData)) {
            echo 'Unable to prepare Zoho contact data.';
            exit;
        }

        $contactResponse = $zb->postContact(
            json_encode($contactData)
        );

        $contactResult = json_decode($contactResponse);

        if (
            empty($contactResult)
            || !isset($contactResult->code)
            || (int)$contactResult->code !== 0
            || !isset($contactResult->contact->contact_id)
        ) {
            $message = isset($contactResult->message)
                ? $contactResult->message
                : 'Unable to create customer in Zoho.';

            echo $message;
            exit;
        }

        $zohoContactId = $contactResult->contact->contact_id;

        $zoho_contact_currency_code = $this->getZohoContactCurrencyCode($contactResult->contact, $currency_code);

        if (
            $currency_code !== ''
            && $zoho_contact_currency_code !== ''
            && $zoho_contact_currency_code !== $currency_code
        ) {
            echo 'Unable to post to Zoho: CRM customer "' . $client->company
                . '" is set to ' . $currency_code
                . ', but Zoho created/returned the customer in ' . $zoho_contact_currency_code
                . '. Change the Zoho customer currency to ' . $currency_code
                . ' before it has transactions, or enable Multi-Currency Transactions for Customers/Vendors in Zoho Books. Delete the wrongly created Zoho customer/invoice and repost.';
            exit;
        }

        update_zoho_id(
            'tblclients',
            'userid',
            $client->userid,
            'zoho_id',
            $zohoContactId
        );

        return $zohoContactId;
    }

    protected function getZohoContactCurrencyCode($contact, $expected_currency_code = '')
    {
        if (empty($contact)) {
            return '';
        }

        if (!empty($contact->currency_code)) {
            return normalize_receipt_currency_code($contact->currency_code);
        }

        if (!empty($contact->currency_id) && $expected_currency_code !== '') {
            $expected_zoho_currency = $this->getZohoCurrencyByCode($expected_currency_code);

            if (
                !empty($expected_zoho_currency['currency_id'])
                && trim((string)$contact->currency_id) === trim((string)$expected_zoho_currency['currency_id'])
            ) {
                return normalize_receipt_currency_code($expected_currency_code);
            }

            $actual_currency_code = $this->getZohoCurrencyCodeById($contact->currency_id);

            if ($actual_currency_code !== '') {
                return $actual_currency_code;
            }
        }

        return '';
    }

    protected function getZohoCurrencyCodeById($currency_id)
    {
        $currency_id = trim((string)$currency_id);

        if ($currency_id === '') {
            return '';
        }

        if ($this->zohoCurrenciesByCode === null) {
            $this->getZohoCurrencyByCode($this->getBaseCurrencyCode());
        }

        if (!is_array($this->zohoCurrenciesByCode)) {
            return '';
        }

        foreach ($this->zohoCurrenciesByCode as $currency_code => $currency) {
            if (
                !empty($currency['currency_id'])
                && trim((string)$currency['currency_id']) === $currency_id
            ) {
                return normalize_receipt_currency_code($currency_code);
            }
        }

        return '';
    }

    /* Get all reciepts in case user go on index page */
    public function createReceipt($receipt_id)
    {
        $data['staff'] = $this->staff_model->get('', 1);

        $errors = [];
        $res_data = "";


        $where['tblreciepts.receipt_id'] = $receipt_id;

        $receipts = $this->receipts_model->get_zoho('', $where);
        /*  echo $receipt_id;
          echo "<pre>";
          print_r($receipts);
          die;*/

        // Initialize Zoho API Class
        $zb = new ZohoBooks();

        if ($receipts <> null && count($receipts) > 0) {


            foreach ($receipts as $receipt) {

                if (empty($receipt['zoho_id'])) {

                    // Step 1: get or create client/customer in CRM
                    $client_id = "";
                    $client = $this->clients_model->get($receipt['receipt_client_id']);
                    if ($client <> null) {
                        if (!empty($client->zoho_id)) {
                            $client_id = $receipt['receipt_client_id'] = trim($client->zoho_id);
                        } else {
                            $client_id = $client->userid;
                        }
                    }

                    $customer = json_decode($zb->getContact($client_id));

                    if ($customer == null || empty($customer)) {
                        $contact = $this->createContactData($receipt['receipt_client_id']);

                        if (!empty($contact) && count($contact) > 0 && $contact <> null) {

                            $contact = $zb->postContact(json_encode($contact));


                            $result = json_decode($contact);


                            if ($result <> null) {
                                if ($result->code == '0') {
                                    $contact_id_zoho = $result->contact->contact_id;
                                    // $this->clients_model->updateZohoId($client_id, $contact_id_zoho);
                                    update_zoho_id('tblclients', 'userid', $client_id, 'zoho_id', $contact_id_zoho);
                                    $receipt['receipt_client_id'] = $contact_id_zoho;
                                } else {
                                    $client = $this->clients_model->get($receipt['receipt_client_id']);

                                    if ($client <> null && !empty($client)) {
                                        $receipt['receipt_client_id'] = $client->zoho_id;
                                    }
                                }
                            }
                        }
                    }
                    $client_contacts = $this->clients_model->get_contacts($client->userid);
                    $receipt['client_email'] = $client_contacts[0]['email'];

                    $receiptJson = $this->receiptJson($receipt);

                    $receiptJson = json_encode($this->receiptJson($receipt));
                    $receipt_data = json_decode($zb->postPayment($receiptJson));

                    if (!empty($receipt_data) && $receipt_data <> null) {

                        if (isset($receipt_data->code)) {

                            if ($receipt_data->code == '0') {
                                //recipts table
                                $this->db->where('receipt_id', $receipt['receipt_id']);
                                $this->db->update('tblreciepts', array('zoho_id' => $receipt_data->payment->payment_id));
                            }


                            $res_data .= "<div class='alert alert-danger'>";
                            $res_data .= " Code:" . $receipt_data->code;
                            $res_data .= " Message:" . $receipt_data->message;
                            $res_data .= " Receipt Number:" . strip_tags($receipt['receipt_num']);
                            $res_data .= "</div>";

                            $errors[]['code'] = $receipt_data->code;
                            $errors[]['message'] = $receipt_data->message;
                            $errors[]['receipt_id'] = strip_tags($receipt['receipt_num']);
                        }
                    }
                }
                /*$res_data .= "<div class='alert alert-danger'>";
                $res_data .= "Already exist";
                $res_data .= "</div>";*/

                sleep(15);
            }
        } else {
            $res_data .= "<div class='alert alert-danger'>";
            $res_data .= "No Receipt Found!";
            $res_data .= "</div>";
        }

        return;
    }
    
    public function createReceipt_zoho_ajax()
{
    $receipt_id = $this->input->post('receipt_id');

    if (empty($receipt_id)) {
        echo '0';
        exit;
    }

    $where = [
        'tblreciepts.receipt_id' => $receipt_id,
    ];

    $receipts = $this->receipts_model->get_zoho('', $where);

    if (empty($receipts)) {
        echo 'No Receipt Found!';
        exit;
    }

    $zb = new ZohoBooks();

    foreach ($receipts as $receipt) {

        if (!empty($receipt['zoho_id'])) {
            echo 'Receipt already exists in Zoho.';
            exit;
        }

        /*
         * Get the local client.
         */
        $client = $this->clients_model->get(
            $receipt['receipt_client_id']
        );

        if (empty($client)) {
            echo 'Client not found.';
            exit;
        }

        $receipt['local_client_id'] = $client->userid;

        $receiptInvoices = $this->receipts_model
            ->get_zoho_recipt_invoices($receipt['receipt_id']);

        if (empty($receiptInvoices)) {
            echo 'Unable to prepare payment data: no invoice payment allocations found for this receipt.';
            exit;
        }

        $transactionCurrencyCode = $this->getReceiptTransactionCurrencyCode(
            $receipt,
            $receiptInvoices
        );

        $receipt['receipt_client_id'] =
            $this->getOrCreateZohoContactId(
                $client->userid,
                $zb,
                $transactionCurrencyCode
            );

        /*
         * Get client email safely.
         */
        $clientContacts =
            $this->clients_model->get_contacts(
                $client->userid
            );

        $receipt['client_email'] = '';

        if (
            !empty($clientContacts)
            && isset($clientContacts[0]['email'])
        ) {
            $receipt['client_email'] =
                $clientContacts[0]['email'];
        }

        /*
         * Prepare the clean Zoho payment payload.
         */
        $receiptArray = $this->receiptJson($receipt);

        if (empty($receiptArray)) {
            echo 'Unable to prepare payment data.';
            exit;
        }

        $receiptJson = json_encode($receiptArray);

        if ($receiptJson === false) {
            echo 'JSON error: ' . json_last_error_msg();
            exit;
        }

        /*
         * Send payment to Zoho.
         */
        $response = $zb->postPayment($receiptJson);

        $receiptData = json_decode($response);

        /*
         * Temporary debugging.
         * Remove this block after the issue is resolved.
         */
        /*
        echo '<pre>';
        echo 'HTTP Code: ' . $zb->getHttpCode() . PHP_EOL;
        echo PHP_EOL . 'Request:' . PHP_EOL;
        echo json_encode(
            $receiptArray,
            JSON_PRETTY_PRINT
        );
        echo PHP_EOL . PHP_EOL . 'Response:' . PHP_EOL;
        echo $response;
        exit;
        */

        if (
            empty($receiptData)
            || !isset($receiptData->code)
        ) {
            echo 'Invalid response received from Zoho.';
            exit;
        }

        if ((int)$receiptData->code === 0) {

            if (
                !isset($receiptData->payment->payment_id)
            ) {
                echo 'Payment created, but payment ID was not returned.';
                exit;
            }

            $this->db->where(
                'receipt_id',
                $receipt['receipt_id']
            );

            $this->db->update(
                'tblreciepts',
                [
                    'zoho_id' =>
                        $receiptData->payment->payment_id,
                ]
            );

            echo '1';
            exit;
        }

        echo $receiptData->message;
        exit;
    }

    echo '0';
    exit;
}
protected function receiptJson($receipt_data)
{
    if (!empty($receipt_data['local_client_id']) && !empty($receipt_data['receipt_currency'])) {
        $this->assertCurrencyMatchesClientDefault(
            $receipt_data['local_client_id'],
            $receipt_data['receipt_currency'],
            'Receipt'
        );
    }

    $invoices =
        $this->receipts_model
            ->get_zoho_recipt_invoices(
                $receipt_data['receipt_id']
            );

	    if (empty($invoices)) {
	        echo 'Unable to prepare payment data: no invoice payment allocations found for this receipt.';
	        exit;
	    }

    $transaction_currency_code = $this->getReceiptTransactionCurrencyCode($receipt_data, $invoices);
    $base_currency_code = $this->getBaseCurrencyCode();

    /*
     * Default account.
     */
    $account_id = '1312911000000073107';

    $payment_mode = 'others';
    $description = '';
    $invoices_array = [];
    $refund = 0;

    /*
     * Determine payment mode and bank account.
     */
    if ($receipt_data['receipt_type'] === 'Cheque') {

        $payment_mode = 'check';
        $account_id = $this->getReceiptDepositZohoAccountId(
            $receipt_data,
            $transaction_currency_code,
            $base_currency_code
        );

    } elseif (
        $receipt_data['receipt_type'] === 'Cash'
    ) {

        $payment_mode = 'cash';

        if (
            isset($receipt_data['reciept_owner'])
            && (int)$receipt_data['reciept_owner'] === 21
        ) {
            // Dalbir
            $account_id = '1312911000000086053';
        } else {
            // Khuram Iqbal
            $account_id = '1312911000000073107';
        }

    } elseif (
        $receipt_data['receipt_type']
        === 'Bank Transfer'
    ) {

        $payment_mode = 'banktransfer';
        $account_id = $this->getReceiptDepositZohoAccountId(
            $receipt_data,
            $transaction_currency_code,
            $base_currency_code
        );

    } elseif (
        $receipt_data['receipt_type'] === 'Stripe'
    ) {

        $payment_mode = 'creditcard';
        $description = 'Stripe payment';

        // Stripe clearing account
        $account_id = '1312911000004871001';
    }

    /*
     * Prepare invoice allocations.
     */
    foreach ($invoices as $invoice) {

        $payment_old =
            $this->receipts_model
                ->get_invoice_previous_payment(
                    $invoice['invoiceid'],
                    $receipt_data['receipt_id']
                );

        if (!empty($invoice['zoho_id'])) {

            $invoice_id = trim($invoice['zoho_id']);

            if ($payment_old !== null) {
                $invoice['total'] =
                    (float)$invoice['total']
                    - (float)$payment_old;
            }

        } else {

            $invoice_id = $this->createInvoice(
                $invoice['invoiceid']
            );
        }

	        if (empty($invoice_id)) {
	            echo 'Unable to prepare payment data: invoice '
	                . strip_tags($invoice['prefix'] . $invoice['number'])
	                . ' was not created or found in Zoho.';
	            exit;
	        }

        $invoiceAmount = (float)$invoice['total'];
        $amountApplied =
            (float)$invoice['applied_amount'];

        /*
         * Prevent applying more than the invoice amount.
         */
        if ($amountApplied > $invoiceAmount) {
            $amountDueOver =
                $amountApplied - $invoiceAmount;

            $amountApplied =
                $amountApplied - $amountDueOver;

            $refund += $amountDueOver;
        }

        if ($amountApplied <= 0) {
            continue;
        }

        $this->assertZohoInvoiceCustomerMatchesReceipt(
            $invoice,
            $invoice_id,
            $receipt_data['receipt_client_id']
        );

        /*
         * Zoho only needs these two invoice fields.
         */
        $invoices_array[] = [
            'invoice_id' =>
                (string)$invoice_id,

            'amount_applied' =>
                round($amountApplied, 2),
        ];
    }

	    if (empty($invoices_array)) {
	        echo 'Unable to prepare payment data: no positive invoice amount is available to apply.';
	        exit;
	    }

    /*
     * Keep the original receipt date.
     */
    $paymentDate = date(
        'Y-m-d',
        strtotime($receipt_data['receipt_date'])
    );

    /*
     * Clean Zoho payment payload.
     */
    $zoho_currency = $this->getZohoCurrencyByCode($transaction_currency_code);

    $receipt = [
        'payment_mode' =>
            $payment_mode,

        'amount' =>
            round(
                (float)$receipt_data['receipt_amount'],
                2
            ),

        'date' =>
            $paymentDate,

        'reference_number' =>
            (string)$receipt_data['receipt_num'],

        'description' =>
            $description,

        'customer_id' =>
            (string)$receipt_data[
                'receipt_client_id'
            ],

        'invoices' =>
            $invoices_array,

        'exchange_rate' =>
            !empty($zoho_currency['exchange_rate']) ? $zoho_currency['exchange_rate'] : 1,

        'account_id' =>
            (string)$account_id,
    ];

    if ($transaction_currency_code !== '') {
        $receipt['currency_code'] = $transaction_currency_code;
    }

    return $receipt;
}

protected function getReceiptDepositZohoAccountId($receipt_data, $transaction_currency_code, $base_currency_code)
{
    $bank_code = isset($receipt_data['deposit_bank']) ? trim((string)$receipt_data['deposit_bank']) : '';
    $bank = get_receipt_deposit_bank($bank_code, true);

    if (!$bank) {
        echo 'Unable to post to Zoho: please select a deposit bank account for this receipt.';
        exit;
    }

    $bank_label = get_receipt_deposit_bank_label($bank);

    if (empty($bank['account_id'])) {
        echo 'Unable to post to Zoho: selected bank "' . $bank_label . '" is not linked with a Zoho account.';
        exit;
    }

    $bank_currency_code = !empty($bank['currency_code']) ? normalize_receipt_currency_code($bank['currency_code']) : '';

    if (
        $transaction_currency_code !== ''
        && $bank_currency_code !== ''
        && $transaction_currency_code !== $base_currency_code
        && $transaction_currency_code !== $bank_currency_code
    ) {
        echo 'Unable to post to Zoho: selected bank "' . $bank_label . '" is ' . $bank_currency_code
            . ', but this receipt/invoice is ' . $transaction_currency_code
            . '. Please select a ' . $transaction_currency_code . ' bank account or change the receipt currency.';
        exit;
    }

    $this->assertZohoBankAccountExists($bank, $bank['account_id']);

    return $bank['account_id'];
}

protected function assertZohoBankAccountExists($bank, $account_id)
{
    $account_id = trim((string)$account_id);

    if ($account_id === '') {
        return;
    }

    $zb = new ZohoBooks();
    $response = $zb->allAccounts();
    $result = json_decode($response, true);

    if (!is_array($result) || !isset($result['code'])) {
        return;
    }

    if ((int)$result['code'] !== 0) {
        return;
    }

    $accounts = isset($result['bankaccounts']) && is_array($result['bankaccounts'])
        ? $result['bankaccounts']
        : [];

    foreach ($accounts as $account) {
        if (isset($account['account_id']) && (string)$account['account_id'] === $account_id) {
            return;
        }
    }

    $bank_label = get_receipt_deposit_bank_label($bank);

    echo 'Unable to post to Zoho: selected bank "' . $bank_label
        . '" is linked to Zoho account ID ' . $account_id
        . ', but that account does not exist in the current Zoho organization. Sync Zoho bank accounts, edit this CRM bank, and select the current Zoho bank account.';
    exit;
}

protected function getReceiptTransactionCurrencyCode($receipt_data, $invoices)
{
    $invoice_currency_codes = [];
    $invoice_currency_labels = [];
    $receipt_currency_code = '';

    if (!empty($receipt_data['receipt_currency_code'])) {
        $receipt_currency_code = normalize_receipt_currency_code($receipt_data['receipt_currency_code']);
    } elseif (!empty($receipt_data['receipt_currency'])) {
        $receipt_currency_code = get_receipt_currency_code($receipt_data['receipt_currency']);
    }

    foreach ($invoices as $invoice) {
        if (!empty($invoice['currency_code'])) {
            $invoice_currency_code = normalize_receipt_currency_code($invoice['currency_code']);
            $invoice_currency_codes[$invoice_currency_code] = true;
            $invoice_currency_labels[] = trim($invoice['prefix'] . $invoice['number']) . ' (' . $invoice_currency_code . ')';
        }
    }

    $currency_codes = array_keys($invoice_currency_codes);

    if (count($currency_codes) > 1) {
        echo 'Unable to post to Zoho: receipt is allocated to invoices with multiple currencies: ' . implode(', ', $invoice_currency_labels) . '.';
        exit;
    }

    if (count($currency_codes) === 1) {
        if ($receipt_currency_code !== '' && $receipt_currency_code !== $currency_codes[0]) {
            echo 'Unable to post to Zoho: receipt currency is ' . $receipt_currency_code
                . ', but allocated invoice currency is ' . $currency_codes[0]
                . ' [' . implode(', ', $invoice_currency_labels) . ']. Please select invoices with ' . $receipt_currency_code . ' currency or change the receipt currency.';
            exit;
        }

        return $currency_codes[0];
    }

    if ($receipt_currency_code !== '') {
        return $receipt_currency_code;
    }

    return '';
}

protected function getBaseCurrencyCode()
{
    $base_currency = $this->currencies_model->get_base_currency();

    return $base_currency ? normalize_receipt_currency_code($base_currency->name) : '';
}

protected function getInvoiceCurrencyCode($invoice_data)
{
    if (!empty($invoice_data['currency_name'])) {
        return normalize_receipt_currency_code($invoice_data['currency_name']);
    }

    if (!empty($invoice_data['currency_code'])) {
        return normalize_receipt_currency_code($invoice_data['currency_code']);
    }

    if (!empty($invoice_data['currency'])) {
        return get_receipt_currency_code($invoice_data['currency']);
    }

    return '';
}

protected function getZohoCurrencyByCode($currency_code)
{
    $currency_code = normalize_receipt_currency_code($currency_code);

    if ($currency_code === '') {
        return [];
    }

    if ($this->zohoCurrenciesByCode === null) {
        $this->zohoCurrenciesByCode = [];

        $zb = new ZohoBooks();
        $response = $zb->getCurrencies();
        $result = $response ? json_decode($response) : null;

        if (!empty($result) && isset($result->currencies) && is_array($result->currencies)) {
            foreach ($result->currencies as $currency) {
                if (empty($currency->currency_code)) {
                    continue;
                }

                $normalized_currency_code = normalize_receipt_currency_code($currency->currency_code);
                $this->zohoCurrenciesByCode[$normalized_currency_code] = [
                    'currency_code' => $normalized_currency_code,
                    'currency_id' => isset($currency->currency_id) ? $currency->currency_id : '',
                    'exchange_rate' => isset($currency->exchange_rate) ? $currency->exchange_rate : '',
                ];
            }
        }
    }

    return isset($this->zohoCurrenciesByCode[$currency_code])
        ? $this->zohoCurrenciesByCode[$currency_code]
        : [];
}

protected function assertZohoInvoiceCurrencyMatches($invoice_data, $invoice_response)
{
    $expected_currency_code = $this->getInvoiceCurrencyCode($invoice_data);
    $posted_currency_code = '';

    if (!empty($invoice_response->invoice) && !empty($invoice_response->invoice->currency_code)) {
        $posted_currency_code = normalize_receipt_currency_code($invoice_response->invoice->currency_code);
    }

    if (
        $expected_currency_code !== ''
        && $posted_currency_code !== ''
        && $expected_currency_code !== $posted_currency_code
    ) {
        echo 'Unable to create Zoho invoice: CRM invoice currency is ' . $expected_currency_code
            . ', but Zoho created it in ' . $posted_currency_code
            . '. Enable Multi-Currency Transactions for Customers/Vendors in Zoho Books, delete the wrongly created Zoho invoice, and repost from CRM.';
        exit;
    }
}

protected function assertZohoInvoiceCustomerMatchesReceipt($invoice, $zoho_invoice_id, $zoho_contact_id)
{
    $zoho_invoice_id = trim((string)$zoho_invoice_id);
    $zoho_contact_id = trim((string)$zoho_contact_id);

    if ($zoho_invoice_id === '' || $zoho_contact_id === '') {
        return;
    }

    $zb = new ZohoBooks();
    $response = $zb->getInvoice($zoho_invoice_id);
    $result = $response ? json_decode($response) : null;

    if (
        empty($result)
        || !isset($result->code)
        || (int)$result->code !== 0
        || empty($result->invoice)
        || empty($result->invoice->customer_id)
    ) {
        return;
    }

    $invoice_customer_id = trim((string)$result->invoice->customer_id);

    if ($invoice_customer_id !== $zoho_contact_id) {
        echo 'Unable to post receipt to Zoho: invoice '
            . strip_tags($invoice['prefix'] . $invoice['number'])
            . ' belongs to a different Zoho customer. This usually happens when the invoice was previously posted under a duplicate currency customer. Delete/void that wrong Zoho invoice, clear this CRM invoice Zoho ID, and repost the invoice after enabling Zoho Multi-Currency Transactions for Customers/Vendors.';
        exit;
    }
}

    /* Get all reciepts in case user go on index page */
    public function createReceipt_zoho_ajax_27jul()
    {

        if ($this->input->post('receipt_id')) {
            $receipt_id = $this->input->post('receipt_id');
        }
        if ($receipt_id == '') {
            echo '0';
            exit();
        }
        $data['staff'] = $this->staff_model->get('', 1);

        $errors = [];
        $res_data = "";


        $where['tblreciepts.receipt_id'] = $receipt_id;

        $receipts = $this->receipts_model->get_zoho('', $where);

        // Initialize Zoho API Class
        $zb = new ZohoBooks();

        if ($receipts <> null && count($receipts) > 0) {


            foreach ($receipts as $receipt) {

                if (empty($receipt['zoho_id'])) {

                    // Step 1: get or create client/customer in CRM
                    $client_id = "";
                    $client = $this->clients_model->get($receipt['receipt_client_id']);
                    if ($client <> null) {
                        if (!empty($client->zoho_id)) {
                            $client_id = $receipt['receipt_client_id'] = trim($client->zoho_id);
                        } else {
                            $client_id = $client->userid;
                        }
                    }

                    $customer = json_decode($zb->getContact($client_id));

                    if ($customer == null || empty($customer)) {
                        $contact = $this->createContactData($receipt['receipt_client_id']);
                     /*   echo "<pre>";
                        print_r($contact);*/
                        if (!empty($contact) && count($contact) > 0 && $contact <> null) {

                            $contact = $zb->postContact(json_encode($contact));

                         /*   print_r($contact);
                            die;*/


                            $result = json_decode($contact);


                            if ($result <> null) {
                                if ($result->code == '0') {
                                    $contact_id_zoho = $result->contact->contact_id;
                                    // $this->clients_model->updateZohoId($client_id, $contact_id_zoho);
                                    update_zoho_id('tblclients', 'userid', $client_id, 'zoho_id', $contact_id_zoho);
                                    $receipt['receipt_client_id'] = $contact_id_zoho;
                                } else {
                                    $client = $this->clients_model->get($receipt['receipt_client_id']);

                                    if ($client <> null && !empty($client)) {
                                        $receipt['receipt_client_id'] = $client->zoho_id;
                                    }
                                }
                            }
                        }
                    }
                    $client_contacts = $this->clients_model->get_contacts($client->userid);
                    $receipt['client_email'] = $client_contacts[0]['email'];

                    $receiptJson1 = $this->receiptJson($receipt);
                   /* echo "<pre>";
                    print_r($receiptJson1);
                    //print_r($receipt_data);
                    die;*/


                    $receiptJson = json_encode($this->receiptJson($receipt));
                    $receipt_data = json_decode($zb->postPayment($receiptJson));
                   /*  echo "<pre>";
                   print_r($receiptJson);
                    print_r($receipt_data);
                    die;*/


                    if (!empty($receipt_data) && $receipt_data <> null) {

                        if (isset($receipt_data->code)) {

                            if ($receipt_data->code == '0') {
                                //recipts table
                                $this->db->where('receipt_id', $receipt['receipt_id']);
                                $this->db->update('tblreciepts', array('zoho_id' => $receipt_data->payment->payment_id));
                                echo "1";
                                exit();
                            } else {
                                echo $receipt_data->message;
                                exit();
                            }


                            $res_data .= "<div class='alert alert-danger'>";
                            $res_data .= " Code:" . $receipt_data->code;
                            $res_data .= " Message:" . $receipt_data->message;
                            $res_data .= " Receipt Number:" . strip_tags($receipt['receipt_num']);
                            $res_data .= "</div>";

                            $errors[]['code'] = $receipt_data->code;
                            $errors[]['message'] = $receipt_data->message;
                            $errors[]['receipt_id'] = strip_tags($receipt['receipt_num']);
                        }
                    }
                }
                /*$res_data .= "<div class='alert alert-danger'>";
                $res_data .= "Already exist";
                $res_data .= "</div>";*/

                sleep(15);
            }
        } else {
            echo "0";
            exit();
            $res_data .= "<div class='alert alert-danger'>";
            $res_data .= "No Receipt Found!";
            $res_data .= "</div>";
        }
        return;
    }

    /**
     * @param $reciept_data
     * @return array
     */
    protected function receiptJson_27_jul($receipt_data)
    {
        $invoices = $this->receipts_model->get_zoho_recipt_invoices($receipt_data['receipt_id']);
        //khuram iqbal
        $account_id = '1312911000000073107';

        $invoices_array = array();
        $refund = 0;
        $payment_mode = 'others';
        if ($receipt_data['receipt_type'] == 'Cheque') {
            $payment_mode = 'check';
            $account_id = get_receipt_deposit_bank_account_id(
                isset($receipt_data['deposit_bank']) ? $receipt_data['deposit_bank'] : '',
                $account_id
            );
        } else if ($receipt_data['receipt_type'] == 'Cash') {
            $payment_mode = 'cash';
            if ($receipt_data['reciept_owner'] == 21) {
                //dalbir
                $account_id = '1312911000000086053';
            } else {
                //khuram iqbal
                $account_id = '1312911000000073107';
            }
        } else if ($receipt_data['receipt_type'] == 'Bank Transfer') {
            $payment_mode = 'banktransfer';
            $account_id = get_receipt_deposit_bank_account_id(
                isset($receipt_data['deposit_bank']) ? $receipt_data['deposit_bank'] : '',
                $account_id
            );
        }else if ($receipt_data['receipt_type'] == 'Stripe') {
        // Stripe: Zoho does not support "stripe" in payment_mode
        // Use creditcard and DO NOT send account_id (Zoho auto maps to Stripe Clearing)
        $payment_mode = 'creditcard';
        $description  = 'Stripe payment';
      }

        foreach ($invoices as $key => $invoice) {
            $payment_old = $this->receipts_model->get_invoice_previous_payment($invoice['invoiceid'], $receipt_data['receipt_id']);

            if (!empty($invoice['zoho_id'])) {
                $invoice_id = $invoice['zoho_id'];
                if ($payment_old <> null) {

                    $invoice['total'] = $invoice['total'] - $payment_old;
                }

            } else {


                $invoice_id = $this->createInvoice($invoice['invoiceid']);

            }


            $total_amount = $this->receipts_model->getInvoicesTotal($invoice_id);
            if ($total_amount <> null) {
                $total = $total_amount->total;
            }

            $invoices_array[$key]['invoice_id'] = $invoice_id;
            $invoices_array[$key]['invoice_number'] = $invoice['prefix'] . $invoice['number'];
            $invoices_array[$key]['date'] = $invoice['date'];


                $invoices_array[$key]['invoice_amount'] = $invoice['total'];
                $invoices_array[$key]['amount_applied'] = $invoice['applied_amount'];

            /*        $invoices_array[$key]['invoice_amount'] = round($invoice['total']);
                    $invoices_array[$key]['amount_applied'] = round($invoice['applied_amount']);*/
            if ($invoices_array[$key]['amount_applied'] > $invoices_array[$key]['invoice_amount']) {
                //$amount_due_over = round($invoice['applied_amount']) - round($invoice['total']);
                $amount_due_over = $invoice['applied_amount'] - $invoices_array[$key]['invoice_amount'];
                $invoices_array[$key]['amount_applied'] = $invoices_array[$key]['amount_applied'] - $amount_due_over;
                $refund += $amount_due_over;
            }
        }


        if (count($receipt_data) > 0) {
            $receipt = [
                "payment_mode" => $payment_mode,
                "amount" => ($receipt_data['receipt_amount']),
                "amount_refunded" => $refund,
               // "date" => $receipt_data['receipt_date'],
               "date" => date('Y-m-d'),
                /*"date" => $receipt_data['paydate'],*/
                "status" => 'success',
                "reference_number" => $receipt_data['receipt_num'],
                "description" => "",
                "customer_id" => $receipt_data['receipt_client_id'],
                "customer_name" => $small = substr($receipt_data['client_name'], 0, 100),
                "invoices" => $invoices_array,
                "email" => $receipt_data['client_email'],
                "currency_code" => 'AED',
                "currency_symbol" => 'AED',
                "exchange_rate" => 1,
                "exchange_rate" => 1,
                "account_id" => $account_id
              
            ];
        }
         if ($receipt_data['receipt_type'] == 'Stripe') {
            $receipt["account_id"] = '1312911000004871001';
        }
          /* echo $refund;
           echo "<pre>";
           print_r($invoices);
           print_r($receipt_data);
           print_r($invoices_array);
           print_r($receipt);
           die;*/
        return $receipt;
    }

    /* create invoice if it id not exist */
    public function createInvoice($id)
    {
        $data['staff'] = $this->staff_model->get('', 1);

        $where['type'] = 'invoice';
        $where['tblinvoices.id'] = $id;

        $invoices = $this->invoices_model->get('', $where);

        // Initialize Zoho API Class
        $zb = new ZohoBooks();

        if ($invoices <> null && count($invoices) > 0) {


            foreach ($invoices as $invoice) {

                if (empty($invoice['zoho_id'])) {

                    // Step 1: get or create client/customer in CRM
                    $client_id = "";
                    $client = $this->clients_model->get($invoice['clientid']);

                    if ($client <> null) {
                        if (!empty($client->vat)) {
                            $invoice['vat_reg_no'] = $client->vat;
                            $invoice['vat_treatment'] = "vat_registered";
                        } else {
                            $invoice['vat_reg_no'] = $client->vat;
                            $invoice['vat_treatment'] = "vat_not_registered";
                        }


                        if ($client->city == "Dubai" || $client->city == "dubai") {
                            $invoice['place_of_supply'] = "DU";
                        } else if ($client->city == "Abu Dhabi" || $client->city == "abu dhabi") {
                            $invoice['place_of_supply'] = "AB";
                        } else if ($client->city == "Sharjah" || $client->city == "sharjah") {
                            $invoice['place_of_supply'] = "SH";
                        } else if ($client->city == "Ajman" || $client->city == "ajman") {
                            $invoice['place_of_supply'] = "AJ";
                        } else if ($client->city == "Fujairah" || $client->city == "fujairah") {
                            $invoice['place_of_supply'] = "FU";
                        } else if ($client->city == "Ras Al Khaimah" || $client->city == "ras al khaimah") {
                            $invoice['place_of_supply'] = "RA";
                        } else if ($client->city == "Umm Al Quwain" || $client->city == "umm al quwain") {
                            $invoice['place_of_supply'] = "UM";
                        } else {
                            $invoice['place_of_supply'] = "DU";
                        }
                        $this->assertCurrencyMatchesClientDefault(
                            $client->userid,
                            isset($invoice['currency']) ? $invoice['currency'] : '',
                            'Invoice'
                        );
                        $invoice['clientid'] = $this->getOrCreateZohoContactId(
                            $client->userid,
                            $zb,
                            $this->getInvoiceCurrencyCode($invoice)
                        );
                    }

                    $invoice_data = $this->postZohoInvoice($zb, $this->invoiceJson($invoice));
                   /* echo "<pre>";
                    print_r($this->invoiceJson($invoice));
                    print_r($invoice_data);
                    die;*/

                    if (!empty($invoice_data) && $invoice_data <> null) {

                        if (isset($invoice_data->code)) {

	                            if ($invoice_data->code == 0 || $invoice_data->code == '0') {
                                    $this->assertZohoInvoiceCurrencyMatches($invoice, $invoice_data);
                                    //items table
	                                $this->db->where('rel_id', $invoice['id']);
	                                $this->db->update('tblitems_in', array('zoho_id' => $invoice_data->invoice->invoice_id));
                                //invoice table
                                $this->db->where('id', $invoice['id']);
	                                $this->db->update('tblinvoices', array('zoho_id' => $invoice_data->invoice->invoice_id));
	                                return $invoice_data->invoice->invoice_id;
	                            } else {
                                    echo isset($invoice_data->message)
                                        ? 'Unable to create Zoho invoice: ' . $invoice_data->message
                                        : 'Unable to create Zoho invoice.';
                                    exit;
	                            }
	                        }
	                    }
                        echo 'Invalid response received while creating Zoho invoice.';
                        exit;
	                }
	            }
	        } else {

        }

        return;
    }

    /* create invoice if it id not exist */
    public function createInvoice_zoho_ajax()
    {

        if ($this->input->post('invoice_id')) {
            $id = $this->input->post('invoice_id');
        }
        if ($id == '') {
            echo '0';
            exit();
        }

        $data['staff'] = $this->staff_model->get('', 1);

        $where['type'] = 'invoice';
        $where['tblinvoices.id'] = $id;

        $invoices = $this->invoices_model->get('', $where);

        // Initialize Zoho API Class
        $zb = new ZohoBooks();

        if ($invoices <> null && count($invoices) > 0) {

            foreach ($invoices as $invoice) {

                if (empty($invoice['zoho_id'])) {

                    // Step 1: get or create client/customer in CRM
                    $client_id = "";
                    $client = $this->clients_model->get($invoice['clientid']);

                    if ($client <> null) {
                        if (!empty($client->vat)) {
                            $invoice['vat_reg_no'] = $client->vat;
                            $invoice['vat_treatment'] = "vat_registered";
                        } else {
                            $invoice['vat_reg_no'] = $client->vat;
                            $invoice['vat_treatment'] = "vat_not_registered";
                        }


                        if ($client->city == "Dubai" || $client->city == "dubai") {
                            $invoice['place_of_supply'] = "DU";
                        } else if ($client->city == "Abu Dhabi" || $client->city == "abu dhabi") {
                            $invoice['place_of_supply'] = "AB";
                        } else if ($client->city == "Sharjah" || $client->city == "sharjah") {
                            $invoice['place_of_supply'] = "SH";
                        } else if ($client->city == "Ajman" || $client->city == "ajman") {
                            $invoice['place_of_supply'] = "AJ";
                        } else if ($client->city == "Fujairah" || $client->city == "fujairah") {
                            $invoice['place_of_supply'] = "FU";
                        } else if ($client->city == "Ras Al Khaimah" || $client->city == "ras al khaimah") {
                            $invoice['place_of_supply'] = "RA";
                        } else if ($client->city == "Umm Al Quwain" || $client->city == "umm al quwain") {
                            $invoice['place_of_supply'] = "UM";
                        } else {
                            $invoice['place_of_supply'] = "DU";
                        }
                        $this->assertCurrencyMatchesClientDefault(
                            $client->userid,
                            isset($invoice['currency']) ? $invoice['currency'] : '',
                            'Invoice'
                        );
                        $invoice['clientid'] = $this->getOrCreateZohoContactId(
                            $client->userid,
                            $zb,
                            $this->getInvoiceCurrencyCode($invoice)
                        );
                    }

                    $invoice_data = $this->postZohoInvoice($zb, $this->invoiceJson($invoice));
                    if($invoice['id'] == 6919) {
                         // echo "<pre>";
                        //  print_r($this->invoiceJson($invoice));
                       //   print_r($invoice_data);
                      //    die;
                    }

                    if (!empty($invoice_data) && $invoice_data <> null) {

                        if (isset($invoice_data->code)) {

                            if ($invoice_data->code == '0') {
                                $this->assertZohoInvoiceCurrencyMatches($invoice, $invoice_data);
                                //items table
                                $this->db->where('rel_id', $invoice['id']);
                                $this->db->update('tblitems_in', array('zoho_id' => $invoice_data->invoice->invoice_id));
                                //invoice table
                                $this->db->where('id', $invoice['id']);
                                $this->db->update('tblinvoices', array('zoho_id' => $invoice_data->invoice->invoice_id));
                                $this->createInvoice_sent_zoho_ajax($invoice_data->invoice->invoice_id);
                                echo "1";
                                exit();
                            } else {
                                echo !empty($invoice_data->message) ? $invoice_data->message : "0";
                                exit();
                            }
                        }
                    }
                }
            }
        } else {

        }

        return;
    }
    public function createInvoice_sent_zoho_ajax($invoice_id){
        $zb = new ZohoBooks();
        $zb->postInvoice_status_sent($invoice_id);
    }


}
