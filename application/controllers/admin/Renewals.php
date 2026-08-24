<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 7/10/18
 * Time: 11:33 AM
 */

defined('BASEPATH') or exit('No direct script access allowed');
class Renewals extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoices_model');
        $this->load->model('customnotes_model');
        $this->load->model('tasks_model');
    }

    /* Get all Renewals in case user go on index page */
    /**
     * @param bool $id
     */
    public function index($id = false)
    {
        $data['staff'] = $this->staff_model->get('', 1);
        $filtersArray = array();


        if ($this->input->get()) {
            $data['renewal_items'] = $this->invoices_model->get_invoices_renewals($this->input->get());
        } else {
            $data['renewal_items'] = $this->invoices_model->get_invoices_renewals();
        }
        $data['invoiceid'] = '';

        // manage-renewal-items
        //$this->load->view('admin/invoices/manage-renewal-items', $data);
        $this->load->view('admin/renewals/manage-renewal-items', $data);
    }

    /* update existing ivoice item rewal status to cancel */
    /**
     *
     */
    public function invoiceRenewalCancel()
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('invoices');
        }

        $customer_id='';
        $items='';
        $inoice_id ='';
        $renewal_reason ='';


        if ($this->input->post('renewal_customer_id')) {
            $customer_id = $this->input->post('renewal_customer_id');
        }
        if ($this->input->post('renewal_cancel_items')) {
            $items = $this->input->post('renewal_cancel_items');
            $items= explode(',', $items);
        }
        if ($this->input->post('renewal_prev_invoice')) {
            $inoice_id = $this->input->post('renewal_prev_invoice');
        }
        if ($this->input->post('renewal_reason')) {
            $renewal_reason = $this->input->post('renewal_reason');
        }
        $this->invoices_model->invoice_item_renwal_cancel($items,$renewal_reason);
        set_alert('success', _l('Invoice renewal has bessn cancelled successfully'));
       // redirect(admin_url('invoices/invoice_renewels_items'));
        redirect(admin_url('renewals'));
        return;
    }
}