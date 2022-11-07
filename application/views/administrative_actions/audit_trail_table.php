<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 5/9/2016
 * Time: 8:05 AM
 */
$print = isset($print);
if(!empty($log_entries)){
?>
    <table style="font-size: 12px" <?php
        if($print){
           ?>
            cellspacing="0" border="1" width="100%"
            <?php
        } else {
            ?>
            class="table table-bordered table-hover table-striped"
            <?php
        }
    ?>>
        <thead>
            <tr>
                <th>Date and Time</th><th>Employee</th><th>Project</th><th>Action</th><th>IP Address</th><th>User Agent</th><th>Description</th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach($log_entries as $log_entry){
                $project_name = $log_entry->project()->project_name;
                $project_name = $project_name != '' ? $project_name : '';
                $employee_name = $log_entry->employee()->full_name();
                $employee_name = $employee_name != '' ? $employee_name : '';
                ?>
                <tr>
                    <td><?= standard_datetime($log_entry->datetime_logged) ?></td>
                    <td><?= check_permission('Human Resources')&& !$print && $employee_name != '' ? anchor(base_url('human_resources/employee_profile/'.$log_entry->employee_id),$employee_name) : $employee_name ?></td>
                    <td><?= check_permission('Projects') && !$print && $project_name != '' ? anchor(base_url('projects/profile/'.$log_entry->project_id),$project_name) : $project_name ?></td>
                    <td><?= $log_entry->action ?></td>
                    <td><?= $log_entry->ip_address ?></td>
                    <td><?= $log_entry->user_agent ?></td>
                    <td><?= $log_entry->description ?></td>
                </tr>
                <?php
            }
        ?>
        </tbody>

    </table>
<?php
} else {
?>
    <div <?= !$print ? 'class="alert alert-warning"': '' ?>>
        No activities performed in the system with that matches the filters
    </div>
<?php
}