<?php init_head(); ?>
<?php
$selected_currency_id = $bank_account ? $bank_account->currency_id : '';
$selected_zoho_account_id = $bank_account ? $bank_account->zoho_account_id : '';
$selected_active = $bank_account ? (int)$bank_account->active : 1;
$selected_account_type = ($bank_account && isset($bank_account->account_type) && $bank_account->account_type === 'cash') ? 'cash' : 'bank';
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-mtop"><?= html_escape($title); ?></h4>
                        <p class="text-muted">Add bank or cash account details to receive payments and link them with Zoho.</p>
                        <hr class="hr-panel-heading"/>

                        <?php echo form_open(current_url(), ['id' => 'bank_account_form']); ?>
                        <input type="hidden" name="zoho_account_name" id="zoho_account_name" value="<?= $bank_account ? html_escape($bank_account->zoho_account_name) : ''; ?>">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account_type" class="control-label"><small class="req text-danger">*</small> Account Type</label>
                                    <select name="account_type" id="account_type" class="form-control" required>
                                        <option value="bank"<?= $selected_account_type === 'bank' ? ' selected' : ''; ?>>Bank Account</option>
                                        <option value="cash"<?= $selected_account_type === 'cash' ? ' selected' : ''; ?>>Cash Account</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="title" class="control-label"><small class="req text-danger">*</small> Entity Title</label>
                                    <select name="title" id="title" class="form-control">
                                        <?php
                                        $titles = ['Wisdom Information Technology Solutions LLC'];
                                        $selected_title = $bank_account ? $bank_account->title : 'Wisdom Information Technology Solutions LLC';
                                        foreach ($titles as $title_option) {
                                            $selected = $selected_title === $title_option ? ' selected' : '';
                                            echo '<option value="' . html_escape($title_option) . '"' . $selected . '>' . html_escape($title_option) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="currency_id" class="control-label"><small class="req text-danger">*</small> Currency</label>
                                    <select name="currency_id" id="currency_id" class="form-control" required>
                                        <option value="">Select currency</option>
                                        <?php foreach ($currencies as $currency) { ?>
                                            <?php $selected = (int)$selected_currency_id === (int)$currency['id'] ? ' selected' : ''; ?>
                                            <option value="<?= html_escape($currency['id']); ?>"
                                                    data-currency-code="<?= html_escape(get_receipt_currency_code($currency['id'])); ?>"<?= $selected; ?>>
                                                <?= html_escape($currency['name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_nick_name" class="control-label" id="lbl_bank_nick_name">
                                        <small class="req text-danger">*</small> Nick Name
                                    </label>
                                    <input type="text" id="bank_nick_name" name="bank_nick_name" class="form-control" required
                                           value="<?= $bank_account ? html_escape($bank_account->bank_nick_name) : ''; ?>"
                                           placeholder="e.g. Main Bank / Office Cash">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="full_bank_name" class="control-label" id="lbl_full_bank_name">
                                        <small class="req text-danger">*</small> Full Name / Description
                                    </label>
                                    <input type="text" id="full_bank_name" name="full_bank_name" class="form-control" required
                                           value="<?= $bank_account ? html_escape($bank_account->full_bank_name) : ''; ?>"
                                           placeholder="e.g. Emirates NBD Main Branch / Dubai Office Petty Cash">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="zoho_account_id" class="control-label"><small class="req text-danger">*</small> Linked Zoho Account</label>
                                    <button type="button" class="btn btn-default btn-xs pull-right" id="sync_zoho_bank_accounts">
                                        <i class="fa fa-refresh"></i> Sync Zoho Accounts
                                    </button>
                                    <select name="zoho_account_id" id="zoho_account_id" class="form-control" required>
                                        <option value="">Select Zoho account</option>
                                    </select>
                                    <p class="text-muted">Choose a Zoho account to link with this account.</p>
                                    <p id="zoho_account_currency_error" class="text-danger mtop5" style="display:none;"></p>
                                    <p id="sync_zoho_bank_accounts_status" class="text-muted"></p>
                                </div>
                            </div>

                            <div class="col-md-6" id="iban_wrapper">
                                <?php echo render_input('iban', 'IBAN / Account #', $bank_account ? $bank_account->iban : '', 'text', ['placeholder' => 'Enter IBAN or Account Number']); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6" id="swift_wrapper">
                                <?php echo render_input('swift', 'Swift Code', $bank_account ? $bank_account->swift : '', 'text', ['placeholder' => 'Enter SWIFT code']); ?>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label display-block">Active</label>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="active" id="active" value="1"<?= $selected_active === 1 ? ' checked' : ''; ?>>
                                        <label for="active">Yes</label>
                                    </div>
                                    <p class="text-muted">Toggle switch to set account active status.</p>
                                </div>
                            </div>
                        </div>

                        <hr/>

                        <a href="<?= admin_url('bankaccounts'); ?>" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-info pull-right" id="save_bank_account">
                            <i class="fa fa-save"></i> Save Account
                        </button>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(function () {
        var zohoAccounts = <?php echo json_encode($zoho_accounts); ?>;
        var selectedZohoAccountId = <?php echo json_encode($selected_zoho_account_id); ?>;

        function zohoAccountLabel(account) {
            var label = account.zoho_name || account.name || 'Zoho Account';
            var meta = [];

            if (account.account_type) {
                var typeStr = (account.account_type === 'cash') ? 'Cash' : 'Bank';
                meta.push(typeStr);
            }

            if (account.currency_code) {
                meta.push(account.currency_code);
            }

            return meta.length ? label + ' (' + meta.join(' - ') + ')' : label;
        }

        function selectedCurrencyCode() {
            return $.trim($('#currency_id option:selected').data('currency-code') || '').toUpperCase();
        }

        function selectedAccountType() {
            return $.trim($('#account_type').val() || 'bank').toLowerCase();
        }

        function updateFormFieldsForType() {
            var isCash = (selectedAccountType() === 'cash');

            if (isCash) {
                $('#lbl_bank_nick_name').html('<small class="req text-danger">*</small> Cash Account Nick Name');
                $('#bank_nick_name').attr('placeholder', 'e.g. Petty Cash / Dubai Cash Drawer');
                $('#lbl_full_bank_name').html('<small class="req text-danger">*</small> Full Cash Account Name');
                $('#full_bank_name').attr('placeholder', 'e.g. Dubai Office Petty Cash');
                $('#iban_wrapper').hide();
                $('#swift_wrapper').hide();
                $('#iban').removeAttr('required');
                $('#swift').removeAttr('required');
            } else {
                $('#lbl_bank_nick_name').html('<small class="req text-danger">*</small> Bank Nick Name');
                $('#bank_nick_name').attr('placeholder', 'e.g. Main Bank');
                $('#lbl_full_bank_name').html('<small class="req text-danger">*</small> Full Bank Name');
                $('#full_bank_name').attr('placeholder', 'e.g. Emirates NBD');
                $('#iban_wrapper').show();
                $('#swift_wrapper').show();
                $('#iban').attr('required', true);
                $('#swift').attr('required', true);
            }
        }

        function refreshZohoAccounts() {
            var $zohoAccount = $('#zoho_account_id');
            var currentType = selectedAccountType();
            var options = '<option value="">Select Zoho account</option>';
            var selectedStillAvailable = false;

            // Prioritize matching account_type, but allow all if none matched
            var matchingAccounts = [];
            var otherAccounts = [];

            $.each(zohoAccounts, function (_, account) {
                var accType = (account.account_type || 'bank').toLowerCase();
                var isAccCash = (accType === 'cash' || accType === 'petty_cash');
                var normType = isAccCash ? 'cash' : 'bank';

                if (normType === currentType) {
                    matchingAccounts.push(account);
                } else {
                    otherAccounts.push(account);
                }
            });

            var accountsToRender = matchingAccounts.length > 0 ? matchingAccounts : zohoAccounts;

            $.each(accountsToRender, function (_, account) {
                var selected = selectedZohoAccountId && selectedZohoAccountId === account.account_id ? ' selected' : '';
                if (selected) {
                    selectedStillAvailable = true;
                }

                options += '<option value="' + $('<div>').text(account.account_id).html() + '" data-name="' + $('<div>').text(account.zoho_name || account.name || '').html() + '"' + selected + '>' + $('<div>').text(zohoAccountLabel(account)).html() + '</option>';
            });

            if (matchingAccounts.length > 0 && otherAccounts.length > 0) {
                options += '<optgroup label="Other Accounts">';
                $.each(otherAccounts, function (_, account) {
                    var selected = selectedZohoAccountId && selectedZohoAccountId === account.account_id ? ' selected' : '';
                    if (selected) {
                        selectedStillAvailable = true;
                    }
                    options += '<option value="' + $('<div>').text(account.account_id).html() + '" data-name="' + $('<div>').text(account.zoho_name || account.name || '').html() + '"' + selected + '>' + $('<div>').text(zohoAccountLabel(account)).html() + '</option>';
                });
                options += '</optgroup>';
            }

            $zohoAccount.html(options);

            if (!selectedStillAvailable) {
                $zohoAccount.val('');
            }

            $('#zoho_account_name').val($('#zoho_account_id option:selected').data('name') || '');
            validateZohoAccountCurrency(false);
        }

        function getSelectedZohoAccount(accountId) {
            var selectedAccount = null;

            $.each(zohoAccounts, function (_, account) {
                if (account.account_id === accountId) {
                    selectedAccount = account;
                    return false;
                }
            });

            return selectedAccount;
        }

        function validateZohoAccountCurrency(showAlert) {
            var currencyCode = selectedCurrencyCode();
            var selectedAccount = getSelectedZohoAccount($('#zoho_account_id').val());
            var accountCurrency = selectedAccount ? $.trim(selectedAccount.currency_code || '').toUpperCase() : '';
            var $error = $('#zoho_account_currency_error');

            if (!currencyCode || !accountCurrency || currencyCode === accountCurrency) {
                $error.hide().text('');
                $('#save_bank_account').prop('disabled', false);
                return true;
            }

            var message = 'Selected Zoho account currency ' + accountCurrency + ' does not match account currency ' + currencyCode + '.';
            $error.text(message).show();
            $('#save_bank_account').prop('disabled', true);

            if (showAlert && typeof alert_float === 'function') {
                alert_float('warning', message);
            }

            return false;
        }

        $('#account_type').change(function () {
            updateFormFieldsForType();
            refreshZohoAccounts();
        });

        $('#currency_id').change(function () {
            selectedZohoAccountId = $('#zoho_account_id').val();
            refreshZohoAccounts();
        });

        $('#zoho_account_id').change(function () {
            selectedZohoAccountId = $(this).val();
            $('#zoho_account_name').val($('#zoho_account_id option:selected').data('name') || '');
            validateZohoAccountCurrency(true);
        });

        $('#sync_zoho_bank_accounts').click(function () {
            var $button = $(this);
            var $status = $('#sync_zoho_bank_accounts_status');
            var data = {};

            if (typeof csrfData !== 'undefined') {
                data[csrfData.token_name] = csrfData.hash;
            }

            $button.prop('disabled', true);
            $status.removeClass('text-danger text-success').addClass('text-muted').text('Syncing...');

            $.post(admin_url + 'bankaccounts/sync_zoho_accounts', data)
                .done(function (response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }

                    if (!response.success) {
                        $status.removeClass('text-muted text-success').addClass('text-danger').text(response.message || 'Unable to sync Zoho accounts.');
                        return;
                    }

                    zohoAccounts = response.accounts || [];
                    selectedZohoAccountId = $('#zoho_account_id').val();
                    refreshZohoAccounts();
                    $status.removeClass('text-muted text-danger').addClass('text-success').text(response.message);
                    alert_float('success', response.message);
                })
                .fail(function () {
                    $status.removeClass('text-muted text-success').addClass('text-danger').text('Sync request failed.');
                })
                .always(function () {
                    $button.prop('disabled', false);
                });
        });

        updateFormFieldsForType();
        refreshZohoAccounts();

        _validate_form($('#bank_account_form'), {
            title: 'required',
            account_type: 'required',
            bank_nick_name: 'required',
            full_bank_name: 'required',
            currency_id: 'required',
            zoho_account_id: 'required'
        });
    });
</script>
</body>
</html>

