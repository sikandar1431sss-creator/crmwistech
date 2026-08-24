/* v2.0.1 */
/* jshint expr: true */
/* jshint sub:true */
/* jshint loopfunc:true */
/**
 * Core JS Files
 * Not recommened to edit this file directly if you plan to upgrade the script when new versions are released.
 * Use hooks to inject custom javascript code
 */

// Append the added items to the preview to the table as items
function add_item_to_table(data, itemid, merge_invoice, bill_expense) {

    // If not custom data passed get from the preview
    data = typeof(data) == 'undefined' || data == 'undefined' ? get_item_preview_values() : data;
    if (data.description === "" && data.long_description === "" && data.rate === "") {
        return;
    }

    var type_item = $("#input_item_type_dom").val();
    // console.log(type_item);

    var table_row = '';
    var unit_placeholder = '';
    var item_key = $("body").find('tbody .item').length + 1;

    table_row += '<tr class="sortable item" data-merge-invoice="' + merge_invoice + '" data-bill-expense="' + bill_expense + '">';

    table_row += '<td class="dragger">';

    // Check if quantity is number
    if (isNaN(data.qty)) {
        data.qty = 1;
    }

    // Check if rate is number
    if (data.rate === '' || isNaN(data.rate)) {
        data.rate = 0;
    }

    // Check if rate is number
    if (data.renewable == "0") {
        data.renewable = 0;
    }

    var amount = data.rate * data.qty;
    amount = accounting.formatNumber(amount);
    var tax_name = 'newitems[' + item_key + '][taxname][]';
    $("body").append('<div class="dt-loader"></div>');
    var regex = /<br[^>]*>/gi;
    get_taxes_dropdown_template(tax_name, data.taxname).done(function (tax_dropdown) {

        // order input
        table_row += '<input type="hidden" class="order" name="newitems[' + item_key + '][order]">';
        var long_desc = data.long_description;

        table_row += '</td>';

        table_row += '<td class="bold description"><textarea name="newitems[' + item_key + '][description]" class="form-control" rows="5">' + data.description + '</textarea></td>';

        if (type_item == "domain") {
            data.renewable = 1;
            var domain_name = stripHtml(data.long_description.replace(regex, "\n"));
            table_row += '<td><input type="url"  onblur="checkTypeURL(this)" name="newitems[' + item_key + '][long_description]"  id="long_description' + item_key + '"  class="form-control item_long_descriptions" value="' + domain_name + '" /></td>';
        } else {

            if(typeof long_desc != "undefined"){
                long_desc = long_desc.replace(regex, "\n");
            }
            table_row += '<td><textarea name="newitems[' + item_key + '][long_description]" id="long_description' + item_key + '"  class="form-control item_long_description" rows="5">' + long_desc + '</textarea></td>';
        }

        table_row += '<input type="hidden" id="item_type_dom' + item_key + '" value="' + type_item + '"   name="newitems[' + item_key + '][item_type]" />';

        $("body").find("#item_type_dom" + item_key).val(type_item);

        //##################################################

        if (typeof(data.renewable) != 'undefined' && data.renewable != 'undefined') {

            var isChecked = '';
            var hidden = 'hidden';
            var reuired = '';

            // console.log(data.renewable);

            if (data.renewable == "1") {
                isChecked = 'checked';
                hidden = '';
                reuired = 'required';
            } else {
                isChecked = ""
            }

            table_row += '<td><div class="form-group">';
            table_row += '    <div class="checkbox checkbox-primary no-mtop checkbox-inline">';
            table_row += '        <input type="checkbox" class="isRenewable"  ' + isChecked + ' id="isRenewable-' + item_key + '"  name="newitems[' + item_key + '][ItemRenwable]" value="1">';
            table_row += '        <label for="isRenewable-' + item_key + '" >Yes</label></div></div>';
            table_row += '</td>';

            table_row += '    <td>';
            table_row += '        <div class="form-group">';
            table_row += '            <div class="input-group date isRenewable-' +item_key+'">';
            table_row += '                 <input type="text" ' + reuired + ' id="start_date-' + item_key + '"  name="newitems[' + item_key + '][start_date]" class="form-control from_to_date datepicker" placeholder="Start Date"  value="' + data.start_date + '">';
            table_row += '                 <div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i></div>';
            table_row += '            </div>';
            table_row += '        </div>';
            table_row += '    </td>';

            table_row += '    <td>';
            table_row += '        <div class="form-group">';
            table_row += '            <div class="input-group date isRenewable-' +item_key+'">';
            table_row += '                 <input type="text" ' + reuired + ' id="end_date-' + item_key + '"  name="newitems[' + item_key + '][end_date]" placeholder="End Date" class="form-control from_to_date datepicker" placeholder="End Date" value="' + data.end_date + '">';
            table_row += '                 <div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i></div>';
            table_row += '            </div>';
            table_row += '        </div>';
            table_row += '    </td>';

        }


        //##################################################

        var custom_fields = $('tr.main td.custom_field');
        var cf_has_required = false;

        if (custom_fields.length > 0) {

            $.each(custom_fields, function () {

                var cf = $(this).clone();
                var cf_html = '';
                var cf_field = $(this).find('[data-fieldid]');
                var cf_name = 'newitems[' + item_key + '][custom_fields][items][' + cf_field.attr('data-fieldid') + ']';

                if (cf_field.is(':checkbox')) {

                    var checked = $(this).find('input[type="checkbox"]:checked');
                    var checkboxes = cf.find('input[type="checkbox"]');

                    $.each(checkboxes, function (i, e) {
                        var random_key = Math.random().toString(20).slice(2);
                        $(this).attr('id', random_key)
                            .attr('name', cf_name)
                            .next('label').attr('for', random_key);
                        if ($(this).attr('data-custom-field-required') == '1') {
                            cf_has_required = true;
                        }
                    });

                    $.each(checked, function (i, e) {
                        cf.find('input[value="' + $(e).val() + '"]')
                            .attr('checked', true);
                    });

                    cf_html = cf.html();

                } else if (cf_field.is('input') || cf_field.is('textarea')) {
                    if (cf_field.is('input')) {
                        cf.find('[data-fieldid]').attr('value', cf_field.val());
                    } else {
                        cf.find('[data-fieldid]').html(cf_field.val());
                    }
                    cf.find('[data-fieldid]').attr('name', cf_name);
                    if (cf.find('[data-fieldid]').attr('data-custom-field-required') == '1') {
                        cf_has_required = true;
                    }
                    cf_html = cf.html();
                } else if (cf_field.is('select')) {

                    if ($(this).attr('data-custom-field-required') == '1') {
                        cf_has_required = true;
                    }

                    var selected = $(this).find('select[data-fieldid]').selectpicker('val');
                    selected = typeof(selected != 'array') ? new Array(selected) : selected;

                    // Check if is multidimensional by multi-select customfield
                    selected = selected[0].constructor === Array ? selected[0] : selected;

                    var selectNow = cf.find('select');
                    var $wrapper = $('<div/>');
                    selectNow.attr('name', cf_name);

                    var $select = selectNow.clone();
                    $wrapper.append($select);
                    $.each(selected, function (i, e) {
                        $wrapper.find('select option[value="' + e + '"]').attr('selected', true);
                    });

                    cf_html = $wrapper.html();
                }
                table_row += '<td class="custom_field">' + cf_html + '</td>';
            });
        }

        table_row += '<td><input type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="newitems[' + item_key + '][qty]" value="' + data.qty + '" class="form-control">';

        unit_placeholder = '';

        if (!data.unit || typeof(data.unit) == 'undefined') {
            unit_placeholder = appLang.unit;
            data.unit = '';
        }

        table_row += '<input type="text" placeholder="' + unit_placeholder + '" name="newitems[' + item_key + '][unit]" class="form-control input-transparent text-right" value="' + data.unit + '">';

        table_row += '</td>';

        table_row += '<td class="rate"><input type="number" data-toggle="tooltip" title="' + appLang.item_field_not_formatted + '" onblur="calculate_total();" onchange="calculate_total();" name="newitems[' + item_key + '][rate]" value="' + data.rate + '" class="form-control"></td>';

        table_row += '<td class="taxrate">' + tax_dropdown + '</td>';

        table_row += '<td class="amount" align="right">' + amount + '</td>';

        table_row += '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' + itemid + '); return false;"><i class="fa fa-trash"></i></a></td>';

        table_row += '</tr>';

        $('table.items tbody').append(table_row);

        $(document).trigger({
            type: "item-added-to-table",
            data: data,
            row: table_row
        });

        setTimeout(function () {
            calculate_total();
        }, 15);

        var billed_task = $('input[name="task_id"]').val();
        var billed_expense = $('input[name="expense_id"]').val();

        if (billed_task !== '' && typeof(billed_task) != 'undefined') {
            billed_tasks = billed_task.split(',');
            $.each(billed_tasks, function (i, obj) {
                $('#billed-tasks').append(hidden_input('billed_tasks[' + item_key + '][]', obj));
            });
        }

        if (billed_expense !== '' && typeof(billed_expense) != 'undefined') {
            billed_expenses = billed_expense.split(',');
            $.each(billed_expenses, function (i, obj) {
                $('#billed-expenses').append(hidden_input('billed_expenses[' + item_key + '][]', obj));
            });
        }

        if ($('#item_select').hasClass('ajax-search') && $('#item_select').selectpicker('val') !== '') {
            $('#item_select').prepend('<option></option>');
        }

        init_selectpicker();
        init_datepicker();
        init_color_pickers();
        clear_item_preview_values();
        reorder_items();

        $('body').find('#items-warning').remove();
        $("body").find('.dt-loader').remove();
        $('#item_select').selectpicker('val', '');

        if (cf_has_required && $('.invoice-form').length) {
            validate_invoice_form();
        } else if (cf_has_required && $('.estimate-form').length) {
            validate_estimate_form();
        } else if (cf_has_required && $('.proposal-form').length) {
            validate_proposal_form();
        } else if (cf_has_required && $('.credit-note-form').length) {
            validate_credit_note_form();
        }

        return true;

    });

    return false;
}

