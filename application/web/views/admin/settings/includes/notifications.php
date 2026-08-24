<?php echo form_hidden('notifications_settings'); ?>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="general">
        <h4 class="bold">
            <?php echo _l('settings_notifications_general'); ?>
        </h4>
        <hr/>
        <div class="row">
            <div class="col-md-12">
                <h4 class="bold">
                    <?php echo _l('settings_notifications_tasks'); ?>
                </h4>
                <label for="decimal_separator"><?php echo _l('settings_tasks_lead_creation_remind_call'); ?></label>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php $time = (get_option('lead_assigned_task_duration')) ? get_option('lead_assigned_task_duration') : "30"; ?>
                    <?php echo render_input('settings[lead_assigned_task_duration]', '', $time); ?>
                </div>
            </div>
            <div class="col-md-6">
                <select id="decimal_separator" class="selectpicker" name="settings[lead_assigned_task_duration_type]"
                        data-width="100%">
                    <option value="minute" <?= (get_option('lead_assigned_task_duration_type') == "minute") ? "selected" : ""; ?> ><?php echo _l('task_recurring_minutes'); ?></option>
                    <option value="hour" <?= (get_option('lead_assigned_task_duration_type') == "hour") ? "selected" : ""; ?> >Hour(s)</option>
                    <option value="day" <?= (get_option('lead_assigned_task_duration_type') == "day") ? "selected" : ""; ?> ><?php echo _l('task_recurring_days'); ?></option>
                    <option value="week" <?= (get_option('lead_assigned_task_duration_type') == "week") ? "selected" : ""; ?> ><?php echo _l('task_recurring_weeks'); ?></option>
                    <option value="month" <?= (get_option('lead_assigned_task_duration_type') == "month") ? "selected" : ""; ?> ><?php echo _l('task_recurring_months'); ?></option>
                    <option value="year" <?= (get_option('lead_assigned_task_duration_type')  == "year") ? "selected" : ""; ?> ><?php echo _l('task_recurring_years'); ?></option>
                </select>
            </div>
            <div class="col-md-12">
                <label for="decimal_separator"><?php echo _l('settings_tasks_lead_creation_due_date_auto'); ?></label>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php $time = (get_option('lead_assigned_task_duedate_duration')) ? get_option('lead_assigned_task_duedate_duration') : "30"; ?>
                    <?php echo render_input('settings[lead_assigned_task_duedate_duration]', '', $time); ?>
                </div>
            </div>
            <div class="col-md-6">
                <select id="decimal_separator" class="selectpicker" name="settings[lead_assigned_task_duedate_duration_type]"
                        data-width="100%">
                    <option value="minute" <?= (get_option('lead_assigned_task_duedate_duration_type') == "minute") ? "selected" : ""; ?> ><?php echo _l('task_recurring_minutes'); ?></option>
                    <option value="hour" <?= (get_option('lead_assigned_task_duedate_duration_type') == "hour") ? "selected" : ""; ?> >Hour(s)</option>
                    <option value="day" <?= (get_option('lead_assigned_task_duedate_duration_type') == "day") ? "selected" : ""; ?> ><?php echo _l('task_recurring_days'); ?></option>
                    <option value="week" <?= (get_option('lead_assigned_task_duedate_duration_type') == "week") ? "selected" : ""; ?> ><?php echo _l('task_recurring_weeks'); ?></option>
                    <option value="month" <?= (get_option('lead_assigned_task_duedate_duration_type') == "month") ? "selected" : ""; ?> ><?php echo _l('task_recurring_months'); ?></option>
                    <option value="year" <?= (get_option('lead_assigned_task_duedate_duration_type')  == "year") ? "selected" : ""; ?> ><?php echo _l('task_recurring_years'); ?></option>
                </select>
            </div>
        </div>
        <hr class="no-mtop"/>
        <div class="row">
            <div class="col-md-12">
                <h4 class="bold">
                    <?php echo _l('settings_notifications_renewal'); ?>
                </h4>
                <label for="decimal_separator"><b><?php echo _l('settings_notifications_first_reminder'); ?></b></label>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php $time = (get_option('renewal_first_reminder_duration')) ? get_option('renewal_first_reminder_duration') : "30"; ?>
                    <?php echo render_input('settings[renewal_first_reminder_duration]', '', $time); ?>
                </div>
            </div>
            <div class="col-md-6">
                <select id="decimal_separator" class="selectpicker"
                        name="settings[renewal_first_reminder_duration_type]"
                        data-width="100%">
                    <option value="minute" <?= (get_option('renewal_first_reminder_duration_type') == "minute") ? "selected" : ""; ?> ><?php echo _l('task_recurring_minutes'); ?></option>
                    <option value="hour" <?= (get_option('renewal_first_reminder_duration_type') == "hour") ? "selected" : ""; ?> >Hour(s)</option>
                    <option value="day" <?= (get_option('renewal_first_reminder_duration_type') == "day") ? "selected" : ""; ?> ><?php echo _l('task_recurring_days'); ?></option>
                    <option value="week" <?= (get_option('renewal_first_reminder_duration_type') == "week") ? "selected" : ""; ?> ><?php echo _l('task_recurring_weeks'); ?></option>
                    <option value="month" <?= (get_option('renewal_first_reminder_duration_type') == "month") ? "selected" : ""; ?> ><?php echo _l('task_recurring_months'); ?></option>
                    <option value="year" <?= (get_option('renewal_first_reminder_duration_type')  == "year") ? "selected" : ""; ?> ><?php echo _l('task_recurring_years'); ?></option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label for="decimal_separator"><b><?php echo _l('settings_notifications_second_reminder'); ?></b></label>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php $time = (get_option('renewal_second_reminder_duration')) ? get_option('renewal_second_reminder_duration') : "30"; ?>
                    <?php echo render_input('settings[renewal_second_reminder_duration]', '', $time); ?>
                </div>
            </div>
            <div class="col-md-6">
                <select id="decimal_separator" class="selectpicker"
                        name="settings[renewal_second_reminder_duration_type]"
                        data-width="100%">
                    <option value="minute" <?= (get_option('renewal_second_reminder_duration_type') == "minute") ? "selected" : ""; ?> ><?php echo _l('task_recurring_minutes'); ?></option>
                    <option value="hour" <?= (get_option('renewal_second_reminder_duration_type') == "hour") ? "selected" : ""; ?> >Hour(s)</option>

                    <option value="day" <?= (get_option('renewal_second_reminder_duration_type') == "day") ? "selected" : ""; ?> ><?php echo _l('task_recurring_days'); ?></option>
                    <option value="week" <?= (get_option('renewal_second_reminder_duration_type') == "week") ? "selected" : ""; ?> ><?php echo _l('task_recurring_weeks'); ?></option>
                    <option value="month" <?= (get_option('renewal_second_reminder_duration_type') == "month") ? "selected" : ""; ?> ><?php echo _l('task_recurring_months'); ?></option>
                    <option value="year" <?= (get_option('renewal_second_reminder_duration_type')  == "year") ? "selected" : ""; ?> ><?php echo _l('task_recurring_years'); ?></option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label for="decimal_separator"><b><?php echo _l('settings_notifications_third_reminder'); ?></b></label>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php $time = (get_option('renewal_third_reminder_duration')) ? get_option('renewal_third_reminder_duration') : "30"; ?>
                    <?php echo render_input('settings[renewal_third_reminder_duration]', '', '30'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <select id="decimal_separator" class="selectpicker"
                        name="settings[renewal_third_reminder_duration_type]"
                        data-width="100%">
                    <option value="minute" <?= (get_option('renewal_third_reminder_duration_type') == "minute") ? "selected" : ""; ?> ><?php echo _l('task_recurring_minutes'); ?></option>
                    <option value="hour" <?= (get_option('renewal_third_reminder_duration_type') == "hour") ? "selected" : ""; ?> >Hour(s)</option>
                    <option value="day" <?= (get_option('renewal_third_reminder_duration_type') == "day") ? "selected" : ""; ?> ><?php echo _l('task_recurring_days'); ?></option>
                    <option value="week" <?= (get_option('renewal_third_reminder_duration_type') == "week") ? "selected" : ""; ?> ><?php echo _l('task_recurring_weeks'); ?></option>
                    <option value="month" <?= (get_option('renewal_third_reminder_duration_type') == "month") ? "selected" : ""; ?> ><?php echo _l('task_recurring_months'); ?></option>
                    <option value="year" <?= (get_option('renewal_third_reminder_duration_type')  == "year") ? "selected" : ""; ?> ><?php echo _l('task_recurring_years'); ?></option>
                </select>
            </div>
        </div>
    </div>
