<?php

//#######################
## For Navigation Menu
#######################

add_action('after_render_single_aside_menu', 'my_custom_menu_items');
add_action('app_init', 'ensure_bankaccounts_permission');
add_action('app_init', 'remove_bankaccounts_setup_menu_item');
add_action('staff_permissions_conditions', 'register_bankaccounts_permission_conditions');
add_action('before_settings_updated', 'register_custom_zoho_settings');

function ensure_bankaccounts_permission()
{
    $CI = &get_instance();

    if (total_rows('tblpermissions', ['shortname' => 'bankaccounts']) == 0) {
        $CI->db->insert('tblpermissions', [
            'name' => 'Bank Accounts',
            'shortname' => 'bankaccounts',
        ]);
    }
}

function register_bankaccounts_permission_conditions($permissions)
{
    $permissions['bankaccounts'] = [
        'view' => true,
        'view_own' => false,
        'edit' => true,
        'create' => true,
        'delete' => true,
    ];

    return $permissions;
}

function remove_bankaccounts_setup_menu_item()
{
    $menu = json_decode(get_option('setup_menu_active'));

    if (!$menu || !isset($menu->setup_menu_active) || !is_array($menu->setup_menu_active)) {
        return;
    }

    $changed = false;

    foreach ($menu->setup_menu_active as $index => $item) {
        if ((isset($item->id) && $item->id === 'bankaccounts') || (isset($item->url) && $item->url === 'bankaccounts')) {
            unset($menu->setup_menu_active[$index]);
            $changed = true;
            continue;
        }

        if (!isset($item->children) || !is_array($item->children)) {
            continue;
        }

        $children = [];

        foreach ($item->children as $child) {
            $is_bankaccounts_child = (isset($child->id) && $child->id === 'bankaccounts')
                || (isset($child->url) && $child->url === 'bankaccounts');

            if (!$is_bankaccounts_child) {
                $children[] = $child;
                continue;
            }

            $changed = true;
        }

        $menu->setup_menu_active[$index]->children = $children;
    }

    $menu->setup_menu_active = array_values($menu->setup_menu_active);

    if ($changed) {
        update_option('setup_menu_active', json_encode($menu));
    }
}

function get_safe_currency_symbol($currency_code = '', $symbol = '')
{
    $currency_code = strtoupper(trim((string)$currency_code));
    $symbol = trim((string)$symbol);

    $standard_symbols = [
        'USD' => '$',
        'GBP' => 'GBP',
        'EUR' => 'EUR',
        'AED' => 'AED',
        'PKR' => 'Rs.',
        'SAR' => 'SAR',
        'CNY' => 'CNY',
        'JPY' => 'JPY',
        'ZAR' => 'R',
    ];

    if ($currency_code === '') {
        return $symbol;
    }

    if ($symbol === '') {
        return isset($standard_symbols[$currency_code]) ? $standard_symbols[$currency_code] : $currency_code;
    }

    $known_bank_words = [
        'MASHREQ',
        'MASHREQ BANK',
        'MASHREQBANK',
        'ENBD',
        'EMIRATES NBD',
        'RAK',
        'RAK BANK',
    ];

    $symbol_upper = strtoupper($symbol);

    if (in_array($symbol_upper, $known_bank_words, true)) {
        return isset($standard_symbols[$currency_code]) ? $standard_symbols[$currency_code] : $currency_code;
    }

    if (preg_match('/[A-Z]{4,}/', $symbol_upper) && $symbol_upper !== $currency_code) {
        return isset($standard_symbols[$currency_code]) ? $standard_symbols[$currency_code] : $currency_code;
    }

    return $symbol;
}

/**
 * @param $order
 */
