<?php
$edit = isset($talk_register);
$status_options = [
    '' => '',
    'Active' => 'Active',
    'Overdue' => 'Overdue',
    'Closed' => 'Closed'
];

?>

<div class="modal-dialog " style="width: 70%;">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $edit ? 'Edit Talk Register ' : 'Toolbox Talk Register Form'?> </h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12 table-responsive">
                        <table id="" class="table table-striped table-hover" style="table-layout: fixed">
                            <thead>
                            <tr>
                                <th style="width: 25%">Talk Date</th>
                                <th style="width: 25%">Site</th>
                                <th style="width: 25%">Activity</th>
                                <th style="width: 25%">Supervisor</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td style="width: 20%;"><input type="text" name="date" value="<?= $edit ? $talk_register->date : '' ?>" class="form-control datepicker"></td>
                                <td style="width: 20%;"><?= form_dropdown('site_id', $projects_options, $edit ? $talk_register->site_id : '', ' class="form-control searchable" ') ?></td>
                                <td><?= form_dropdown('activity_id', $edit ? $talk_register->activity()->activity_name : [],  $edit ? $talk_register->activity_id : '', ' class="form-control searchable activities" ') ?></td>
                                <td><?= form_dropdown('supervisor_id', employee_options(),  $edit ? $talk_register->supervisor_id : '', ' class="form-control searchable" ') ?></td>
                                <input type="hidden" name="toolbox_talk_register_id" value="<?= $edit ? $talk_register->{$talk_register::DB_TABLE_PK} : ''?>">
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class='row '>
                    <div class="col-xs-12">
                        <table class="table table-bordered table-hover" width="100%">
                            <thead>
                            <tr>
                                <th style="padding-left: 15px;">Topics/Participants</th><th></th>
                            </tr>
                            <tr style="display: none" class="topics_row_template">
                                <td style="width: 80%">
                                    <?= form_dropdown('topic_id', $topics_options,  '', ' placeholder="select topic" class="form-control" ') ?>
                                    <input type="hidden" name="selected_item" value="topic">
                                </td>
                                <td style="width: 10%">
                                    <button title="Remove Row" type="button" class="btn btn-sm btn-danger topic_row_remover">
                                        <i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            <tr style="display: none" class="member_row_template">
                                <td style="width: 80%">
                                    <input type="text" name="member_name" class="form-control" placeholder="Enter Member ">
                                    <input type="hidden" name="selected_item" value="member"/>
                                </td>
                                <td style="width: 10%">
                                    <button title="Remove Row" type="button" class="btn btn-sm btn-danger member_row_remover">
                                        <i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!$edit) { ?>
                                <tr>
                                    <td style="width: 80%">
                                        <?= form_dropdown('topic_id', $topics_options,  '', ' placeholder="select topic" class="form-control searchable" ') ?>
                                        <input type="hidden" name="selected_item" value="topic">
                                    </td>
                                    <td style="width: 10%">

                                    </td>
                                </tr>
                              <?php } else {
                                foreach ($talk_register->talk_register_topics() as $topic){
                                    ?>
                                    <tr>
                                        <td style="width: 80%">
                                            <?= form_dropdown('topic_id', $topics_options,  $topic->topic_id, ' placeholder="select topic" class="form-control" ') ?>
                                            <input type="hidden" name="selected_item" value="topic">
                                        </td>
                                        <td style="width: 10%">
                                            <button title="Remove Row" type="button" class="btn btn-sm btn-danger topic_row_remover">
                                                <i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                  foreach ($talk_register->talk_register_participants() as $participant) {
                                    ?>

                                    <tr>
                                        <td style="width: 80%">
                                            <input type="text" name="member_name" value="<?= $participant->name ?>" class="form-control" placeholder="Enter Member ">
                                            <input type="hidden" name="selected_item" value="member"/>
                                        </td>
                                        <td style="width: 10%">
                                            <button title="Remove Row" type="button" class="btn btn-sm btn-danger member_row_remover">
                                                <i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>

                                  <?php
                                }
                                ?>

                            <?php
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3">
                                    <button type="button" class="btn btn-default btn-xs topic_row_adder pull-right">Add Topic</button>
                                    <span class="pull-right">&nbsp;</span>

                                    <button type="button" class="btn btn-default btn-xs member_row_adder pull-right">Add Member</button>
                                    <span class="pull-right">&nbsp;</span>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm save_toolbox_talk_register">
                        Save
                    </button>
                </div>
        </form>
    </div>
</div>

