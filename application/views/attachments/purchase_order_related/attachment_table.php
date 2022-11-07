<?php
if (!empty($attachments)) {
?>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Datetime Attached</th>
                <th>File Name</th>
                <th>Attached To</th>
                <th>Attached By</th>
                <th>Caption</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($attachments as $attachment) {
                $employee = $attachment->employee();
            ?>
                <tr>
                    <td><?= date('Y M d H:i:s', strtotime($attachment->created_at)) ?></td>
                    <td><?= $attachment->attachment_name ?></td>
                    <td><?= $attachment->type_junction()->reffering_object_number() ?></td>
                    <td>
                        <?php
                        if (check_permission('Manage Employees')) {
                            echo anchor(base_url('employees/profile/' . $employee->{$employee::DB_TABLE_PK}), $employee->full_name());
                        } else {
                            echo $employee->full_name();
                        }
                        ?>
                    </td>
                    <td>
                        <?= $attachment->caption ?>
                    </td>
                    <td>
                        <div style="font-size: 14px" class="pull-right">
                            <?= $attachment->action_buttons() ?>
                        </div>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<?php
} else {
?>
    <div style="text-align: center" class="alert alert-info col-xs-12">
        No attachments for this entry
    </div>
    <?php
}
