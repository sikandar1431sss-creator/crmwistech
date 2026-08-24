<div class="col-md-5">
    <div class="text-right">
        <h4 class="no-margin bold"><?php echo _l('account_summary'); ?></h4>
        <p class="text-muted"><?php echo _l('statement_from_to', array($from, $to)); ?></p>
        <hr/>
        <table class="table statement-account-summary">
            <tbody>
            <tr>
                <td class="text-left"><?php echo _l('statement_beginning_balance'); ?>:</td>
                <td><?php echo format_money($statement['beginning_balance'], $statement['currency']->symbol); ?></td>
            </tr>
            <tr>
                <td class="text-left"><?php echo _l('invoiced_amount'); ?>:</td>
                <td><?php echo format_money($statement['invoiced_amount'], $statement['currency']->symbol); ?></td>
            </tr>
            <tr>
                <td class="text-left"><?php echo _l('amount_paid'); ?>:</td>
                <td><?php echo format_money($statement['amount_paid'], $statement['currency']->symbol); ?></td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-left"><b><?php echo _l('balance_due'); ?></b>:</td>
                <td><?php echo format_money($statement['balance_due'], $statement['currency']->symbol); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="col-md-12">
    <div class="text-center bold padding-10">
        <?php echo _l('customer_statement_info', array($from, $to)); ?>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th><b><?php echo _l('statement_heading_date'); ?></b></th>
                <th><b><?php echo _l('statement_heading_details'); ?></b></th>
                <th class="text-right"><b><?php echo _l('statement_heading_amount'); ?></b></th>
                <th class="text-right"><b><?php echo _l('statement_heading_payments'); ?></b></b></th>
                <th class="text-right"><b><?php echo _l('statement_heading_balance'); ?></b></b></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo $from; ?></td>
                <td><?php echo _l('statement_beginning_balance'); ?></td>
                <td class="text-right"><?php echo _format_number($statement['beginning_balance']); ?></td>
                <td></td>
                <td class="text-right"><?php echo _format_number($statement['beginning_balance']); ?></td>
            </tr>
            <?php
            $tmpBeginningBalance = $statement['beginning_balance'];
            foreach ($statement['result'] as $data) { ?>
            <tr>
                <td><?php echo _d($data['date']); ?></td>
                <td>
                    <?php

                    $color = '#FF0000';
                    if (isset($data['invoice_id'])) {

                        if(getInvoiceType($data['invoice_id']) == "performa"){
                            $color = '#cc6600';
                        }

                        if ($data['invoice_status'] != 5) {
                            echo _l('statement_invoice_details', array('<a style="color:' . $color . '" href="' . admin_url('invoices/list_invoices/' . $data['invoice_id']) . '" target="_blank">' . format_invoice_number($data['invoice_id']) . '</a>', '')) . $data['invoice_note'];
                        } else {
                            echo '<strike>' . _l('statement_invoice_details', array('<a style="color:' . $color . '" href="' . admin_url('invoices/list_invoices/' . $data['invoice_id']) . '" target="_blank">' . format_invoice_number($data['invoice_id']) . '</a>', '')) . '</strike>' . $data['invoice_note'];
                        }

                    } else if (isset($data['receipt_id'])) {
                        if (isset($data['receipt_status']) && $data['receipt_status'] == 'verified') {
                            $color = '#008000';
                        }
                        echo _l('statement_receipt_details', array('<a style="color:' . $color . '" href="' . admin_url('receipts#/' . $data['receipt_id']) . '" target="_blank">' . '#' . $data['receipt_num'] . '</a>', '')) . $data['receipt_note'];
                    }
                    ?>
                </td>
                <td class="text-right">
                    <?php
                    if (isset($data['invoice_id'])) {
                        if ($data['invoice_status'] != 5) {
                            echo _format_number($data['invoice_amount']);
                        } else {
                            echo '<strike>' . _format_number($data['invoice_amount']) . '</strike>';
                        }
                    } else if (isset($data['pinvoice_id'])) {
                        echo _format_number($data['pinvoice_amount']);
                    }
                    ?>
                </td>
                <td class="text-right">
                    <?php
                    if (isset($data['receipt_id'])) {
                        echo _format_number($data['receipt_amount']);
                    }
                    ?>
                </td>
                <td class="text-right">
                    <?php
                    if (isset($data['invoice_id'])) {
                        if ($data['invoice_status'] != 5) {
                            $tmpBeginningBalance = ($tmpBeginningBalance + $data['invoice_amount']);
                        }
                    } else if (isset($data['receipt_id'])) {
                        $tmpBeginningBalance = ($tmpBeginningBalance - $data['receipt_amount']);
                    } else if (isset($data['pinvoice_id'])) {
                        if ($data['pinvoice_status'] != 5) {
                            $tmpBeginningBalance = ($tmpBeginningBalance + $data['pinvoice_amount']);
                        }
                    }
                    echo _format_number($tmpBeginningBalance);
                    ?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
            <tfoot class="statement_tfoot">
            <tr>
                <td colspan="3" class="text-right">
                    <b><?php echo _l('balance_due'); ?></b>
                </td>
                <td class="text-right" colspan="2">
                    <b><?php echo format_money($statement['balance_due'], $statement['currency']->symbol); ?></b>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