function my_custom_menu_items($order)
{
    if ($order == 2) {
        echo '<li>';
        echo '    <a href="' . base_url() . 'admin/renewals" aria-expanded="false"><i class="fa fa-registered menu-icon"></i>Renewals</a>';
        echo '</li>';
    }

    if ($order == 3) {

        echo '<li>';
        echo '    <a href="#" aria-expanded="false"><i class="fa fa-registered menu-icon"></i>Receipts<span class="fa arrow"></span></a>';
        echo '    <ul class="nav nav-second-level collapse" aria-expanded="false">';


        if (is_admin() || has_permission('receipts', '', 'view') || has_permission('receipts', '', 'view_own')) {
            echo '        <li><a href="' . base_url() . 'admin/receipts/">All Receipts</a></li>';
        }


        if (is_admin() || has_permission('receipts', '', 'create')) {
            echo '        <li><a href="' . base_url() . 'admin/receipts/create">Create New</a></li>';
        }

        if (is_admin() || has_permission('bankaccounts', '', 'view')) {
            echo '        <li><a href="' . base_url() . 'admin/bankaccounts">Bank Accounts</a></li>';
        }

        if (is_admin() || has_permission('receipt_handover', '', 'edit')) {
            echo '        <li><a href="' . base_url() . 'admin/receipts/index/handover">Handover</a></li>';

        }

        if (is_admin() || has_permission('receipt_deposit', '', 'edit')) {
            echo '        <li><a href="' . base_url() . 'admin/receipts/index/deposited">Deposit</a></li>';
        }

        if (is_admin() || has_permission('receipt_verify', '', 'edit')) {
            echo '        <li><a href="' . base_url() . 'admin/receipts/index/verified">Verify</a></li>';
        }

        echo '    </ul>';
        echo '</li>';

    }

    if ($order == 7) {

        echo '<li>';
        echo '    <a href="#" aria-expanded="false"><i class="fa fa-file-text-o menu-icon"></i>Custom Notes<span class="fa arrow"></span></a>';
        echo '    <ul class="nav nav-second-level collapse" aria-expanded="false">
                    <li><a href="' . base_url() . 'admin/custom_notes/">View All</a></li> 
                    <li><a href="' . base_url() . 'admin/custom_notes/create">Create New</a></li> 
                  </ul>';
        echo '</li>';

    }

    if ($order == 9) {
        echo '<li>';
        echo '    <a href="' . base_url() . 'admin/sync_invoices/create" aria-expanded="false"><i class="fa fa-file-archive-o menu-icon"></i>Sync Invoices</a>';
        echo '</li>';
    }

    if ($order == 11) {
        echo '<li>';
        echo '    <a href="' . base_url() . 'admin/vendors/" aria-expanded="false"><i class="fa fa-industry menu-icon"></i>Vendors</a>';
        echo '</li>';
    }
}

/**
 * @param $date
 * @return false|string
 */
function date_format_dmy($date)
{
    return date("d-m-Y", strtotime($date));
}

/**
 * @param $data
 */
function pre_array($data)
{
    echo '<pre>';
    print_r($data);
    die();
}

/**
 * @return bool|int
 */
function make_next_invoice_num()
{
    $CI = &get_instance();
    $CI->db->select('number');
    $CI->db->from('tblinvoices');
    $CI->db->where(['type' => 'invoice']);
    $CI->db->limit(1);
    $CI->db->order_by('number', 'DESC');
    $number = $CI->db->get()->row();

    if ($number <> null) {
        return $number->number + 1;
    }

    return false;
}

/**
 * @return mixed
 */
function make_next_performa_invoice_number()
{
    $CI = &get_instance();
    $CI->db->select('number');
    $CI->db->from('tblinvoices');
    $CI->db->where(['type' => 'performa']);
    $CI->db->limit(1);
    $CI->db->order_by('number', 'DESC');
    $number = $CI->db->get()->row()->number;

    if(!isset($number)){
        $p_number = 1;
    }

    if ($number <> null) {
        $p_number = $number + 1;
    }

    return $p_number;
}

/**
 * @param $id
 * @return mixed
 */
function getInvoiceTerms($id)
{
    $CI = &get_instance();
    $CI->db->select('cn_title');
    $CI->db->from('tblcustomnotes');
    $CI->db->where(['cn_id' => $id]);
    $CI->db->limit(1);
    return $CI->db->get()->row()->cn_title;
}

/**
 *
 */
function getInvoiceType($id)
{

    $CI = &get_instance();
    $CI->db->select('type');
    $CI->db->from('tblinvoices');
    $CI->db->where(['id' => $id]);
    $CI->db->limit(1);
    $CI->db->order_by('number', 'DESC');
    $type = $CI->db->get()->row()->type;

    if ($type <> null) {
        return $type;
    }

    return false;
}

/**
 * @param $id
 * @return bool
 */
function get_lead_name($id)
{

    $CI = &get_instance();
    $CI->db->select('name');
    $CI->db->from('tblleads');
    $CI->db->where(['id' => $id]);
    $CI->db->limit(1);
    $name = $CI->db->get()->row()->name;

    if ($name <> null) {
        return $name;
    }

    return false;
}


/**
 * @param $id
 * @return bool
 */
function get_lead_company($id)
{

    $CI = &get_instance();
    $CI->db->select('company');
    $CI->db->from('tblleads');
    $CI->db->where(['id' => $id]);
    $CI->db->limit(1);
    $name = $CI->db->get()->row()->company;

    if ($name <> null) {
        return $name;
    }

    return false;
}

/**
 * @param $id
 * @return bool
 */
