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
        $(document).on('click', 'a.post_to_zoho_invoice', function(e) {
            e.preventDefault();
            var that = $(this);
            if (that.hasClass('disabled') || that.prop('disabled')) {
                return;
            }
            var invoice_id = $(this).attr('data_id');
            if (invoice_id) {
                startZohoPostInvoice(invoice_id, that);
            } else {
                openZohoPostProgress('Posting Invoice to Zoho Books');
                renderInvoiceProgressSteps();
                addZohoPostProgress('warning', 'This invoice already exists in Zoho Books.');
                finishZohoPostProgress(false, 'This invoice already exists in Zoho Books.');
                alert_float('warning', 'This invoice already exists on zoho.');
            }
        });
    });
</script>
</body>
</html>
