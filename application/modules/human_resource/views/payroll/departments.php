<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 09/04/2019
 * Time: 13:25
 */


?>

<?php foreach($departments as $department){
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <a class="department_button" department_id="<?= $department->department_id ?>"  id="<?= 'department'.$department->department_id ?>" data-toggle="collapse" data-parent="#accordion"
                   target="#collapse"
                   href="#collapse_<?= $department->department_id ?>">
                    <span style="color: #3c8dbc">
                        <?= strtoupper($department->department_name).' PAYROLLS' ?>
                    </span>&nbsp;&nbsp;&nbsp;
                </a>

            </div>
        </div>
        <div id="collapse_<?= $department->department_id ?>" class="panel-collapse collapse">
            <div class="panel-body" id="panel<?= $department->department_id ?>">
                <div class="department_payrolls" id="<?= 'div'.$department->department_id ?>">

                </div>
            </div>
        </div>
    </div>
<?php } ?>
