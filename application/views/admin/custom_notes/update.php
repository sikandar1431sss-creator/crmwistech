<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (isset($client) && $client->active == 0) { ?>
                    <div class="alert alert-warning">
                        <?php echo _l('customer_inactive_message'); ?>
                        <br/>
                        <a href="<?php echo admin_url('clients/mark_as_active/' . $client->userid); ?>"><?php echo _l('mark_as_active'); ?></a>
                    </div>
                <?php } ?>
                <?php if (isset($client) && $client->leadid != NULL) { ?>
                    <div class="alert alert-info">
                        <a href="#"
                           onclick="init_lead(<?php echo $client->leadid; ?>); return false;"><?php echo _l('customer_from_lead', _l('lead')); ?></a>
                    </div>
                <?php } ?>
                <?php if (isset($client) && (!has_permission('customers', '', 'view') && is_customer_admin($client->userid))) { ?>
                    <div class="alert alert-info">
                        <?php echo _l('customer_admin_login_as_client_message', get_staff_full_name(get_staff_user_id())); ?>
                    </div>
                <?php } ?>
            </div>

            <form action="<?= base_url() ?>admin/custom_notes/update/<?= $id; ?>" method="post" accept-charset="utf-8"
                  novalidate="novalidate">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4 class="no-mtop"><?php echo _l('update_note_custom'); ?></h4>
                            <div class="col-md-12">
                                <div class="alert-danger alert-validation">
                                    <?php
                                    if (validation_errors()) {
                                        echo validation_errors();
                                    }
                                    ?>
                                </div>
                            </div>
                            <hr class="hr-panel-heading">
                            <div class="col-md-12 form-group">
                                <label for="date" class="control-label">
                                    <?= _l('add_note_custom_title'); ?>
                                </label><input type="text" class="form-control" name="noteTitle"
                                               placeholder="Enter Tilte" value="<?= $cus_notes->cn_title; ?>"/>
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="date" class="control-label">
                                    <?= _l('add_note_custom_type'); ?>
                                </label>
                                <select name="noteType"
                                        class="form-control">
                                    <option value="">Select Type</option>
                                    <option <?= ($cus_notes->cn_type == 'invoice') ? 'selected' : ''; ?>
                                            value="invoice">Invoice
                                    </option>
                                    <option <?= ($cus_notes->cn_type == 'proposal') ? 'selected' : ''; ?>
                                            value="proposal">Proposal
                                    </option>
                                    <option <?= ($cus_notes->cn_type == 'receipt') ? 'selected' : ''; ?>
                                            value="receipt">Receipt
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="date" class="control-label">
                                    <?= _l('add_note_custom_position'); ?>
                                </label>
                                <select name="notePosition"
                                        class="form-control">
                                    <option value="">Select Position</option>
                                    <option <?= ($cus_notes->cn_position == 'top') ? 'selected' : ''; ?> value="top">
                                        Top
                                    </option>
                                    <option <?= ($cus_notes->cn_position == 'bottom') ? 'selected' : ''; ?>
                                            value="bottom">Bottom
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <div class="form-group">
                                    <?php echo render_textarea('noteDescription', 'Note', $cus_notes->cn_notes, array(), array(), '', 'tinymce'); ?>
                                </div>
                            </div>
                            <div class="col-md-12 form-group">
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary pull-right" value="Save">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>