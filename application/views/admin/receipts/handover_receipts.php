<?php init_head(); ?>
<div id="wrapper" class="customer_profile" xmlns="http://www.w3.org/1999/html">
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
            <form action="<?= base_url() ?>admin/receipts/index" method="post" accept-charset="utf-8"
                  novalidate="novalidate">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <div class="col-md-12">
                            <form method="post" enctype="application/x-www-form-urlencoded" action="">
                                <div class="row">
                                    <div class="col-md-2">
                                        <?php echo render_select('created_by', $staff, array('staffid', array('firstname', 'lastname')), 'Created By', '', array('data-width' => '100%', 'data-none-selected-text' => 'All')); ?>
                                    </div>
                                    <div class="col-md-2">
                                        <?php echo render_select('owner', $staff, array('staffid', array('firstname', 'lastname')), 'Owner', '', array('data-width' => '100%', 'data-none-selected-text' => 'All')); ?>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group"><label for="date" class="control-label">
                                                Date</label>
                                            <div class="input-group date">
                                                <input type="text" required id="date" name="date"
                                                       class="form-control datepicker" value="">
                                                <div class="input-group-addon"><i
                                                            class="fa fa-calendar calendar-icon"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group"><label for="date" class="control-label">
                                                Cheque Date</label>
                                            <div class="input-group date">
                                                <input type="text" required id="date" name="cheque_date"
                                                       class="form-control datepicker" value="">
                                                <div class="input-group-addon"><i
                                                            class="fa fa-calendar calendar-icon"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="deposited_verified"><?= _l('receipt_status'); ?></label>
                                            <select id="deposited_verified" name="status" class="form-control">
                                                <option value="">Select Status</option>
                                                <option value="created" selected><?= _l('receipt_created'); ?></option>
                                                <?php if (is_admin()) { ?>
                                                    <option value="deposited"><?= _l('receipt_deposited'); ?></option>
                                                    <option value="handover"><?= _l('receipt_handover'); ?></option>
                                                    <option value="verified"><?= _l('receipt_verified'); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2" style="    padding: 20px;">
                                        <p class="bold"><?php echo _l(' '); ?></p>
                                        <input type="submit" class="btn btn-info only-save customer-form-submiter"
                                               value="Filter"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </form>
            <div class="panel_s">
                <div class="panel-body">
                    <h4 class="no-mtop">
                        <?= _l('receipt_details'); ?>
                    </h4>
                    <hr class="hr-panel-heading">
                    <form action="<?= base_url() ?>admin/receipts/update" method="post" accept-charset="utf-8"
                          novalidate="novalidate">
                    <table class="table table-striped table-responsive display" id="ReceiptTable">
                        <thead>
                        <tr role="row">
                            <th><?= _l('receipt_number'); ?></th>
                            <th><?= _l('receipt_date'); ?></th>
                            <th><?= _l('slip_number'); ?></th>
                            <th><?= _l('receipt_amount'); ?></th>
                            <th><?= _l('receipt_type'); ?></th>
                            <th><?= _l('receipt_cheque_date'); ?></th>
                            <th><?= _l('receipt_status'); ?></th>
                            <th><?= _l('receipt_trnxn_no'); ?></th>
                            <th><?= _l('invoice_select_owner'); ?></th>
                            <th><?= _l('receipt_created_by'); ?></th>
                            <th><?= _l('receipt_note'); ?></th>
                            <th><?= _l('receipt_handover_title'); ?></th>
                        </tr>
                        </thead>
                        <tbody id="invoices_data">
                        <?php
                        if ($receipts != null && count($receipts) > 0) {
                            $i = 0;
                            foreach ($receipts as $receipt) {
                                if($receipt->reciept_owner == $receipt->receipt_created_by){
                                    $reciept_owner = $this->receipts_model->staffNameById($receipt->reciept_owner);
                                    $created_by = $reciept_owner;
                                }else{
                                    $reciept_owner = $this->receipts_model->staffNameById($receipt->reciept_owner);
                                    $created_by = $this->receipts_model->staffNameById($receipt->receipt_created_by);
                                }
                                ?>
                                <tr>
                                    <td><?= $receipt->receipt_id; ?>  </td>
                                    <td><?= date('d-m-Y', strtotime($receipt->receipt_date)); ?></td>
                                    <td><?= $receipt->receipt_slip_no; ?>  </td>
                                    <td><?= $receipt->receipt_amount; ?>  </td>
                                    <td><?= $receipt->receipt_type; ?>  </td>
                                    <td><?= date('d-m-Y', strtotime($receipt->receipt_cheque_date)); ?></td>
                                    <td>
                                        <span class="label label-success  s-status"> <?= ucfirst($receipt->receipt_status); ?> </span>
                                    </td>
                                    <td><?= $receipt->receipt_transaction_no; ?>  </td>
                                    <td><?= $reciept_owner->firstname . ' '.$reciept_owner->lastname  ; ?>  </td>
                                    <td><?= $created_by->firstname . ' '.$created_by->lastname  ; ?>  </td>
                                    <td><?= $receipt->receipt_note; ?>  </td>

                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="form-group pull-right">
                        <input type="submit" class="btn btn-primary" value="Save">
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

<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.16/api/sum().js"></script>

<script>
    $(document).ready(function () {

        var table = $('#ReceiptTable').DataTable({});



    });
</script>