<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 27/05/2019
 * Time: 16:55
 */

if(!empty($attachments)){
    ?>
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Datetime Attached</th><th>Attached By</th><th>Caption</th><th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($attachments as $attachment){
            $employee = $attachment->employee();
            ?>
            <tr>
                <td><?= standard_datetime($attachment->created_at) ?></td>
                <td>
                    <?= $employee->full_name() ?>
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
        No attachments for this Voucher
    </div>
    <?php
}
?>