<?php init_head(); ?>
<style>
    .width100 {
        width: 100%;
    }

    .cpointor {
        cursor: pointer;
    }

    .wrapper_custom_height {
        min-height: 100% !important;
    }

    ul li ul.dropdown_custom {
        min-width: 100%;
        background: #f2f2f2;
        display: none;
        position: absolute;
        z-index: 999;
        border-radius: 10px;
        padding: 10px;
        margin-left: 90px;
        top: 114px;
    }
    ul li:hover ul.dropdown_custom {
        display: block; /* Display the dropdown */
    }
    ul li ul.dropdown_custom li {
        display: block;
    }

</style>
<div id="wrapper" class="wrapper_custom_height" style="min-height: 100% !important;">

    <div class="content">

        <div class="row">

            <form action="<?= base_url() ?>admin/receipts" method="post" accept-charset="utf-8"

                  novalidate="novalidate">

                <div class="panel_s">

                    <div class="panel-body _buttons">
                        <?php 
                       
                        if (has_permission('receipts', '', 'create')) { ?>
                            <a href="<?php echo admin_url('receipts/create'); ?>"
                               class="btn btn-info pull-left"
                               style="margin-bottom: 10px;"><?php echo 'CREATE NEW RECEIPT'; ?></a>
                        <?php } 
                        ?>

                        <div class="col-md-12">

                            <form method="post" enctype="application/x-www-form-urlencoded" action="">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                                <div class="row">

                                    <div class="col-md-2">

                                        <?php echo render_select('created_by', $staff, array('staffid', array('firstname', 'lastname')), 'Created By', $receipt_created_by, array('data-width' => '100%', 'data-none-selected-text' => 'All')); ?>

                                    </div>

                                    <div class="col-md-2">

                                        <?php echo render_select('owner', $staff, array('staffid', array('firstname', 'lastname')), 'Owner', $reciept_owner, array('data-width' => '100%', 'data-none-selected-text' => 'All')); ?>

                                    </div>

                                    <div class="col-md-2">

                                        <div class="form-group"><label for="date" class="control-label">

                                                Date</label>

                                            <div class="input-group date">

                                                <input type="text" required id="date" name="date"

                                                       class="form-control datepicker"
                                                       value="<?php echo $receipt_date; ?>">

                                                <div class="input-group-addon"><i

                                                            class="fa fa-calendar calendar-icon"></i></div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-2">

                                        <div class="form-group"><label for="date" class="control-label">

                                                Cheque Date</label>

                                            <div class="input-group date">

                                                <input type="text" required id="date" name="cheque_date"

                                                       class="form-control datepicker"
                                                       value="<?php echo $receipt_cheque_date; ?>">

                                                <div class="input-group-addon"><i

                                                            class="fa fa-calendar calendar-icon"></i></div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-2">

                                        <div class="form-group">

                                            <label for="deposited_verified"><?= _l('receipt_status'); ?></label>

                                            <select id="deposited_verified" name="status" class="form-control">

                                                <option value="">Select Status</option>

                                                <option value="created" <?php if ($receipt_status == 'created') {
                                                    echo 'selected';
                                                } ?> ><?= _l('receipt_created'); ?></option>

                                                <?php if (is_admin()) { ?>

                                                    <option value="deposited" <?php if ($receipt_status == 'deposited') {
                                                        echo 'selected';
                                                    } ?>><?= _l('receipt_deposited'); ?></option>

                                                    <option value="handover" <?php if ($receipt_status == 'handover') {
                                                        echo 'selected';
                                                    } ?>><?= _l('receipt_handover'); ?></option>

                                                    <option value="verified" <?php if ($receipt_status == 'verified') {
                                                        echo 'selected';
                                                    } ?>><?= _l('receipt_verified'); ?></option>
                                                    <option value="posted" <?php if ($receipt_status == 'posted') {
                                                        echo 'selected';
                                                    } ?>><?= _l('Posted'); ?></option>

                                                    <option value="not_posted" <?php if ($receipt_status == 'not_posted') {
                                                        echo 'selected';
                                                    } ?>><?= _l('Not Posted'); ?></option>

                                                <?php } ?>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="col-md-2" style="    padding: 20px;">

                                        <p class="bold"><?php echo _l(' '); ?></p>

                                        <input type="submit" class="btn btn-info only-save customer-form-submiter"

                                               value="Filter"/>

                                    </div>

                                </div>

                            </form>

                        </div>
                        <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs pull-right"
                           onclick="toggle_small_view_receipt('.table-reciept','#receiptView'); return false;"
                           data-toggle="tooltip"
                           title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i
                                    class="fa fa-angle-double-left"></i></a>
                    </div>
                </div>
                <div class="col-sm-12" id="small-table">

                    <div class="panel_s">

                        <div class="panel-body">
                            <?php echo form_hidden('invoiceid', $invoiceid); ?>

                            <h4 class="no-mtop">

                                <?= _l('receipt_details'); ?>

                            </h4>

                            <hr class="hr-panel-heading">


                            <table cellspacing="0"
                                   class="table table-striped table-reciept table-responsive dt-responsive"

                                   id="ReceiptTable">

                                <thead>

                                <tr role="row">

                                    <th><?= _l('receipt_number'); ?></th>

                                    <th><?= _l('receipt_date'); ?></th>

                                    <th><?= _l('client_name'); ?></th>

                                    <th><?= _l('slip_number'); ?></th>

                                    <th><?= _l('receipt_amount'); ?></th>

                                    <th><?= _l('receipt_type'); ?></th>

                                    <th><?= _l('receipt_cheque_date'); ?></th>

                                    <th><?= _l('receipt_note'); ?></th>
                                    <th><?= _l('receipt_status'); ?></th>
                                    <th><?= _l('zoho_status'); ?></th>
                                    <th>Type</th>
                                    <th>&nbsp;Actions</th>
                                    <?php
                                    if (is_admin()) {
                                        ?>
                                        <th><?= _l('invoice_select_owner'); ?></th>
                                        <th><?= _l('receipt_created_by'); ?></th>
                                    <?php } ?>
                                </tr>
                                </thead>

                                <tbody id="invoices_data">
                                <tr>
                                    <td colspan="4">Receipts are loading...</td>
                                </tr>


                                </tbody>

                            </table>
                            <!-- </div>-->
                            <!-- <div class="col-md-7 small-table-right-col">
                                 <div id="invoice" class="hide">
                                 </div>
                             </div>-->

                        </div>

                    </div>
                </div>
                <div class="col-md-7 small-table-right-col">
                    <div id="receiptView" class="hide">
                    </div>
                </div>

                <?php

                if ($change_status) {

                    ?>

                    <div class="panel_s">

                        <div class="panel-body">

                            <div class="form-group pull-right">

                                <input type="submit" class="btn btn-primary" value="Save">

                            </div>

                        </div>

                    </div>

                    <?php

                }

                ?>

            </form>

        </div>

    </div>