// Get the preview main values
function get_item_preview_values() {

    var response = {};
    response.description = $('.main textarea[name="description"]').val();
    response.long_description = $('.main textarea[name="long_description"]').val();
    response.qty = $('.main input[name="quantity"]').val();
    response.taxname = $('.main select.tax').selectpicker('val');
    response.rate = $('.main input[name="rate"]').val();
    response.unit = $('.main input[name="unit"]').val();
    response.renewable = $('.main input[name="isRenewable"]').val();
    response.start_date = $('.main input[name="start_date"]').val();
    response.end_date = $('.main input[name="end_date"]').val();

    return response;
}

// Clear the items added to preview
function clear_item_preview_values(default_taxes) {

    // Get the last taxes applied to be available for the next item
    var last_taxes_applied = $('table.items tbody').find('tr:last-child').find('select').selectpicker('val');
    var previewArea = $('.main');

    previewArea.find('textarea').val(''); // includes cf
    previewArea.find('td.custom_field input[type="checkbox"]').prop('checked', false); // cf
    previewArea.find('td.custom_field input:not(:checkbox):not(:hidden)').val(''); // cf // not hidden for chkbox hidden helpers
    previewArea.find('td.custom_field select').selectpicker('val', ''); // cf
    previewArea.find('input[name="quantity"]').val(1);
    previewArea.find('select.tax').selectpicker('val', last_taxes_applied);
    previewArea.find('input[name="rate"]').val('');
    previewArea.find('input[name="unit"]').val('');
    previewArea.find('input[name="start_date"]').val('');
    previewArea.find('input[name="end_date"]').val('');
    previewArea.find('input[name="isRenewable"]').val('0');

    $('input[name="task_id"]').val('');
    $('input[name="expense_id"]').val('');
}

