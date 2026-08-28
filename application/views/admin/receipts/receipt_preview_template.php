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
<?php
$receipt_currency = !empty($receipts->receipt_currency_symbol)
    ? $receipts->receipt_currency_symbol
    : (!empty($receipts->receipt_currency_code) ? $receipts->receipt_currency_code : '');
?>
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
                <?php
                if ($receipts->adjustment != 1 && $receipts->adjustment != 2 && $receipts->adjustment != 3) {
                    $receipt_zoho_id = trim((string)$receipts->zoho_id);
                    if ($receipt_zoho_id !== '' && strtoupper($receipt_zoho_id) !== 'NULL') {

                        ?>
                        <a id="zoho_disabled" class="pull-right btn btn-default btn-with-tooltip" data-toggle="tooltip"
                           title="Posted" data-placement="bottom"
                           style="margin-right: 5px;"><i class="fa fa-clipboard"> Posted</i>
                        </a>
                    <?php } else { ?>
                        <a id="post_to_zoho" class="pull-right btn btn-success post_to_zoho btn-with-tooltip"
                           data_id="<?= $receipts->receipt_id; ?>" data-toggle="tooltip"
                           title="Post To Zoho" data-placement="bottom"
                           style="margin-right: 5px;"><i class="fa fa-clipboard"> Post</i>
                        </a>
                        <?php
                    }
                }
                ?>

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
                                    <?php echo format_money($receipts->receipt_amount, $receipt_currency); ?>
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
                                <?php if (!empty($receipts->receipt_slip_no)) { ?>
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('slip_number'); ?>:
                                         </span>
                                    <?php echo $receipts->receipt_slip_no; ?>
                                </p>
                                <?php } ?>
                                <?php
                                if ($cashAdvance <> null) {
                                    if ($cashAdvance->amount > 0) {
                                        ?>
                                        <p class="no-mbot">
                                            <span class="bold">Advance Added: </span>
                                            <?php echo format_money($cashAdvance->amount, $receipt_currency); ?>
                                        </p>
                                        <?php
                                    }
                                    if ($cashAdvance->withdraw > 0) {
                                        ?>
                                        <p class="no-mbot">
                                            <span class="bold">Advance Settlled: </span>
                                            <?php echo format_money($cashAdvance->withdraw, $receipt_currency); ?>
                                        </p>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <div class="col-sm-6 text-right">
                                <?php 
                                $bank_name = get_receipt_bank_name($receipts);
                                if (!empty($bank_name)) { ?>
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('bank_info'); ?>:
                                         </span>
                                    <?php echo $bank_name; ?>
                                </p>
                                <?php } ?>
                                <?php if (!empty($receipts->receipt_transaction_no)) { ?>
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('receipt_trnxn_no'); ?>:
                                         </span>
                                    <?php echo $receipts->receipt_transaction_no; ?>
                                </p>
                                <?php } ?>
                                <?php if (!empty($receipts->receipt_note)) { ?>
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('receipt_note'); ?>:
                                         </span>
                                    <?php echo $receipts->receipt_note; ?>
                                </p>
                                <?php } ?>
                                <?php if (!empty($staff)) { 
                                    $staff_name = trim($staff->firstname . " " . $staff->lastname);
                                    if (!empty($staff_name)) { ?>
                                <p class="no-mbot">
                                         <span class="bold">
                                            <?php echo _l('invoice_select_owner'); ?>:
                                         </span>
                                    <?php echo $staff_name; ?>
                                </p>
                                <?php } } ?>
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
                                                    <td> <?= format_money($total, $receipt_currency); ?>  </td>
                                                    <td> <?= format_money($amount_due, $receipt_currency); ?> </td>
                                                    <td> <?= $item->discount; ?>  </td>
                                                    <td> <?= format_money($item->amount, $receipt_currency); ?>  </td>
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
                                            <?= format_money($receipts->receipt_amount, $receipt_currency); ?>
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


<ul>
    <li>Graphically Rich web Design With Jquery Banners (English)</li>
    <li>Powerful CMS.</li>
    <li>Add/Edit/Delete Unlimited Pages and add Unlimited Pictures</li>
    <li>Complete Structure will be developed as per the Sitemap provided</li>
    <li>Video&nbsp;Enabled&nbsp;</li>
    <li>Specialized Media Pages with News Updating Facility</li>
    <li>Dynamic Picture Gallery&nbsp;</li>
    <li>Social Media Sharing (Facebook Share, Like, Twitter, etc)</li>
    <li>SEO Friendly</li>
    <li>
        <g class="gr_ gr_18 gr-alert gr_spell gr_inline_cards gr_disable_anim_appear ContextualSpelling multiReplace"
           id="18" data-gr-id="18">High class
        </g>
        <g class="gr_ gr_16 gr-alert gr_spell gr_inline_cards gr_disable_anim_appear ContextualSpelling ins-del multiReplace"
           id="16" data-gr-id="16">XHTM
        </g>
        Standard based development allows future extendibility of features easy
    </li>
    <li>Cross-Browser compatibility</li>
    <li>Cross-Device Compatibility</li>
    <li>CMS support Adding New features fast and easy</li>
</ul>
Additional Language: 3000


<!--zoho code start-->
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
