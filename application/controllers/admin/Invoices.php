    <?php

    defined('BASEPATH') or exit('No direct script access allowed');

    class Invoices extends Admin_controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->load->model('invoices_model');
            $this->load->model('credit_notes_model');
            $this->load->model('estimates_model');
        }

        /* Get all invoices in case user go on index page */
        public function index($id = '')
        {
            $this->list_invoices($id);
        }

        /* List all invoices datatables */
        public function list_invoices($id = '')
        {
            if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && get_option('allow_staff_view_invoices_assigned') == '0') {
                access_denied('invoices');
            }

            close_setup_menu();

            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', [], true);
            $data['invoiceid'] = $id;
            $data['title'] = _l('invoices');
            $data['invoices_years'] = $this->invoices_model->get_invoices_years();
            $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();
            $data['invoices_statuses'] = $this->invoices_model->get_statuses();
            $data['bodyclass'] = 'invoices-total-manual';
            $this->load->view('admin/invoices/manage', $data);
        }

        public function table($clientid = '')
        {

            if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && get_option('allow_staff_view_invoices_assigned') == '0') {
                ajax_access_denied();
            }
            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', [], true);

            $this->app->get_table_data('invoices', [
                'clientid' => $clientid,
                'data' => $data,
            ]);
        }

        public function client_change_data($customer_id, $current_invoice = '')
        {
            if ($this->input->is_ajax_request()) {
                $this->load->model('projects_model');
                $this->load->model('currencies_model');
                $data = [];
                $data['billing_shipping'] = $this->clients_model->get_customer_billing_and_shipping_details($customer_id);
                $client_currency = (int)$this->clients_model->get_customer_default_currency($customer_id);
                $currency_source = 'client';

                if ($client_currency <= 0) {
                    $base_currency = $this->currencies_model->get_base_currency();
                    $client_currency = $base_currency ? (int)$base_currency->id : 0;
                    $currency_source = 'base';
                }

                $currency = $client_currency > 0 ? $this->currencies_model->get($client_currency) : null;

                $data['client_currency'] = $client_currency;
                $data['client_currency_code'] = $currency && !empty($currency->name) ? get_receipt_currency_code($currency->id) : '';
                $data['client_currency_source'] = $currency_source;
                $data['client_currency_message'] = $currency
                    ? ($currency_source === 'client'
                        ? 'Currency is set from selected customer: ' . $currency->name . '.'
                        : 'Customer currency is not defined. Base currency ' . $currency->name . ' is used.')
                    : '';

                $data['customer_has_projects'] = customer_has_projects($customer_id);
                $data['billable_tasks'] = $this->tasks_model->get_billable_tasks($customer_id);

                if ($current_invoice != '') {
                    $this->db->select('status');
                    $this->db->where('id', $current_invoice);
                    $current_invoice_status = $this->db->get('tblinvoices')->row()->status;
                }

                $_data['invoices_to_merge'] = !isset($current_invoice_status) || (isset($current_invoice_status) && $current_invoice_status != 5) ? $this->invoices_model->check_for_merge_invoice($customer_id, $current_invoice) : [];

                $data['merge_info'] = $this->load->view('admin/invoices/merge_invoice', $_data, true);

                $__data['expenses_to_bill'] = !isset($current_invoice_status) || (isset($current_invoice_status) && $current_invoice_status != 5) ? $this->invoices_model->get_expenses_to_bill($customer_id) : [];

                $data['expenses_bill_info'] = $this->load->view('admin/invoices/bill_expenses', $__data, true);
                echo json_encode($data);
            }
        }

        public function update_number_settings($id)
        {
            $response = [
                'success' => false,
                'message' => '',
            ];
            if (has_permission('invoices', '', 'edit')) {
                $affected_rows = 0;

                $this->db->where('id', $id);
                $this->db->update('tblinvoices', [
                    'prefix' => $this->input->post('prefix'),
                ]);
                if ($this->db->affected_rows() > 0) {
                    $affected_rows++;
                }

                if ($affected_rows > 0) {
                    $response['success'] = true;
                    $response['message'] = _l('updated_successfully', _l('invoice'));
                }
            }
            echo json_encode($response);
            die;
        }

        public function validate_invoice_number()
        {
            $isedit = $this->input->post('isedit');
            $number = $this->input->post('number');
            $date = $this->input->post('date');
            $type = $this->input->post('type');
            $original_number = $this->input->post('original_number');
            $number = trim($number);
            $number = ltrim($number, '0');
            if ($isedit == 'true') {
                if ($number == $original_number) {
                    echo json_encode(true);
                    die;
                }
            }
            if (total_rows('tblinvoices', [
                    'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
                    'number' => $number,
                    'type' => $type,
                ]) > 0) {
                echo 'false';
            } else {
                echo 'true';
            }
        }

        public function add_note($rel_id)
        {
            if ($this->input->post() && user_can_view_invoice($rel_id)) {
                $this->misc_model->add_note($this->input->post(), 'invoice', $rel_id);
                echo $rel_id;
            }
        }

        public function get_notes($id)
        {
            if (user_can_view_invoice($id)) {
                $data['notes'] = $this->misc_model->get_notes($id, 'invoice');
                $this->load->view('admin/includes/sales_notes_template', $data);
            }
        }

        public function pause_overdue_reminders($id)
        {
            if (has_permission('invoices', '', 'edit')) {
                $this->db->where('id', $id);
                $this->db->update('tblinvoices', ['cancel_overdue_reminders' => 1]);
            }
            redirect(admin_url('invoices/list_invoices/' . $id));
        }

        public function resume_overdue_reminders($id)
        {
            if (has_permission('invoices', '', 'edit')) {
                $this->db->where('id', $id);
                $this->db->update('tblinvoices', ['cancel_overdue_reminders' => 0]);
            }
            redirect(admin_url('invoices/list_invoices/' . $id));
        }

        public function mark_as_cancelled($id)
        {
            if (!has_permission('invoices', '', 'edit') && !has_permission('invoices', '', 'create')) {
                access_denied('invoices');
            }

            $success = $this->invoices_model->mark_as_cancelled($id);

            if ($success) {
                set_alert('success', _l('invoice_marked_as_cancelled_successfully'));
            }

            redirect(admin_url('invoices/list_invoices/' . $id));
        }

        public function unmark_as_cancelled($id)
        {
            if (!has_permission('invoices', '', 'edit') && !has_permission('invoices', '', 'create')) {
                access_denied('invoices');
            }
            $success = $this->invoices_model->unmark_as_cancelled($id);
            if ($success) {
                set_alert('success', _l('invoice_unmarked_as_cancelled'));
            }
            redirect(admin_url('invoices/list_invoices/' . $id));
        }

        public function copy($id)
        {
            if (!$id) {
                redirect(admin_url('invoices'));
            }
            if (!has_permission('invoices', '', 'create')) {
                access_denied('invoices');
            }
            $new_id = $this->invoices_model->copy($id);
            if ($new_id) {
                set_alert('success', _l('invoice_copy_success'));
                redirect(admin_url('invoices/invoice/' . $new_id));
            } else {
                set_alert('success', _l('invoice_copy_fail'));
            }
            redirect(admin_url('invoices/invoice/' . $id));
        }

        public function get_merge_data($id)
        {
            $invoice = $this->invoices_model->get($id);
            $cf = get_custom_fields('items');

            $i = 0;

            foreach ($invoice->items as $item) {
                $invoice->items[$i]['taxname'] = get_invoice_item_taxes($item['id']);
                $invoice->items[$i]['long_description'] = clear_textarea_breaks($item['long_description']);
                $this->db->where('item_id', $item['id']);
                $rel = $this->db->get('tblitemsrelated')->result_array();
                $item_related_val = '';
                $rel_type = '';
                foreach ($rel as $item_related) {
                    $rel_type = $item_related['rel_type'];
                    $item_related_val .= $item_related['rel_id'] . ',';
                }
                if ($item_related_val != '') {
                    $item_related_val = substr($item_related_val, 0, -1);
                }
                $invoice->items[$i]['item_related_formatted_for_input'] = $item_related_val;
                $invoice->items[$i]['rel_type'] = $rel_type;

                $invoice->items[$i]['custom_fields'] = [];

                foreach ($cf as $custom_field) {
                    $custom_field['value'] = get_custom_field_value($item['id'], $custom_field['id'], 'items');
                    $invoice->items[$i]['custom_fields'][] = $custom_field;
                }
                $i++;
            }
            echo json_encode($invoice);
        }

        public function get_bill_expense_data($id)
        {
            $this->load->model('expenses_model');
            $expense = $this->expenses_model->get($id);

            $expense->qty = 1;
            $expense->long_description = clear_textarea_breaks($expense->description);
            $expense->description = $expense->name;
            $expense->rate = $expense->amount;
            if ($expense->tax != 0) {
                $expense->taxname = [];
                array_push($expense->taxname, $expense->tax_name . '|' . $expense->taxrate);
            }
            if ($expense->tax2 != 0) {
                array_push($expense->taxname, $expense->tax_name2 . '|' . $expense->taxrate2);
            }
            echo json_encode($expense);
        }

        /* Add new invoice or update existing */
        public function invoice($id = '')
        {
            if ($this->input->post()) {
                $invoice_data = $this->input->post();
                $this->enforceClientDefaultCurrency($invoice_data);
                $bank_currency_error = $this->validateInvoiceBankCurrencySelection($invoice_data);
                if ($bank_currency_error !== '') {
                    set_alert('danger', $bank_currency_error);
                    redirect($id == '' ? admin_url('invoices/invoice') : admin_url('invoices/invoice/' . $id));
                }
                if ($id == '') {
                    if (!has_permission('invoices', '', 'create')) {
                        access_denied('invoices');
                    }
                    $id = $this->invoices_model->add($invoice_data);
                    if ($id) {
                        set_alert('success', _l('added_successfully', _l('invoice')));
                        redirect(admin_url('invoices/list_invoices/' . $id . '#' . $id));
                    }
                } else {
                    if (!has_permission('invoices', '', 'edit')) {
                        access_denied('invoices');
                    }
                    $success = $this->invoices_model->update($invoice_data, $id);
                    if ($success) {
                        set_alert('success', _l('updated_successfully', _l('invoice')));
                    }
                    redirect(admin_url('invoices/list_invoices/' . $id . '#' . $id));
                }
            }
            if ($id == '') {
                $title = _l('create_new_invoice');
                $data['billable_tasks'] = [];
            } else {
                $invoice = $this->invoices_model->get($id);

                if (!$invoice || !user_can_view_invoice($id)) {
                    blank_page(_l('invoice_not_found'));
                }

                $data['invoices_to_merge'] = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $invoice->id);
                $data['expenses_to_bill'] = $this->invoices_model->get_expenses_to_bill($invoice->clientid);

                $data['invoice'] = $invoice;
                $data['edit'] = true;
                $data['billable_tasks'] = $this->tasks_model->get_billable_tasks($invoice->clientid, !empty($invoice->project_id) ? $invoice->project_id : '');

                $title = _l('edit', _l('invoice_lowercase')) . ' - ' . format_invoice_number($invoice->id);
            }

            if ($this->input->get('customer_id')) {
                $data['customer_id'] = $this->input->get('customer_id');
            }

            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', [
                'expenses_only !=' => 1,
            ]);

            $this->load->model('taxes_model');
            $data['taxes'] = $this->taxes_model->get();
            $this->load->model('invoice_items_model');

            $data['ajaxItems'] = false;
            if (total_rows('tblitems') <= ajax_on_total_items()) {
                $data['items'] = $this->invoice_items_model->get_grouped();
            } else {
                $data['items'] = [];
                $data['ajaxItems'] = true;
            }
            $data['items_groups'] = $this->invoice_items_model->get_groups();

            $this->load->model('currencies_model');
            $data['currencies'] = $this->currencies_model->get();

            $data['base_currency'] = $this->currencies_model->get_base_currency();

            $this->load->model('bankaccounts_model');
            $data['bank_accounts'] = $this->bankaccounts_model->get('', true);

            $data['staff'] = $this->staff_model->get('', ['active' => 1]);
            $data['title'] = $title;
            $data['bodyclass'] = 'invoice';
            $this->load->view('admin/invoices/invoice', $data);
        }

        private function enforceClientDefaultCurrency(&$invoice_data)
        {
            if (empty($invoice_data['clientid'])) {
                return;
            }

            $client_currency = (int)$this->clients_model->get_customer_default_currency($invoice_data['clientid']);

            if ($client_currency <= 0) {
                $this->load->model('currencies_model');
                $base_currency = $this->currencies_model->get_base_currency();
                $client_currency = $base_currency ? (int)$base_currency->id : 0;
            }

            if ($client_currency <= 0) {
                return;
            }

            if (!empty($invoice_data['currency']) && (int)$invoice_data['currency'] !== $client_currency) {
                set_alert('danger', 'Invoice currency must match the selected customer currency.');
            }

            $invoice_data['currency'] = $client_currency;
        }

        private function validateInvoiceBankCurrencySelection($invoice_data)
        {
            if (empty($invoice_data['bank_account_id']) || empty($invoice_data['currency'])) {
                return '';
            }

            $this->load->model('bankaccounts_model');
            $bank = $this->bankaccounts_model->get((int)$invoice_data['bank_account_id'], true);

            if (!$bank || empty($bank->currency_code)) {
                return '';
            }

            $invoice_currency_code = get_receipt_currency_code($invoice_data['currency']);
            $bank_currency_code = normalize_receipt_currency_code($bank->currency_code);

            if ($invoice_currency_code !== '' && $bank_currency_code !== '' && $invoice_currency_code !== $bank_currency_code) {
                return 'Selected bank currency is ' . $bank_currency_code
                    . ', but invoice/customer currency is ' . $invoice_currency_code
                    . '. Please select a matching bank account or change the customer currency.';
            }

            return '';
        }

        /* Get all invoice data used when user click on invoiec number in a datatable left side*/
        public function get_invoice_data_ajax($id)
        {

            if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && get_option('allow_staff_view_invoices_assigned') == '0') {
                echo _l('access_denied');
                die;
            }

            if (!$id) {
                die(_l('invoice_not_found'));
            }

            $invoice = $this->invoices_model->get($id);

            if (!$invoice) {
                echo _l('invoice_not_found');
                die;
            }

            if (!user_can_view_invoice($id)) {
                $clientid = $invoice->clientid;
                if(($clientid == 1617 || $clientid == 1098 || $clientid == 310 || $clientid == 1606 || $clientid == 111 || $clientid == 1525 || $clientid == 1295 || $clientid == 1355 || $clientid == 327) && get_staff_user_id() == 36){

                }else {
                    echo _l('invoice_not_found');
                    die;
                }
            }
            
            $invoice->total_left_to_pay = get_invoice_total_left_to_pay($id, $invoice->total);
            $invoice->date = _d($invoice->date);
            $invoice->duedate = !empty($invoice->duedate) ? _d($invoice->duedate) : '';

            $template_name = 'invoice-send-to-client';
            if ($invoice->sent == 1) {
                $template_name = 'invoice-already-send';
            }

            $template_name = do_action('after_invoice_sent_template_statement', $template_name);

            $contact = $this->clients_model->get_contact(get_primary_contact_user_id($invoice->clientid));
            $email = '';
            if ($contact) {
                $email = $contact->email;
            }

            $data['template'] = get_email_template_for_sending($template_name, $email);
            if (!$data['template']) {
                $data['template'] = new stdClass();
                $data['template']->message = '';
                $data['template']->subject = '';
            }

            $data['invoices_to_merge'] = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $id);
            if (!is_array($data['invoices_to_merge'])) {
                $data['invoices_to_merge'] = [];
            }

            $data['template_name'] = $template_name;
            $this->db->where('slug', $template_name);
            $this->db->where('language', 'english');
            $template_result = $this->db->get('tblemailtemplates')->row();

            $data['template_system_name'] = $template_result ? $template_result->name : '';
            $data['template_id'] = $template_result ? $template_result->emailtemplateid : '';

            $data['template_disabled'] = false;
            if (total_rows('tblemailtemplates', ['slug' => $data['template_name'], 'active' => 0]) > 0) {
                $data['template_disabled'] = true;
            }

            // Check for recorded payments
            $this->load->model('payments_model');
            $data['members'] = $this->staff_model->get('', ['active' => 1]);
            if (!is_array($data['members'])) {
                $data['members'] = [];
            }
            $data['payments'] = $this->payments_model->get_invoice_payments($id);
            if (!is_array($data['payments'])) {
                $data['payments'] = [];
            }
            $invoice->payments = $data['payments'];
            $data['activity'] = $this->invoices_model->get_invoice_activity($id);
            if (!is_array($data['activity'])) {
                $data['activity'] = [];
            }
            $data['totalNotes'] = total_rows('tblnotes', ['rel_id' => $id, 'rel_type' => 'invoice']);
            $data['invoice_recurring_invoices'] = $this->invoices_model->get_invoice_recurring_invoices($id);
            if (!is_array($data['invoice_recurring_invoices'])) {
                $data['invoice_recurring_invoices'] = [];
            }

            $data['applied_credits'] = $this->credit_notes_model->get_applied_invoice_credits($id);
            if (!is_array($data['applied_credits'])) {
                $data['applied_credits'] = [];
            }

            $data['credits_available'] = 0;
            $data['open_credits'] = [];

            // This data is used only when credit can be applied to invoice
            if (credits_can_be_applied_to_invoice($invoice->status)) {
                $data['credits_available'] = $this->credit_notes_model->total_remaining_credits_by_customer($invoice->clientid);
                if ($data['credits_available'] > 0) {
                    $data['open_credits'] = $this->credit_notes_model->get_open_credits($invoice->clientid);
                    if (!is_array($data['open_credits'])) {
                        $data['open_credits'] = [];
                    }
                }
            }

            $this->load->model('currencies_model');
            $data['customer_currency'] = $this->currencies_model->get($invoice->currency);
            if (!$data['customer_currency']) {
                $customer_currency_id = $this->clients_model->get_customer_default_currency($invoice->clientid);
                if ($customer_currency_id != 0) {
                    $data['customer_currency'] = $this->currencies_model->get($customer_currency_id);
                }
            }
            if (!$data['customer_currency']) {
                $data['customer_currency'] = $this->currencies_model->get_base_currency();
            }

            $data['invoice'] = $invoice;

            try {
                echo $this->load->view('admin/invoices/invoice_preview_template', $data, true);
            } catch (Throwable $e) {
                log_message(
                    'error',
                    'Invoice preview render failed for invoice #' . $id . ': ' . $e->getMessage()
                    . ' in ' . $e->getFile() . ':' . $e->getLine()
                );
                $this->output->set_status_header(200);
                echo $this->load->view('admin/invoices/invoice_preview_fallback', $data, true);
            }
            exit;
        }

        public function apply_credits($invoice_id)
        {
            $total_credits_applied = 0;
            foreach ($this->input->post('amount') as $credit_id => $amount) {
                $success = $this->credit_notes_model->apply_credits($credit_id, [
                    'invoice_id' => $invoice_id,
                    'amount' => $amount,
                ]);
                if ($success) {
                    $total_credits_applied++;
                }
            }

            if ($total_credits_applied > 0) {
                update_invoice_status($invoice_id, true);
                set_alert('success', _l('invoice_credits_applied'));
            }
            redirect(admin_url('invoices/list_invoices/' . $invoice_id));
        }

        public function get_invoices_total()
        {
            if ($this->input->post()) {
                load_invoices_total_template();
            }
        }

        /* Record new inoice payment view */
        public function record_invoice_payment_ajax($id)
        {
            $this->load->model('payment_modes_model');
            $this->load->model('payments_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', [
                'expenses_only !=' => 1,
            ]);
            $data['invoice'] = $invoice = $this->invoices_model->get($id);
            $data['payments'] = $this->payments_model->get_invoice_payments($id);
            $this->load->view('admin/invoices/record_payment_template', $data);
        }

        /* This is where invoice payment record $_POST data is send */
        public function record_payment()
        {
            if (!has_permission('payments', '', 'create')) {
                access_denied('Record Payment');
            }
            if ($this->input->post()) {
                $this->load->model('payments_model');
                $id = $this->payments_model->process_payment($this->input->post(), '');
                if ($id) {
                    set_alert('success', _l('invoice_payment_recorded'));
                    redirect(admin_url('payments/payment/' . $id));
                } else {
                    set_alert('danger', _l('invoice_payment_record_failed'));
                }
                redirect(admin_url('invoices/list_invoices/' . $this->input->post('invoiceid')));
            }
        }

        /* Send invoiece to email */
        public function send_to_email($id)
        {
            $canView = user_can_view_invoice($id);
            if (!$canView) {
                access_denied('Invoices');
            } else {
                if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && $canView == false) {
                    access_denied('Invoices');
                }
            }

            try {
                $success = $this->invoices_model->send_invoice_to_client($id, '', $this->input->post('attach_pdf'), $this->input->post('cc'),$this->input->post('subject'));
            } catch (Exception $e) {
                $message = $e->getMessage();
                echo $message;
                if (strpos($message, 'Unable to get the size of the image') !== false) {
                    show_pdf_unable_to_get_image_size_error();
                }
                die;
            }

            // In case client use another language
            load_admin_language();
            if ($success) {
                set_alert('success', _l('invoice_sent_to_client_success'));
            } else {
                set_alert('danger', _l('invoice_sent_to_client_fail'));
            }
            redirect(admin_url('invoices/list_invoices/' . $id));
        }

        /* Delete invoice payment*/
        public function delete_payment($id, $invoiceid)
        {
            if (!has_permission('payments', '', 'delete')) {
                access_denied('payments');
            }
            $this->load->model('payments_model');
            if (!$id) {
                redirect(admin_url('payments'));
            }
            $response = $this->payments_model->delete($id);
            if ($response == true) {
                set_alert('success', _l('deleted', _l('payment')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('payment_lowercase')));
            }
            redirect(admin_url('invoices/list_invoices/' . $invoiceid));
        }

        /* Delete invoice */
        public function delete($id)
        {
            if (!has_permission('invoices', '', 'delete')) {
                access_denied('invoices');
            }
            if (!$id) {
                redirect(admin_url('invoices/list_invoices'));
            }
            $success = $this->invoices_model->delete($id);

            if ($success) {
                set_alert('success', _l('deleted', _l('invoice')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('invoice_lowercase')));
            }
            if (strpos($_SERVER['HTTP_REFERER'], 'list_invoices') !== false) {
                redirect(admin_url('invoices/list_invoices'));
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        public function delete_attachment($id)
        {
            $file = $this->misc_model->get_file($id);
            if ($file->staffid == get_staff_user_id() || is_admin()) {
                echo $this->invoices_model->delete_attachment($id);
            } else {
                header('HTTP/1.0 400 Bad error');
                echo _l('access_denied');
                die;
            }
        }

        /* Will send overdue notice to client */
        public function send_overdue_notice($id)
        {
            $canView = user_can_view_invoice($id);
            if (!$canView) {
                access_denied('Invoices');
            } else {
                if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && $canView == false) {
                    access_denied('Invoices');
                }
            }

            $send = $this->invoices_model->send_invoice_overdue_notice($id);
            if ($send) {
                set_alert('success', _l('invoice_overdue_reminder_sent'));
            } else {
                set_alert('warning', _l('invoice_reminder_send_problem'));
            }
            redirect(admin_url('invoices/list_invoices/' . $id));
        }

        /* Generates invoice PDF and senting to email of $send_to_email = true is passed */
        public function pdf($id)
        {
            if (!$id) {
                redirect(admin_url('invoices/list_invoices'));
            }

            $canView = user_can_view_invoice($id);
            if (!$canView) {
                access_denied('Invoices');
            } else {
                if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && $canView == false) {
                    access_denied('Invoices');
                }
            }

            $invoice = $this->invoices_model->get($id);
            $invoice = do_action('before_admin_view_invoice_pdf', $invoice);
            $invoice_number = format_invoice_number($invoice->id);

            try {
                $pdf = invoice_pdf($invoice);
            } catch (Exception $e) {
                $message = $e->getMessage();
                echo $message;
                if (strpos($message, 'Unable to get the size of the image') !== false) {
                    show_pdf_unable_to_get_image_size_error();
                }
                die;
            }

            $type = 'D';

            if ($this->input->get('output_type')) {
                $type = $this->input->get('output_type');
            }

            if ($this->input->get('print')) {
                $type = 'I';
            }

            $pdf->Output(mb_strtoupper(slug_it($invoice_number)) . '.pdf', $type);
        }

        public function mark_as_sent($id)
        {
            if (!$id) {
                redirect(admin_url('invoices/list_invoices'));
            }
            if (!user_can_view_invoice($id)) {
                access_denied('Invoice Mark As Sent');
            }
            $success = $this->invoices_model->set_invoice_sent($id, true);
            if ($success) {
                set_alert('success', _l('invoice_marked_as_sent'));
            } else {
                set_alert('warning', _l('invoice_marked_as_sent_failed'));
            }
            redirect(admin_url('invoices/list_invoices/' . $id));
        }

        public function get_due_date()
        {
            if ($this->input->post()) {
                $date = $this->input->post('date');
                $duedate = '';
                if (get_option('invoice_due_after') != 0) {
                    $date = $date;
                    $d = date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime($date)));
                    $duedate = _d($d);
                    echo $duedate;
                }
            }
        }

        public function import_performa()
        {
            $invoice_data = [];
            $performa = $this->estimates_model->get('', ['invoiceid' => null]);

            if (count($performa) > 0) {
                foreach ($performa as $pInvoices) {


                    $invoice_data['sent'] = $pInvoices['sent'];
                    $invoice_data['datesend'] = $pInvoices['datesend'];
                    $invoice_data['clientid'] = $pInvoices['clientid'];
                    $invoice_data['project_id'] = $pInvoices['project_id'];
                    $invoice_data['number'] = $pInvoices['number'];
                    $invoice_data['prefix'] = $pInvoices['prefix'];
                    $invoice_data['number_format'] = $pInvoices['number_format'];
                    $invoice_data['datecreated'] = $pInvoices['datecreated'];
                    $invoice_data['date'] = $pInvoices ['date'];
                    $invoice_data['currency'] = $pInvoices ['currency'];
                    $invoice_data['subtotal'] = $pInvoices ['subtotal'];
                    $invoice_data['total_tax'] = $pInvoices  ['total_tax'];
                    $invoice_data['total'] = $pInvoices ['total'];
                    $invoice_data['adjustment'] = $pInvoices   ['adjustment'];
                    $invoice_data['addedfrom'] = $pInvoices ['addedfrom'];
                    $invoice_data['adjustment_reason'] = $pInvoices  ['adjustment_reason'];
                    $invoice_data['hash'] = $pInvoices  ['hash'];
                    $invoice_data['status'] = $pInvoices  ['status'];
                    $invoice_data['clientnote'] = $pInvoices ['clientnote'];
                    $invoice_data['adminnote'] = $pInvoices ['adminnote'];
                    $invoice_data['discount_percent'] = $pInvoices ['discount_percent'];
                    $invoice_data['discount_total'] = $pInvoices ['discount_total'];
                    $invoice_data['discount_reason'] = $pInvoices ['discount_reason'];
                    $invoice_data['discount_type'] = $pInvoices ['discount_type'];
                    $invoice_data['discount_calculation'] = $pInvoices ['discount_calculation'];
                    $invoice_data['terms'] = $pInvoices ['terms'];
                    $invoice_data['sale_agent'] = $pInvoices ['sale_agent'];
                    $invoice_data['billing_street'] = $pInvoices ['billing_street'];
                    $invoice_data['billing_city'] = $pInvoices  ['billing_city'];
                    $invoice_data['billing_state'] = $pInvoices ['billing_state'];
                    $invoice_data['billing_zip'] = $pInvoices ['billing_zip'];
                    $invoice_data['billing_country'] = $pInvoices ['billing_country'];
                    $invoice_data['shipping_street'] = $pInvoices['shipping_street'];
                    $invoice_data['shipping_city'] = $pInvoices ['shipping_city'];
                    $invoice_data['shipping_state'] = $pInvoices ['shipping_state'];
                    $invoice_data['shipping_zip'] = $pInvoices ['shipping_zip'];
                    $invoice_data['shipping_country'] = $pInvoices ['shipping_country'];
                    $invoice_data['include_shipping'] = $pInvoices ['include_shipping'];
                    $invoice_data['show_shipping_on_invoice'] = $pInvoices ['show_shipping_on_estimate'];
                    $invoice_data['show_quantity_as'] = $pInvoices ['show_quantity_as'];
                    $invoice_data['type'] = "performa";

                    $this->db->insert('tblinvoices', $invoice_data);
                    $insert_id = $this->db->insert_id();

                    if ($insert_id) {

                        $items = get_items_by_type('estimate', $pInvoices['id']);

                        if (count($items) > 0) {

                            foreach ($items as $item) {

                                $this->db->where('id', $item['id']);
                                $update = $this->db->update('tblitems_in', ['rel_id' => $insert_id, 'rel_type' => 'invoice']);

                                if ($update) {

                                    echo '<p style="color: green">Update => ' . $insert_id . '</p>';
                                } else {
                                    print_r($this->db->error());
                                }
                            }

                        }

                        echo '<p style="color: green">Inserted => ' . $insert_id . '</p>';
                    } else {
                        print_r($this->db->error());
                    }
                }
            }
        }


    /**
         * 
         */
        public function list_prvious_tax(){

            $this->db->select('*');
            $this->db->from('tblitemstaxPrevious');
            $result = $this->db->get()->result();

            foreach ($result as $r){

                $this->db->select('*');
                $this->db->from('tblitemstax');
                $this->db->where(array('itemid' => $r->itemid, 'rel_id' => $r->rel_id, 'rel_type' => $r->rel_type));
                $isAvailable = $this->db->get()->row();

                if(count($isAvailable) < 1) {

                    $data = array(
                        'itemid'    => $r->itemid,
                        'rel_id'    => $r->rel_id,
                        'rel_type'    => $r->rel_type,
                        'taxrate'    => $r->taxrate,
                        'taxname'    => $r->taxname,
                    );


                    //$this->db->insert('tblitemstax', $data);
                    //$insert_id = $this->db->insert_id();

                    //echo '<pre>';
                    //print_r($insert_id);
                    //echo '</pre>';

                }

            }
            die;
        }
    }
