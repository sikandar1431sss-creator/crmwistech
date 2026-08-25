<?php init_head(); ?>
<?php
$selected_currency_id = $bank_account ? $bank_account->currency_id : '';
$selected_zoho_account_id = $bank_account ? $bank_account->zoho_account_id : '';
$selected_active = $bank_account ? (int)$bank_account->active : 1;
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-mtop"><?= html_escape($title); ?></h4>
                        <p class="text-muted">Add bank details to receive payments and link them with Zoho.</p>
                        <hr class="hr-panel-heading"/>

                        <?php echo form_open(current_url(), ['id' => 'bank_account_form']); ?>
                        <input type="hidden" name="zoho_account_name" id="zoho_account_name" value="<?= $bank_account ? html_escape($bank_account->zoho_account_name) : ''; ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title" class="control-label"><small class="req text-danger">*</small> Title</label>
                                    <select name="title" id="title" class="form-control">
                                        <?php
                                        $titles = ['Wisdom Information Technology Solutions LLC'];
                                        $selected_title = $bank_account ? $bank_account->title : 'Bank Account';
                                        foreach ($titles as $title_option) {
                                            $selected = $selected_title === $title_option ? ' selected' : '';
                                            echo '<option value="' . html_escape($title_option) . '"' . $selected . '>' . html_escape($title_option) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <?php echo render_input('bank_nick_name', 'Bank Nick Name', $bank_account ? $bank_account->bank_nick_name : '', 'text', ['required' => true, 'placeholder' => 'e.g. Main Bank']); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_input('full_bank_name', 'Full Bank Name', $bank_account ? $bank_account->full_bank_name : '', 'text', ['required' => true, 'placeholder' => 'Enter full bank name']); ?>
                            </div>

                            <div class="col-md-6">
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
                                    <label for="zoho_account_id" class="control-label"><small class="req text-danger">*</small> Zoho Account</label>
                                    <button type="button" class="btn btn-default btn-xs pull-right" id="sync_zoho_bank_accounts">
                                        <i class="fa fa-refresh"></i> Sync Zoho Banks
                                    </button>
                                    <select name="zoho_account_id" id="zoho_account_id" class="form-control" required>
                                        <option value="">Select Zoho account</option>
                                    </select>
                                    <p class="text-muted">Choose a Zoho account to link with this bank.</p>
                                    <p id="zoho_account_currency_error" class="text-danger mtop5" style="display:none;"></p>
                                    <p id="sync_zoho_bank_accounts_status" class="text-muted"></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <?php echo render_input('iban', 'IBAN', $bank_account ? $bank_account->iban : '', 'text', ['required' => true, 'placeholder' => 'Enter IBAN']); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_input('swift', 'Swift', $bank_account ? $bank_account->swift : '', 'text', ['required' => true, 'placeholder' => 'Enter SWIFT code']); ?>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label display-block">Active</label>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="active" id="active" value="1"<?= $selected_active === 1 ? ' checked' : ''; ?>>
                                        <label for="active">Yes</label>
                                    </div>
                                    <p class="text-muted">Toggle switch to set account status.</p>
                                </div>
                            </div>
                        </div>

                        <hr/>

                        <a href="<?= admin_url('bankaccounts'); ?>" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-info pull-right" id="save_bank_account">
                            <i class="fa fa-save"></i> Save Bank Account
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

            if (account.currency_code) {
                meta.push(account.currency_code);
            }

            return meta.length ? label + ' (' + meta.join(' - ') + ')' : label;
        }

        function selectedCurrencyCode() {
            return $.trim($('#currency_id option:selected').data('currency-code') || '').toUpperCase();
        }

        function refreshZohoAccounts() {
            var $zohoAccount = $('#zoho_account_id');
            var options = '<option value="">Select Zoho account</option>';
            var selectedStillAvailable = false;

            $.each(zohoAccounts, function (_, account) {
                var selected = selectedZohoAccountId && selectedZohoAccountId === account.account_id ? ' selected' : '';
                if (selected) {
                    selectedStillAvailable = true;
                }

                options += '<option value="' + $('<div>').text(account.account_id).html() + '" data-name="' + $('<div>').text(account.zoho_name || account.name || '').html() + '"' + selected + '>' + $('<div>').text(zohoAccountLabel(account)).html() + '</option>';
            });

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

            var message = 'Selected Zoho account currency ' + accountCurrency + ' does not match bank currency ' + currencyCode + '.';
            $error.text(message).show();
            $('#save_bank_account').prop('disabled', true);

            if (showAlert && typeof alert_float === 'function') {
                alert_float('warning', message);
            }

            return false;
        }

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
                        $status.removeClass('text-muted text-success').addClass('text-danger').text(response.message || 'Unable to sync Zoho bank accounts.');
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

        refreshZohoAccounts();

        _validate_form($('#bank_account_form'), {
            title: 'required',
            bank_nick_name: 'required',
            full_bank_name: 'required',
            currency_id: 'required',
            zoho_account_id: 'required',
            iban: 'required',
            swift: 'required'
        });
    });
</script>
</body>
</html>
