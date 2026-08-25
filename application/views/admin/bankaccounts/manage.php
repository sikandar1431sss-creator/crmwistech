<?php init_head(); ?>
<?php
$can_create_bankaccounts = is_admin() || has_permission('bankaccounts', '', 'create');
$can_edit_bankaccounts = is_admin() || has_permission('bankaccounts', '', 'edit');
$can_delete_bankaccounts = is_admin() || has_permission('bankaccounts', '', 'delete');
$can_sync_zoho_bankaccounts = $can_create_bankaccounts || $can_edit_bankaccounts;
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
                                    New Bank Account
                                </a>
                            <?php } ?>
                            <?php if ($can_sync_zoho_bankaccounts) { ?>
                                <button type="button" class="btn btn-default pull-left mleft5" id="sync_zoho_bank_accounts">
                                    <i class="fa fa-refresh"></i> Sync Zoho Bank Accounts
                                </button>
                            <?php } ?>
                            <span id="sync_zoho_bank_accounts_status" class="text-muted mleft10"></span>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading"/>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Nick Name</th>
                                    <th>Full Bank Name</th>
                                    <th>Currency</th>
                                    <th>Zoho Account</th>
                                    <th>IBAN</th>
                                    <th>Swift</th>
                                    <th>Active</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($bank_accounts)) { ?>
                                    <?php foreach ($bank_accounts as $bank_account) { ?>
                                        <tr>
                                            <td><?= html_escape($bank_account['title']); ?></td>
                                            <td>
                                                <?php if ($can_edit_bankaccounts) { ?>
                                                    <a href="<?= admin_url('bankaccounts/account/' . $bank_account['id']); ?>">
                                                        <?= html_escape($bank_account['bank_nick_name']); ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <?= html_escape($bank_account['bank_nick_name']); ?>
                                                <?php } ?>
                                            </td>
                                            <td><?= html_escape($bank_account['full_bank_name']); ?></td>
                                            <td><?= html_escape($bank_account['currency_code']); ?></td>
                                            <td>
                                                <?= html_escape($bank_account['zoho_account_name']); ?>
                                            </td>
                                            <td><?= html_escape($bank_account['iban']); ?></td>
                                            <td><?= html_escape($bank_account['swift']); ?></td>
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
                                        <td colspan="9" class="text-muted">No bank accounts found.</td>
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
<script>
    $(function () {
        $('#sync_zoho_bank_accounts').click(function () {
            var $button = $(this);
            var $status = $('#sync_zoho_bank_accounts_status');
            var data = {};

            if (typeof csrfData !== 'undefined') {
                data[csrfData.token_name] = csrfData.hash;
            }

            $button.prop('disabled', true);
            $status.removeClass('text-danger text-success').addClass('text-muted').text('Syncing...');

            $.post(admin_url + 'bankaccounts/sync_zoho_accounts', data)
                .done(function (response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }

                    if (!response.success) {
                        $status.removeClass('text-muted text-success').addClass('text-danger').text(response.message || 'Unable to sync Zoho bank accounts.');
                        return;
                    }

                    $status.removeClass('text-muted text-danger').addClass('text-success').text(response.message);
                    alert_float('success', response.message);
                })
                .fail(function () {
                    $status.removeClass('text-muted text-success').addClass('text-danger').text('Sync request failed.');
                })
                .always(function () {
                    $button.prop('disabled', false);
                });
        });
    });
</script>
</body>
</html>
