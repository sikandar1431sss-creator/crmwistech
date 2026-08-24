<div class="col-md-12">
    <div class="panel_s">
        <div class="panel-body">
            <div class="row">
                <a href="<?= base_url(); ?>admin/receipts/details/<?= $receipts->receipt_id; ?>"
                   data-toggle="tooltip" title="" id="<?= $receipts->receipt_id; ?>"
                   class="pull-right btn btn-danger btn-sm btn-with-tooltip delete" data-placement="bottom"
                   data-original-title="Delete">
                    <i class="fa fa-trash-o"></i>
                </a>

                <a href="<?= base_url(); ?>admin/receipts/update/<?= $receipts->receipt_id; ?>"
                   data-toggle="tooltip" title=""
                   class="pull-right btn btn-default btn-sm btn-with-tooltip" data-placement="bottom"
                   data-toggle="modal" data-target="#receipt_send_to_client_modal"
                   data-original-title="Edit" style="margin-right: 5px;">
                    <i class="fa fa-pencil-square-o"></i>
                </a>

                <!-- Trigger the modal with a button -->
                <a type="button" class="btn btn-info btn-lg pull-right btn btn-primary btn-sm btn-with-tooltip"
                   data-toggle="modal" data-target="#myModal" style="margin-right: 5px;">
                    <i class="fa fa-envelope"></i>
                </a>

                <a href="<?php echo admin_url('receipts/pdf/' . $receipts->receipt_id . '?print=true'); ?>"
                   target="_blank" class="pull-right btn btn-default btn-with-tooltip" data-toggle="tooltip"
                   title="<?php echo _l('print'); ?>" data-placement="bottom"
                   style="margin-right: 5px;"><i class="fa fa-print"></i>
                </a>

                <a href="<?php echo admin_url('receipts/pdf/' . $receipts->receipt_id); ?>"
                   class="pull-right btn btn-default btn-with-tooltip" data-toggle="tooltip"
                   title="<?php echo _l('view_pdf'); ?>" data-placement="bottom"
                   style="margin-right: 5px;"><i class="fa fa-file-pdf-o"></i>
                </a>

            </div>

            <hr/>
            <div class="tab-content">
                <div class="ptop10" id="">
                    <div id="invoice-preview">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="bold">
                                    <a href="#">
                                               <span id="receipt-number">
                                                <?php echo format_receipt_number($receipts->receipt_num); ?>
                                               </span>
                                    </a>
                                </h4>
                                <address>
                                    <?php echo format_organization_info(); ?>
                                </address>
                            </div>

                            <div class="col-sm-6 text-right">
                                <span class="bold"><?php echo _l('invoice_bill_to'); ?>:</span>
                                <address>
                                    <?php echo format_customer_info($receipts, 'invoice', 'billing', true); ?>
                                </address>
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('receipt_date'); ?>
                                         </span>
                                    <?php echo date('d-m-Y', strtotime($receipts->receipt_date)); ?>
                                </p>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('receipt_date'); ?>:
                                         </span>
                                    <?php echo date('d-m-Y', strtotime($receipts->receipt_date)); ?>
                                </p>
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('client_amount_table_heading'); ?>:
                                         </span>
                                    <?php echo $receipts->receipt_amount; ?>
                                </p>
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('receipt_type'); ?>:
                                         </span>
                                    <?php echo $receipts->receipt_type; ?>
                                </p>
                                <?php
                                if ($receipts->receipt_type == 'Cheque') {
                                    ?>
                                    <p class="no-mbot">
                                                <span class="bold">
                                                    <?php echo _l('receipt_cheque_number'); ?>:
                                                </span>
                                        <?php echo $receipts->receipt_cheque_num; ?>
                                    </p>
                                    <p class="no-mbot">
                                                    <span class="bold">
                                                        <?php echo _l('receipt_cheque_date'); ?>:
                                                    </span>
                                        <?php echo date('d-m-Y', strtotime($receipts->receipt_cheque_date)); ?>
                                    </p>
                                    <?php
                                }
                                ?>
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('slip_number'); ?>:
                                         </span>
                                    <?php echo $receipts->receipt_slip_no; ?>
                                </p>
                                <?php
                                if ($cashAdvance <> null) {
                                    if ($cashAdvance->amount > 0) {
                                        ?>
                                        <p class="no-mbot">
                                            <span class="bold">Advance Added: </span>
                                            <?php echo $cashAdvance->amount; ?>
                                        </p>
                                        <?php
                                    }
                                    if ($cashAdvance->withdraw > 0) {
                                        ?>
                                        <p class="no-mbot">
                                            <span class="bold">Advance Settlled: </span>
                                            <?php echo $cashAdvance->withdraw; ?>
                                        </p>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <div class="col-sm-6 text-right">
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('bank_info'); ?>:
                                         </span>
                                    <?php echo $receipts->receipt_bank; ?>
                                </p>
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('receipt_trnxn_no'); ?>:
                                         </span>
                                    <?php echo $receipts->receipt_transaction_no; ?>
                                </p>
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('receipt_note'); ?>:
                                         </span>
                                    <?php echo $receipts->receipt_note; ?>
                                </p>
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('invoice_select_owner'); ?>:
                                         </span>
                                    <?php echo $staff->firstname . " " . $staff->lastname; ?>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table items invoice-items-preview">
                                        <thead>
                                        <tr>
                                            <th><?= _l('receipt_date'); ?></th>
                                            <th style="text-align: center;"><?= _l('client_invoice_number_table_heading'); ?></th>
                                            <th><?= _l('client_amount_table_heading'); ?></th>
                                            <th><?= _l('invoice_amount_due'); ?></th>
                                            <th><?= _l('invoice_discount'); ?></th>
                                            <th><?= _l('invoice_payment_table_number_heading'); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if (count($invoices) > 0) {
                                            foreach ($invoices as $item) {

                                                $total_to_pay = 0;
                                                $total_due = 0;
                                                $total = 0;
                                                $date = $item->date;

                                                $total_amount = $this->receipts_model->getInvoicesTotal($item->invoiceid);

                                                if ($total_amount <> null) {
                                                    $total = $total_amount->total;
                                                    $total_to_pay = $total_to_pay + $total;
                                                    $date = $total_amount->invoice_date;
                                                }

                                                $amount_due = get_invoice_total_left_to_pay($item->invoiceid, $total);

                                                ?>
                                                <tr>
                                                    <td> <?= date('d-m-Y', strtotime($date)); ?>  </td>
                                                    <td style="text-align: center;">
                                                        <a href="<?= base_url(); ?>admin/invoices/list_invoices#<?= $item->invoiceid; ?>"> <?= format_invoice_number($item->invoiceid); ?>  </a>
                                                    </td>
                                                    <td> <?= $total; ?>  </td>
                                                    <td> <?= $amount_due; ?> </td>
                                                    <td> <?= $item->discount; ?>  </td>
                                                    <td> <?= $item->amount; ?>  </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4 col-md-offset-8">
                                <table class="table text-right">
                                    <tbody>
                                    <tr id="subtotal">
                                        <td><span class="bold">Total Paid</span>
                                        </td>
                                        <td class="subtotal">
                                            AED <?= $receipts->receipt_amount; ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {

        $("a.delete").click(function (e) {
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
                        console.log(data);
                    },
                    error: function (e) {
                        console.log(e);
                    }
                });
            }
        });

        $("a.sendReceipt").click(function (e) {

            e.preventDefault();

            if (!confirm('Are you sure?')) {
                e.preventDefault();
                return false;
            } else {
                var id = $(this).attr('id');
                // console.log(id);
            }
        });

    });

</script>
<style>
    .center-align {
        text-align: center !important;
    }
</style>
<?php $this->load->view('admin/receipts/receipt_send_to_client', [
    'receipts' => $receipts
]); ?>
<!-- Modal -->
