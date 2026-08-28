<style>
    #zoho_post_progress_modal .modal-dialog {
        max-width: 680px;
        width: 92%;
        margin: 30px auto;
    }
    #zoho_post_progress_modal .modal-content {
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        border: none;
        overflow: hidden;
    }
    #zoho_post_progress_modal .modal-header {
        background: #ffffff;
        color: #000000;
        padding: 16px 22px;
        border-bottom: 1px solid #e2e8f0;
    }
    #zoho_post_progress_modal .modal-header .close {
        color: #000000 !important;
        opacity: 0.85;
        font-size: 24px;
        font-weight: bold;
        text-shadow: none;
        margin-top: -2px;
    }
    #zoho_post_progress_modal .modal-header .close:hover,
    #zoho_post_progress_modal .modal-header .close:focus {
        color: #000000 !important;
        opacity: 1;
    }
    #zoho_post_progress_modal .modal-title {
        font-size: 17px;
        font-weight: 600;
        letter-spacing: 0.3px;
        color: #000000;
        display: flex;
        align-items: center;
    }
    #zoho_post_progress_modal .modal-title i.zoho-icon {
        background: #0284c7;
        color: #ffffff;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-size: 14px;
    }
    #zoho_post_progress_modal .modal-body {
        padding: 20px 24px 15px;
        background: #f8fafc;
    }
    .zoho-info-banner {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 12px 16px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .zoho-info-item {
        font-size: 12px;
        color: #64748b;
    }
    .zoho-info-item strong {
        color: #1e293b;
        font-size: 13px;
        display: block;
    }
    .zoho-progress-container {
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 18px;
    }
    .zoho-progress-bar {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #3b82f6 0%, #10b981 100%);
        transition: width 0.4s ease;
    }
    .zoho-steps-wrapper {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 16px;
    }
    .zoho-step-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        align-items: flex-start;
        transition: all 0.25s ease;
        position: relative;
    }
    .zoho-step-card.step-active {
        border-color: #3b82f6;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
        background: #f0f7ff;
    }
    .zoho-step-card.step-success {
        border-color: #86efac;
        background: #f0fdf4;
    }
    .zoho-step-card.step-error {
        border-color: #fca5a5;
        background: #fef2f2;
    }
    .zoho-step-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        margin-right: 14px;
        flex-shrink: 0;
        background: #f1f5f9;
        color: #94a3b8;
        border: 2px solid #cbd5e1;
        transition: all 0.2s ease;
    }
    .step-active .zoho-step-icon {
        background: #3b82f6;
        color: #ffffff;
        border-color: #2563eb;
        animation: zohoPulse 1.5s infinite;
    }
    .step-success .zoho-step-icon {
        background: #10b981;
        color: #ffffff;
        border-color: #059669;
    }
    .step-error .zoho-step-icon {
        background: #ef4444;
        color: #ffffff;
        border-color: #dc2626;
    }
    @keyframes zohoPulse {
        0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(59, 130, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
    .zoho-step-body {
        flex: 1;
        min-width: 0;
    }
    .zoho-step-title {
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 2px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .zoho-step-desc {
        font-size: 12px;
        color: #64748b;
        line-height: 1.4;
    }
    .step-active .zoho-step-desc {
        color: #1e40af;
    }
    .step-success .zoho-step-desc {
        color: #166534;
    }
    .step-error .zoho-step-desc {
        color: #991b1b;
        font-weight: 500;
    }
    .zoho-step-badge {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .badge-pending {
        background: #f1f5f9;
        color: #64748b;
    }
    .badge-running {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .badge-exists {
        background: #dcfce7;
        color: #15803d;
    }
    .badge-created {
        background: #e0e7ff;
        color: #4338ca;
    }
    .badge-failed {
        background: #fee2e2;
        color: #b91c1c;
    }
    .zoho-subinvoices-list {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed #cbd5e1;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .zoho-subinvoice-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .zoho-subinvoice-item.inv-exists {
        border-left: 3px solid #10b981;
    }
    .zoho-subinvoice-item.inv-created {
        border-left: 3px solid #6366f1;
    }
    .zoho-subinvoice-item.inv-creating {
        border-left: 3px solid #3b82f6;
    }
    .zoho-subinvoice-item.inv-failed {
        border-left: 3px solid #ef4444;
    }
    .zoho-log-header {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 6px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .zoho-log-header:hover {
        color: #334155;
    }
    .zoho-log-console {
        background: #0f172a;
        color: #e2e8f0;
        border-radius: 6px;
        padding: 10px 14px;
        font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
        font-size: 11.5px;
        max-height: 140px;
        overflow-y: auto;
        line-height: 1.5;
    }
    .zoho-log-line {
        margin-bottom: 3px;
        word-break: break-word;
    }
    .zoho-log-line .log-time {
        color: #64748b;
        margin-right: 6px;
    }
    .zoho-log-line.log-info { color: #93c5fd; }
    .zoho-log-line.log-success { color: #86efac; font-weight: 600; }
    .zoho-log-line.log-warning { color: #fde047; }
    .zoho-log-line.log-error { color: #fca5a5; font-weight: 600; }
    .zoho-final-alert {
        margin-top: 14px;
        border-radius: 6px;
        padding: 12px 16px;
        font-size: 13px;
        display: none;
    }
    #zoho_post_progress_modal .modal-footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 12px 24px;
    }
</style>

<script>
    if (typeof window.openZohoPostProgress === 'undefined') {

        window.ensureZohoPostProgressModal = function () {
            if ($('#zoho_post_progress_modal').length) {
                return;
            }

            var modalHtml =
                '<div class="modal fade" id="zoho_post_progress_modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">' +
                '  <div class="modal-dialog" role="document">' +
                '    <div class="modal-content">' +
                '      <div class="modal-header">' +
                '        <button type="button" class="close zoho-post-close" data-dismiss="modal" aria-label="Close" disabled><span aria-hidden="true">&times;</span></button>' +
                '        <h4 class="modal-title">' +
                '          <i class="fa fa-cloud-upload zoho-icon"></i>' +
                '          <span id="zoho_post_progress_title">Posting to Zoho Books</span>' +
                '        </h4>' +
                '      </div>' +
                '      <div class="modal-body">' +
                '        <div class="zoho-info-banner" id="zoho_info_banner" style="display:none;">' +
                '          <div class="zoho-info-item"><span id="zoho_info_label_1">Reference:</span> <strong id="zoho_info_ref">-</strong></div>' +
                '          <div class="zoho-info-item"><span>Customer:</span> <strong id="zoho_info_customer">-</strong></div>' +
                '          <div class="zoho-info-item"><span>Amount:</span> <strong id="zoho_info_amount">-</strong></div>' +
                '        </div>' +
                '        <div class="zoho-progress-container">' +
                '          <div class="zoho-progress-bar" id="zoho_progress_bar"></div>' +
                '        </div>' +
                '        <div class="zoho-steps-wrapper" id="zoho_steps_wrapper">' +
                '        </div>' +
                '        <div class="zoho-final-alert" id="zoho_final_alert"></div>' +
                '        <div class="mtop10">' +
                '          <div class="zoho-log-header" id="zoho_log_toggle">' +
                '            <span><i class="fa fa-terminal"></i> Activity Log</span>' +
                '            <span id="zoho_log_toggle_text"><i class="fa fa-chevron-up"></i></span>' +
                '          </div>' +
                '          <div class="zoho-log-console" id="zoho_log_console"></div>' +
                '        </div>' +
                '      </div>' +
                '      <div class="modal-footer">' +
                '        <button type="button" class="btn btn-default zoho-post-close" data-dismiss="modal" disabled>Close</button>' +
                '      </div>' +
                '    </div>' +
                '  </div>' +
                '</div>';

            $('body').append(modalHtml);

            $(document).on('click', '#zoho_log_toggle', function () {
                $('#zoho_log_console').slideToggle(200, function () {
                    var isVisible = $(this).is(':visible');
                    $('#zoho_log_toggle_text').html(isVisible ? '<i class="fa fa-chevron-up"></i>' : '<i class="fa fa-chevron-down"></i>');
                });
            });
        };

        window.openZohoPostProgress = function (title) {
            window.ensureZohoPostProgressModal();
            $('#zoho_post_progress_title').text(title || 'Posting to Zoho Books');
            $('#zoho_info_banner').hide();
            $('#zoho_progress_bar').css('width', '5%');
            $('#zoho_steps_wrapper').html('');
            $('#zoho_log_console').html('');
            $('#zoho_final_alert').hide().removeClass('alert alert-success alert-danger alert-warning').html('');
            $('#zoho_post_progress_modal .zoho-post-close').prop('disabled', true);
            $('#zoho_post_progress_modal').modal('show');
        };

        window.addZohoPostProgress = function (type, message) {
            window.ensureZohoPostProgressModal();
            var now = new Date();
            var timeStr = ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2) + ':' + ('0' + now.getSeconds()).slice(-2);
            var logClass = 'log-info';
            if (type === 'success') logClass = 'log-success';
            if (type === 'error') logClass = 'log-error';
            if (type === 'warning') logClass = 'log-warning';

            var $line = $('<div class="zoho-log-line ' + logClass + '"><span class="log-time">[' + timeStr + ']</span> ' + $('<div>').text(message).html() + '</div>');
            $('#zoho_log_console').append($line);
            var consoleEl = document.getElementById('zoho_log_console');
            if (consoleEl) {
                consoleEl.scrollTop = consoleEl.scrollHeight;
            }
        };

        window.finishZohoPostProgress = function (isSuccess, finalMessage) {
            $('#zoho_post_progress_modal .zoho-post-close').prop('disabled', false);
            if (isSuccess) {
                $('#zoho_progress_bar').css('width', '100%');
                if (finalMessage) {
                    $('#zoho_final_alert').removeClass('alert-danger alert-warning').addClass('alert alert-success').html('<i class="fa fa-check-circle"></i> ' + finalMessage).slideDown(200);
                }
            } else if (finalMessage) {
                $('#zoho_final_alert').removeClass('alert-success alert-warning').addClass('alert alert-danger').html('<i class="fa fa-exclamation-triangle"></i> ' + finalMessage).slideDown(200);
            }
        };

        // Render standard receipt steps in the modal
        window.renderReceiptProgressSteps = function () {
            var html =
                '<div class="zoho-step-card" id="step_card_customer">' +
                '  <div class="zoho-step-icon"><i class="fa fa-user"></i></div>' +
                '  <div class="zoho-step-body">' +
                '    <div class="zoho-step-title">' +
                '      <span>1. Checking Customer</span>' +
                '      <span class="zoho-step-badge badge-pending" id="badge_step_customer">Waiting</span>' +
                '    </div>' +
                '    <div class="zoho-step-desc" id="desc_step_customer">Verifying customer existence & currency mapping in Zoho Books...</div>' +
                '  </div>' +
                '</div>' +
                '<div class="zoho-step-card" id="step_card_invoices">' +
                '  <div class="zoho-step-body" style="margin-left: 46px;">' +
                '    <div class="zoho-step-title">' +
                '      <span>2. Checking Invoice(s)</span>' +
                '      <span class="zoho-step-badge badge-pending" id="badge_step_invoices">Waiting</span>' +
                '    </div>' +
                '    <div class="zoho-step-desc" id="desc_step_invoices">Verifying invoices linked to this receipt...</div>' +
                '    <div class="zoho-subinvoices-list" id="subinvoices_list" style="display:none;"></div>' +
                '  </div>' +
                '  <div class="zoho-step-icon" style="position:absolute; left:16px; top:12px;"><i class="fa fa-file-text-o"></i></div>' +
                '</div>' +
                '<div class="zoho-step-card" id="step_card_receipt">' +
                '  <div class="zoho-step-icon"><i class="fa fa-credit-card"></i></div>' +
                '  <div class="zoho-step-body">' +
                '    <div class="zoho-step-title">' +
                '      <span>3. Creating Receipt</span>' +
                '      <span class="zoho-step-badge badge-pending" id="badge_step_receipt">Waiting</span>' +
                '    </div>' +
                '    <div class="zoho-step-desc" id="desc_step_receipt">Creating payment allocation in Zoho Books...</div>' +
                '  </div>' +
                '</div>';
            $('#zoho_steps_wrapper').html(html);
        };

        // Render standard invoice steps in the modal
        window.renderInvoiceProgressSteps = function () {
            var html =
                '<div class="zoho-step-card" id="step_card_customer">' +
                '  <div class="zoho-step-icon"><i class="fa fa-user"></i></div>' +
                '  <div class="zoho-step-body">' +
                '    <div class="zoho-step-title">' +
                '      <span>1. Checking Customer</span>' +
                '      <span class="zoho-step-badge badge-pending" id="badge_step_customer">Waiting</span>' +
                '    </div>' +
                '    <div class="zoho-step-desc" id="desc_step_customer">Verifying customer existence & currency mapping in Zoho Books...</div>' +
                '  </div>' +
                '</div>' +
                '<div class="zoho-step-card" id="step_card_invoice">' +
                '  <div class="zoho-step-icon"><i class="fa fa-file-text-o"></i></div>' +
                '  <div class="zoho-step-body">' +
                '    <div class="zoho-step-title">' +
                '      <span>2. Creating Invoice</span>' +
                '      <span class="zoho-step-badge badge-pending" id="badge_step_invoice">Waiting</span>' +
                '    </div>' +
                '    <div class="zoho-step-desc" id="desc_step_invoice">Preparing items, taxes and posting invoice to Zoho Books...</div>' +
                '  </div>' +
                '</div>';
            $('#zoho_steps_wrapper').html(html);
        };

        // Stream Zoho Post Receipt Live
        window.startZohoPostReceipt = function (receipt_id, $btn) {
            if (!receipt_id) {
                alert_float('danger', 'Invalid receipt ID.');
                return;
            }

            if ($btn) {
                if ($btn.hasClass('disabled') || $btn.prop('disabled')) {
                    return;
                }
                $btn.prop('disabled', true).addClass('disabled');
            }

            window.openZohoPostProgress('Posting Receipt #' + receipt_id + ' to Zoho Books');
            window.renderReceiptProgressSteps();
            window.addZohoPostProgress('info', 'Connecting to Zoho Books service for Receipt #' + receipt_id + '...');

            var sseUrl = "<?php echo admin_url('receipts/post_receipt_zoho_stream'); ?>?receipt_id=" + encodeURIComponent(receipt_id);
            var eventSource = new EventSource(sseUrl);

            var isCompleted = false;

            eventSource.onmessage = function (event) {
                if (!event.data) return;
                var data;
                try {
                    data = JSON.parse(event.data);
                } catch (e) {
                    console.error('Invalid JSON event:', event.data);
                    return;
                }

                if (data.type === 'init') {
                    $('#zoho_info_label_1').text('Receipt:');
                    $('#zoho_info_ref').text(data.receipt_num || ('#' + receipt_id));
                    $('#zoho_info_customer').text(data.client_name || '-');
                    $('#zoho_info_amount').text((data.amount ? data.amount : '') + ' ' + (data.currency || ''));
                    $('#zoho_info_banner').slideDown(150);
                    $('#zoho_progress_bar').css('width', '15%');
                    window.addZohoPostProgress('info', data.message || ('Initializing receipt #' + receipt_id));
                }
                else if (data.type === 'step_start') {
                    if (data.step === 'customer') {
                        $('#step_card_customer').removeClass('step-success step-error').addClass('step-active');
                        $('#step_card_customer .zoho-step-icon').html('<i class="fa fa-spinner fa-spin"></i>');
                        $('#badge_step_customer').removeClass('badge-pending badge-exists badge-created badge-failed').addClass('badge-running').text('Checking...');
                        $('#desc_step_customer').text(data.message || 'Checking customer in Zoho...');
                        $('#zoho_progress_bar').css('width', '25%');
                    } else if (data.step === 'invoices') {
                        $('#step_card_invoices').removeClass('step-success step-error').addClass('step-active');
                        $('#step_card_invoices .zoho-step-icon').html('<i class="fa fa-spinner fa-spin"></i>');
                        $('#badge_step_invoices').removeClass('badge-pending badge-exists badge-created badge-failed').addClass('badge-running').text('Checking...');
                        $('#desc_step_invoices').text(data.message || 'Checking linked invoice(s)...');
                        $('#subinvoices_list').show();
                        $('#zoho_progress_bar').css('width', '50%');
                    } else if (data.step === 'receipt') {
                        $('#step_card_receipt').removeClass('step-success step-error').addClass('step-active');
                        $('#step_card_receipt .zoho-step-icon').html('<i class="fa fa-spinner fa-spin"></i>');
                        $('#badge_step_receipt').removeClass('badge-pending badge-exists badge-created badge-failed').addClass('badge-running').text('Creating...');
                        $('#desc_step_receipt').text(data.message || 'Creating receipt in Zoho Books...');
                        $('#zoho_progress_bar').css('width', '80%');
                    }
                    window.addZohoPostProgress('info', data.message);
                }
                else if (data.type === 'step_update') {
                    if (data.step === 'customer') {
                        $('#desc_step_customer').text(data.message);
                        if (data.status === 'exists') {
                            $('#badge_step_customer').removeClass('badge-pending badge-running').addClass('badge-exists').text('Already Exists');
                        } else if (data.status === 'created') {
                            $('#badge_step_customer').removeClass('badge-pending badge-running').addClass('badge-created').text('Created');
                        } else if (data.status === 'creating') {
                            $('#badge_step_customer').removeClass('badge-pending').addClass('badge-running').text('Creating...');
                        }
                    } else if (data.step === 'invoices') {
                        $('#desc_step_invoices').text(data.message);
                    } else if (data.step === 'receipt') {
                        $('#desc_step_receipt').text(data.message);
                    }
                    window.addZohoPostProgress(data.log_type || 'info', data.message);
                }
                else if (data.type === 'step_done') {
                    if (data.step === 'customer') {
                        $('#step_card_customer').removeClass('step-active').addClass('step-success');
                        $('#step_card_customer .zoho-step-icon').html('<i class="fa fa-check"></i>');
                        $('#desc_step_customer').text(data.message || 'Customer verified in Zoho Books.');
                        $('#zoho_progress_bar').css('width', '40%');
                    } else if (data.step === 'invoices') {
                        $('#step_card_invoices').removeClass('step-active').addClass('step-success');
                        $('#step_card_invoices .zoho-step-icon').html('<i class="fa fa-check"></i>');
                        $('#badge_step_invoices').removeClass('badge-running').addClass('badge-exists').text('Verified');
                        $('#desc_step_invoices').text(data.message || 'All linked invoices verified in Zoho.');
                        $('#zoho_progress_bar').css('width', '75%');
                    } else if (data.step === 'receipt') {
                        $('#step_card_receipt').removeClass('step-active').addClass('step-success');
                        $('#step_card_receipt .zoho-step-icon').html('<i class="fa fa-check"></i>');
                        $('#badge_step_receipt').removeClass('badge-running').addClass('badge-exists').text('Posted');
                        $('#desc_step_receipt').text(data.message || 'Receipt posted to Zoho Books.');
                        $('#zoho_progress_bar').css('width', '100%');
                    }
                    window.addZohoPostProgress('success', data.message);
                }
                else if (data.type === 'invoice_update') {
                    var invKey = 'subinv_' + (data.invoice_id || data.index);
                    var $invRow = $('#' + invKey);
                    if (!$invRow.length) {
                        $invRow = $('<div class="zoho-subinvoice-item" id="' + invKey + '">' +
                            '  <div><strong>' + $('<div>').text(data.invoice_number || ('Invoice #' + data.index)).html() + '</strong>: <span class="subinv-msg">Checking...</span></div>' +
                            '  <span class="zoho-step-badge badge-running subinv-badge">Checking</span>' +
                            '</div>');
                        $('#subinvoices_list').append($invRow);
                    }

                    $invRow.find('.subinv-msg').text(data.message || '');
                    if (data.status === 'exists') {
                        $invRow.removeClass('inv-creating inv-failed').addClass('inv-exists');
                        $invRow.find('.subinv-badge').removeClass('badge-running badge-failed').addClass('badge-exists').text('Already Exists');
                    } else if (data.status === 'creating') {
                        $invRow.removeClass('inv-exists inv-failed').addClass('inv-creating');
                        $invRow.find('.subinv-badge').removeClass('badge-exists badge-failed').addClass('badge-running').text('Creating...');
                    } else if (data.status === 'created') {
                        $invRow.removeClass('inv-creating inv-failed').addClass('inv-created');
                        $invRow.find('.subinv-badge').removeClass('badge-running badge-failed').addClass('badge-created').text('Created');
                    } else if (data.status === 'error') {
                        $invRow.removeClass('inv-creating inv-exists').addClass('inv-failed');
                        $invRow.find('.subinv-badge').removeClass('badge-running badge-exists badge-created').addClass('badge-failed').text('Failed');
                    }
                    window.addZohoPostProgress(data.log_type || 'info', data.message);
                }
                else if (data.type === 'complete') {
                    isCompleted = true;
                    eventSource.close();
                    window.finishZohoPostProgress(true, data.message || 'Receipt posted to Zoho Books successfully!');
                    window.addZohoPostProgress('success', data.message || 'Receipt posted to Zoho Books successfully!');
                    alert_float('success', 'Receipt has been posted to Zoho successfully.');
                    if ($btn) {
                        $btn.removeAttr('data_id').removeClass('btn-success').addClass('btn-default').html('<i class="fa fa-clipboard"> Posted</i>');
                    }
                }
                else if (data.type === 'error') {
                    isCompleted = true;
                    eventSource.close();
                    var errStep = data.step || 'receipt';
                    if (errStep === 'customer') {
                        $('#step_card_customer').removeClass('step-active step-success').addClass('step-error');
                        $('#step_card_customer .zoho-step-icon').html('<i class="fa fa-times"></i>');
                        $('#badge_step_customer').removeClass('badge-running badge-exists badge-created').addClass('badge-failed').text('Failed');
                        $('#desc_step_customer').text(data.message);
                    } else if (errStep === 'invoices') {
                        $('#step_card_invoices').removeClass('step-active step-success').addClass('step-error');
                        $('#step_card_invoices .zoho-step-icon').html('<i class="fa fa-times"></i>');
                        $('#badge_step_invoices').removeClass('badge-running badge-exists badge-created').addClass('badge-failed').text('Failed');
                        $('#desc_step_invoices').text(data.message);
                    } else {
                        $('#step_card_receipt').removeClass('step-active step-success').addClass('step-error');
                        $('#step_card_receipt .zoho-step-icon').html('<i class="fa fa-times"></i>');
                        $('#badge_step_receipt').removeClass('badge-running badge-exists badge-created').addClass('badge-failed').text('Failed');
                        $('#desc_step_receipt').text(data.message);
                    }
                    window.finishZohoPostProgress(false, data.message);
                    window.addZohoPostProgress('error', data.message);
                    alert_float('danger', data.message);
                    if ($btn) {
                        $btn.prop('disabled', false).removeClass('disabled');
                    }
                }
            };

            eventSource.onerror = function () {
                if (!isCompleted) {
                    eventSource.close();
                    var errMsg = 'Connection closed or lost before Zoho confirmed the final response.';
                    window.finishZohoPostProgress(false, errMsg);
                    window.addZohoPostProgress('error', errMsg);
                    if ($btn) {
                        $btn.prop('disabled', false).removeClass('disabled');
                    }
                }
            };
        };

        // Stream Zoho Post Invoice Live
        window.startZohoPostInvoice = function (invoice_id, $btn) {
            if (!invoice_id) {
                alert_float('danger', 'Invalid invoice ID.');
                return;
            }

            if ($btn) {
                if ($btn.hasClass('disabled') || $btn.prop('disabled')) {
                    return;
                }
                $btn.prop('disabled', true).addClass('disabled');
            }

            window.openZohoPostProgress('Posting Invoice #' + invoice_id + ' to Zoho Books');
            window.renderInvoiceProgressSteps();
            window.addZohoPostProgress('info', 'Connecting to Zoho Books service for Invoice #' + invoice_id + '...');

            var sseUrl = "<?php echo admin_url('receipts/post_invoice_zoho_stream'); ?>?invoice_id=" + encodeURIComponent(invoice_id);
            var eventSource = new EventSource(sseUrl);

            var isCompleted = false;

            eventSource.onmessage = function (event) {
                if (!event.data) return;
                var data;
                try {
                    data = JSON.parse(event.data);
                } catch (e) {
                    console.error('Invalid JSON event:', event.data);
                    return;
                }

                if (data.type === 'init') {
                    $('#zoho_info_label_1').text('Invoice:');
                    $('#zoho_info_ref').text(data.invoice_num || ('#' + invoice_id));
                    $('#zoho_info_customer').text(data.client_name || '-');
                    $('#zoho_info_amount').text((data.amount ? data.amount : '') + ' ' + (data.currency || ''));
                    $('#zoho_info_banner').slideDown(150);
                    $('#zoho_progress_bar').css('width', '20%');
                    window.addZohoPostProgress('info', data.message || ('Initializing invoice #' + invoice_id));
                }
                else if (data.type === 'step_start') {
                    if (data.step === 'customer') {
                        $('#step_card_customer').removeClass('step-success step-error').addClass('step-active');
                        $('#step_card_customer .zoho-step-icon').html('<i class="fa fa-spinner fa-spin"></i>');
                        $('#badge_step_customer').removeClass('badge-pending badge-exists badge-created badge-failed').addClass('badge-running').text('Checking...');
                        $('#desc_step_customer').text(data.message || 'Checking customer in Zoho...');
                        $('#zoho_progress_bar').css('width', '35%');
                    } else if (data.step === 'invoice') {
                        $('#step_card_invoice').removeClass('step-success step-error').addClass('step-active');
                        $('#step_card_invoice .zoho-step-icon').html('<i class="fa fa-spinner fa-spin"></i>');
                        $('#badge_step_invoice').removeClass('badge-pending badge-exists badge-created badge-failed').addClass('badge-running').text('Creating...');
                        $('#desc_step_invoice').text(data.message || 'Posting invoice to Zoho Books...');
                        $('#zoho_progress_bar').css('width', '70%');
                    }
                    window.addZohoPostProgress('info', data.message);
                }
                else if (data.type === 'step_update') {
                    if (data.step === 'customer') {
                        $('#desc_step_customer').text(data.message);
                        if (data.status === 'exists') {
                            $('#badge_step_customer').removeClass('badge-pending badge-running').addClass('badge-exists').text('Already Exists');
                        } else if (data.status === 'created') {
                            $('#badge_step_customer').removeClass('badge-pending badge-running').addClass('badge-created').text('Created');
                        } else if (data.status === 'creating') {
                            $('#badge_step_customer').removeClass('badge-pending').addClass('badge-running').text('Creating...');
                        }
                    } else if (data.step === 'invoice') {
                        $('#desc_step_invoice').text(data.message);
                    }
                    window.addZohoPostProgress(data.log_type || 'info', data.message);
                }
                else if (data.type === 'step_done') {
                    if (data.step === 'customer') {
                        $('#step_card_customer').removeClass('step-active').addClass('step-success');
                        $('#step_card_customer .zoho-step-icon').html('<i class="fa fa-check"></i>');
                        $('#desc_step_customer').text(data.message || 'Customer verified in Zoho Books.');
                        $('#zoho_progress_bar').css('width', '60%');
                    } else if (data.step === 'invoice') {
                        $('#step_card_invoice').removeClass('step-active').addClass('step-success');
                        $('#step_card_invoice .zoho-step-icon').html('<i class="fa fa-check"></i>');
                        $('#badge_step_invoice').removeClass('badge-running').addClass('badge-exists').text('Posted');
                        $('#desc_step_invoice').text(data.message || 'Invoice posted to Zoho Books.');
                        $('#zoho_progress_bar').css('width', '100%');
                    }
                    window.addZohoPostProgress('success', data.message);
                }
                else if (data.type === 'complete') {
                    isCompleted = true;
                    eventSource.close();
                    window.finishZohoPostProgress(true, data.message || 'Invoice posted to Zoho Books successfully!');
                    window.addZohoPostProgress('success', data.message || 'Invoice posted to Zoho Books successfully!');
                    alert_float('success', 'Invoice has been posted to Zoho successfully.');
                    if ($btn) {
                        $btn.removeAttr('data_id').removeClass('btn-success').addClass('btn-default').html('<i class="fa fa-clipboard"> Posted</i>');
                    }
                }
                else if (data.type === 'error') {
                    isCompleted = true;
                    eventSource.close();
                    var errStep = data.step || 'invoice';
                    if (errStep === 'customer') {
                        $('#step_card_customer').removeClass('step-active step-success').addClass('step-error');
                        $('#step_card_customer .zoho-step-icon').html('<i class="fa fa-times"></i>');
                        $('#badge_step_customer').removeClass('badge-running badge-exists badge-created').addClass('badge-failed').text('Failed');
                        $('#desc_step_customer').text(data.message);
                    } else {
                        $('#step_card_invoice').removeClass('step-active step-success').addClass('step-error');
                        $('#step_card_invoice .zoho-step-icon').html('<i class="fa fa-times"></i>');
                        $('#badge_step_invoice').removeClass('badge-running badge-exists badge-created').addClass('badge-failed').text('Failed');
                        $('#desc_step_invoice').text(data.message);
                    }
                    window.finishZohoPostProgress(false, data.message);
                    window.addZohoPostProgress('error', data.message);
                    alert_float('danger', data.message);
                    if ($btn) {
                        $btn.prop('disabled', false).removeClass('disabled');
                    }
                }
            };

            eventSource.onerror = function () {
                if (!isCompleted) {
                    eventSource.close();
                    var errMsg = 'Connection closed or lost before Zoho confirmed the final response.';
                    window.finishZohoPostProgress(false, errMsg);
                    window.addZohoPostProgress('error', errMsg);
                    if ($btn) {
                        $btn.prop('disabled', false).removeClass('disabled');
                    }
                }
            };
        };
    }
</script>
