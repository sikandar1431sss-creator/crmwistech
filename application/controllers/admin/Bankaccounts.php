<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Bankaccounts extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('bankaccounts_model');
        $this->load->model('currencies_model');
    }

    public function index()
    {
        $this->require_permission('view');

        $data['title'] = 'Bank Accounts';
        $data['bank_accounts'] = $this->bankaccounts_model->get();
        $this->load->view('admin/bankaccounts/manage', $data);
    }

    public function account($id = '')
    {
        $this->require_permission($id == '' ? 'create' : 'edit');

        if ($this->input->post()) {
            $post = $this->input->post();
            $this->validate_zoho_account_currency($post);

            if ($id == '') {
                $insert_id = $this->bankaccounts_model->add($post);

                if ($insert_id) {
                    set_alert('success', 'Bank account added successfully.');
                    redirect(admin_url('bankaccounts'));
                }

                set_alert('warning', 'Bank account was not added.');
            } else {
                $success = $this->bankaccounts_model->update($id, $post);

                if ($success) {
                    set_alert('success', 'Bank account updated successfully.');
                    redirect(admin_url('bankaccounts'));
                }

                set_alert('warning', 'No bank account changes were saved.');
            }
        }

        if ($id != '') {
            $data['bank_account'] = $this->bankaccounts_model->get($id);

            if (!$data['bank_account']) {
                show_404();
            }

            $data['title'] = 'Edit Bank Account';
        } else {
            $data['bank_account'] = null;
            $data['title'] = 'New Bank Account';
        }

        $data['currencies'] = $this->currencies_model->get();
        $data['zoho_accounts'] = $this->bankaccounts_model->get_zoho_accounts();

        $this->load->view('admin/bankaccounts/account', $data);
    }

    public function delete($id)
    {
        $this->require_permission('delete');

        if (!$id) {
            redirect(admin_url('bankaccounts'));
        }

        $response = $this->bankaccounts_model->delete($id);

        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', 'This bank account is used in receipts and cannot be deleted. Mark it inactive instead.');
        } elseif ($response) {
            set_alert('success', 'Bank account deleted successfully.');
        } else {
            set_alert('warning', 'Problem deleting bank account.');
        }

        redirect(admin_url('bankaccounts'));
    }

    public function change_status($id, $status)
    {
        $this->require_permission('edit');

        if ($this->input->is_ajax_request()) {
            $this->bankaccounts_model->change_status($id, $status);
        }
    }

    public function zoho_accounts()
    {
        $this->require_manage_permission();

        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $currency_code = $this->input->get('currency_code');

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'accounts' => $this->bankaccounts_model->get_zoho_accounts($currency_code),
            ]));
    }

    public function sync_zoho_accounts()
    {
        $this->require_manage_permission();

        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->library('ZohoBooks');

        if (get_option('zoho_refresh_token') != '') {
            $this->zohobooks->getAccessTokenFromRefreshTocken();
        }

        $response = $this->zohobooks->allAccounts();
        $result = json_decode($response, true);

        if (!is_array($result) || !isset($result['code'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Invalid response received from Zoho.',
                    'response' => $response,
                ]));
            return;
        }

        if ((int)$result['code'] !== 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => isset($result['message']) ? $result['message'] : 'Unable to fetch Zoho bank accounts.',
                    'response' => $result,
                ]));
            return;
        }

        $accounts = $this->bankaccounts_model->sync_zoho_accounts(isset($result['bankaccounts']) ? $result['bankaccounts'] : []);

        if (empty($accounts)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'No Zoho bank accounts found. Existing bank list was not changed.',
                ]));
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Synced ' . count($accounts) . ' Zoho bank account(s).',
                'count' => count($accounts),
                'accounts' => $accounts,
            ]));
    }

    private function require_permission($capability)
    {
        if (!is_admin() && !has_permission('bankaccounts', '', $capability)) {
            access_denied('Bank Accounts');
        }
    }

    private function require_manage_permission()
    {
        if (
            !is_admin()
            && !has_permission('bankaccounts', '', 'create')
            && !has_permission('bankaccounts', '', 'edit')
        ) {
            access_denied('Bank Accounts');
        }
    }

    private function validate_zoho_account_currency($post)
    {
        if (empty($post['currency_id']) || empty($post['zoho_account_id'])) {
            return;
        }

        $currency_code = get_receipt_currency_code($post['currency_id']);

        if ($currency_code === '') {
            return;
        }

        foreach ($this->bankaccounts_model->get_zoho_accounts() as $account) {
            if ($account['account_id'] !== $post['zoho_account_id']) {
                continue;
            }

            $account_currency_code = !empty($account['currency_code'])
                ? normalize_receipt_currency_code($account['currency_code'])
                : '';

            if ($account_currency_code !== '' && $account_currency_code !== $currency_code) {
                show_error(
                    'Selected Zoho account currency is ' . $account_currency_code
                    . ', but bank currency is ' . $currency_code
                    . '. Please select a matching Zoho account or change the bank currency.',
                    400
                );
            }

            return;
        }
    }
}
