<?php if (isset($client)) { ?>
    <h4 class="customer-profile-group-heading"><?php echo _l('Services'); ?></h4>
    <table class="table table-striped display" id="InvoiceItems">
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
            <th>Item</th>
            <th>Amount</th>
            <th>From Date</th>
            <th>To Date</th>
            <th>Invoice Approval</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($invoice_items <> null) {

            foreach ($invoice_items as $items) {

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
                        <a href="/admin/invoices/list_invoices/<?= $items->rel_id; ?>">
                            <?= format_invoice_number($items->rel_id); ?>
                            <input type="hidden" id="inv-<?= $items->userid; ?>"
                                   value="<?= $items->rel_id; ?>"/>
                        </a>
                    </td>
                    <td><?= $items->item_name . '<br/>' . $items->qty . ' * ' . $items->item_rate; ?>
                    </td>
                    <td><?= $items->qty * $items->item_rate; ?></td>
                    <td><?= date("d-m-Y", strtotime($items->item_start_date)); ?></td>
                    <td><?= date("d-m-Y", strtotime($items->item_end_date)); ?></td>
                    <td>
                        <?php
                        $invoice_accepted = get_invoice_status(trim($items->rel_id));
                        $invStatus = '<span id="invoiceApprovalStatus-' . trim($items->rel_id) . '"><span class="label label-info">OPEN</span></span>';
                        if ($invoice_accepted <> null) {

                            if ($invoice_accepted->invst_status == 'open') {
                                $invStatus = '<span class="invoiceApprovalStatus-' . trim($items->rel_id) . '"><span class="label label-info">OPEN</span></span>';
                            }
                            if ($invoice_accepted->invst_status == 'accepted') {
                                $invStatus = '<span class="invoiceApprovalStatus-' . trim($items->rel_id) . '"><span class="label-success label">ACCEPTED</span></span>';
                            }
                            if ($invoice_accepted->invst_status == 'rejected') {
                                $invStatus = '<span class="invoiceApprovalStatus-' . trim($items->rel_id) . '"><span class="label label-danger">REJECTED</span></span>';
                            }
                        }

                        echo $invStatus;
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="9" style="text-align: right;"></th>
        </tr>
        </tfoot>
    </table>
<?php } ?>