</div>


<?php


init_tail();

?>

<div id="verfyDateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button>-->
                <h4 class="modal-title"> Verify Date</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group date">
                            <input type="text" id="date_verify"
                                   class="form-control datepicker" value="<?= date("Y-m-d"); ?>">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar calendar-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" clearfix"></div>
            </div>
            <div class="modal-footer">

                <button type="button" id="verify_update" class="btn btn-default" data-dismiss="modal">Update</button>
            </div>
        </div>

    </div>
</div>
<input type="hidden" id="date_verify_value" class="form-control" value="<?= date('d-m-Y'); ?>">
<input type="hidden" id="verify_id" class="form-control" value="">

</body>

</html>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.16/api/sum().js"></script>

<!-- Update the status with ajax request-->
<script>

    var hidden_columns = [6, 7,9];
    $(document).ready(function () {

        var source = <?php echo $receipts_data1;  ?>;
        // console.log(source);
        var table = $('.table-reciept').DataTable({

            // "ajax": '<?= base_url() ?>admin/receipts/grid',
            data: source,
            "order": [[0, "desc"]],
        });

    });


    $(document).ready(function () {

        $(document).on("click", '.delete', function (event) {

            if (!confirm('Are you sure?')) {
                e.preventDefault();
                return false;
            } else {

                var id = $(this).attr('id');

                $.ajax({
                    url: '<?= base_url(); ?>admin/receipts/delete',
                    type: 'POST',
                    data: {'receipt_id': id},
                    success: function (data) {
                        //called when successfulc
                        location.reload();
                        console.log(data);
                    },
                    error: function (e) {
                        console.log(e);
                    }
                });
            }
        });


        //  $('.dropdown-menu .dropdown_custom li').click(function () {
        $(document).on("click", '.dropdown-menu .dropdown_custom li', function (event) {

            var str = $(this).attr('id');
            var ret = str.split("_");
            var text = ret[0];

            if (text == 'verified') {
                $('#verify_id').val(ret[1]);
                $('#verfyDateModal').modal('show');
                $('#date_verify').on('change', function () {
                    $('#date_verify_value').val($(this).val());
                });
                return false;
            }

            var id = ret[1];

            $.ajax({

                url: '<?= base_url(); ?>admin/receipts/updateStatus',
                type: 'POST',
                data: {"changeStatus": true, "status": text, "id": id},
                success: function (data) {
                    console.log(data);
                    if (data) {
                        if (text == 'handover') {
                            html = '<span class="label label-success s-status">Handover</span>';
                        }
                        if (text == 'deposited') {
                            html = '<span class="label label-info s-status">Deposited</span>';
                        }
                        if (text == 'verified') {
                            html = '<span class="label label-warning s-status">Verified</span>';
                        }
                        if (text == 'created') {
                            html = '<span class="label label-default s-status">Created</span>';
                        }
                        $("#invoices_data .invoice_status_li-" + id).html(html);
                        alert_float('success', "Status updated successfully!");
                    }
                },
                error: function (e) {
                    alert_float('danger', "Status not Updated!");
                }
            });

        });

        $('#verify_update').on('click', function () {

            var text = 'verified';
            var verify_date = $('#date_verify_value').val();
            var id = $('#verify_id').val();
            $.ajax({
                url: '<?= base_url(); ?>admin/receipts/updateStatusVerify',
                type: 'POST',
                data: {"changeStatus": true, "status": text, "id": id, "verify_date": verify_date},
                success: function (data) {
                    console.log(data);
                    if (data) {
                        if (text == 'handover') {
                            html = '<span class="label label-success s-status">Handover</span>';
                        }
                        if (text == 'deposited') {
                            html = '<span class="label label-info s-status">Deposited</span>';
                        }
                        if (text == 'verified') {
                            html = '<span class="label label-warning s-status">Verified</span>';
                        }
                        if (text == 'created') {
                            html = '<span class="label label-default s-status">Created</span>';
                        }

                        $("#invoices_data .invoice_status_li-" + id).html(html);
                        alert_float('success', "Status updated successfully!");

                    }

                },
                error: function (e) {
                    alert_float('danger', "Status not Updated!");
                }
            });
        });

    });

    $(function () {
        init_reciept();
    });


