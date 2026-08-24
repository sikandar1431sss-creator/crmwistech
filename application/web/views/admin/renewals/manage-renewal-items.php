<?php init_head();
$this->load->helper('form');
?>
<style>

    .post-tabs li.active a {
        border-bottom: 0;
        color: #FFFFFF;
        z-index: 2;
        font-size: 14px;
    }

    .post-tabs li a:hover {

        border-bottom: 0;
        color: #FFFFFF;
        z-index: 2;
        font-weight: bold;
        font-size: 14px;
        background: #5C6D78;
    }

    .post-tabs li.active a:hover {
        border-bottom: 0;
        color: #FFFFFF;
        z-index: 2;
        font-weight: bold;
        font-size: 14px;
    }

    .post-tabs li.active:first-child a {
        border-right: 1px solid #dddddd;
        box-shadow: inset -3px 0px 3px 0px rgba(0, 0, 0, 0.4);
        background: #5C6D78;
    }

    .post-tabs li.active:nth-child(2) a {
        border-right: 1px solid #dddddd;
        box-shadow: inset -3px 0px 3px 0px rgba(0, 0, 0, 0.4);
        background: #5C6D78;
    }

    .post-tabs li.active:last-child a {
        border-left: 1px solid #dddddd;
        box-shadow: inset 3px 0px 3px 0px rgba(0, 0, 0, 0.4);
        background: #5C6D78;
    }

    .cpointor {
        cursor: pointer;
    }

    .wrapper_custom_height {
        min-height: 100% !important;
    }

