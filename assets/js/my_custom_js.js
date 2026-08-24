$(document).ready(function () {

    $long_description = "";

    $(document).on('click', '.item_long_description', function () {

        $long_description = $(this).attr("id");
        // $('.item_long_description').click(function () {

        var datadesc = $(this).val();
        // $("#set_description").val(description);
        $("#modalBox").trigger('click');

        // tinyMCE.activeEditor.setContent(datadesc);
        tinymce.get('description').setContent(datadesc);

    });

    $(document).on('click', '.item_custom_note', function () {

        $long_description = $(this).attr("id");
        // $('.item_long_description').click(function () {

        var datadesc = $(this).val();
        // $("#set_description").val(description);
        $("#modalBox").trigger('click');

        // tinyMCE.activeEditor.setContent(datadesc);
        tinymce.get('description').setContent(datadesc);

    });

    $(document).on('click', '#apply_description', function () {
        // $('.item_long_description').click(function () {

        var description = tinyMCE.activeEditor.getContent();
        $("#" + $long_description).val(description);
    });

    $(document).on('change', '#selectInvoiceNotes', function () {
        // $('.item_long_description').click(function () {
        var noteId = $(this).val();
        // alert("");
        $.ajax({
            url: "/admin/custom_notes/getDescription",
            type: 'POST',
            data: {"noteId": noteId},
            success: function (data) {
                if (data != null && data != "") {
                    tinymce.get('terms').setContent(data);
                }
            },
            error: function (e) {
                alert("Error!");
            }
        });
    });

    $(document).on('change', '#selectProposalCustomNoteFooter', function () {
        // $('.item_long_description').click(function () {
        var noteId = $(this).val();
        $.ajax({
            url: "/admin/custom_notes/getDescription",
            type: 'POST',
            data: {"noteId": noteId},
            success: function (data) {
                if (data != null && data != "") {
                    tinymce.get('customNote_footer').setContent(data);
                }
            },
            error: function (e) {
                alert("Error!");
            }
        });
    });

    $(document).on('change', '#selectProposalCustomNoteHeader', function () {
        // $('.item_long_description').click(function () {
        var noteId = $(this).val();
        $.ajax({
            url: "/admin/custom_notes/getDescription",
            type: 'POST',
            data: {"noteId": noteId},
            success: function (data) {
                if (data != null && data != "") {
                    tinymce.get('customNote_header').setContent(data);
                }
            },
            error: function (e) {
                alert("Error!");
            }
        });
    });

    $(document).on('change', '.changeInvoiceAcceptsStatus', function () {

        var id = $(this).attr('id');
        var status = $(this).val();
        var html = '';

        if (status != "") {
            $.post(admin_url + 'invoices/change_accept_status', {
                invoiceId: id,
                status: status
            }).done(function (data) {
                if (status == 'open') {
                    html = '<span class="label label-info">OPEN</span>';
                }
                if (status == 'accepted') {
                    html = '<span class="label-success label">ACCEPTED</span>';
                }
                if (status == 'rejected') {
                    html = '<span class="label label-danger">REJECTED</span>';
                }
                $("#invoiceApprovalStatus-" + id).html(html);
                $(".invoiceApprovalStatus-" + id).html(html);
                alert_float('success', "Status updated successfully!");
            }).fail(function (data) {
                alert_float('danger', data.responseText);
            });
        }

    });

    $(document).ready(function () {

        $("#clientid").change(function () {
            // alert_float('success', 'yes');
            var clientid = $(this).val();
            $.ajax({
                type: "POST",
                url: "/admin/invoices/get_ajax_client_contacts",
                data: {client: clientid},
                success: function (data) {
                    $(document).find("#customerContacts").html(data);
                    $(document).find('.selectpicker').selectpicker('refresh');
                },
            });
        });

        function getGroupsItems() {

            var group_id = $("#group_id option:selected").val();
            var client = $("#group_client_id").val();

            $.ajax({
                type: "POST",
                url: "/admin/clients/get_clients_services",
                data: {client: client, item_group: group_id},
                success: function (data) {
                    $("#invoice_items").html(data);
                },
            });

        }

        $("#group_id").change(function () {
            // alert_float('success', 'yes');
            var group_id = $(this).val();
            var client = $("#group_client_id").val();

            $.ajax({
                type: "POST",
                url: "/admin/clients/get_clients_services",
                data: {client: client, item_group: group_id},
                success: function (data) {
                    $("#invoice_items").html(data);
                },
            });
        });

        $(".customer_listing").click(function () {
            var status  =   $(this).attr('id');
            alert_float('success',status);
        });

    });

    $("#report-from-agents").change(function () {
        event.preventDefault();
        $('#report-to-agents').prop("disabled", false);
    });

    $("#report-to-agents").change(function () {
        event.preventDefault();
        var time_to = $(this).val();
        var time_from = $("#report-from-agents").val();
        $.ajax({
            type: "POST",
            url:  admin_url+'home/home_sale_agent_filter',
            data: {time_to: time_to, time_from: time_from},
            success: function (data) {
                if (data) {
                    $("#agents_details #AgentsDetailsTable #agents_invoice_details").html(data);
                    alert_float('success', "Record filtered successfully!");
                }
            },
            error: function (e) {
                alert_float('danger', "Something went wrong!");
            }
        });
    });

    //Filter for the agents recipts and invoices
    $("#agents-months-report").change(function () {
        var time_period = $(this).val();
        if(time_period == 'custom_period'){
            $("#agents_cutom_date_range").removeClass("hide");
            return false;
        }else{
            $("#report-from-agents").val("");
            $("#report-to-agents").val("");
            $('#report-to-agents').prop("disabled", true);
            $("#agents_cutom_date_range").addClass("hide");
        }
        $.ajax({
            type: "POST",
            url:  admin_url+'home/home_sale_agent_filter',
            data: {time_period: time_period},
            success: function (data) {
                if (data) {
                    $("#agents_details #AgentsDetailsTable #agents_invoice_details").html(data);
                    alert_float('success', "Record filtered successfully!");
                }
            },
            error: function (e) {
                alert_float('danger', "Something went wrong!");
            }
        });
    });

    //Filter for the agents recipts and invoices
    $('#agents_details #filter_agent_detail').click(function () {
        var date = $('#date').val();
            $.ajax({
                url: admin_url+'home/home_sale_agent_filter',
                type: 'POST',
                data: {"filterRequest": true, "date": date},
                success: function (data) {
                    //console.log(data);
                    if (data) {
                        $("#agents_details #AgentsDetailsTable #agents_invoice_details").html(data);
                        alert_float('success', "Record updated successfully!");
                    }
                },
                error: function (e) {
                    alert_float('danger', "Status not Updated!");
                }
            });
        });




    //Filter for the agents Tax reports
    //proformal , Total Tax invoices and total collected invoices
    $("#agents-tax-report").change(function () {
        var year = $(this).val();
        $.ajax({
            type: "POST",
            url:  admin_url+'home/home_tax_agent_filter',
            data: {year: year},
            success: function (data) {
                if (data) {
                    $("#agents_details #AgentsTaxTable #agents_tax_details").html(data);
                    alert_float('success', "Record filtered successfully!");
                }
            },
            error: function (e) {
                alert_float('danger', "Something went wrong!");
            }
        });
    });


});
