<?php init_head();
$used_advance = 0;
$added_advance = 0;
$previous_advance_id = 0;
if ($cashAdvance <> null) {
    $used_advance = $cashAdvance->withdraw;
    $added_advance = $cashAdvance->amount;
    $previous_advance_id = $cashAdvance->id;
}
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<div id="wrapper" class="customer_profile">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (isset($client) && $client->active == 0) { ?>
                    <div class="alert alert-warning">
                        <?php echo _l('customer_inactive_message'); ?>
                        <br/>
                        <a href="<?php echo admin_url('clients/mark_as_active/' . $client->userid); ?>"><?php echo _l('mark_as_active'); ?></a>
                    </div>
                <?php } ?>
                <?php if (isset($client) && $client->leadid != NULL) { ?>
                    <div class="alert alert-info">
                        <a href="#"
                           onclick="init_lead(<?php echo $client->leadid; ?>); return false;"><?php echo _l('customer_from_lead', _l('lead')); ?></a>
                    </div>
                <?php } ?>
                <?php if (isset($client) && (!has_permission('customers', '', 'view') && is_customer_admin($client->userid))) { ?>
                    <div class="alert alert-info">
                        <?php echo _l('customer_admin_login_as_client_message', get_staff_full_name(get_staff_user_id())); ?>
                    </div>
                <?php } ?>
            </div>

            <form action="<?= base_url() ?>admin/receipts/update/<?= $receipt_id; ?>" method="post"
                  accept-charset="utf-8"
                  novalidate="novalidate">
                <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-mtop"><?php echo _l('payment_details'); ?></h4>
                        <span class="label label-success pull-right s-status"> Receipt Number: <?= $receipts->receipt_num; ?></span>
                        <hr class="hr-panel-heading">
                        <div class="col-md-3">
                            <div class="form-group">
                                <small class="req text-danger">*</small>
                                <label for="customer"><?php echo _l('invoice_select_customer'); ?></label>
                                <select required id="customer" class="selectpicker form-control" data-live-search="true"
                                        name="data[client_id]">
                                    <option value=""><?= _l('client_select_title') ?></option>
                                    <?php
                                    foreach ($clients as $client) {
                                        $selected = "";
                                        if ($receipts->receipt_client_id == $client['userid']) {
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="<?= $client['userid'] ?>" <?= $selected; ?> ><?= $client['company']; ?> </option>
                                        <?php
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <?php if (is_admin()) { ?>
                            <div class="col-md-3">
                                <?php echo render_select('data[owner]', $staff, array('staffid', array('firstname', 'lastname')), 'Owner', $receipts->reciept_owner, array('data-width' => '100%', 'data-none-selected-text' => 'All')); ?>
                            </div>
                        <?php } ?>
                        <div class="col-md-3">
                            <div class="form-group"><label for="date" class="control-label">
                                    <small class="req text-danger">*</small>
                                    <?= _l('receipt_date'); ?>
                                </label>
                                <div class="input-group date">
                                    <input required type="text" id="date" name="data[date]"
                                           class="form-control datepicker" value="<?= $receipts->receipt_date; ?>">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar calendar-icon"></i>
                                    </div>
                                </div>
                                <input name="data[receipt_id]" type="hidden" value="<?= $receipts->receipt_id; ?>">
                                <input name="data[receipt_num]" type="hidden" value="<?= $receipts->receipt_num; ?>">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <small class="req text-danger">*</small>
                                <label for="amount" class="control-label"><?= _l('receipt_amount'); ?></label>
                                <input required type="text" id="amount" name="data[amount]" class="form-control"
                                       value="<?= $receipts->receipt_amount; ?>">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="SlipNo" class="control-label"><?= _l('slip_number') ?></label>
                                <input required type="text" id="SlipNo" name="data[slip_no]" class="form-control"
                                       value="<?= $receipts->receipt_slip_no; ?>">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="receipt_type"><?= _l('receipt_type'); ?></label>
                                <select id="receipt_type" name="data[type]" class="form-control">
                                    <option value="Cash" <?= ($receipts->receipt_type == 'Cash') ? 'selected' : ''; ?> >
                                        Cash
                                    </option>
                                    <option value="Cheque" <?= ($receipts->receipt_type == 'Cheque') ? 'selected' : ''; ?> >
                                        Cheque
                                    </option>
                                    <option value="Bank Transfer" <?= ($receipts->receipt_type == 'Bank Transfer') ? 'selected' : ''; ?> >
                                        Bank Transfer
                                    </option>
                                     <option value="Stripe" <?= ($receipts->receipt_type == 'Stripe') ? 'selected' : ''; ?> >
                                        Stripe
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div id="cheque_date">
                            <?php
                            if ($receipts->receipt_type == 'Cheque') {
                                ?>
                                <div class="col-md-3">
                                    <div class="form-group"><label for="date" class="control-label">
                                            <small class="req text-danger">*</small>
                                            Cheque Date</label>
                                        <div class="input-group date">
                                            <input type="text" required="" id="date"
                                                   name="data[cheque_date]"
                                                   class="form-control datepicker chk_date"
                                                   value="<?= $receipts->receipt_cheque_date; ?>">
                                            <div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"><label for="date" class="control-label">
                                            <small class="req text-danger">*</small>
                                            Cheque number</label>
                                        <input required="" type="text" id="SlipNo"
                                               name="data[cheque_num]" class="form-control"
                                               value="<?= $receipts->receipt_cheque_num; ?>"></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"><label for="date" class="control-label">
                                            <small class="req text-danger">*</small>
                                            Bank</label>
                                        <input required="" type="text" id="SlipNo"
                                               name="data[bank_name]"
                                               class="form-control" value="<?= $receipts->receipt_bank; ?>"></div>
                                </div>
                                <?php
                            }
                            ?>
                            <input type="hidden" id="use_advance" name="data[use_advance]" class="form-control"
                                   value="<?= $used_advance; ?>">
                            <input type="hidden" id="add_advance" name="data[add_advance]" class="form-control"
                                   value="<?= $added_advance; ?>">
                            <input type="hidden" id="add_advance" name="data[previous_advance_entry]"
                                   class="form-control"
                                   value="<?= $previous_advance_id; ?>">
                        </div>
                        <div id="bank_dropdown">
                            <?php
                            if ($receipts->receipt_type == 'Cheque' || $receipts->receipt_type == 'Bank Transfer') {
                                ?>
                                <div class="col-md-3">
                                    <div class="form-group">
	                                        <label for="type">Deposit To</label>
	                                        <select id="deposit_to" name="data[bank]" class="form-control">
	                                            <?= get_receipt_deposit_bank_options($receipts->deposit_bank, $receipts->receipt_currency); ?>
	                                        </select>
                                    </div>
                                </div>

                            <?php } ?>

                        </div>

	                        <div class="col-md-3">
	                            <div class="form-group">
	                                <label for="receipt_currency_display"><?= _l('receipt_currency'); ?></label>
	                                <?php $selected_currency_display = ''; ?>
	                                <div class="receipt-currency-select-wrapper hide">
	                                <select id="receipt_currency" name="data[currency]" class="form-control">
	                                    <?php
	                                    foreach ($currencies as $currency) {
	                                        $selected = "";
	                                        if ($receipts->receipt_currency == $currency['id']) {
	                                            /* if ($currency['id'] == $default_currency) {*/
	                                            $selected = 'selected';
	                                            $selected_currency_display = get_receipt_currency_code($currency['id']);
	                                            if ($selected_currency_display == '') {
	                                                $selected_currency_display = $currency['name'];
	                                            }
	                                        }
	                                        echo '<option value="' . $currency['id'] . '" data-currency-code="' . html_escape(get_receipt_currency_code($currency['id'])) . '"' . $selected . '>' . $currency['name'] . '</option>';
	                                    } ?>
	                                </select>
	                                </div>
	                                <input type="text" id="receipt_currency_display" class="form-control" value="<?= html_escape($selected_currency_display); ?>" readonly>
	                                <p id="receipt_currency_notice" class="text-muted mtop5"></p>
	                            </div>
	                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="deposited_verified"><?= _l('receipt_deposited_verified'); ?></label>
                                <select id="deposited_verified" name="data[status]"
                                        class="form-control">
                                    <option value="created"><?= _l('receipt_created'); ?></option>
                                    <?php if (is_admin() || has_permission('receipt_handover', '', 'edit')) {
                                        ?>
                                        <option value="handover" <?= ($receipts->receipt_status == 'handover') ? 'selected' : ''; ?>><?= _l('receipt_handover'); ?></option>
                                        <?php
                                    } ?>

                                    <?php if (is_admin() || has_permission('receipt_deposit', '', 'edit')) {
                                        ?>
                                        <option value="deposited" <?= ($receipts->receipt_status == 'deposited') ? 'selected' : ''; ?>><?= _l('receipt_deposited'); ?></option>
                                        <?php
                                    } ?>

                                    <?php if (is_admin() || has_permission('receipt_verify', '', 'edit')) {
                                        ?>
                                        <option value="verified" <?= ($receipts->receipt_status == 'verified') ? 'selected' : ''; ?> ><?= _l('receipt_verified'); ?></option>
                                        <?php
                                    } ?>
                                </select>


                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="note" class="control-label"><?= _l('receipt_note'); ?></label>
                                <?php echo render_textarea('data[note]', '', $receipts->receipt_note, array(), array(), '', 'form-control'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" style="padding-top: 20px;">
                                <label class="check-label"> <input type="checkbox" name="data[adjustment]"
                                                                   class="padding-10"
                                                                   value="1" <?= ($receipts->adjustment == 1) ? 'checked' : ''; ?>>
                                    Adjustment</label>
                               <br> <label class="check-label"> <input type="checkbox" name="data[adjustment]"
                                                                   class="padding-10"
                                                                   value="2" <?= ($receipts->adjustment == 2) ? 'checked' : ''; ?>>
                                    Out of Book</label>
                                <br> <label class="check-label"> <input type="checkbox" name="data[adjustment]"
                                                                   class="padding-10"
                                                                   value="3" <?= ($receipts->adjustment == 3) ? 'checked' : ''; ?>>
                                    Bad Debts</label>
                            </div>
                        </div>

                    </div>
                </div>

	                <div class="panel_s">
	                    <div class="panel-body">
	                        <h6 class="no-mtop">
	                            <?= _l('advance_available_text'); ?>
	                            <span class="label label-success  s-status"
                                  id="CashAdvanceAmount"><?php echo $receipts->advance_amount; ?></span> <br/>

                        </h6>
                        <table class="table table-striped " id="">
                            <thead class="thead-inverse">
                            <tr role="row">
                                <th><?= _l('receipt_change_to_tax_invoice'); ?></th>
                                <th><?= _l('receipt_date'); ?></th>
                                <th><?= _l('client_invoice_number_table_heading'); ?></th>
                                <th><?= _l('client_amount_table_heading'); ?></th>
                                <th><?= _l('invoice_amount_due'); ?></th>
                                <th><?= _l('invoice_discount'); ?></th>
                                <th><?= _l('invoice_payment_table_number_heading'); ?></th>
                            </tr>
                            </thead>
                            <tbody id="invoices_data">
                            <?php
                            if (count($invoices) > 0) {

                                $i = 0;
                                $total_payable = 0;
                                $total_due = 0;
                                $item_amount = 0;
                                foreach ($invoices as $item) {

                                    $total = 0;
                                    $total_amount = $this->receipts_model->getInvoicesTotal($item->invoiceid);

                                    if ($total_amount <> null) {
                                        $total = $total_amount->total;
                                    }
                                    $total_payable = $total_payable + $total;
                                    $amount_due = get_invoice_total_left_to_pay($item->invoiceid, $total);
                                    $inv_data = $this->invoices_model->get($item->invoiceid);
                                    $total_due = $total_due + $amount_due;
                                    $item_amount = $item->amount + $item_amount;

                                    ?>
                                    <tr>
                                        <input name="invoice[<?= $i; ?>][paymentmode]" type="hidden" value="1">
                                        <input name="invoice[<?= $i; ?>][paymentrecordid]" type="hidden"
                                               value="<?= $item->id; ?>">
                                        <td>
                                            <?php
                                            if (getInvoiceType($item->invoiceid) == 'performa') {
                                                echo '<input name="invoice[' . $i . '][change_to_tax_invoice]" type="checkbox" value="1" />';
                                            } ?>
                                        </td>
                                        <td>
                                            <input name="invoice[<?= $i; ?>][paymentmethod]" type="hidden"
                                                   value="1"><?= $inv_data->date; ?>
                                        </td>
                                        <td>
                                            <input name="invoice[<?= $i; ?>][invoiceid]" type="hidden"
                                                   value="<?= $item->invoiceid; ?>">
                                            <a href="<?=  admin_url('/invoices/list_invoices#' . $item->invoiceid);?>" target="_blank">
                                                <?= format_invoice_number($item->invoiceid); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <input name="invoice[<?= $i; ?>][total]" class="form-control" type="text"
                                                   value="<?= $total; ?>" style="width: 100px;">
                                        </td>
                                        <td>
                                            <input name="invoice[<?= $i; ?>][amount_due]" class="form-control"
                                                   type="text" id="amount_due<?= $item->invoiceid; ?>"
                                                   value="<?= $amount_due; ?>"
                                                   style="width: 100px;">
                                        </td>
                                        <td>
                                            <input name="invoice[<?= $i; ?>][discount]" class="form-control" type="text"
                                                   value="<?= $item->discount; ?>"
                                                   style="width: 100px;">
                                        </td>
                                        <td>
                                            <input name="invoice[<?= $i; ?>][amount]"
                                                   class="form-control payment_amount" type="text"
                                                   value="<?= $item->amount; ?>" id="<?= $item->invoiceid; ?>"
                                                   style="width: 100px;">
                                        </td>
                                    </tr>
                                    <?php $i++;
                                } ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td><h5>Total Payable: <?= $total_payable; ?></h5></td>
                                    <td><h5>Total Due: <?= $total_due; ?></h5></td>
                                    <td>&nbsp;</td>
                                    <td><h5>Total Amount: <span id="amount_total"><?= $item_amount; ?></span></h5></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="button" class="btn btn-primary" id="savePayments" value="Save">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>

<script>
    $('input[type="checkbox"]').on('change', function() {
        $('input[type="checkbox"]').not(this).prop('checked', false);
    });
	    $(document).ready(function () {
	        var receiptDepositBanks = <?php echo json_encode(get_receipt_deposit_banks()); ?>;
	        var receiptFormReady = false;
	        var receiptInvoicesRequestId = 0;

        function receiptDepositBankLabel(bank) {
            var label = bank.name || '';
            var meta = [];

            if (bank.currency_code) {
                meta.push(bank.currency_code);
            }

            if (bank.zoho_name && bank.zoho_name !== bank.name) {
                meta.push('Zoho: ' + bank.zoho_name);
            }

            return meta.length ? label + ' (' + meta.join(' - ') + ')' : label;
        }

	        function getReceiptCurrencyCode() {
	            return $.trim($("#receipt_currency option:selected").data('currency-code') || $("#receipt_currency option:selected").text()).toUpperCase();
	        }

	        function updateReceiptCurrencyDisplay(currencyCode) {
	            var displayValue = currencyCode || $("#receipt_currency option:selected").data('currency-code') || $("#receipt_currency option:selected").text();
	            $("#receipt_currency_display").val($.trim(displayValue || '').toUpperCase());
	        }

	        function emptyReceiptInvoices(message) {
	            $("#invoices_data").html('<tr><td colspan="7">' + (message || 'No Receipt Available') + '</td></tr>');
	            $("#total_amount").text('0');
	            $("#total_due").text('0');
	            $("#amount_total").text('0');
	        }

		        function getReceiptDepositBanksForCurrency() {
		            var receiptCurrencyCode = getReceiptCurrencyCode();
		            var banks = [];

		            $.each(receiptDepositBanks, function (_, bank) {
		                var bankCurrencyCode = $.trim(bank.currency_code || '').toUpperCase();

		                if (receiptCurrencyCode && bankCurrencyCode && bankCurrencyCode === receiptCurrencyCode) {
		                    banks.push(bank);
		                }
		            });

		            return banks;
		        }

	        function getReceiptDepositBankOptions(selected) {
	            var bankOptions = '';
	            var found = false;
		            var banks = getReceiptDepositBanksForCurrency();
		            var receiptCurrencyCode = getReceiptCurrencyCode();

		            $.each(banks, function (_, bank) {
	                var selectedAttr = selected && selected === bank.code ? ' selected' : '';
	                if (selectedAttr) {
	                    found = true;
	                }
	                bankOptions += '<option value="' + $('<div>').text(bank.code).html() + '"' + selectedAttr + '>' + $('<div>').text(receiptDepositBankLabel(bank)).html() + '</option>';
	            });

	            if (!bankOptions) {
		                return '<option value="">No bank account added for ' + $('<div>').text(receiptCurrencyCode || 'selected').html() + ' currency</option>';
	            } else if (selected && !found) {
		                $.each(banks, function (_, bank) {
	                    if (bank.code === selected) {
	                        bankOptions += '<option value="' + $('<div>').text(bank.code).html() + '" selected>' + $('<div>').text(receiptDepositBankLabel(bank)).html() + '</option>';
	                        return false;
                    }
                });
            }

            return '<option value="">Select Bank</option>' + bankOptions;
        }

        function buildDepositBankHtml(selected) {
            var bankHtml = '';

            bankHtml += '<div class="col-md-3">';
            bankHtml += '<div class="form-group">';
            bankHtml += '<label for="type">Select Bank</label>';
            bankHtml += '<select id="deposit_to" name="data[bank]" class="form-control">';
            bankHtml += getReceiptDepositBankOptions(selected || '');
            bankHtml += ' </select>';
            bankHtml += '<p id="receipt_bank_currency_error" class="text-danger mtop5" style="display:none;"></p>';
            bankHtml += '</div>';
            bankHtml += '</div>';

            return bankHtml;
        }

        function getSelectedReceiptBank(bankCode) {
            var selectedBank = null;

            $.each(receiptDepositBanks, function (_, bank) {
                if (bank.code === bankCode) {
                    selectedBank = bank;
                    return false;
                }
            });

            return selectedBank;
        }

	        function validateReceiptBankCurrency(showAlert, enforceDisable) {
	            if (typeof enforceDisable === 'undefined') {
	                enforceDisable = true;
	            }

	            var type = $("#receipt_type").val();
	            var bankCode = $("#deposit_to").val();
	            var selectedBank = getSelectedReceiptBank(bankCode);
	            var $error = $("#receipt_bank_currency_error");

            if (type == 'Cash' || type == 'Stripe' || !bankCode || !selectedBank || !selectedBank.currency_code) {
                $error.hide().text('');
                $("#savePayments").prop('disabled', false);
                return true;
            }

            var receiptCurrencyCode = getReceiptCurrencyCode();
            var bankCurrencyCode = $.trim(selectedBank.currency_code).toUpperCase();

	            if (receiptCurrencyCode && bankCurrencyCode && receiptCurrencyCode !== bankCurrencyCode) {
	                var message = 'Selected bank currency ' + bankCurrencyCode + ' does not match receipt currency ' + receiptCurrencyCode + '.';
	                if (enforceDisable) {
	                    $error.text(message).show();
	                    $("#savePayments").prop('disabled', true);
	                } else {
	                    $error.hide().text('');
	                    $("#savePayments").prop('disabled', false);
	                }

	                if (showAlert && typeof alert_float === 'function') {
	                    alert_float('warning', message);
                }

                return false;
            }

            $error.hide().text('');
            $("#savePayments").prop('disabled', false);
            return true;
        }

        function selectReceiptCurrencyFromBank(bankCode) {
            if (!validateReceiptBankCurrency(true)) {
                return;
            }

            if (!bankCode) {
                return;
            }

            loadClientInvoices();
        }

	        function refreshReceiptBankCurrencyValidation(enforceDisable) {
	            validateReceiptBankCurrency(false, enforceDisable);
	        }

        function updateReceiptCurrencyNotice(response) {
            var $notice = $("#receipt_currency_notice");

            if (!$notice.length) {
                return;
            }

            if (response && response.message) {
                $notice
                    .text(response.message)
                    .toggleClass('text-warning', response.source === 'base')
                    .toggleClass('text-muted', response.source !== 'base')
                    .show();
            } else {
                $notice.text('').hide();
            }
        }

	        function refreshDepositBanksForCurrency() {
	            var type = $("#receipt_type").val();

	            if (type != 'Cash' && type != 'Stripe') {
	                $("#bank_dropdown").html(buildDepositBankHtml($("#deposit_to").val()));
		                if (getReceiptDepositBanksForCurrency().length === 0) {
		                    $("#receipt_bank_currency_error").text('No bank account added for ' + (getReceiptCurrencyCode() || 'selected') + ' currency.').show();
		                    $("#savePayments").prop('disabled', true);
		                } else {
		                    refreshReceiptBankCurrencyValidation(receiptFormReady);
		                }
	            } else {
	                $("#savePayments").prop('disabled', false);
	            }
        }

        function enforceCustomerCurrency(callback) {
            var client = $("#customer").val();

            if (client != "" && client != "undefined") {
                applyCustomerCurrency(client, function () {
                    refreshDepositBanksForCurrency();
                    if (typeof callback === 'function') {
                        callback();
                    }
                });
            } else {
                refreshDepositBanksForCurrency();
                if (typeof callback === 'function') {
                    callback();
                }
            }
        }

        function applyCustomerCurrency(client, callback) {
            if (client == "" || client == "undefined") {
                updateReceiptCurrencyNotice(null);
                if (typeof callback === 'function') {
                    callback();
                }
                return;
            }

            $.ajax({
                url: '<?php echo base_url();?>admin/receipts/client_currency/' + client,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    updateReceiptCurrencyNotice(response);

	                    if (response && response.success && response.id) {
	                        $("#receipt_currency").val(response.id);
	                        if ($("#receipt_currency").data('selectpicker')) {
	                            $("#receipt_currency").selectpicker('refresh');
	                        }
	                        updateReceiptCurrencyDisplay(response.currency_code || response.name);
	                    }

                    if (typeof callback === 'function') {
                        callback();
                    }
                },
                error: function () {
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            });
        }

	        function loadClientInvoices() {
		            var client = $("#customer").val();
		            var currency = $("#receipt_currency").val();
		            var requestId = ++receiptInvoicesRequestId;

		            if (client == "" || client == "undefined") {
		                emptyReceiptInvoices();
		                return;
		            }

		            $.ajax({
		                url: '<?php echo base_url();?>admin/receipts/clients_invoices/' + client,
	                type: 'GET',
	                data: {
	                    currency: currency
	                },
	                success: function (data) {
	                    var obj = null;
	                    try {
                        obj = JSON.parse(data);
                    } catch (ex) {
                        // Invalid JSON (likely an error page). Do not overwrite existing invoice rows.
	                        console.log('clients_invoices: invalid json response', ex, data);
		                        return;
		                    }

		                    if (requestId !== receiptInvoicesRequestId || client !== $("#customer").val() || currency !== $("#receipt_currency").val()) {
	                        return;
	                    }

	                    if (typeof obj.html !== 'undefined') {
	                        if (obj.html === '') {
	                            emptyReceiptInvoices();
	                        } else {
	                            $("#invoices_data").html(obj.html);
	                            $("#total_amount").html(obj.total_payable);
	                            $("#total_due").html(obj.amount_due);
	                        }
	                    }
	                },
                error: function (e) {
                    console.log(e);
                }
            });
        }

        var html = '';

        html += '<div class="col-md-3">';
        html += '   <div class="form-group"><label for="date" class="control-label"><small class="req text-danger">*</small>Cheque Date</label>';
        html += '       <div class="input-group date">';
        html += '           <input type="text" required id="date" name="data[cheque_date]" class="form-control datepicker" value="<?=date("Y-m-d"); ?>">';
        html += '           <div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i></div>';
        html += '       </div>';
        html += '   </div>';
        html += '</div>';

        html += '<div class="col-md-3">';
        html += '   <div class="form-group"><label for="date" class="control-label"><small class="req text-danger">*</small>Cheque number</label>';
        html += '           <input required type="text" id="SlipNo" name="data[cheque_num]" class="form-control" value="">';
        html += '   </div>';
        html += '</div>';


        html += '<div class="col-md-3">';
        html += '   <div class="form-group"><label for="date" class="control-label"><small class="req text-danger">*</small>Bank</label>';
        html += '           <input required type="text" id="SlipNo" name="data[bank_name]" class="form-control" value="">';
        html += '   </div>';
        html += '</div>';

	        $("#customer").change(function (event) {
	            event.preventDefault();
	            var client = $(this).val();
	            emptyReceiptInvoices('Loading invoices...');
	            enforceCustomerCurrency(loadClientInvoices);

            if (client != "" && client != "undefined") {
                $.ajax({
                    url: '<?php echo base_url();?>admin/receipts/get_clients_advance_cash/' + client,
                    type: 'GET',
                    data: 'twitterUsername=jquery4u',
                    success: function (data) {
                        //called when successfulc
                        // console.log(data);
                        $("#CashAdvanceAmount").html(data);
                        $("#advance_chkbx").removeClass("hidden");
                    },
                    error: function (e) {
                        console.log(e);
                    }
                });
            }

        });


        $("#receipt_type").change(function (event) {

            event.preventDefault();
            var type = $(this).val();

            if (type == 'Cheque' || type == 'cheque') {
                $("#cheque_date").html(html);
            } else {
                $("#cheque_date").html("");
            }

	            if(type == 'Cash' || type == 'Stripe'){
	                $("#bank_dropdown").html('');
	                $("#savePayments").prop('disabled', false);
	            }else{
	                refreshDepositBanksForCurrency();
	            }

        });

	        $("#receipt_currency").change(function () {
	            updateReceiptCurrencyDisplay();
	            enforceCustomerCurrency(loadClientInvoices);
	        });

        $(document).on('change', '#deposit_to', function () {
            selectReceiptCurrencyFromBank($(this).val());
        });

	        setTimeout(function () {
	            enforceCustomerCurrency(function () {
	                updateReceiptCurrencyDisplay();
	                // If server-side already rendered invoices (editing existing receipt), do not overwrite them.
	                if ($('#invoices_data').children().length === 0) {
                    loadClientInvoices();
                } else {
                    // Ensure totals and currency validation still run
                    receiptFormReady = true;
                }
            });
        }, 0);


        $("#savePayments").click(function (e) {
            e.preventDefault();

            var total_amount = parseFloat($("#amount").val());
            var payments_cleared = parseFloat($("#amount_total").text());
            // console.log(total_amount);
            console.log(payments_cleared);
            var types = $("#receipt_type").val();

            if (types == 'Cheque' || types == 'cheque') {
                var chk_date = $(".chk_date").val();
                if (chk_date == '') {
                    BootstrapDialog.alert({
                        title: 'WARNING',
                        message: 'Check date cannot be empty!',
                        type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                        closable: true, // <-- Default value is false
                        buttonLabel: 'Close',
                    });
                    return false;
                }

            }
            if (types != 'Cash' && types != 'Stripe') {
                if (!validateReceiptBankCurrency(true)) {
                    return false;
                }

                var deposit_to = $("#deposit_to").val();
                if(deposit_to == ''){
                    BootstrapDialog.alert({
                        title: 'WARNING',
                        message: 'Deposit to cannot be empty!',
                        type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                        closable: true, // <-- Default value is false
                        buttonLabel: 'Close',
                    });
                    return false;
                }
            }

            if (total_amount == payments_cleared) {
                $('form').submit();
            }

            if (total_amount > payments_cleared) {
                var advance = parseFloat(total_amount - payments_cleared);

                BootstrapDialog.confirm({

                    title: 'Information',
                    message: 'You have ' + advance + ' addtitional. Do you want to add it as advance?',
                    type: BootstrapDialog.TYPE_PRIMARY, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                    btnCancelLabel: 'No', // <-- Default value is 'Cancel',
                    btnOKLabel: 'Yes', // <-- Default value is 'OK',
                    btnOKClass: 'btn-info', // <-- If you didn't specify it, dialog type will be used,
                    callback: function (result) {
                        // result will be true if button was click, while it will be false if users close the dialog directly.
                        if (result) {
                            $("#add_advance").val(advance);
                            $('form').submit();
                        } else {
                            $("#add_advance").val(0);
                        }
                    }
                });

            }

            if (total_amount < payments_cleared) {

                var cash_advance = parseFloat($("#CashAdvanceAmount").text());
                if (cash_advance > 0) {
                    BootstrapDialog.show({

                        message: 'Total amount is less than invoices total. You have <span class="label label-success  s-status" id="CashAdvanceAmount">' + cash_advance + '</span> advance: <br/><label>Enter Amount to use advance (Max: ' + cash_advance + ')</label><input min="0" max="' + cash_advance + '" type="text" class="form-control" value="0" placeholder="Enter Amount to use Advance" id="advanceInput" style="width: 200px; ">',

                        onhide: function (dialogRef) {

                            var value = dialogRef.getModalBody().find('input[type=text]').val();

                            if (!($.isNumeric(value))) {
                                alert('Please enter correct amount!');
                                return false;
                            }

                            if (value > cash_advance) {
                                alert('You cannot use more advance than available limit');
                                return false;
                            }

                            $("#use_advance").val(value);
                            $("#add_advance").val(0);

                            $('form').submit();
                        },
                        buttons: [{
                            label: 'Confirm',
                            action: function (dialogRef) {
                                dialogRef.close();
                            }
                        }]
                    });
                } else {

                    BootstrapDialog.alert({
                        title: 'WARNING',
                        message: 'Total amount should be equal to invoices payment!',
                        type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                        closable: true, // <-- Default value is false
                        buttonLabel: 'Close',
                    });

                    return false;
                }
            }
        });


        $('form').on({

            keyup: function (e) {

                e.preventDefault();
                var total = 0;

                $(".payment_amount").each(function () {

                    total = parseFloat($(this).val()) + parseFloat(total);
                    // console.log(parseFloat(total));

                });

                if (!isNaN(total)) {
                    $("#amount_total").text(total);
                }
            }

        }, '.payment_amount');


        $(document).on('click', '.use_advance_check', function () {
            if ($(this).is(":checked")) {
                $(document).find('#advanceInput').removeClass('hidden');
            } else {
                $(document).find('#advanceInput').addClass('hidden');
            }
        });


        $(document).on('dblclick', '.payment_amount', function () {

            // $(document).find( ".payment_amount" ).dblclick(function() {
            if ($(this).is('[readonly]')) {
                return;
            }

            var id = $(this).attr('id');
            var amount_due = $(document).find( "#amount_due" + id ).val();
            $(document).find( "#" + id ).val(parseFloat(amount_due));
            var total = 0;

            $(".payment_amount").each(function () {

                total = parseFloat($(this).val()) + parseFloat(total);
                // console.log(parseFloat(total));

            });

            if (!isNaN(total)) {
                $("#amount_total").text(total);
            }
        });

    });


</script>
