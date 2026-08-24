<div class="col-md-12 no-padding">
    <div class="panel_s">
        <div class="panel-body">
            <div class="alert alert-warning">
                Full invoice preview could not be loaded. Basic invoice summary is shown.
            </div>
            <h4 class="bold no-mtop">
                Invoice <?php echo format_invoice_number($invoice->id); ?>
            </h4>
            <p>
                <span class="bold">Status:</span>
                <?php echo format_invoice_status($invoice->status, '', false); ?>
            </p>
            <p>
                <span class="bold">Date:</span>
                <?php echo $invoice->date; ?>
            </p>
            <?php if (!empty($invoice->duedate)) { ?>
                <p>
                    <span class="bold">Due Date:</span>
                    <?php echo $invoice->duedate; ?>
                </p>
            <?php } ?>
            <p>
                <span class="bold">Total:</span>
                <?php echo format_money($invoice->total, $invoice->symbol); ?>
            </p>
            <p>
                <span class="bold">Amount Due:</span>
                <?php echo format_money($invoice->total_left_to_pay, $invoice->symbol); ?>
            </p>
            <div class="mtop20">
                <a href="<?php echo admin_url('invoices/invoice/' . $invoice->id); ?>" class="btn btn-default">
                    Edit Invoice
                </a>
                <a href="<?php echo site_url('invoice/' . $invoice->id . '/' . $invoice->hash); ?>" target="_blank" class="btn btn-info">
                    View Customer Preview
                </a>
            </div>
        </div>
    </div>
</div>