function get_customer_name($id)
{

    $CI = &get_instance();
    $CI->db->select('company');
    $CI->db->from('tblleads');
    $CI->db->where(['id' => $id]);
    $CI->db->limit(1);
    $name = $CI->db->get()->row()->company;

    if ($name <> null) {
        return $name;
    }

    return false;
}


/**
 * @param string $type
 * @param string $position
 * @return mixed
 */
function getByTypePosition($type = "", $position = "")
{
    $CI = &get_instance();
    $CI->db->select('*');
    $CI->db->from('tblcustomnotes');

    if ($type) {
        $CI->db->where(['cn_type' => $type]);
    }

    if ($position) {
        $CI->db->where(['cn_position' => $position]);
    }

    return $CI->db->get()->result();
}

/**
 * @param $description
 * @return string
 */
function getDescriptionFristLine($description)
{
    $array = array_filter(explode(PHP_EOL, strip_tags($description, '<br>')));

    if (count($array) > 0) {
        foreach ($array as $str) {
            if (!empty(trim($str)) && $str != " ") {

                $myArray = preg_split('/<br[^>]*>/i', $str);

                if (count($myArray) > 0) {
                    foreach ($myArray as $arr) {
                        if (!empty(trim($arr)) && $arr != " ") {
                            return $arr . '<br/>';
                        }
                    }
                    return $str . '<br/>';
                }
            }
        }
    }

    return $description;
}

/**
 * @param $id
 * @return string
 */