// Add item to preview
function add_item_to_preview(id) {

    requestGetJSON('invoice_items/get_item_by_id/' + id).done(function (response) {

        //console.log("Gel");

        clear_item_preview_values();
        // alert("");
        console.log(response);

        $('.main textarea[name="description"]').val(response.description);
        $('.main textarea[name="long_description"]').val(response.long_description.replace(/(<|&lt;)br\s*\/*(>|&gt;)/g, " "));
        $(document).find('.main #input_item_type_dom').val(response.item_type);

        _set_item_preview_custom_fields_array(response.custom_fields);

        $('.main input[name="quantity"]').val(1);

        var taxSelectedArray = [];
        if (response.taxname && response.taxrate) {
            taxSelectedArray.push(response.taxname + '|' + response.taxrate);
        }
        if (response.taxname_2 && response.taxrate_2) {
            taxSelectedArray.push(response.taxname_2 + '|' + response.taxrate_2);
        }

        $('.main select.tax').selectpicker('val', taxSelectedArray);
        $('.main input[name="unit"]').val(response.unit);

        var $currency = $("body").find('.accounting-template select[name="currency"]');
        var baseCurency = $currency.attr('data-base');
        var selectedCurrency = $currency.find('option:selected').val();
        var $rateInputPreview = $('.main input[name="rate"]');

        if (baseCurency == selectedCurrency) {
            $rateInputPreview.val(response.rate);
        } else {
            var itemCurrencyRate = response['rate_currency_' + selectedCurrency];
            if (!itemCurrencyRate || parseFloat(itemCurrencyRate) === 0) {
                $rateInputPreview.val(response.rate);
            } else {
                $rateInputPreview.val(itemCurrencyRate);
            }
        }

        $(document).trigger({
            type: "item-added-to-preview",
            item: response,
            item_type: 'item',
        });
    });
}

/**
 *
 * @param html
 * @returns {((string | null) | string) | string}
 */
function stripHtml(html){
    return html.replace(/<[^>]+>/g, '');
}

/**
 *
 * @param abc
 * @returns {*}
 */
function checkTypeURL(abc) {
    var string = abc.value;
    if (!~string.indexOf("http")) {
        string = "http://" + string;
    }
    abc.value = string;
    return abc
}

/**
 *
 * @param id
 */
function init_reciept(id) {
    var _invoiceid = $('input[name="invoiceid"]').val();
    // Check if invoice passed from url, hash is prioritized becuase is last
    if (_invoiceid != '' && !window.location.hash) {
        id = _invoiceid;
        // Clear the current invoice value in case user click on the left sidebar invoices
        $('input[name="invoiceid"]').val('');
    } else {
        // check first if hash exists and not id is passed, becuase id is prioritized
        if (window.location.hash && !id) {
            id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        }
    }
    if (typeof(id) == 'undefined' || id == '') {
        return;
    }

    if (!$('body').hasClass('small-table')) {

        toggle_small_view_receipt('.table-reciept', '#receiptView');
    }
    $('input[name="invoiceid"]').val(id);
    do_hash_helper(id);
    if(id) {
        $('#receiptView').load(admin_url + 'receipts/details/' + id);
    }
    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $('#receiptView').offset().top + 150
        }, 600);
    }
}

/**
 *
 * @param id
 */
function init_renewal(id) {

    var _invoiceid = $('input[name="invoiceid"]').val();
    // Check if invoice passed from url, hash is prioritized becuase is last
    if (_invoiceid != '' && !window.location.hash) {
        id = _invoiceid;
        // Clear the current invoice value in case user click on the left sidebar invoices
        $('input[name="invoiceid"]').val('');
    } else {
        // check first if hash exists and not id is passed, becuase id is prioritized
        if (window.location.hash && !id) {
            id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        }
    }
    if (typeof(id) == 'undefined' || id == '') {
        return;
    }

    if (!$('body').hasClass('small-table')) {

        toggle_small_view_renewal('.table-invoices_renewal', '#invoiceRenewalView');
    }
    $('input[name="invoiceid"]').val(id);
    do_hash_helper(id);
    if(id) {
        $('#invoiceRenewalView').load(admin_url + 'invoices/get_invoice_data_ajax/' + id);
    }
    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $('#invoiceRenewalView').offset().top + 150
        }, 600);
    }
}

