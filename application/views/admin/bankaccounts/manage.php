<?php init_head(); ?>
<?php
$can_create_bankaccounts = is_admin() || has_permission('bankaccounts', '', 'create');
$can_edit_bankaccounts = is_admin() || has_permission('bankaccounts', '', 'edit');
$can_delete_bankaccounts = is_admin() || has_permission('bankaccounts', '', 'delete');
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if ($can_create_bankaccounts) { ?>
                                <a href="<?= admin_url('bankaccounts/account'); ?>" class="btn btn-info pull-left">
                                    Add New Account
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading"/>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Nick Name</th>
                                    <th>Full Name</th>
                                    <th>Currency</th>
                                    <th>Zoho Account</th>
                                    <th>IBAN / Acc #</th>
                                    <th>Swift</th>
                                    <th>Active</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($bank_accounts)) { ?>
                                    <?php foreach ($bank_accounts as $bank_account) { ?>
                                        <?php $is_cash = (isset($bank_account['account_type']) && $bank_account['account_type'] === 'cash'); ?>
                                        <tr>
                                            <td>
                                                <?php if ($is_cash) { ?>
                                                    <span class="label label-warning">Cash</span>
                                                <?php } else { ?>
                                                    <span class="label label-info">Bank</span>
                                                <?php } ?>
                                            </td>
                                            <td><?= html_escape($bank_account['title']); ?></td>
                                            <td>
                                                <?php if ($can_edit_bankaccounts) { ?>
                                                    <a href="<?= admin_url('bankaccounts/account/' . $bank_account['id']); ?>" class="bold">
                                                        <?= html_escape($bank_account['bank_nick_name']); ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <?= html_escape($bank_account['bank_nick_name']); ?>
                                                <?php } ?>
                                            </td>
                                            <td><?= html_escape($bank_account['full_bank_name']); ?></td>
                                            <td><span class="label label-default"><?= html_escape($bank_account['currency_code']); ?></span></td>
                                            <td>
                                                <?= html_escape($bank_account['zoho_account_name']); ?>
                                            </td>
                                            <td><?= !empty($bank_account['iban']) ? html_escape($bank_account['iban']) : '<span class="text-muted">-</span>'; ?></td>
                                            <td><?= !empty($bank_account['swift']) ? html_escape($bank_account['swift']) : '<span class="text-muted">-</span>'; ?></td>
                                            <td>
                                                <?php if ((int)$bank_account['active'] === 1) { ?>
                                                    <span class="label label-success">Yes</span>
                                                <?php } else { ?>
                                                    <span class="label label-default">No</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if ($can_edit_bankaccounts) { ?>
                                                    <?= icon_btn('bankaccounts/account/' . $bank_account['id'], 'pencil-square-o', 'btn-default'); ?>
                                                <?php } ?>
                                                <?php if ($can_delete_bankaccounts) { ?>
                                                    <?= icon_btn('bankaccounts/delete/' . $bank_account['id'], 'remove', 'btn-danger _delete'); ?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="10" class="text-muted">No bank or cash accounts found.</td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>

