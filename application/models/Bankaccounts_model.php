<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Bankaccounts_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensure_table();
    }

    private function ensure_table()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `tblbankaccounts` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(100) NOT NULL DEFAULT '',
            `bank_nick_name` varchar(191) NOT NULL DEFAULT '',
            `full_bank_name` varchar(191) NOT NULL DEFAULT '',
            `account_type` varchar(20) NOT NULL DEFAULT 'bank',
            `currency_id` int(11) NOT NULL DEFAULT '0',
            `currency_code` varchar(10) NOT NULL DEFAULT '',
            `zoho_account_id` varchar(100) NOT NULL DEFAULT '',
            `zoho_account_name` varchar(191) NOT NULL DEFAULT '',
            `iban` varchar(100) NOT NULL DEFAULT '',
            `swift` varchar(100) NOT NULL DEFAULT '',
            `active` tinyint(1) NOT NULL DEFAULT '1',
            `created_by` int(11) NOT NULL DEFAULT '0',
            `datecreated` datetime DEFAULT NULL,
            `dateupdated` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `currency_id` (`currency_id`),
            KEY `account_type` (`account_type`),
            KEY `zoho_account_id` (`zoho_account_id`),
            KEY `active` (`active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        if ($this->db->table_exists('tblbankaccounts')) {
            if (!$this->db->field_exists('account_type', 'tblbankaccounts')) {
                $this->db->query("ALTER TABLE `tblbankaccounts` ADD COLUMN `account_type` varchar(20) NOT NULL DEFAULT 'bank' AFTER `full_bank_name`;");
            }
        }
    }

    public function get($id = '', $active_only = false, $account_type = '')
    {
        $this->db->select('tblbankaccounts.*, tblcurrencies.name as currency_name, tblcurrencies.symbol as currency_symbol');
        $this->db->from('tblbankaccounts');
        $this->db->join('tblcurrencies', 'tblcurrencies.id = tblbankaccounts.currency_id', 'left');

        if ($active_only) {
            $this->db->where('tblbankaccounts.active', 1);
        }

        if ($account_type !== '') {
            $this->db->where('tblbankaccounts.account_type', strtolower($account_type));
        }

        if (is_numeric($id)) {
            $this->db->where('tblbankaccounts.id', $id);
            return $this->db->get()->row();
        }

        $this->db->order_by('tblbankaccounts.active', 'DESC');
        $this->db->order_by('tblbankaccounts.bank_nick_name', 'ASC');

        return $this->db->get()->result_array();
    }

    public function add($data)
    {
        $insert = $this->format_data($data);
        $insert['created_by'] = get_staff_user_id();
        $insert['datecreated'] = date('Y-m-d H:i:s');

        $this->db->insert('tblbankaccounts', $insert);
        $id = $this->db->insert_id();

        if ($id) {
            $type_label = ($insert['account_type'] === 'cash') ? 'Cash Account' : 'Bank Account';
            logActivity($type_label . ' Created [ID: ' . $id . ', Name: ' . $insert['bank_nick_name'] . ']');
        }

        return $id;
    }

    public function update($id, $data)
    {
        $update = $this->format_data($data);
        $update['dateupdated'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update('tblbankaccounts', $update);

        if ($this->db->affected_rows() > 0) {
            $type_label = ($update['account_type'] === 'cash') ? 'Cash Account' : 'Bank Account';
            logActivity($type_label . ' Updated [ID: ' . $id . ', Name: ' . $update['bank_nick_name'] . ']');
            return true;
        }

        return false;
    }

    public function delete($id)
    {
        if ($this->is_referenced($id)) {
            return [
                'referenced' => true,
            ];
        }

        $this->db->where('id', $id);
        $this->db->delete('tblbankaccounts');

        return $this->db->affected_rows() > 0;
    }

    public function change_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblbankaccounts', ['active' => (int)$status]);

        return $this->db->affected_rows() > 0;
    }

    public function is_referenced($id)
    {
        $this->db->where('deposit_bank', 'bankaccount_' . (int)$id);
        return $this->db->count_all_results('tblreciepts') > 0;
    }

    public function get_zoho_accounts($currency_code = '', $account_type = '')
    {
        $currency_code = normalize_receipt_currency_code($currency_code);
        $account_type = strtolower(trim((string)$account_type));
        $accounts = normalize_receipt_deposit_banks(get_option('receipt_deposit_banks'));

        if (empty($accounts)) {
            $accounts = get_default_receipt_deposit_banks();
        }

        $filtered = [];

        foreach ($accounts as $account) {
            if (empty($account['account_id'])) {
                continue;
            }

            if (
                $currency_code !== ''
                && !empty($account['currency_code'])
                && normalize_receipt_currency_code($account['currency_code']) !== $currency_code
            ) {
                continue;
            }

            if ($account_type !== '') {
                $raw_type = isset($account['account_type']) ? strtolower($account['account_type']) : 'bank';
                $is_cash = in_array($raw_type, ['cash', 'petty_cash', 'petty cash']);
                $normalized_type = $is_cash ? 'cash' : 'bank';

                if ($normalized_type !== $account_type) {
                    continue;
                }
            }

            $filtered[] = $account;
        }

        return $filtered;
    }

    public function sync_zoho_accounts($zoho_accounts)
    {
        $existing_by_account = [];

        foreach (get_receipt_legacy_deposit_banks(true) as $bank) {
            if (!empty($bank['account_id'])) {
                $existing_by_account[$bank['account_id']] = $bank;
            }
        }

        $accounts = [];

        foreach ($zoho_accounts as $account) {
            if (!isset($account['account_id']) || !isset($account['account_name'])) {
                continue;
            }

            $raw_type = isset($account['account_type']) ? strtolower($account['account_type']) : 'bank';
            $is_cash = in_array($raw_type, ['cash', 'petty_cash', 'petty cash']);
            $is_bank = in_array($raw_type, ['bank', 'credit_card', 'creditcard']);

            if (!$is_cash && !$is_bank) {
                continue;
            }

            $account_type = $is_cash ? 'cash' : 'bank';
            $account_id = (string)$account['account_id'];
            $zoho_name = trim($account['account_name']);
            $existing = isset($existing_by_account[$account_id]) ? $existing_by_account[$account_id] : [];
            $code = !empty($existing['code'])
                ? $existing['code']
                : $this->build_zoho_bank_code($zoho_name, $account_id, $accounts, $account_type);
            $name = !empty($existing['name']) ? $existing['name'] : $zoho_name;

            $account_number = '';
            if (!empty($account['bank_account_number'])) {
                $account_number = $account['bank_account_number'];
            } elseif (!empty($account['account_number'])) {
                $account_number = $account['account_number'];
            }

            $accounts[] = [
                'code' => $code,
                'name' => $name,
                'zoho_name' => $zoho_name,
                'account_id' => $account_id,
                'active' => !isset($account['is_active']) || $account['is_active'] ? 1 : 0,
                'account_number' => $account_number,
                'account_type' => $account_type,
                'currency_code' => isset($account['currency_code']) ? normalize_receipt_currency_code($account['currency_code']) : '',
                'current_balance' => isset($account['current_balance']) ? $account['current_balance'] : '',
            ];
        }

        if (empty($accounts)) {
            return [];
        }

        add_option('receipt_deposit_banks', json_encode([], JSON_PRETTY_PRINT), 1);
        update_option('receipt_deposit_banks', json_encode($accounts, JSON_PRETTY_PRINT));

        logActivity('Zoho Bank & Cash Accounts Synced [Count: ' . count($accounts) . ']');

        return $accounts;
    }

    private function format_data($data)
    {
        $currency_id = isset($data['currency_id']) ? (int)$data['currency_id'] : 0;
        $currency_code = $this->get_currency_code($currency_id);
        $zoho_account_id = isset($data['zoho_account_id']) ? trim($data['zoho_account_id']) : '';
        $zoho_account_name = isset($data['zoho_account_name']) ? trim($data['zoho_account_name']) : '';
        $account_type = (isset($data['account_type']) && strtolower(trim($data['account_type'])) === 'cash') ? 'cash' : 'bank';

        if ($zoho_account_name === '' && $zoho_account_id !== '') {
            foreach ($this->get_zoho_accounts() as $account) {
                if ($account['account_id'] === $zoho_account_id) {
                    $zoho_account_name = !empty($account['zoho_name']) ? $account['zoho_name'] : $account['name'];
                    break;
                }
            }
        }

        return [
            'title' => isset($data['title']) ? trim($data['title']) : '',
            'bank_nick_name' => isset($data['bank_nick_name']) ? trim($data['bank_nick_name']) : '',
            'full_bank_name' => isset($data['full_bank_name']) ? trim($data['full_bank_name']) : '',
            'account_type' => $account_type,
            'currency_id' => $currency_id,
            'currency_code' => $currency_code,
            'zoho_account_id' => $zoho_account_id,
            'zoho_account_name' => $zoho_account_name,
            'iban' => isset($data['iban']) ? trim($data['iban']) : '',
            'swift' => isset($data['swift']) ? strtoupper(trim($data['swift'])) : '',
            'active' => isset($data['active']) ? 1 : 0,
        ];
    }

    private function get_currency_code($currency_id)
    {
        if (!$currency_id) {
            return '';
        }

        $currency = $this->db
            ->select('name')
            ->where('id', $currency_id)
            ->get('tblcurrencies')
            ->row();

        return $currency ? normalize_receipt_currency_code($currency->name) : '';
    }

    private function build_zoho_bank_code($name, $account_id, $existing_accounts, $account_type = 'bank')
    {
        $base = strtolower(url_title($name, '_', true));
        $base = trim($base, '_');

        if ($base === '') {
            $prefix = ($account_type === 'cash') ? 'cash_' : 'bank_';
            $base = $prefix . substr($account_id, -6);
        }

        $existing_codes = [];
        foreach ($existing_accounts as $account) {
            if (!empty($account['code'])) {
                $existing_codes[] = $account['code'];
            }
        }

        $code = $base;
        $i = 2;

        while (in_array($code, $existing_codes)) {
            $code = $base . '_' . $i;
            $i++;
        }

        return $code;
    }
}