/**
 *
 * @param table
 * @param main_data
 */
// Show/hide full table
function toggle_small_view_renewal(table, main_data) {

    $('body').toggleClass('small-table');
    var tablewrap = $('#small-table');
    if (tablewrap.length == 0) {
        return;
    }
    var _visible = false;
    if (tablewrap.hasClass('col-md-5')) {
        tablewrap.removeClass('col-md-5').addClass('col-md-12');
        _visible = true;
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
    } else {
        tablewrap.addClass('col-md-5').removeClass('col-md-12');
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
    }

    var _table = $(table).DataTable();
    $(table).width('100%');
    // Show hide hidden columns
    _table.columns(hidden_columns).visible(_visible, false);
    _table.columns.adjust();
    $(main_data).toggleClass('hide');

}

/**
 *
 * @param name
 * @param val
 * @returns {string}
 */
// Generate hidden input field
function hidden_input(name, val) {
    return '<input type="hidden" name="' + name + '" value="' + val + '">';
}

/**
 *
 * @param table
 * @param main_data
 */
// Show/hide full table
function toggle_small_view_receipt(table, main_data) {

    $('body').toggleClass('small-table');
    var tablewrap = $('#small-table');
    if (tablewrap.length == 0) {
        return;
    }
    var _visible = false;
    if (tablewrap.hasClass('col-md-5')) {
        tablewrap.removeClass('col-md-5').addClass('col-md-12');
        _visible = true;
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
    } else {
        tablewrap.addClass('col-md-5').removeClass('col-md-12');
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
    }

    var _table = $(table).DataTable();
    $(table).width('100%');
    // Show hide hidden columns
    _table.columns(hidden_columns).visible(_visible, false);
    _table.columns.adjust();
    $(main_data).toggleClass('hide');
}


