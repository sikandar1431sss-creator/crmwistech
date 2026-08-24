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
	jQuery(function(){
		        var invoiceBankOptions = jQuery('#bank_account_id option').clone();

		        jQuery('body').on('changed.bs.select change', '#bank_account_id', function () {
		            setTimeout(function () {
		                applyCustomerCurrency(true);
		            }, 0);
	        });

	        jQuery('body').on('submit', 'form._transaction_form', function () {
	            applyCustomerCurrency(true);
	        });

        function updateClientCurrencyNotice(response) {
            var $notice = jQuery('#client_currency_notice');

            if (!$notice.length) {
                return;
            }

            if (response && response.client_currency_message) {
                $notice
                    .text(response.client_currency_message)
                    .toggleClass('text-warning', response.client_currency_source === 'base')
                    .toggleClass('text-muted', response.client_currency_source !== 'base')
                    .show();
            } else {
                $notice.text('').hide();
            }
		        }

		        function refreshInvoiceBanksForCurrency() {
		            var $bank = jQuery('#bank_account_id');
		            var $currency = jQuery('select[name="currency"]');
		            var selectedCurrencyId = $currency.val();
		            var selectedCurrencyCode = normalizeCurrencyCode($currency.attr('data-current-code') || getCurrencyDisplayText($currency));
		            var selectedBank = $bank.val();
		            var matches = [];
		            var $error = jQuery('#invoice_bank_currency_error');

		            if (!$bank.length || !$currency.length || !invoiceBankOptions.length || !selectedCurrencyId) {
		                return;
		            }

		            invoiceBankOptions.each(function () {
		                var $option = jQuery(this);
		                var value = $option.attr('value') || '';

		                if (value === '') {
		                    return;
		                }

		                var bankCurrencyId = $option.attr('data-currency-id') || '';
		                var bankCurrencyCode = normalizeCurrencyCode($option.attr('data-currency-code') || '');

		                if (
		                    (bankCurrencyId && parseInt(bankCurrencyId, 10) === parseInt(selectedCurrencyId, 10))
		                    || (bankCurrencyCode && selectedCurrencyCode && bankCurrencyCode === selectedCurrencyCode)
		                ) {
		                    matches.push($option.clone());
		                }
		            });

		            $bank.empty().append(jQuery('<option value="">Select bank account</option>'));

		            if (matches.length) {
		                jQuery.each(matches, function (_, $option) {
		                    $bank.append($option);
		                });
		                $error.hide().text('');
		            } else {
		                $bank.append(jQuery('<option value=""></option>').text('No bank account added for ' + (selectedCurrencyCode || 'selected') + ' currency'));
		                $error.text('No bank account added for ' + (selectedCurrencyCode || 'selected') + ' currency.').show();
		            }

		            if (selectedBank && $bank.find('option[value="' + selectedBank + '"]').length) {
		                $bank.val(selectedBank);
		            } else {
		                $bank.val('');
		            }

		            if ($bank.data('selectpicker')) {
		                $bank.selectpicker('refresh');
		            }
		        }

		        function validateSelectedBankForCurrency(showAlert) {
		            if (typeof showAlert === 'undefined') {
		                showAlert = true;
	            }

	            var $bank = jQuery('#bank_account_id');
            var $selectedBank = $bank.find('option:selected');
            var bankCurrencyId = $selectedBank.attr('data-currency-id') || $selectedBank.data('currency-id');
            var bankCurrencyCode = normalizeCurrencyCode($selectedBank.attr('data-currency-code') || $selectedBank.data('currency-code'));
            var $currency = jQuery('select[name="currency"]');
            var selectedCurrencyCode = normalizeCurrencyCode($currency.attr('data-current-code') || getCurrencyDisplayText($currency));

            if (!$bank.val() || !$currency.length || !$currency.val()) {
                return;
            }

	            if (bankCurrencyCode && selectedCurrencyCode) {
	                if (bankCurrencyCode !== selectedCurrencyCode) {
	                    if (showAlert && typeof alert_float === 'function') {
	                        alert_float('warning', 'Selected bank currency ' + bankCurrencyCode + ' does not match the customer currency ' + selectedCurrencyCode + '.');
	                    }

	                    if (!showAlert) {
	                        return;
	                    }

	                    if ($bank.data('selectpicker')) {
                        $bank.selectpicker('val', '');
                        $bank.selectpicker('refresh');
                    } else {
                        $bank.val('');
                    }
                }

                return;
            }

	            if (bankCurrencyId && parseInt(bankCurrencyId, 10) !== parseInt($currency.val(), 10)) {
	                if (showAlert && typeof alert_float === 'function') {
	                    alert_float('warning', 'Selected bank currency ' + bankCurrencyCode + ' does not match the customer currency.');
	                }

	                if (!showAlert) {
	                    return;
	                }

	                if ($bank.data('selectpicker')) {
                    $bank.selectpicker('val', '');
                    $bank.selectpicker('refresh');
                } else {
                    $bank.val('');
                }
            }
        }

		        function normalizeCurrencyCode(value) {
		            value = jQuery('<textarea/>').html((value || '').toString()).text();
	            value = jQuery.trim(value).replace(/\s+/g, ' ').toUpperCase();

	            if (!value) {
	                return '';
	            }

	            var compact = value.replace(/[^A-Z0-9]/g, '');
	            var aliases = {
	                UAE: 'AED',
	                AE: 'AED',
	                ARE: 'AED',
	                UAEDIRHAM: 'AED',
	                UAEDIRHAMS: 'AED',
	                UNITEDARABEMIRATESDIRHAM: 'AED',
	                UNITEDARABEMIRATESDIRHAMS: 'AED',
	                EMIRATIDIRHAM: 'AED',
	                EMIRATIDIRHAMS: 'AED',
	                DIRHAM: 'AED',
	                DIRHAMS: 'AED',
	                USDOLLAR: 'USD',
	                USDOLLARS: 'USD',
	                UNITEDSTATESDOLLAR: 'USD',
	                UNITEDSTATESDOLLARS: 'USD',
	                EURO: 'EUR',
	                EUROS: 'EUR',
	                PAKISTANIRUPEE: 'PKR',
	                PAKISTANIRUPEES: 'PKR',
	                SAUDIRIYAL: 'SAR',
	                SAUDIRIYALS: 'SAR'
	            };

	            if (aliases[compact]) {
	                return aliases[compact];
	            }

	            if (/\bAED\b/.test(value) || compact.indexOf('AED') !== -1) {
	                return 'AED';
	            }

		            var match = value.match(/\b[A-Z]{3}\b/);
		            return match ? match[0] : value;
		        }

		        function getCurrencyDisplayText($currency, currencyCode) {
		            if (currencyCode) {
		                return normalizeCurrencyCode(currencyCode);
		            }

		            var $selected = $currency.find('option:selected');
		            var selectedText = jQuery.trim($selected.text());
		            var selectedSubtext = jQuery.trim($selected.attr('data-subtext') || $selected.data('subtext') || '');
		            var normalizedText = normalizeCurrencyCode(selectedText);

		            return normalizedText || selectedText || selectedSubtext;
		        }

		        function updateCurrencyDisplay($currency, currencyCode) {
		            var $display = jQuery('#client_currency_display');

		            if (!$display.length) {
		                return;
		            }

		            $display.val(getCurrencyDisplayText($currency, currencyCode));
		        }

	        function applyCustomerCurrency(showAlert) {
	            var customerId = jQuery('#clientid').val();
            var $currency = jQuery('select[name="currency"]');
	            var $submitButtons = jQuery('form.invoice-form button[type="submit"], form.invoice-form input[type="submit"], form._transaction_form button[type="submit"], form._transaction_form input[type="submit"]');

		            if (!customerId || !$currency.length) {
		                updateClientCurrencyNotice(null);
		                if ($currency.length) {
		                    updateCurrencyDisplay($currency);
		                    refreshInvoiceBanksForCurrency();
		                }
		                return;
		            }

	            var $notice = jQuery('#client_currency_notice');
	            if ($notice.length) {
	                $notice
	                    .text('Loading customer currency...')
	                    .removeClass('text-muted')
	                    .addClass('text-warning')
	                    .show();
	            }

	            $submitButtons.prop('disabled', true);

	            jQuery.getJSON(admin_url + 'invoices/client_change_data/' + customerId)
	                .done(function (response) {
	                    var currencyId = response && response.client_currency ? response.client_currency : $currency.attr('data-base');
	                    updateClientCurrencyNotice(response);

	                    if (!currencyId) {
	                        $submitButtons.prop('disabled', false);
	                        return;
	                    }

	                    $currency.attr('data-current-code', response && response.client_currency_code ? response.client_currency_code : '');
	                    $currency.prop('disabled', false);
		                    if ($currency.data('selectpicker')) {
		                        $currency.selectpicker('val', currencyId);
		                        $currency.selectpicker('refresh');
		                    } else {
		                        $currency.val(currencyId);
			                    }
			                    updateCurrencyDisplay($currency, response && response.client_currency_code ? response.client_currency_code : '');
			                    refreshInvoiceBanksForCurrency();
			                    $currency.trigger('change');

		                    if (typeof init_currency_symbol === 'function') {
	                        init_currency_symbol();
	                    }

	                    validateSelectedBankForCurrency(showAlert !== false);
	                    $submitButtons.prop('disabled', false);
	                })
	                .fail(function () {
	                    if ($notice.length) {
	                        $notice.text('Unable to load customer currency. Please try again.').removeClass('text-warning').addClass('text-danger').show();
	                    }
	                    $submitButtons.prop('disabled', false);
	                });
	        }

		        jQuery('body').on('changed.bs.select change', '#clientid', function () {
		            setTimeout(function () {
		                applyCustomerCurrency(true);
		            }, 0);
		        });

		        jQuery('body').on('changed.bs.select change', 'select[name="currency"]', function () {
		            refreshInvoiceBanksForCurrency();
		        });

		        setTimeout(function () {
		            applyCustomerCurrency(false);
	        }, 0);

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

    jQuery("#wis_invoice_type").change(function (event) {

        var prefix = "";
        var invoice_number = "";
        var prev_date = jQuery("#PrevInvDate").text();
        var prev_type = jQuery("#prevInvoiceType").text();

        if (jQuery(this).val() == 'performa') {

            prefix = "<?= get_option('performa_invoice_prefix'); ?>";
            invoice_number = "<?= str_pad(make_next_performa_invoice_number(), get_option('number_padding_prefixes'), '0', STR_PAD_LEFT); ?>";
            jQuery('body').find("#date").val(prev_date);
        } else {

            prefix = "<?= get_option('invoice_prefix'); ?>";
            invoice_number = "<?= str_pad(make_next_invoice_num(), get_option('number_padding_prefixes'), '0', STR_PAD_LEFT); ?>";
            if(prev_type == "performa"){
                jQuery('body').find("#date").val("<?php echo date('d-m-Y');?>");
            }
        }

        jQuery(document).find("#wis_inv_number").val(invoice_number);
        jQuery(document).find("#inv_prefix_tax").text(prefix);
        jQuery(document).find("#wis_inv_prefix").val(prefix);
        jQuery(document).find("#wis_inv_number_prefix").val(prefix);

    });


</script>
</body>
</html>
