<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(),array('id'=>'invoice-form','class'=>'_transaction_form invoice-form'));
			if(isset($invoice)){
				echo form_hidden('isedit');
			}
			?>
			<div class="col-md-12">
				<?php $this->load->view('admin/invoices/invoice_template'); ?>
			</div>
			<?php echo form_close(); ?>
			<?php $this->load->view('admin/invoice_items/item'); ?>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
	$(function(){
	    <?php
	    if(!isset($invoice)){
		  echo  'validate_invoice_form();';
		}
		?>
		// Init accountacy currency symbol
	    init_currency_symbol();
	    // Project ajax search
	    init_ajax_project_search_by_customer_id();
	    // Maybe items ajax search
	    init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'items/search');
	});

    $("#wis_invoice_type").change(function (event) {

        var prefix = "";
        var invoice_number = "";

        if ($(this).val() == 'performa') {

            prefix = "<?= get_option('performa_invoice_prefix'); ?>";
            invoice_number = "<?= str_pad(make_next_performa_invoice_number(), get_option('number_padding_prefixes'), '0', STR_PAD_LEFT); ?>";

        } else {

            prefix = "<?= get_option('invoice_prefix'); ?>";
            invoice_number = "<?= str_pad(make_next_invoice_num(), get_option('number_padding_prefixes'), '0', STR_PAD_LEFT); ?>";

        }

        $(document).find("#wis_inv_number").val(invoice_number);
        $(document).find("#inv_prefix_tax").text(prefix);
        $(document).find("#wis_inv_prefix").val(prefix);
        $(document).find("#wis_inv_number_prefix").val(prefix);

    });


</script>
</body>
</html>