function format_receipt_number($id)
{
    $format = 1;
    $prefix = 'REC-';
    if ($format == 1) {
        // Number based
        return $prefix . str_pad($id, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
    } elseif ($format == 2) {
        return $prefix . date('Y', strtotime(date('Y-m-d'))) . '/' . str_pad($id, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
    }

}

function get_default_receipt_deposit_banks()
{
    return [
        [
            'code' => 'mqe',
            'name' => 'Mashreq AED',
            'account_id' => '1312911000012488002',
            'active' => 1,
            'currency_code' => 'AED',
        ],
        [
            'code' => 'rak',
            'name' => 'RAK',
            'account_id' => '1312911000000081257',
            'active' => 1,
            'currency_code' => 'AED',
        ],
        [
            'code' => 'enbd',
            'name' => 'ENBD',
            'account_id' => '1312911000000077839',
            'active' => 1,
            'currency_code' => 'AED',
        ],
    ];
}

function normalize_receipt_deposit_banks($banks)
{
    if (is_string($banks)) {
        $decoded = json_decode($banks, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $banks = $decoded;
        }
    }

    if (!is_array($banks)) {
        return [];
    }

    if (isset($banks['banks']) && is_array($banks['banks'])) {
        $banks = $banks['banks'];
    }

    $normalized = [];

    foreach ($banks as $key => $bank) {
        if (is_string($bank)) {
            $bank = [
                'code' => $key,
                'name' => $bank,
            ];
        }

        if (!is_array($bank)) {
            continue;
        }

        $code = isset($bank['code']) ? trim($bank['code']) : '';
        $name = isset($bank['name']) ? trim($bank['name']) : '';
        $zoho_name = isset($bank['zoho_name']) ? trim($bank['zoho_name']) : '';
        $account_id = isset($bank['account_id']) ? trim($bank['account_id']) : '';
        $active = isset($bank['active']) ? (int) $bank['active'] : 1;
        $account_number = isset($bank['account_number']) ? trim($bank['account_number']) : '';
        $account_type = isset($bank['account_type']) ? trim($bank['account_type']) : '';
        $currency_code = isset($bank['currency_code']) ? normalize_receipt_currency_code($bank['currency_code']) : '';
        $current_balance = isset($bank['current_balance']) ? $bank['current_balance'] : '';

        if ($code === '' || $name === '') {
            continue;
        }

        if ($zoho_name === '') {
            $zoho_name = $name;
        }

        $normalized[] = [
            'code' => $code,
            'name' => $name,
            'zoho_name' => $zoho_name,
            'account_id' => $account_id,
            'active' => $active,
            'account_number' => $account_number,
            'account_type' => $account_type,
            'currency_code' => $currency_code,
            'current_balance' => $current_balance,
        ];
    }

    return $normalized;
}

function get_receipt_deposit_bankaccounts_from_database($include_inactive = false, $currency = '')
{
    $CI = &get_instance();

    if (!$CI->db->table_exists('tblbankaccounts')) {
        return [];
    }

    $currency_code = get_receipt_currency_code($currency);

    $CI->db->select('tblbankaccounts.*, tblcurrencies.name as currency_name');
    $CI->db->from('tblbankaccounts');
    $CI->db->join('tblcurrencies', 'tblcurrencies.id = tblbankaccounts.currency_id', 'left');

    if (!$include_inactive) {
        $CI->db->where('tblbankaccounts.active', 1);
    }

    $CI->db->order_by('tblbankaccounts.bank_nick_name', 'ASC');
    $rows = $CI->db->get()->result_array();
    $banks = [];

    foreach ($rows as $row) {
        $name = trim($row['bank_nick_name']) !== '' ? $row['bank_nick_name'] : $row['full_bank_name'];
        $bank_currency_code = normalize_receipt_currency_code($row['currency_code']);

        if ($name === '') {
            continue;
        }

        if ($currency_code !== '' && $bank_currency_code !== '' && $bank_currency_code !== $currency_code) {
            continue;
        }

        $banks[] = [
            'code' => 'bankaccount_' . $row['id'],
            'bank_account_id' => $row['id'],
            'name' => $name,
            'zoho_name' => $row['zoho_account_name'],
            'account_id' => $row['zoho_account_id'],
            'active' => (int)$row['active'],
            'account_number' => $row['iban'],
            'account_type' => $row['title'],
            'currency_id' => $row['currency_id'],
            'currency_code' => $bank_currency_code,
            'current_balance' => '',
            'full_bank_name' => $row['full_bank_name'],
            'iban' => $row['iban'],
            'swift' => $row['swift'],
        ];
    }

    return $banks;
}

function has_receipt_deposit_bankaccounts_in_database()
{
    $CI = &get_instance();

    if (!$CI->db->table_exists('tblbankaccounts')) {
        return false;
    }

    return $CI->db->count_all_results('tblbankaccounts') > 0;
}

function get_receipt_currency_code($currency = '')
{
    $currency = trim((string)$currency);

    if ($currency === '') {
        return '';
    }

    if (!is_numeric($currency)) {
        return normalize_receipt_currency_code($currency);
    }

    $CI = &get_instance();
    $row = $CI->db
        ->select('name')
        ->where('id', $currency)
        ->get('tblcurrencies')
        ->row();

    return $row ? normalize_receipt_currency_code($row->name) : '';
}

function normalize_receipt_currency_code($currency = '')
{
    $currency = html_entity_decode(trim((string)$currency), ENT_QUOTES, 'UTF-8');
    $currency = strtoupper(trim(preg_replace('/\s+/', ' ', $currency)));

    if ($currency === '') {
        return '';
    }

    $compact = preg_replace('/[^A-Z0-9]/', '', $currency);
    $aliases = [
        'UAE' => 'AED',
        'AE' => 'AED',
        'ARE' => 'AED',
        'UAEDIRHAM' => 'AED',
        'UAEDIRHAMS' => 'AED',
        'UNITEDARABEMIRATESDIRHAM' => 'AED',
        'UNITEDARABEMIRATESDIRHAMS' => 'AED',
        'EMIRATIDIRHAM' => 'AED',
        'EMIRATIDIRHAMS' => 'AED',
        'DIRHAM' => 'AED',
        'DIRHAMS' => 'AED',
        'USDOLLAR' => 'USD',
        'USDOLLARS' => 'USD',
        'UNITEDSTATESDOLLAR' => 'USD',
        'UNITEDSTATESDOLLARS' => 'USD',
        'EURO' => 'EUR',
        'EUROS' => 'EUR',
        'PAKISTANIRUPEE' => 'PKR',
        'PAKISTANIRUPEES' => 'PKR',
        'SAUDIRIYAL' => 'SAR',
        'SAUDIRIYALS' => 'SAR',
    ];

    if (isset($aliases[$compact])) {
        return $aliases[$compact];
    }

    if (strlen($compact) === 3) {
        return $compact;
    }

    return $currency;
}

function get_receipt_legacy_deposit_banks($include_inactive = false, $currency = '')
{
    $banks = normalize_receipt_deposit_banks(get_option('receipt_deposit_banks'));

    if (empty($banks)) {
        $banks = get_default_receipt_deposit_banks();
    }

    $currency_code = get_receipt_currency_code($currency);

    $banks = array_values(array_filter($banks, function ($bank) use ($include_inactive, $currency_code) {
        if (!$include_inactive && isset($bank['active']) && (int) $bank['active'] !== 1) {
            return false;
        }

        if ($currency_code === '') {
            return true;
        }

        if (empty($bank['currency_code'])) {
            return true;
        }

        return normalize_receipt_currency_code($bank['currency_code']) === $currency_code;
    }));

    return $banks;
}

function get_receipt_deposit_banks($include_inactive = false, $currency = '')
{
    $database_banks = get_receipt_deposit_bankaccounts_from_database($include_inactive, $currency);

    if (!empty($database_banks) || has_receipt_deposit_bankaccounts_in_database()) {
        return $database_banks;
    }

    return get_receipt_legacy_deposit_banks($include_inactive, $currency);
}

function get_receipt_deposit_banks_setting_json()
{
    return json_encode(get_receipt_legacy_deposit_banks(true), JSON_PRETTY_PRINT);
}

function get_receipt_deposit_bank_account_id($code, $fallback = '')
{
    $bank = get_receipt_deposit_bank($code, true);

    if ($bank && !empty($bank['account_id'])) {
        return $bank['account_id'];
    }

    return $fallback;
}

function get_receipt_deposit_bank($code, $include_inactive = true)
{
    $code = trim((string)$code);

    if ($code === '') {
        return null;
    }

    foreach (get_receipt_deposit_banks(true) as $bank) {
        if ($bank['code'] === $code) {
            return $bank;
        }
    }

    foreach (get_receipt_legacy_deposit_banks(true) as $bank) {
        if ($bank['code'] === $code) {
            return $bank;
        }
    }

    return null;
}

function get_receipt_deposit_bank_label($bank)
{
    $label = $bank['name'];
    $meta = [];

    if (!empty($bank['currency_code'])) {
        $meta[] = $bank['currency_code'];
    }

    if (!empty($bank['zoho_name']) && $bank['zoho_name'] !== $bank['name']) {
        $meta[] = 'Zoho: ' . $bank['zoho_name'];
    }

    if (!empty($meta)) {
        $label .= ' (' . implode(' - ', $meta) . ')';
    }

    return $label;
}

function get_receipt_deposit_bank_options($selected = '', $currency = '')
{
    $options = '';
    $banks = get_receipt_deposit_banks(false, $currency);
    $selected_found = false;
    $currency_code = get_receipt_currency_code($currency);

    foreach ($banks as $bank) {
        $is_selected = ($selected === $bank['code']) ? ' selected' : '';
        if ($is_selected !== '') {
            $selected_found = true;
        }
        $options .= '<option value="' . html_escape($bank['code']) . '"' . $is_selected . '>' . html_escape(get_receipt_deposit_bank_label($bank)) . '</option>';
    }

    if ($selected !== '' && !$selected_found) {
        foreach (get_receipt_deposit_banks(true) as $bank) {
            $bank_currency_code = !empty($bank['currency_code']) ? normalize_receipt_currency_code($bank['currency_code']) : '';
            if ($selected === $bank['code'] && ($currency_code === '' || $bank_currency_code === '' || $bank_currency_code === $currency_code)) {
                $options .= '<option value="' . html_escape($bank['code']) . '" selected>' . html_escape(get_receipt_deposit_bank_label($bank)) . '</option>';
                break;
            }
        }

        foreach (get_receipt_legacy_deposit_banks(true) as $bank) {
            $bank_currency_code = !empty($bank['currency_code']) ? normalize_receipt_currency_code($bank['currency_code']) : '';
            if ($selected === $bank['code'] && ($currency_code === '' || $bank_currency_code === '' || $bank_currency_code === $currency_code)) {
                $options .= '<option value="' . html_escape($bank['code']) . '" selected>' . html_escape(get_receipt_deposit_bank_label($bank)) . '</option>';
                break;
            }
        }
    }

    return $options;
}

function get_invoice_bank_account_details($invoice)
{
    if (
        !isset($invoice->bank_account_id)
        || (int)$invoice->bank_account_id <= 0
        || (isset($invoice->print_bank_details) && (int)$invoice->print_bank_details !== 1)
    ) {
        return null;
    }

    $CI = &get_instance();

    if (!$CI->db->table_exists('tblbankaccounts')) {
        return null;
    }

    return $CI->db
        ->select('tblbankaccounts.*, tblcurrencies.name as currency_name')
        ->from('tblbankaccounts')
        ->join('tblcurrencies', 'tblcurrencies.id = tblbankaccounts.currency_id', 'left')
        ->where('tblbankaccounts.id', (int)$invoice->bank_account_id)
        ->get()
        ->row();
}

function get_invoice_bank_details_html($invoice)
{
    $bank = get_invoice_bank_account_details($invoice);

    if (!$bank) {
        return '';
    }

    $bank_name = $bank->full_bank_name !== '' ? $bank->full_bank_name : $bank->bank_nick_name;
    $currency = $bank->currency_code !== '' ? $bank->currency_code : $bank->currency_name;

    $html = '<div class="invoice-bank-details">';
    $html .= '<p class="bold">Bank Details:</p>';
    $html .= '<table cellpadding="2" cellspacing="0" border="0">';
    $html .= '<tr><td width="90"><strong>Title:</strong></td><td>' . html_escape($bank->title) . '</td></tr>';
    $html .= '<tr><td width="90"><strong>Bank Name:</strong></td><td>' . html_escape($bank_name) . '</td></tr>';
    $html .= '<tr><td width="90"><strong>Currency:</strong></td><td>' . html_escape($currency) . '</td></tr>';
    $html .= '<tr><td width="90"><strong>Swift:</strong></td><td>' . html_escape($bank->swift) . '</td></tr>';
    $html .= '<tr><td width="90"><strong>Acc #:</strong></td><td>' . html_escape($bank->iban) . '</td></tr>';
    $html .= '</table>';
    $html .= '</div>';

    return $html;
}

function get_receipt_invoice_payment_mode_id($receipt_type, $fallback = 1)
{
    $receipt_type = strtolower(trim((string) $receipt_type));

    $mode_names = [
        'cash' => ['Cash'],
        'cheque' => ['Cheque', 'Check', 'Bank'],
        'bank transfer' => ['Bank Transfer', 'Bank'],
        'stripe' => ['Stripe', 'Credit Card', 'Card'],
    ];

    if (!isset($mode_names[$receipt_type])) {
        return $fallback;
    }

    $CI = &get_instance();
    $payment_modes = $CI->db
        ->select('id, name')
        ->get('tblinvoicepaymentsmodes')
        ->result_array();

    foreach ($mode_names[$receipt_type] as $mode_name) {
        foreach ($payment_modes as $payment_mode) {
            if (strtolower(trim($payment_mode['name'])) === strtolower($mode_name)) {
                return $payment_mode['id'];
            }
        }
    }

    return $fallback;
}

function register_custom_zoho_settings($data)
{
    if (!isset($data['settings'])) {
        return $data;
    }

    add_option('zoho_refresh_token', '', 1);
    add_option('zoho_api_domain', 'https://www.zohoapis.com', 1);

    $data = maybe_exchange_zoho_auth_code_for_tokens($data);

    if (!isset($data['settings']['receipt_deposit_banks'])) {
        return $data;
    }

    add_option('receipt_deposit_banks', get_receipt_deposit_banks_setting_json(), 1);

    $banks = normalize_receipt_deposit_banks($data['settings']['receipt_deposit_banks']);
    if (empty($banks)) {
        $banks = get_receipt_deposit_banks(true);
    }

    $data['settings']['receipt_deposit_banks'] = json_encode($banks, JSON_PRETTY_PRINT);

    return $data;
}

function maybe_exchange_zoho_auth_code_for_tokens($data)
{
    $settings = $data['settings'];

    if (empty($settings['zoho_auth_code'])
        || empty($settings['zoho_client_id'])
        || empty($settings['zoho_client_secret'])) {
        return $data;
    }

    $current_auth_code = get_option('zoho_auth_code');
    $current_refresh_token = get_option('zoho_refresh_token');

    if ($settings['zoho_auth_code'] === $current_auth_code && $current_refresh_token != '') {
        return $data;
    }

    $params = [
        'code' => $settings['zoho_auth_code'],
        'client_id' => $settings['zoho_client_id'],
        'client_secret' => $settings['zoho_client_secret'],
        'grant_type' => 'authorization_code',
    ];

    if (!empty($settings['zoho_redirect_uri'])) {
        $params['redirect_uri'] = $settings['zoho_redirect_uri'];
    }

    $url = 'https://accounts.zoho.com/oauth/v2/token?' . http_build_query($params);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $token = json_decode($response);

    if ($http_code == 200 && isset($token->access_token)) {
        $data['settings']['zoho_access_token'] = $token->access_token;

        if (isset($token->refresh_token) && $token->refresh_token != '') {
            $data['settings']['zoho_refresh_token'] = $token->refresh_token;
        }

        if (isset($token->api_domain) && $token->api_domain != '') {
            $data['settings']['zoho_api_domain'] = $token->api_domain;
        }
    }

    return $data;
}

/**
 * @param string $userid
 * @return string
 */
function get_staff_info_signature($userid = '')
{
    $_userid = get_staff_user_id();
    if ($userid !== '') {
        $_userid = $userid;
    }
    $CI =& get_instance();
    $CI->db->where('staffid', $_userid);
    $staff = $CI->db->select('staffid,email_signature,email_signature_image')->from('tblstaff')->get()->row();
    if ($staff) {
        return $staff;
    } else {
        return '';
    }
}

/**
 * Fetches custom pdf logo url for pdf or use the default logo uploaded for the company
 * Additional statements applied because this function wont work on all servers. All depends how the server is configured.
 * @return [type] [description]
 */
function pdf_email_signature($id, $signature)
{
    $cimg = base_url() . "uploads/staff_profile_images/" . $id . "/" . $signature;

    $width = 100;

    if ($cimg != '') {
        $logo_url = '<img   width="' . $width . 'px" src="' . $cimg . '">';
    }

    return do_action('pdf_logo_url', $logo_url);
}

/**
 * @return mixed
 */
function get_wisdom_stamp()
{

    $cimg = get_wisdom_stamp_link();
    $width = 150;

    if ($cimg != '') {
        $logo_url = '<img  width="' . $width . 'px" src="' . $cimg . '">';
    }

    return do_action('pdf_logo_url', $logo_url);
}

/**
 * @return string
 */
function get_wisdom_stamp_link()
{

    return base_url() . "uploads/company/wisdom-stamp.png";
}

/**
 * Prepare general invoice pdf
 * @param  object $invoice Invoice as object with all necessary fields
 * @return mixed object
 */
function receipt_pdf($data, $tag = '')
{
    $CI =& get_instance();
    load_pdf_language($data['receipts']->receipt_client_id);
    $CI->load->library('pdf');
    $receipt_number = format_receipt_number($data['receipts']->receipt_num);
    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');

    if ($font_size == '') {
        $font_size = 10;
    }

    $CI->load->model('payment_modes_model');
    $payment_modes = $CI->payment_modes_model->get();

    // In case user want to include {invoice_number} in PDF offline mode description
    foreach ($payment_modes as $key => $mode) {
        if (isset($mode['description'])) {
            $payment_modes[$key]['description'] = str_replace('{invoice_number}', $receipt_number, $mode['description']);
        }
    }

    $formatArray = get_pdf_format('pdf_format_invoice');
    if (!file_exists(APPPATH . 'libraries/Invoice_pdf.php')) {
        $pdf = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false, false, 'invoice');
    } else {
        include_once(APPPATH . 'libraries/Invoice_pdf.php');
        $pdf = new receipt_pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false, false);
    }

    if (defined('APP_PDF_MARGIN_LEFT') && defined('APP_PDF_MARGIN_TOP') && defined('APP_PDF_MARGIN_RIGHT')) {
        $pdf->SetMargins(APP_PDF_MARGIN_LEFT, APP_PDF_MARGIN_TOP, APP_PDF_MARGIN_RIGHT);
    }

    $pdf->SetTitle($receipt_number);

    $pdf->SetAutoPageBreak(true, (defined('APP_PDF_MARGIN_BOTTOM') ? APP_PDF_MARGIN_BOTTOM : PDF_MARGIN_BOTTOM));

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->setImageScale(1.53);
    $pdf->setJPEGQuality(100);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);

    if ($CI->input->get('print') == 'true') {
        // force print dialog
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }
    $data['hide_signature'] = true;

    $swap = get_option('swap_pdf_info');
    $CI->load->library('numberword', array(
        'clientid' => $data['receipts']->receipt_client_id
    ));

    $invoice = do_action('invoice_html_pdf_data', $data);

    if (file_exists(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_receipt_preview_template.php')) {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_receipt_preview_template.php');
    } else {
        include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/receipt_preview_template.php');
    }

    if (ob_get_length() > 0 && ENVIRONMENT == 'production') {
        ob_end_clean();
    }

    return $pdf;
}

