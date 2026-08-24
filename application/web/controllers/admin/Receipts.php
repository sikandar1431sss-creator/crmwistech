<?php ob_start();
defined('BASEPATH') or exit('No direct script access allowed');

class Receipts extends Admin_controller
{
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
    }

    /**
     * @param string $status
     */
    /* Get all invoices in case user go on index page */
    public function index($status = '')
    {

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
                $where['receipt_status'] = $this->input->post('status');
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
            // pre_array($post);
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

                            $payment = $this->payments_model->process_payment($inv, '');

                            if ($payment && $changeToTax) {

                                if ($changeToTax) {

                                    $update_data['number'] = make_next_invoice_num();
                                    $update_data['prefix'] = get_option('invoice_prefix');
                                    $update_data['type'] = 'invoice';

                                    $this->db->where('id', $inv['invoiceid']);
                                    $this->db->update('tblinvoices', $update_data);

                                }
                            }
                        }
                    }
                }
            }
            // pre_array($receipt_id);
            // Get total advance remaining
            $balance = $this->get_clients_advance_cash($client);

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

        $advance = $this->get_clients_advance_cash($data['receipts']->receipt_client_id);
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
    public function AddCashAdnvance($receipt_id, $client, $advance = '', $withdraw = '', $remaining, $owner)
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

            $data = $this->invoices_model->get_all_Customer_invoices($client);
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

                        $total_payable = $item['total'] + $total_payable;
                        $total_due = $amount_due + $total_due;
                        $to_pay = get_invoice_total_left_to_pay($item['id'], $item['total']);

                        // }

                        $html .= '<tr><input name="invoice[' . $i . '][paymentmode]" type="hidden" value="1"/>';

                        $html .= '<td>';

                        if ($item['type'] == "performa") {

                            $html .= '<input name="invoice[' . $i . '][change_to_tax_invoice]" type="checkbox" value="1" />';
                        }
                        $html .= '</td>';

                        $html .= '<td><input name="invoice[' . $i . '][paymentmethod]" type="hidden" value="' . date("
                                             Y-m-d H:i:s") . '" />' . $item['date'] . '</td>';

                        if ($item['type'] == "invoice") {

                            $html .= '<td><input name="invoice[' . $i . '][invoiceid]" type="hidden"
                                             value="' . $item['id'] . '"/><a href="' . admin_url('/invoices/list_invoices#' . $item['id']) . '" target="_blank">' . format_invoice_number($item['id']) . '</a></td>';
                        } else {
                            $html .= '<td><input name="invoice[' . $i . '][invoiceid]" type="hidden"
                                             value="' . $item['id'] . '"/><strike><a style="color:#cc6600;" href="' . admin_url('/invoices/list_invoices#' . $item['id']) . '" target="_blank">' . format_invoice_number($item['id']) . '</a></strike></td>';
                        }

                        $html .= '<td><input ' . $disable . ' name="invoice[' . $i . '][total]" class="form-control" type="text"
                                             value="' . $item['total'] . '" style="width: 100px;"/></td>';
                        $html .= '<td><input ' . $disable . ' name="invoice[' . $i . '][amount_due]" class="form-control" type="text"
                                             value="' . $amount_due . '" style="width: 100px;"/></td>';
                        $html .= '<td><input ' . $disable . ' name="invoice[' . $i . '][discount]" class="form-control" type="text"
                                             value="0" min="0" max="' . $amount_due . '" style="width: 100px;"/></td>';
                        $html .= '<td><input ' . $disable . ' name="invoice[' . $i . '][amount]" class="payment_amount form-control"
                                             type="text" value="0" style="width: 100px;"/></td>';
                        $html .= '</tr>';

                        $i++;
                    }

                }

                $html_total .= '<tr>';
                $html_total .= '<td>&nbsp;</td>';
                $html_total .= '<td>&nbsp;</td>';
                $html_total .= '<td><h5>Total Payable: ' . $total_payable . '</h5></td>';
                $html_total .= '<td><h5>Total Due: ' . $total_due . '</h5></td>';
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

    /**
     * @param $client
     */
    public function get_clients_advance_cash($client)
    {
        echo $this->cashadvance_model->get_total_advance_amount($client);
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

        redirect(admin_url('receipts/details/' . $id));
    }

    public function convert_performa_invoices_to_tax_invoice()
    {

        if ($this->input->post()) {

            $id = $this->input->post('invoice_id');
            $update_data['number'] = make_next_invoice_num();
            $update_data['prefix'] = get_option('invoice_prefix');
            $update_data['type'] = 'invoice';

            $this->db->where('id', $id);
            $update = $this->db->update('tblinvoices', $update_data);

            if ($update) {
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

}