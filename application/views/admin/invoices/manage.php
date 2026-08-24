<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			include_once(APPPATH.'views/admin/invoices/filter_params.php');
			$this->load->view('admin/invoices/list_template');
			?>
		</div>
	</div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>var hidden_columns = [2,6,7,8,9];</script>
<?php init_tail(); ?>
<script>
	var invoicePreviewId = <?php echo json_encode($invoiceid); ?>;
	$(function(){
		if (invoicePreviewId) {
			init_invoice(invoicePreviewId);
		} else {
			init_invoice();
		}
	});
</script>

<div id="loading" class="hide">
    <div id="loading-content">
        <img src="<?= base_url() . 'uploads/syncing.gif' ?>" width="500"/>
    </div>
</div>

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
<?php $this->load->view('admin/includes/zoho_post_progress_modal'); ?>
<script>
    $(document).ready(function () {
        $("#DataTables_Table_0").on('click','a.post_to_zoho_invoice', function(e) {
        e.preventDefault();
        var that = $(this);
        if (that.hasClass('disabled')) {
            return;
        }
        var invoice_id = $(this).attr('data_id');

        if(invoice_id) {
            openZohoPostProgress('Posting invoice to Zoho');
            addZohoPostProgress('info', 'Step 1/4: checking invoice #' + invoice_id + ' and validating the customer, currency, items and taxes.');
            addZohoPostProgress('info', 'Step 2/4: preparing the Zoho payload and confirming all item/customer IDs are valid.');
            addZohoPostProgress('info', 'Step 3/4: sending the invoice to Zoho Books. The CRM will only show success after Zoho confirms it accepted the invoice.');
            addZohoPostProgress('info', 'Step 4/4: waiting for the final response. If the connection drops before success, this post is treated as failed and no Zoho invoice is marked as posted.');
            that.prop('disabled', true).addClass('disabled');
            $.ajax({
                type: 'POST',
                url: "<?php echo admin_url('receipts/createInvoice_zoho_ajax'); ?>",
                data: {invoice_id: invoice_id},
                dataType: "html",
                timeout: 180000,
                cache: false,
                beforeSend: function () {
                    addZohoPostProgress('info', 'Request started. CRM is waiting for Zoho confirmation before marking the invoice as posted.');
                },
                success: function (response) {
                    that.prop('disabled', false).removeClass('disabled');
                    var result = $.trim(response || '');

                    if (result === '1') {
                        addZohoPostProgress('success', 'Zoho accepted the invoice and the CRM received the final confirmation. Saving the Zoho ID and refreshing this page...');
                        finishZohoPostProgress();
                        alert_float('success', 'Invoice has been posted to zoho successfully.');
                        that.removeAttr('data_id');
                        setTimeout(function(){
                            window.location.reload();
                        }, 1500);
                    } else {
                        var errorMessage = result || 'There is some problem to post this invoice.';
                        addZohoPostProgress('error', 'Zoho did not confirm success. No invoice was marked as posted. Details: ' + errorMessage);
                        finishZohoPostProgress();
                        alert_float('danger', errorMessage);
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    that.prop('disabled', false).removeClass('disabled');

                    var message = 'Connection lost or request failed before CRM received the final Zoho response. No invoice was marked as posted.';
                    if (textStatus === 'timeout') {
                        message = 'The request timed out before Zoho returned a final result. No invoice was marked as posted.';
                    }

                    addZohoPostProgress('error', message);
                    finishZohoPostProgress();
                    alert_float('danger', message);
                }
            });
        } else {
            openZohoPostProgress('Posting invoice to Zoho');
            addZohoPostProgress('warning', 'This invoice already exists on Zoho.');
            finishZohoPostProgress();
            alert_float('warning', 'This invoice already exist on zoho.');
        }

        $("#close_div").click(function (e) {
            e.preventDefault();
            $("#loading").addClass('hide');
        });
    });
    });
</script>






</body>
</html>
