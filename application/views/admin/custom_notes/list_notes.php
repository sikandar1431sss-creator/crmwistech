<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
    <div class="content">
        <div class="row">
            <form action="<?= base_url() ?>admin/custom_notes/index" method="post" accept-charset="utf-8"
                  novalidate="novalidate">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <div class="col-md-12">
                            <form method="post" enctype="application/x-www-form-urlencoded" action="">
                                <div class="row">
                                    <div class="col-md-5 form-group">
                                        <label for="date" class="control-label">
                                            <?= _l('add_note_custom_type'); ?>
                                        </label>
                                        <select name="noteType"
                                                class="form-control">
                                            <option value="">Select Type</option>
                                            <option value="invoice">Invoice</option>
                                            <option value="proposal">Proposal</option>
                                            <option value="receipt">Receipt</option>
                                        </select>
                                    </div>

                                    <div class="col-md-5 form-group">
                                        <label for="date" class="control-label">
                                            <?= _l('add_note_custom_position'); ?>
                                        </label>
                                        <select name="notePosition"
                                                class="form-control">
                                            <option value="">Select Position</option>
                                            <option value="top">Top</option>
                                            <option value="proposal">Bottom</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" style="    padding: 20px;">
                                        <p class="bold"><?php echo _l(' '); ?></p>
                                        <input type="submit" class="btn btn-info only-save customer-form-submiter"
                                               value="Filter"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-mtop">
                            <?= _l('title_listings'); ?>
                        </h4>
                        <hr class="hr-panel-heading">
                        <table cellspacing="0" class="table table-striped table-responsive dt-responsive"
                               id="ReceiptTable">
                            <thead>
                            <tr role="row">
                                <th><?= _l('add_note_custom_title'); ?></th>
                                <th><?= _l('add_note_custom_type'); ?></th>
                                <th><?= _l('add_note_custom_position'); ?></th>
                                <th><?= _l('Created By'); ?></th>
                                <th>&nbsp;</th>
                                <th><?= _l('Note'); ?></th>
                            </tr>
                            </thead>
                            <tbody id="invoices_data">
                            <?php
                            if ($cus_notes != null && count($cus_notes) > 0) {
                                // pre_array($cus_notes);
                                foreach ($cus_notes as $note) {
                                     ?>
                                    <tr>
                                        <td><?= $note->cn_title; ?>  </td>
                                        <td><?= $note->cn_type; ?>  </td>
                                        <td><?= $note->cn_position; ?>  </td>
                                        <td><?= $note->firstname . " ".$note->lastname; ?>  </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="label label-default-light dropdown-toggle"
                                                        data-toggle="dropdown">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="<?= base_url(); ?>admin/custom_notes/update/<?= $note->cn_id; ?>"
                                                           id="<?= $note->cn_id; ?>"
                                                           class="text text-primary" target="_blank">Preview</a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= base_url(); ?>admin/custom_notes/update/<?= $note->cn_id; ?>"
                                                           id="<?= $note->cn_id; ?>"
                                                           class="text text-primary" target="_blank">Edit</a>
                                                    </li>
                                                    <li>
                                                        <a href="#"
                                                           id="<?= $note->cn_id; ?>"
                                                           class="delete-notes text text-danger">Delete</a></li>
                                                </ul>
                                            </div><!-- /btn-group -->
                                        </td>
                                        <td><?= $note->cn_notes; ?>  </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
    </div>
    </div>
<?php init_tail(); ?>
</body>
</html>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.16/api/sum().js"></script>
<script>


    $(document).ready(function () {
        $("a.delete-notes").click(function (e) {
            if (!confirm('Are you sure?')) {
                e.preventDefault();
                return false;
            } else {
                var id = $(this).attr('id');
                $.ajax({
                    url: '<?= base_url(); ?>admin/custom_notes/delete',
                    type: 'POST',
                    data: {'note_id': id},
                    success: function (data) {
                        //called when successfulc
                        console.log(data);
                    },
                    error: function (e) {
                        console.log(e);
                    }
                });
            }
        });


    });
</script>