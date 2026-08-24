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
                                <label for="type"><?= _l('receipt_type'); ?></label>
                                <select id="type" name="data[type]" class="form-control">
                                    <option value="Cash" <?= ($receipts->receipt_type == 'Cash') ? 'selected' : ''; ?> >
                                        Cash
                                    </option>
                                    <option value="Cheque" <?= ($receipts->receipt_type == 'Cheque') ? 'selected' : ''; ?> >
                                        Cheque
                                    </option>
                                    <option value="Bank Transfer" <?= ($receipts->receipt_type == 'Bank Transfer') ? 'selected' : ''; ?> >
                                        Bank Transfer
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

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="clientid"><?= _l('receipt_currency'); ?></label>
                                <select id="type" name="data[currency]" class="form-control">
                                    <?php
                                    foreach ($currencies as $currency) {
                                        $selected = "";
                                        if ($receipts->receipt_currency == $currency['id']) {
                                            /* if ($currency['id'] == $default_currency) {*/
                                            $selected = 'selected';
                                        }
                                        echo '<option value="' . $currency['id'] . '"' . $selected . '>' . $currency['name'] . '</option>';
                                    } ?>
                                </select>
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
                            </div>
                        </div>

                    </div>
                </div>

                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-mtop">
                            <?= _l('receipt_details'); ?>
                        </h4>
                        <hr>
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
                                                   value="1"><?= $item->date; ?>
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
                                                   type="text"
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
                                                   value="<?= $item->amount; ?>"
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

    $(document).ready(function () {


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
            if (client != "" && client != "undefined") {
                $.ajax({
                    url: '<?php echo base_url();?>admin/receipts/clients_invoices/' + client,
                    type: 'GET',
                    data: 'twitterUsername=jquery4u',
                    success: function (data) {
                        //called when successfulc
                        obj = JSON.parse(data);
                        // console.log(obj);
                        $("#invoices_data").html(obj.html);
                        $("#total_amount").html(obj.total_payable);
                        $("#total_due").html(obj.amount_due);
                    },
                    error: function (e) {
                        console.log(e);
                    }
                });

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


        $("#type").change(function (event) {

            event.preventDefault();
            var type = $(this).val();

            if (type == 'Cheque' || type == 'cheque') {
                $("#cheque_date").html(html);
            } else {
                $("#cheque_date").html("");
            }
        });


        $("#savePayments").click(function (e) {
            e.preventDefault();

            var total_amount = parseFloat($("#amount").val());
            var payments_cleared = parseFloat($("#amount_total").text());
            // console.log(total_amount);
            console.log(payments_cleared);
            var types = $("#type").val();

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

    });


</script>