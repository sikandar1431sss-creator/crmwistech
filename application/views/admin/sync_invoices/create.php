<?php init_head(); ?>
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
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<div id="wrapper" class="customer_profile">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="validation_erros">

                </div>
            </div>
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

            <form action="<?= base_url() ?>admin/sync_invoices/create" method="post" id="sync_invoices">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4 class="no-mtop"><?php echo _l('sync_invoices'); ?></h4>

                            <hr class="hr-panel-heading">

                            <div class="col-md-12 form-group">
                                <?php echo render_date_input('from_date', 'invoice_add_edit_sync_from_date', date('Y-m-d')); ?>
                            </div>

                            <div class="col-md-12 form-group">
                                <?php echo render_date_input('to_date', 'invoice_add_edit_sync_to_date', date('Y-m-d')); ?>
                            </div>
                            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                            <div class="col-md-12 form-group">
                                <div class="form-group">
                                    <input type="button" class="btn btn-primary pull-right" id="sync_with_zoho"
                                           value="Sync with Zoho">
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

<div id="loading" class="hide">
    <div id="loading-content">
        <a href="#" id="close_div"
           style="float: right;position: absolute;top: 0%;right: 0%;color: white;font-size: 16px;background: red;padding: 5px;">x
            Close</a>
        <img src="<?= base_url() . 'uploads/syncing.gif' ?>" width="500"/>
    </div>
</div>


<script>
    $(document).ready(function () {

        $("#sync_with_zoho").click(function (e) {

            e.preventDefault();
            $(document).find("#validation_erros").html("");
            $("#loading").removeClass('hide');

            var frm = $("#sync_invoices");
            var data = frm.serialize();

            $.ajax({

                type: frm.attr("method"),
                url: frm.attr("action"),
                dataType: 'application/json',
                data: data,
                success: function (data) {

                    $(document).find("#validation_erros").html(data.responseText);

                    setTimeout(function () {
                        // $(document).find("#validation_erros").html("");
                    }, 3000);

                    $("#loading").addClass('hide');
                },
                error: function (data) {

                    $(document).find("#validation_erros").html(data.responseText);
                    setTimeout(function () {
                        // $(document).find("#validation_erros").html("");
                    }, 3000);

                    //console.log(data.responseText);
                    $("#loading").addClass('hide');
                }
            });

        });

        $("#close_div").click(function (e) {
            e.preventDefault();
            $("#loading").addClass('hide');
        });

    });
</script>