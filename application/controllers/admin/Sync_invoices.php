<?php ob_start();
defined('BASEPATH') or exit('No direct script access allowed');

class Sync_invoices extends Admin_controller
{
    /**
     * Sync_invoices constructor.
     */
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


    /* Get all invoices in case user go on index page */
    public function index()
    {
        $where = [];

        if ($this->input->post()) {
            if ($this->input->post('noteType')) {
                $where['cn_type'] = $this->input->post('noteType');
            }

            if ($this->input->post('notePosition')) {
                $where['cn_position'] = $this->input->post('notePosition');
            }
        }

        $data['cus_notes'] = $this->customnotes_model->get('', $where);
        $this->load->view('admin/custom_notes/list_notes', $data);
    }

    /* Get all invoices in case user go on index page */
    public function create()
    {
        $this->load->library('form_validation');
        $data['staff'] = $this->staff_model->get('', 1);

        if ($this->input->post()) {
            $errors = [];
            $res_data = "";

            $this->form_validation->set_rules('from_date', 'From Date', 'required|trim');
            $this->form_validation->set_rules('to_date', 'To Date', 'required');

            if ($this->form_validation->run() == FALSE) {

                // $this->load->view('admin/custom_notes/create', $data);
                $errors = "<div class='alert alert-danger'>" . validation_errors() . "</div>";

                //$errors['response'] = validation_errors();
                print_r($errors);
                //echo json_encode($errors);
                return;

            } elseif ($this->input->post('from_date') > $this->input->post('to_date')) {
                $errors = "<div class='alert alert-danger'>Start date should be less than end date</div>";
                print_r(($errors));
                // echo json_encode($errors);
                return;
            } else {

                // find invoice for given dates
                $where['date >='] = date("Y-m-d", strtotime($this->input->post('from_date')));
                $where['date <='] = date("Y-m-d", strtotime($this->input->post('to_date')));
                $where['type'] = 'invoice';

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
                                $invoice['vat_treatment'] = get_client_tax_treatment($client);
                                $invoice['place_of_supply'] = get_client_place_of_supply($client);
                                $invoice['vat_reg_no'] = !empty($client->vat) ? $client->vat : '';
                                if (!empty($client->zoho_id)) {
                                    $client_id = $invoice['clientid'] = $client->zoho_id;
                                } else {
                                    $client_id = $client->userid;
                                }
                            }

                            $customer = json_decode($zb->getContact($client_id));

                            if ($customer == null || empty($customer)) {

                                $contact = $this->createContactData($invoice['clientid']);

                                if (!empty($contact) && count($contact) > 0 && $contact <> null) {

                                    $result = $this->postZohoContact($zb, $contact);

                                    if ($result <> null) {
                                        if ($result->code == 0) {
                                            $contact_id_zoho = $result->contact->contact_id;
                                            // $this->clients_model->updateZohoId($client_id, $contact_id_zoho);
                                            update_zoho_id('tblclients', 'userid', $client_id, 'zoho_id', $contact_id_zoho);
                                            $invoice['clientid'] = $contact_id_zoho;
                                        } else {
                                            $client = $this->clients_model->get($invoice['clientid']);

                                            if ($client <> null && !empty($client)) {
                                                $invoice['clientid'] = $client->zoho_id;
                                            }
                                        }
                                    }
                                }
                            }

                            $invoice_data = $this->postZohoInvoice($zb, $this->invoiceJson($invoice));

                            if (!empty($invoice_data) && $invoice_data <> null) {

                                if (isset($invoice_data->code)) {

                                    if ($invoice_data->code == 0) {
                                        //items table
                                        $this->db->where('rel_id', $invoice['id']);
                                        $this->db->update('tblitems_in', array('zoho_id' => $invoice_data->invoice->invoice_id));
                                        //invoice table
                                        $this->db->where('id', $invoice['id']);
                                        $this->db->update('tblinvoices', array('zoho_id' => $invoice_data->invoice->invoice_id));
                                    }

                                    $res_data .= "<div class='alert alert-danger'>";
                                    $res_data .= " Code:" . $invoice_data->code;
                                    $res_data .= " Message:" . $invoice_data->message;
                                    $res_data .= " Invoice Number:" . strip_tags($invoice['prefix'] . $invoice['number']);
                                    $res_data .= "</div>";

                                    $errors[]['code'] = $invoice_data->code;
                                    $errors[]['message'] = $invoice_data->message;
                                    $errors[]['invoice_id'] = strip_tags($invoice['prefix'] . $invoice['number']);
                                }
                            }
                        }

                        sleep(20);
                    }
                } else {
                    $res_data .= "<div class='alert alert-danger'>";
                    $res_data .= "No Invoice Found!";
                    $res_data .= "</div>";

                }
                print_r($res_data);
                return;
            }
        }

        $this->load->view('admin/sync_invoices/create', $data);
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
                                $invoice['vat_treatment'] = get_client_tax_treatment($client);
                                $invoice['place_of_supply'] = get_client_place_of_supply($client);
                                $invoice['vat_reg_no'] = !empty($client->vat) ? $client->vat : '';
                                if (!empty($client->zoho_id)) {
                                    $client_id = $invoice['clientid'] = $client->zoho_id;
                                } else {
                                    $client_id = $client->userid;
                                }
                            }

                            $customer = json_decode($zb->getContact($client_id));

                            if ($customer == null || empty($customer)) {

                                $contact = $this->createContactData($invoice['clientid']);

                                if (!empty($contact) && count($contact) > 0 && $contact <> null) {

                                    $result = $this->postZohoContact($zb, $contact);

                                    if ($result <> null) {
                                        if ($result->code == 0) {
                                            $contact_id_zoho = $result->contact->contact_id;
                                            // $this->clients_model->updateZohoId($client_id, $contact_id_zoho);
                                            update_zoho_id('tblclients', 'userid', $client_id, 'zoho_id', $contact_id_zoho);
                                            $invoice['clientid'] = $contact_id_zoho;
                                        } else {
                                            $client = $this->clients_model->get($invoice['clientid']);

                                            if ($client <> null && !empty($client)) {
                                                $invoice['clientid'] = $client->zoho_id;
                                            }
                                        }
                                    }
                                }
                            }

                            $invoice_data = $this->postZohoInvoice($zb, $this->invoiceJson($invoice));

                            if (!empty($invoice_data) && $invoice_data <> null) {

                                if (isset($invoice_data->code)) {

                                    if ($invoice_data->code == '0') {
                                        //items table
                                        $this->db->where('rel_id', $invoice['id']);
                                        $this->db->update('tblitems_in', array('zoho_id' => $invoice_data->invoice->invoice_id));
                                        //invoice table
                                        $this->db->where('id', $invoice['id']);
                                        $this->db->update('tblinvoices', array('zoho_id' => $invoice_data->invoice->invoice_id));
                                        return $invoice_data->invoice->invoice_id;
                                    } else {
                                    }
                                }
                            }
                        }
                    }
                } else {

                }

                return;

        $this->load->view('admin/sync_invoices/create', $data);
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
        if ($items <> null && count($items) > 0) {

            foreach ($items as $item) {

                $item_id_zoho = "";
                if (!empty($item['zoho_id'])) {
                    $item_id_zoho = $item['zoho_id'];
                } else {
                    $item_id_zoho = $this->getZohoItemId($item);
                }

                $line_item = [
                    "project_id" => "",
                    "name" => strip_tags($item['description']),
                    "description" => strip_tags($item['long_description']),
                    "item_order" => strip_tags($item['item_order']),
                    "bcy_rate" => strip_tags($item['rate']),
                    "rate" => strip_tags($item['rate']),
                    "quantity" => strip_tags($item['qty']),
                    "unit" => strip_tags($item['unit']),
                    "discount_amount" => strip_tags($item['discount']),
                    "discount" => strip_tags($item['discount'])
                ];

                if (!empty($item_id_zoho)) {
                    $line_item["item_id"] = $item_id_zoho;
                }

                $line_items[$i] = $line_item;

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

            $place_of_supply = isset($invoice_data['place_of_supply']) ? $invoice_data['place_of_supply'] : '';
            $tax_treatment = isset($invoice_data['vat_treatment']) ? $invoice_data['vat_treatment'] : '';

            $invoice = [
                "customer_id" => $invoice_data['clientid'],
                "_crm_invoice_number" => $this->getCrmInvoiceNumber($invoice_data),
                "_cached_place_of_supply" => $place_of_supply,
                "_cached_tax_treatment" => $tax_treatment,
                "reference_number" => $invoice_data['id'],
                "date" => $invoice_data['date'],
                "due_date" => $invoice_data['date'],
                "discount" => $invoice_data['discount_total'],
                "is_discount_before_tax" => ($invoice_data['discount_type'] == "before_tax") ? true : false,
                "discount_type" => "entity_level",
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

            if ($place_of_supply !== '') {
                $invoice['place_of_supply'] = $place_of_supply;
            }

            if ($tax_treatment !== '') {
                $invoice['tax_treatment'] = $tax_treatment;
            }

            if (!empty($invoice_data['vat_reg_no'])) {
                $invoice['tax_reg_no'] = $invoice_data['vat_reg_no'];
            }
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

        $cached_place_of_supply = isset($invoice['_cached_place_of_supply'])
            ? $invoice['_cached_place_of_supply']
            : (isset($invoice['place_of_supply']) ? $invoice['place_of_supply'] : '');
        $cached_tax_treatment = isset($invoice['_cached_tax_treatment'])
            ? $invoice['_cached_tax_treatment']
            : (isset($invoice['tax_treatment']) ? $invoice['tax_treatment'] : '');
        unset($invoice['_cached_place_of_supply']);
        unset($invoice['_cached_tax_treatment']);

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

        // Handle Invalid Element place_of_supply / tax_treatment / tax_reg_no
        if ($this->isZohoPlaceOfSupplyInvalidError($invoice_data) && (isset($invoice['place_of_supply']) || isset($invoice['tax_treatment']) || isset($invoice['tax_reg_no']))) {
            unset($invoice['place_of_supply']);
            unset($invoice['tax_treatment']);
            unset($invoice['tax_reg_no']);
            $invoice_data = json_decode($zb->postInvoice(json_encode($invoice)));

            if ($this->isZohoAutoNumberInvoiceError($invoice_data) && isset($invoice['invoice_number'])) {
                unset($invoice['invoice_number']);
                $invoice_data = json_decode($zb->postInvoice(json_encode($invoice)));
            }
        }

        // Handle Missing / Required place_of_supply error
        if ($this->isZohoPlaceOfSupplyMissingError($invoice_data) && empty($invoice['place_of_supply'])) {
            $invoice['place_of_supply'] = $cached_place_of_supply !== '' ? $cached_place_of_supply : 'DU';
            if ($cached_tax_treatment !== '') {
                $invoice['tax_treatment'] = $cached_tax_treatment;
            } else {
                $invoice['tax_treatment'] = 'vat_not_registered';
            }
            $invoice_data = json_decode($zb->postInvoice(json_encode($invoice)));

            if ($this->isZohoAutoNumberInvoiceError($invoice_data) && isset($invoice['invoice_number'])) {
                unset($invoice['invoice_number']);
                $invoice_data = json_decode($zb->postInvoice(json_encode($invoice)));
            }
        }

        return $invoice_data;
    }

    protected function isZohoPlaceOfSupplyInvalidError($invoice_data)
    {
        if (empty($invoice_data) || empty($invoice_data->message)) {
            return false;
        }

        $message = strtolower($invoice_data->message);

        return strpos($message, 'invalid element place_of_supply') !== false
            || strpos($message, 'invalid element tax_treatment') !== false
            || strpos($message, 'invalid element tax_reg_no') !== false
            || strpos($message, 'place of supply is not applicable') !== false
            || strpos($message, 'place_of_supply is not applicable') !== false;
    }

    protected function isZohoPlaceOfSupplyMissingError($invoice_data)
    {
        if (empty($invoice_data) || empty($invoice_data->message)) {
            return false;
        }

        $message = strtolower($invoice_data->message);

        return strpos($message, 'place of supply is mandatory') !== false
            || strpos($message, 'place_of_supply is mandatory') !== false
            || strpos($message, 'enter a valid place of supply') !== false
            || strpos($message, 'provide a place of supply') !== false
            || strpos($message, 'place of supply is required') !== false
            || (strpos($message, 'place of supply') !== false && strpos($message, 'invalid') !== false);
    }

    protected function postZohoContact(ZohoBooks $zb, $contactData)
    {
        $contactResponse = $zb->postContact(json_encode($contactData));
        $contactResult = json_decode($contactResponse);

        if ($contactResult && isset($contactResult->code) && ((int)$contactResult->code === 8 || (isset($contactResult->message) && strpos(strtolower($contactResult->message), 'invalid element') !== false))) {
            unset($contactData['tax_reg_no']);
            unset($contactData['tax_treatment']);
            unset($contactData['place_of_contact']);
            $contactResponse = $zb->postContact(json_encode($contactData));
            $contactResult = json_decode($contactResponse);
        }

        return $contactResult;
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
    public function getZohoItemId($item, ZohoBooks $zb = null)
    {
        if (!is_array($item) || empty($item)) {
            return '';
        }

        if ($zb === null) {
            $zb = new ZohoBooks();
        }

        $item_sku = trim(strip_tags((string)$item['id']));
        $item_name = $item_sku . '-' . trim(strip_tags((string)$item['description']));

        // 1. Search if item already exists in Zoho Books
        $searchResponse = $zb->getItems(['search_text' => $item_sku]);
        $searchData = $searchResponse ? json_decode($searchResponse) : null;
        if (!empty($searchData) && isset($searchData->code) && (int)$searchData->code === 0 && !empty($searchData->items)) {
            foreach ($searchData->items as $zoho_item) {
                if ((isset($zoho_item->sku) && trim((string)$zoho_item->sku) === $item_sku) || (isset($zoho_item->name) && trim((string)$zoho_item->name) === $item_name)) {
                    $item_id = $zoho_item->item_id;
                    if (isset($zoho_item->status) && $zoho_item->status === 'inactive') {
                        @$zb->markItemActive($item_id);
                    }
                    $this->db->where('id', $item['id']);
                    $this->db->update('tblitems_in', ['zoho_id' => $item_id]);
                    return $item_id;
                }
            }
        }

        // 2. Try creating the item in Zoho Books
        $data = [
            "name" => $item_name,
            "rate" => $item['rate'],
            "description" => strip_tags($item['long_description']),
            "sku" => $item_sku,
        ];

        $response = $zb->postItems(json_encode($data));
        $do_item = json_decode($response);

        if ($do_item !== null && isset($do_item->code)) {
            if ((int)$do_item->code === 0 && isset($do_item->item->item_id)) {
                $item_id = $do_item->item->item_id;
                $this->db->where('id', $item['id']);
                $this->db->update('tblitems_in', ['zoho_id' => $item_id]);
                return $item_id;
            }

            // If item already exists in Zoho
            if ((int)$do_item->code === 1001 || (isset($do_item->message) && strpos(strtolower($do_item->message), 'already exists') !== false)) {
                $searchResponse2 = $zb->getItems(['search_text' => $item_sku]);
                $searchData2 = $searchResponse2 ? json_decode($searchResponse2) : null;
                if (!empty($searchData2) && !empty($searchData2->items)) {
                    $found_item = $searchData2->items[0];
                    $item_id = $found_item->item_id;
                    if (isset($found_item->status) && $found_item->status === 'inactive') {
                        @$zb->markItemActive($item_id);
                    }
                    $this->db->where('id', $item['id']);
                    $this->db->update('tblitems_in', ['zoho_id' => $item_id]);
                    return $item_id;
                }
            }
        }

        return '';
    }

    /* Get all reciepts in case user go on index page */
    public function createReceipt()
    {
        $this->load->library('form_validation');
        $data['staff'] = $this->staff_model->get('', 1);



        if ($this->input->post()) {
            $errors = [];
            $res_data = "";

            $this->form_validation->set_rules('from_date', 'From Date', 'required|trim');
            $this->form_validation->set_rules('to_date', 'To Date', 'required');

            // pre_array($this->input->post());
            if ($this->form_validation->run() == FALSE) {

                // $this->load->view('admin/custom_notes/create', $data);
                $errors = "<div class='alert alert-danger'>" . validation_errors() . "</div>";

                //$errors['response'] = validation_errors();
                print_r($errors);
                //echo json_encode($errors);
                return;

            } elseif ($this->input->post('from_date') > $this->input->post('to_date')) {
                $errors = "<div class='alert alert-danger'>Start date should be less than end date</div>";
                print_r(($errors));
                // echo json_encode($errors);
                return;
            } else {


                // find invoice for given dates
                $where['tblinvoicepaymentrecords.date >='] = date("Y-m-d", strtotime($this->input->post('from_date')));
                $where['tblinvoicepaymentrecords.date <='] = date("Y-m-d", strtotime($this->input->post('to_date')));

              // $where['tblreciepts.receipt_id'] = 1641;

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
/*                                        //payments table
                                        $this->db->where('receipt_id', $receipt['receipt_id']);
                                        $this->db->update('tblinvoicepaymentrecords', array('zoho_id' => $receipt_data->payment->payment_id));*/
                                        //recipts table
                                        $this->db->where('receipt_id', $receipt['receipt_id']);
                                        $this->db->update('tblreciepts', array('zoho_id' => $receipt_data->payment->payment_id));
                                    }


                                    $res_data .= "<div class='alert alert-danger'>";
                                    $res_data .= " Code:" . $receipt_data->code;
                                    $res_data .= " Message:" . $receipt_data->message;
                                    $res_data .= " Receipt Number:" . strip_tags( $receipt['receipt_num']);
                                    $res_data .= "</div>";

                                    $errors[]['code'] = $receipt_data->code;
                                    $errors[]['message'] = $receipt_data->message;
                                    $errors[]['receipt_id'] = strip_tags( $receipt['receipt_num']);
                                }
                            }
                        }
                        /*$res_data .= "<div class='alert alert-danger'>";
                        $res_data .= "Already exist";
                        $res_data .= "</div>";*/

                        sleep(20);
                    }
                } else {
                    $res_data .= "<div class='alert alert-danger'>";
                    $res_data .= "No Receipt Found!";
                    $res_data .= "</div>";
                }
                print_r($res_data);
                return;
            }
        }

        $this->load->view('admin/sync_invoices/create_payment', $data);
    }

    /**
     * @param $reciept_data
     * @return array
     */
    protected function receiptJson($receipt_data)
    {
        $invoices = $this->receipts_model->get_zoho_recipt_invoices($receipt_data['receipt_id']);
        //khuram iqbal
        $account_id= '1312911000000073107';

        $invoices_array = array();
        $refund = 0;
        $payment_mode = 'others';
        if($receipt_data['receipt_type'] == 'Cheque'){
            $payment_mode = 'check';
            $account_id = get_receipt_deposit_bank_account_id(
                isset($receipt_data['deposit_bank']) ? $receipt_data['deposit_bank'] : '',
                $account_id
            );
        }else if($receipt_data['receipt_type'] == 'Cash'){
            $payment_mode = 'cash';
            if($receipt_data['reciept_owner'] == 21){
                //dalbir
                $account_id= '1312911000000086053';
            }else{
                //khuram iqbal
                $account_id= '1312911000000073107';
            }
        }else if($receipt_data['receipt_type'] == 'Bank Transfer'){
            $payment_mode = 'banktransfer';
            $account_id = get_receipt_deposit_bank_account_id(
                isset($receipt_data['deposit_bank']) ? $receipt_data['deposit_bank'] : '',
                $account_id
            );
        }

        foreach ($invoices as $key => $invoice){
            $payment_old = $this->receipts_model->get_invoice_previous_payment($invoice['invoiceid'],$receipt_data['receipt_id']);

                if (!empty($invoice['zoho_id'])) {
                    $invoice_id = $invoice['zoho_id'];
                    if($payment_old <> null){
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
            $invoices_array[$key]['invoice_number'] = $invoice['prefix'].$invoice['number'];
            $invoices_array[$key]['date'] = $invoice['date'];
            $invoices_array[$key]['invoice_amount'] = $invoice['total'];
            $invoices_array[$key]['amount_applied'] = $invoice['applied_amount'];
    /*        $invoices_array[$key]['invoice_amount'] = round($invoice['total']);
            $invoices_array[$key]['amount_applied'] = round($invoice['applied_amount']);*/
            if( $invoices_array[$key]['amount_applied'] > $invoices_array[$key]['invoice_amount'] ){
                //$amount_due_over = round($invoice['applied_amount']) - round($invoice['total']);
                $amount_due_over = $invoice['applied_amount'] - $invoices_array[$key]['invoice_amount'];
                $invoices_array[$key]['amount_applied'] = $invoices_array[$key]['amount_applied'] - $amount_due_over ;
                $refund += $amount_due_over;
            }
        }


        if (count($receipt_data) > 0) {
            $receipt = [
                "payment_mode" => $payment_mode,
                "amount" => $receipt_data['receipt_amount'],
                "amount_refunded" => $refund,
               // "date" => $receipt_data['receipt_date'],
                "date" => $receipt_data['paydate'],
                "status" =>'success',
                "reference_number" => $receipt_data['receipt_num'],
                "description" => "",
                "customer_id" => $receipt_data['receipt_client_id'],
                "customer_name" => $receipt_data['client_name'],
                "invoices" => $invoices_array,
                "email" => $receipt_data['client_email'],
                "currency_code" => 'AED',
                "currency_symbol" => 'AED',
                "exchange_rate" => 1,
                "exchange_rate" => 1,
                "account_id" => $account_id,
            ];
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


    /**
     * @param $client_id
     * @return array
     */
    protected function createContactData($client_id)
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

                        //"salutation" => $contact["title"],
                        "salutation" => '',
                        "first_name" => $contact['firstname'],
                        "last_name" => $contact['lastname'],
                        "email" => $contact['email'],
                        "phone" => $contact['phonenumber'],
                        "mobile" => $contact['email'],
                        "designation" => $contact["title"],
                        // "designation" => $contact["title"],
                        "department" => "",
                        "skype" => "",
                        // "is_primary_contact" => ($contact['is_primary']) ? true : false,
                        "enable_portal" => ($contact['is_primary']) ? true : false
                    ];
                }
            }

            $tax_treatment = get_client_tax_treatment($client);
            $place_of_contact = get_client_place_of_supply($client);

            $contact = [
                "contact_name" => $client->company,
                "company_name" => $client->company,
                "first_name" => $primary_first_name,
                "last_name" => $primary_last_name,
                "email" => $primary_email,
                "phone" => $client->phonenumber,
                "facebook" => "",
                "twitter" => "",
                "tax_treatment" => $tax_treatment,
                "billing_address" => [
                    "attention" => $client->company,
                    "address" => strip_html_tags($client->address),
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
                    "address" => strip_html_tags($client->address),
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

            if (!empty($client->vat)) {
                $contact['tax_reg_no'] = $client->vat;
            }

        }

        return $contact;
    }
}