</script>

<!--zoho code start-->
<style>
    #loading {
        width: 100%;
        height: 100%;
        top: 0px;
        left: 0px;
        position: fixed;
        display: block;
        opacity: 0.7;
        background-color: #fff;
        z-index: 9900;
        text-align: center;
    }

    #loading-content {
        position: absolute;
        top: 20%;
        left: 40%;
        text-align: center;
        z-index: 100;
    }

    .hide {
        display: none;
    }
</style>
<div id="loading" class="hide">
    <div id="loading-content">
        <img src="<?= base_url() . 'uploads/syncing.gif' ?>" width="500"/>
    </div>
</div>
<?php $this->load->view('admin/includes/zoho_post_progress_modal'); ?>
<script>
    $(document).ready(function () {
        $(document).on('click', '.post_to_zoho', function (e) {
            e.preventDefault();
            var that = $(this);
            if (that.hasClass('disabled') || that.prop('disabled')) {
                return;
            }
            var receipt_id = $(this).attr('data_id');
            if (receipt_id) {
                startZohoPostReceipt(receipt_id, that);
            } else {
                openZohoPostProgress('Posting Receipt to Zoho Books');
                renderReceiptProgressSteps();
                addZohoPostProgress('warning', 'This receipt already exists in Zoho Books.');
                finishZohoPostProgress(false, 'This receipt already exists on Zoho.');
                alert_float('warning', 'This receipt already exist on zoho.');
            }
        });
    });
</script>
<!--zoho code end-->
<?php exit; ?>