// Add additional server params $_POST
var LeadsServerParams = {
    "view_created": "[name='view_created']",
    "custom_view": "[name='custom_view']",
    "assigned": "[name='view_assigned']",
    "status": "[name='view_status[]']",
    "source": "[name='view_source']",
};

// Init the table
table_leads = $('table.table-leads');
if (table_leads.length) {
    var tableLeadsConsentHeading = table_leads.find('#th-consent');
    var leadsTableNotSortable = [0];
    var leadsTableNotSearchable = [0, table_leads.find('#th-assigned').index()];

    if (tableLeadsConsentHeading.length > 0) {
        leadsTableNotSortable.push(tableLeadsConsentHeading.index());
        leadsTableNotSearchable.push(tableLeadsConsentHeading.index());
    }

    _table_api = initDataTable(table_leads, admin_url + 'leads/table', leadsTableNotSearchable, leadsTableNotSortable, LeadsServerParams, [table_leads.find('th.date-created').index(), 'desc']);

    if (_table_api && tableLeadsConsentHeading.length > 0) {
        _table_api.on('draw', function () {
            var tableData = table_leads.find('tbody tr');
            $.each(tableData, function () {
                $(this).find('td:eq(3)').addClass('bg-light-gray');
            });
        });
    }

    $.each(LeadsServerParams, function (i, obj) {
        $('select' + obj).on('change', function () {
            if ($(this).val() == 'lost' || $(this).val() == 'junk') {
                $("[name='view_status[]']").prop('disabled', true).selectpicker('refresh');
            } else {
                $("[name='view_status[]']").prop('disabled', false).selectpicker('refresh');
            }
            table_leads.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });
}
