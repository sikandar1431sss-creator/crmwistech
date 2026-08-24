<div class="modal fade email-template" data-editor-id=".<?php echo 'tinymce-1'; ?>" id="invoice_send_to_client_modal"
     tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('invoice_send_to_client_modal_heading'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            
                             <?php
                            if (isset($customer_id)) {
                                $selected = array();
                                $contacts = $this->clients_model->get_contacts($customer_id);
                                foreach ($contacts as $contact) {
                                    if (has_contact_permission('invoices', $contact['id'])) {
                                        array_push($selected, $contact['id']);
                                    }
                                }
                                if (count($selected) == 0) {
                                    echo '<p class="text-danger">' . _l('sending_email_contact_permissions_warning', _l('customer_permission_invoice')) . '</p><hr />';
                                }
                                echo render_select('SaveSend[sent_to][]', $contacts, array('id', 'email', 'firstname,lastname'), 'invoice_estimate_sent_to_email', $selected, array('multiple' => true), array(), '', '', false);
                            }
                            ?>
                            <div id="customerContacts"></div>
                        </div>
                        <?php echo render_input('SaveSend[cc]', 'CC'); ?>
                        <hr/>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="SaveSend[attach_pdf]" id="attach_pdf" checked>
                            <label for="attach_pdf"><?php echo _l('invoice_send_to_client_attach_pdf'); ?></label>
                        </div>
                        <h5 class="bold"><?php echo _l('invoice_send_to_client_preview_template'); ?></h5>
                        <hr/>
                        <?php echo render_textarea('SaveSend[email_template_custom]', '', $template->message, array(), array(), '', 'tinymce-1'); ?>
                        <?php echo form_hidden('SaveSend[template_name]', $template_name); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"
                        class="btn btn-info send-and-save"><?php echo _l('send'); ?></button>
            </div>
        </div>
    </div>
</div>