/**
 * @return int
 */
function update_zoho_id($table, $primary_field, $primary_field_val, $zoho_field, $zoho_id)
{
    $CI = &get_instance();
    $CI->db->where($primary_field, $primary_field_val);
    return $CI->db->update($table, [$zoho_field => $zoho_id]);
}

/**
 * Merge fields for Receipts
 * @param  mixed $invoice_id invoice id
 * @param  mixed $payment_id invoice id
 * @return array
 */
function get_receipt_merge_fields($receipt_id, $payment_id = false)
{
    $fields = array();
    $CI =& get_instance();
    $CI->db->where('receipt_id', $receipt_id);
    $receipt = $CI->db->get('tblreciepts')->row();

    if (!$receipt) {
        return $fields;
    }

    $CI->db->where('id', $receipt->receipt_currency);
    $symbol = $CI->db->get('tblcurrencies')->row()->symbol;

    $fields['{payment_total}'] = '';
    $fields['{payment_date}'] = '';

    if ($payment_id) {
        $CI->db->where('id', $payment_id);
        $payment = $CI->db->get('tblinvoicepaymentrecords')->row();

        $fields['{payment_total}'] = format_money($payment->amount, $symbol);
        $fields['{payment_date}'] = _d($payment->date);
    }

    $fields['{receipt_sale_agent}'] = get_staff_full_name($receipt->reciept_owner);
    $fields['{receipt_total}'] = format_money($receipt->receipt_amount, $symbol);
    $fields['{receipt_id}'] = $receipt_id;
    $fields['{receipt_link}'] = site_url('admin/receipts/details/' . $receipt_id);
    $fields['{receipt_number}'] = format_receipt_number($receipt->receipt_num);
    $fields['{receipt_chequeDate}'] = _d($receipt->receipt_cheque_date);
    $fields['{receipt_bank}'] = _d($receipt->receipt_bank);
    $fields['{receipt_cheque_num}'] = _d($receipt->receipt_cheque_num);
    $fields['{receipt_date}'] = _d($receipt->receipt_date);
    $fields['{receipt_status}'] = format_invoice_status($receipt->receipt_status, '', false);
    $fields['{receipt_note}'] = _d($receipt->receipt_note);

    /*
        $custom_fields = get_custom_fields('invoice');
        foreach ($custom_fields as $field) {
            $fields['{' . $field['slug'] . '}'] = get_custom_field_value($receipt_id, $field['id'], 'invoice');
        }
    */

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'invoice';
    $hook_data['id'] = $receipt_id;

    $hook_data = do_action('receipt_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}


function get_customer_city($client_id)
{

    $CI =& get_instance();
    $CI->db->where('userid', $client_id);
    $staff = $CI->db->select('*')->from('tblclients')->get()->row();
    if ($staff) {
        return $staff;
    } else {
        return '';
    }
}

/**
 * Resolve client Place of Supply (UAE Emirate code)
 *
 * @param object|array $client
 * @return string (DU, AB, SH, AJ, FU, RA, UM or empty for overseas)
 */
function get_client_place_of_supply($client)
{
    if (empty($client)) {
        return 'DU';
    }

    if (is_array($client)) {
        $client = (object)$client;
    }

    $country_id = isset($client->country) ? (int)$client->country : 0;
    // Country 234 in tblcountries is United Arab Emirates
    if ($country_id > 0 && $country_id !== 234) {
        return '';
    }

    $city = isset($client->city) ? strtolower(trim((string)$client->city)) : '';
    $state = isset($client->state) ? strtolower(trim((string)$client->state)) : '';
    $address = isset($client->address) ? strtolower(trim((string)$client->address)) : '';
    $combined = $city . ' ' . $state . ' ' . $address;

    if (strpos($combined, 'abu dhabi') !== false || strpos($combined, 'abudhabi') !== false || strpos($combined, 'al ain') !== false || strpos($combined, 'alain') !== false || strpos($combined, 'mussafah') !== false || strpos($combined, 'musaffah') !== false) {
        return 'AB';
    }
    if (strpos($combined, 'sharjah') !== false || strpos($combined, 'shj') !== false) {
        return 'SH';
    }
    if (strpos($combined, 'ajman') !== false) {
        return 'AJ';
    }
    if (strpos($combined, 'fujairah') !== false || strpos($combined, 'dibba') !== false) {
        return 'FU';
    }
    if (strpos($combined, 'ras al khaimah') !== false || strpos($combined, 'rak') !== false || strpos($combined, 'rasalkhaimah') !== false) {
        return 'RA';
    }
    if (strpos($combined, 'umm al quwain') !== false || strpos($combined, 'uaq') !== false || strpos($combined, 'ummalquwain') !== false) {
        return 'UM';
    }

    return 'DU';
}

/**
 * Resolve client Tax Treatment for Zoho Books
 *
 * @param object|array $client
 * @return string (vat_registered, vat_not_registered, gcc_vat_registered, gcc_vat_not_registered, non_gcc)
 */
function get_client_tax_treatment($client)
{
    if (empty($client)) {
        return 'vat_not_registered';
    }

    if (is_array($client)) {
        $client = (object)$client;
    }

    $has_vat = !empty($client->vat);
    $country_id = isset($client->country) ? (int)$client->country : 0;

    // GCC countries in tblcountries: Bahrain(18), Kuwait(118), Oman(166), Qatar(179), Saudi Arabia(194)
    $gcc_country_ids = [18, 118, 166, 179, 194];

    if ($country_id > 0 && $country_id !== 234) {
        if (in_array($country_id, $gcc_country_ids)) {
            return $has_vat ? 'gcc_vat_registered' : 'gcc_vat_not_registered';
        }
        return 'non_gcc';
    }

    return $has_vat ? 'vat_registered' : 'vat_not_registered';
}