</style>
<div id="wrapper" class="wrapper_custom_height" style="min-height: 100% !important;">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <p class="bold"><?php echo _l('Manage Renewals'); ?></p>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <div class="col-md-12">
                            <div class="row text-center">
                                <div class="col-md-3 col-sm-offset-2" style="    padding: 20px;">
                                    <a href="#" class="btn btn-info " id="create_invoice">Create Proforma Invoice</a>
                                </div>
                                <div class="col-md-2 text-center" style="    padding: 20px;">
                                    <a href="#" class="btn btn-info " id="create_invoice_tax">Create Tax Invoice</a>
                                </div>
                                <div class="col-md-2 text-center" style="    padding: 20px;">
                                    <a href="#" class="btn btn-info " id="cancel_invoice">Cancel</a>
                                </div>
                            </div>
                            <form method="GET" id="filter_form" enctype="application/x-www-form-urlencoded" action="">
                                <div class="row">
                                    <hr/>
                                    <?php if (has_permission('renewals', '', 'view') || is_admin()) { ?>
                                        <div class="col-md-3">
                                            <?php
                                            $user_assigned_id = $this->session->userdata['staff_user_id'];
                                            if ($this->input->get('assigned')) {
                                                $user_assigned_id = $this->input->get('assigned');
                                            }
                                            echo render_select('assigned', $staff, array('staffid', array('firstname', 'lastname')), 'Owner', $user_assigned_id, array('data-width' => '100%', 'data-none-selected-text' => 'All')); ?>
                                        </div>
                                    <?php } ?>
                                    <input type="hidden" name="status" id="status_item" class="form-control"
                                           value="<?php if ($this->input->get('status')) {
                                               echo $this->input->get('status');
                                           } else {
                                               echo "expiring";
                                           } ?>"/>
                                    <div class="col-md-1">
                                        <p class="bold"><?php echo _l('in'); ?></p>
                                        <input type="text" name="in" class="form-control"
                                               value="<?php if ($this->input->get('in')) {
                                                   echo $this->input->get('in');
                                               } else {
                                                   echo "30";
                                               } ?>"/>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="bold"><?php echo _l('Duration'); ?></p>
                                        <select name="duration" title="<?php echo _l('additional_filters'); ?>"
                                                id="custom_view" class="selectpicker" data-width="100%">
                                            <option value="day" <?php if ($this->input->get('duration') && $this->input->get('duration') == 'day') {
                                                echo "selected";
                                            } else if (!$this->input->get('duration')) {
                                                echo 'selected';
                                            } ?> ?>Day
                                            </option>
                                            <option value="months" <?php if ($this->input->get('duration') && $this->input->get('duration') == 'months') {
                                                echo "selected";
                                            } ?> >Month
                                            </option>
                                            <option value="year" <?php if ($this->input->get('duration') && $this->input->get('duration') == 'year') {
                                                echo "selected";
                                            } ?> >Year
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3" style="    padding: 20px;">
                                        <p class="bold"><?php echo _l(' '); ?></p>
                                        <input type="submit" class="btn btn-info only-save customer-form-submiter"
                                               value="Filter"/>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <a href="#" class="btn btn-info btn-with-tooltip toggle-small-view hidden-xs"
                           onclick="toggle_small_view_renewal('.table-invoices_renewal','#invoiceRenewalView'); return false;"
                           data-toggle="tooltip"
                           title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i
                                    class="fa fa-angle-double-left"></i></a>
                        <ul class="nav nav-tabs nav-justified post-tabs">
                            <li class=" <?php if ($this->input->get('status') && $this->input->get('status') == 'expiring') {
                                echo "active";
                            } else if (!$this->input->get('status')) {
                                echo 'active';
                            } ?>">
                                <a class="cpointor status_dropdown" data_val="expiring">Expiring</a>
                            </li>
                            <li class=" <?php if ($this->input->get('status') && $this->input->get('status') == 'expired') {
                                echo "active";
                            } ?>"><a class="cpointor status_dropdown" data_val="expired">Expired</a></li>
                            <li class=" <?php if ($this->input->get('status') && $this->input->get('status') == 'cancelled') {
                                echo "active";
                            } ?>"><a class="cpointor status_dropdown" data_val="cancelled">Cancelled</a></li>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" id="small-table">
                        <div class="panel_s">
                            <div class="panel-body">
                                <?php echo form_hidden('invoiceid', $invoiceid); ?>

                                <!-- if invoiceid found in url -->
                                <table class="table table-striped table-invoices_renewal display " id="InvoiceItems">
                                    <thead>
                                    <tr role="row">
                                        <th>
                                            <div class="form-group">
                                                <div class="checkbox checkbox-primary no-mtop checkbox-inline">
                                                    <input type="checkbox" id="" class="" name="isRenewable" value="0"
                                                           disabled>
                                                    <label for="isRenewable-0">&nbsp;</label>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Invoice #</th>
                                        <th>Client</th>
                                        <th>Sales Person</th>
                                        <th>Item</th>
                                        <th>Amount</th>
                                        <?php if ($this->input->get('status') && $this->input->get('status') == 'cancelled') { ?>
                                            <th>Reason</th>
                                        <?php } else { ?>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($renewal_items <> null) {

                                        foreach ($renewal_items as $items) {

                                            if ($items->userid && format_invoice_number($items->rel_id)) {
                                                ?>
                                                <tr class="client-items-<?= $items->userid; ?> items-renewals">
                                                    <div id=""></div>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary no-mtop checkbox-inline">
                                                                <input type="checkbox" id="<?= $items->userid; ?>"
                                                                       class="ClientsItemsRenewable same-client-<?= $items->userid; ?>"
                                                                       name="ItemsRenewable"
                                                                       value="<?= $items->invoice_itmes_id . '-' . $items->userid; ?>">
                                                                <label for="isRenewable-0">&nbsp;</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a target="_blank"
                                                           href="/admin/invoices/list_invoices/<?= $items->rel_id; ?>"
                                                           onclick="init_renewal(<?= $items->rel_id; ?>); return false;">
                                                            <?= format_invoice_number($items->rel_id); ?>
                                                            <input type="hidden" id="inv-<?= $items->userid; ?>"
                                                                   value="<?= $items->rel_id; ?>"/>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a target="_blank"
                                                           href="/admin/clients/client/<?= $items->userid; ?>"><?= $items->company; ?></a>
                                                        <br/> <a
                                                                href="/admin/clients/client/<?= $items->userid; ?>"> <?= $items->phonenumber; ?></a>
                                                    </td>
                                                    <td>
                                                        <a target="_blank"
                                                           href="/admin/staff/member/<?= $items->staff_id; ?>"><?= $items->firstname . ' ' . $items->lastname; ?>
                                                    </td>
                                                    <td>
                                                        <b><?= $items->item_name; ?></b><br/>
                                                        <?= getDescriptionFristLine($items->item_description); ?>

                                                    </td>
                                                    <td><?= $items->qty * $items->item_rate; ?></td>
                                                    <?php if ($this->input->get('status') && $this->input->get('status') == 'cancelled') { ?>
                                                        <td>
                                                            <?= $items->renewal_cancel_reason; ?>
                                                        </td>

                                                    <?php } else { ?>
                                                        <td><?= date("d-m-Y", strtotime($items->item_start_date)); ?></td>
                                                        <td><?= date("d-m-Y", strtotime($items->item_end_date)); ?></td>
                                                    <?php } ?>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <?php if ($this->input->get('status') && $this->input->get('status') == 'cancelled') { ?>
                                            <th colspan="6" style="text-align: right;"></th>
                                        <?php } else { ?>
                                            <th colspan="7" style="text-align: right;"></th>
                                        <?php } ?>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 small-table-right-col">
                        <div id="invoiceRenewalView" class="hide">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="renewalCancelModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button>-->
                <h4 class="modal-title"> Cancel Renewal</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="renewal_reason_model">Reason:</label>
                        <textarea class="form-control" rows="5" id="renewal_reason_model"></textarea>
                    </div>
                </div>
                <div class=" clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="cancel_update" class="btn btn-info" data-dismiss="modal">Update</button>
            </div>
        </div>

    </div>
</div>
<?php init_tail(); ?>

<form action="<?= base_url(); ?>admin/renewals/invoiceRenewalCancel" method="post" accept-charset="utf-8"
      id="renwal_cancel_form" novalidate="novalidate">
    <input type="hidden" id="renewal_customer_id" name="renewal_customer_id" class="form-control" value="">
    <input type="hidden" id="renewal_prev_invoice" name="renewal_prev_invoice" class="form-control" value="">
    <textarea class="form-control" style="display:none;" id="renewal_cancel_items"
              name="renewal_cancel_items"></textarea>
    <textarea class="form-control" style="display:none;" id="renewal_reason" name="renewal_reason"></textarea>
</form>


<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.16/api/sum().js"></script>
<?php if ($this->input->get('status') && $this->input->get('status') == 'cancelled') { ?>
    <script>var hidden_columns = [3, 4, 6];</script>
<?php } else { ?>
    <script>var hidden_columns = [3, 4, 6, 7];</script>
<?php } ?>

<script>
    $(document).ready(function () {

        var table = $('.table-invoices_renewal').DataTable({
            <?php
            if ($renewal_items <> null) {?>
            "footerCallback": function (row, data, start, end, display) {

                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // Total over all pages
                total = api
                    .column(5)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    });

                // Total over this page
                pageTotal = api
                    .column(5, {page: 'current'})
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(5).footer()).html(
                    '<strong>Page Total: </strong>' + pageTotal + ' ( <strong>Total: </strong>' + total + ')'
                );
            },
            "pageLength": 30
            <?php } ?>
        });


        $(document).on('click', '.ClientsItemsRenewable', function (event) {

            // event.preventDefault();

            var id = $(this).attr("id");

            if ($(this).is(":checked")) {

                $('.items-renewals').find("input[type='checkbox']").prop('disabled', true);
                $('.items-renewals').find('.same-client-' + id).prop('disabled', false);
                $('.client-items-' + id).css('background', 'white');

            } else {
                if ($('.same-client-' + id + ':checked').length <= 0) {
                    $('.items-renewals').find("input[type='checkbox']").prop('disabled', false);
                    $('.items-renewals').find("input[type='checkbox']").prop('disabled', false);
                }
            }

        });


        $("#create_invoice").click(function (event) {

            // event.preventDefault();
            var data = [];
            var client = '';
            var i = 0;
            var id = $(this).attr('id');


            $('input[name="ItemsRenewable"]:checked').each(function () {

                var val = this.value.split("-");
                client = val[1];
                data[i] = val[0];
                i++;
            });
            var invoiceId = $("#inv-" + client).val();
            window.open(
                '/admin/invoices/invoice?customer_id=' + client + '&renew-items=' + data + '&prev-invoice=' + invoiceId + "&type=performa",
                '_blank' // <- This is what makes it open in a new window.
            );

        });

        $("#create_invoice_tax").click(function (event) {

            // event.preventDefault();
            var data = [];
            var client = '';
            var i = 0;
            var id = $(this).attr('id');


            $('input[name="ItemsRenewable"]:checked').each(function () {

                var val = this.value.split("-");
                client = val[1];
                data[i] = val[0];
                i++;
            });
            var invoiceId = $("#inv-" + client).val();

            window.open(
                '/admin/invoices/invoice?customer_id=' + client + '&renew-items=' + data + '&prev-invoice=' + invoiceId + "&type=invoice",
                '_blank' // <- This is what makes it open in a new window.
            );
            // window.location.href =

        });

        $("#cancel_invoice").click(function (event) {

            // event.preventDefault();

            var data = [];
            var client = '';
            var i = 0;
            var id = $(this).attr('id');

            $('input[name="ItemsRenewable"]:checked').each(function () {

                var val = this.value.split("-");
                client = val[1];
                data[i] = val[0];
                i++;
            });
            if (i == 0) {
                BootstrapDialog.alert({
                    title: 'WARNING',
                    message: 'Select iteme(s) first for cancel renewal!',
                    type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                    closable: true, // <-- Default value is false
                    buttonLabel: 'Close',
                });

                return false;
            } else {
                $('#renewal_customer_id').val(client);
                $('#renewal_cancel_items').val(data);
                $('#renewal_prev_invoice').val($("#inv-" + client).val());
                $('#renewalCancelModal').modal('show');
                return false;
            }

        });

        $('#cancel_update').on('click', function (event) {
            var renewal_reason = $('#renewal_reason_model').val();
            $('#renewal_reason').val(renewal_reason);
            $('#renwal_cancel_form').submit();
            return false;
        });
    });

    $(function () {

        init_renewal();
        $(".status_dropdown").on('click', function () {
            $('#status_item').val($(this).attr('data_val'));
            $('#filter_form').submit();
        });

    });

</script>
</body>
</html>
