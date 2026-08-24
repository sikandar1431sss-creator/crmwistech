<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if (isset($credits_available, $customer_currency) && credits_can_be_applied_to_invoice($invoice->status) && $credits_available > 0) { ?>
    <div class="alert alert-warning mbot5">
        <?php echo _l('x_credits_available', format_money($credits_available, $customer_currency->symbol)); ?>
    </div>
<?php } ?>
<?php if (!empty($invoices_to_merge)) { ?>
    <div class="panel_s no-padding mbot5">
        <div class="panel-body">
            <h4 class="font-medium bold no-mtop mbot15"><?php echo _l('invoices_available_for_merging'); ?></h4>
            <hr class="hr-panel-heading hr-10"/>
            <?php foreach ($invoices_to_merge as $_inv) { ?>
                <p>
                    <a href="<?php echo admin_url('invoices/list_invoices/' . $_inv->id); ?>" target="_blank">
                        <?php echo format_invoice_number($_inv->id); ?>
                    </a>
                    - <?php echo format_money($_inv->total, $_inv->symbol); ?>
                    <span class="pull-right text-<?php echo get_invoice_status_label($_inv->status); ?>">
                        <?php echo format_invoice_status($_inv->status, '', false); ?>
                    </span>
                </p>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<?php echo form_hidden('_attachment_sale_id', $invoice->id); ?>
<?php echo form_hidden('_attachment_sale_type', 'invoice'); ?>
<div class="col-md-12 no-padding">
    <div class="panel_s">
        <div class="panel-body">
            <?php if ($invoice->recurring > 0) {
                echo '<div class="ribbon info"><span>' . _l('invoice_recurring_indicator') . '</span></div>';
            } ?>
            <div class="row">
                <div class="col-md-3">
                    <?php echo format_invoice_status($invoice->status, 'mtop5'); ?>
                    <?php if (($invoice->status == 3 || $invoice->status == 4) && $invoice->duedate && date('Y-m-d') > date('Y-m-d', strtotime(to_sql_date($invoice->duedate)))) { ?>
                        <p class="text-danger mtop15 no-mbot">
                            <?php echo _l('invoice_is_overdue', floor((abs(time() - strtotime(to_sql_date($invoice->duedate)))) / (60 * 60 * 24))); ?>
                        </p>
                    <?php } ?>
                </div>
                <div class="col-md-9 _buttons">
                    <div class="visible-xs">
                        <div class="mtop10"></div>
                    </div>
                    <div class="pull-right">
                        <?php if (has_permission('invoices', '', 'edit')) { ?>
                            <a href="<?php echo admin_url('invoices/invoice/' . $invoice->id); ?>"
                               data-toggle="tooltip"
                               title="<?php echo _l('edit_invoice_tooltip'); ?>"
                               class="btn btn-default btn-with-tooltip"
                               data-placement="bottom"><i class="fa fa-pencil-square-o"></i></a>
                        <?php } ?>
                        <div class="btn-group">
                            <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-file-pdf-o"></i><?php if (is_mobile()) { echo ' PDF'; } ?> <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="hidden-xs">
                                    <a href="<?php echo admin_url('invoices/pdf/' . $invoice->id . '?output_type=I'); ?>">
                                        <?php echo _l('view_pdf'); ?>
                                    </a>
                                </li>
                                <li class="hidden-xs">
                                    <a href="<?php echo admin_url('invoices/pdf/' . $invoice->id . '?output_type=I'); ?>" target="_blank">
                                        <?php echo _l('view_pdf_in_new_window'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo admin_url('invoices/pdf/' . $invoice->id); ?>">
                                        <?php echo _l('download'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo admin_url('invoices/pdf/' . $invoice->id . '?print=true'); ?>" target="_blank">
                                        <?php echo _l('print'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <?php if (!empty($invoice->clientid)) { ?>
                            <a href="<?php echo site_url('invoice/' . $invoice->id . '/' . $invoice->hash); ?>"
                               target="_blank"
                               class="btn btn-default btn-with-tooltip"
                               data-toggle="tooltip"
                               title="<?php echo _l('view_invoice_as_customer_tooltip'); ?>"
                               data-placement="bottom"><i class="fa fa-eye"></i></a>
                        <?php } ?>
                        <?php if (has_permission('payments', '', 'create') && abs($invoice->total) > 0) { ?>
                            <a href="#" onclick="record_payment(<?php echo $invoice->id; ?>); return false;"
                               class="mleft10 pull-right btn btn-success<?php if ($invoice->status == 2 || $invoice->status == 5) { echo ' disabled'; } ?>">
                                <i class="fa fa-plus-square"></i> <?php echo _l('payment'); ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr class="hr-panel-heading"/>
            <?php if ($invoice->status == 5 && $invoice->recurring > 0) { ?>
                <div class="alert alert-info">
                    Recurring invoice with status Cancelled <b>is still ongoing recurring invoice</b>. If you
                    want to stop this recurring invoice you should update the invoice recurring field to <b>No</b>.
                </div>
            <?php } ?>
            <?php $this->load->view('admin/invoices/invoice_preview_html'); ?>
        </div>
    </div>
</div>
<script>
    init_items_sortable(true);
    init_btn_with_tooltips();
</script>
