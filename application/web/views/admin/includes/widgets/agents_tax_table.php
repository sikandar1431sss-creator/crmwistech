 <div class="row" id="agents_details">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body padding-10">
                    <div class="col-md-12">
                        <div class="col-md-4 pull-right">
                            <div class="form-group" id="agents-report-time">
                                <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
                                <select class="selectpicker" id="agents-tax-report" name="agents-tax-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>

                                    <?php
                                        $years_list = range(date('Y'), date('Y')-5);
                                        foreach ($years_list as $year){ ?>
                                            <option value="<?= $year ?>"><?= $year ?></option>

                                    <?php } ?>


                                </select>
                            </div>


                            <div id="agents_cutom_date_range" class="hide animated mbot15">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" id="report-from-agents" name="report-from-agents">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" disabled="disabled" id="report-to-agents" name="report-to-agents">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class=" hide col-md-4 pull-right">
                            <div class="form-group"><label for="date" class="control-label">Date</label>
                                <div class="input-group date">
                                    <input type="text" required="" id="date" name="date" class="form-control datepicker" value="">
                                    <div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i></div>
                                    <button type="button" class="btn btn-info pull-right custom_agent_detail" id="filter_agent_detail">Filter</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8 pull-left">
                            <div style="padding-top: 3%; " ><strong>VAT Report</strong></div>
                        </div>


                        <table cellspacing="0" class="table table-striped table-responsive dt-responsive"
                               id="AgentsTaxTable">
                            <thead>
                            <tr role="row">
                                <th>Month</th>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>March</th>
                                <th>April</th>
                                <th>May</th>
                                <th>June</th>
                                <th>July</th>
                                <th>Aug</th>
                                <th>Sep</th>
                                <th>Oct</th>
                                <th>Nov</th>
                                <th>Dec</th>
                            </tr>
                            </thead>
                            <tbody id="agents_tax_details">

                                <tr>
                                    <td>Proformal Invoices</td>
                                    <?php foreach ($proformal_tax as $tax_details ) { ?>
                                        <td><?php echo $tax_details['total']; ?></td>
                                    <?php } ?>
                                </tr>

                                <tr>
                                    <td>Tax Invoice</td>
                                    <?php foreach ($total_tax as $tax_details ) { ?>
                                        <td><?php echo $tax_details['total']; ?></td>
                                    <?php } ?>
                                </tr>

                                <tr>
                                    <td>Collected Tax Invoice</td>
                                    <?php foreach ($collected_tax as $tax_details ) { ?>
                                        <td><?php echo $tax_details['total']; ?></td>
                                    <?php } ?>
                                </tr>

                            </tbody>
                        </table>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

 <style>
     #ui-datepicker-div{
         display: none !important;
     }
 </style>