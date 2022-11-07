<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 09/04/2019
 * Time: 13:25
 */
$this->load->model('department');

?>

<?php foreach($payrolls as $payroll){
    $department = new Department();
    $department->load($payroll->department_id);
    ?>
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="panel-title">
                <a class="payroll_button<?= $department->department_id ?>" department_id="<?= $department->department_id ?>" payroll_id="<?= $payroll->id ?>"
                   payroll_for="<?= $payroll->payroll_for ?>"
                   id="<?= 'department'.$payroll->id ?>"
                   data-toggle="collapse" data-parent="#accordion"
                   target="#collapse"
                   href="#collapse<?= $department->department_id.$payroll->id ?>">
                    <span style="color: #3c8dbc; font-weight: bold">
                        <?= strtoupper(DateTime::createFromFormat('!m', date('m', strtotime($payroll->payroll_for.'-1')))->format('F')) . ' ' . date('Y', strtotime($payroll->payroll_for.'-1')) ?>
                    </span>&nbsp;&nbsp;&nbsp;
                </a>


                <span class="pull-right">
                    <form method="post" target="_blank" action="<?= base_url('human_resource/human_resources/payroll_list_display') ?>">
                        <input type="hidden" name="payroll_date" value="<?= $payroll->payroll_for.'-1' ?>">
                        <input type="hidden" name="department_id" value="<?= $department->department_id ?>">
                        <input type="hidden" name="print" value="true">
                        <input type="hidden" name="payroll_id" value="<?= $payroll->id ?>">
                        <button data-toggle="modal" data-target="#print<?= $payroll->id ?>"
                                class="btn btn-primary btn-xs">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </button>
                    </form>
                </span>

                <a href="#" payroll_id="<?= $payroll->id ?>" style="color: <?= $payroll->status == 'Rejected' ? 'red' : 'green'  ?>" class=" pull-right margin-r-5 payroll_status" id="<?= 'payroll_status'.$payroll->id ?>"><?= $payroll->status ?> </a>

            </div>
        </div>
        <div id="collapse<?= $department->department_id.$payroll->id ?>" class="panel-collapse collapse">
            <div class="panel-body" id="panel<?= $payroll->id ?>">
                <div class="payroll_diplay_div" id="<?= 'div'.$department->department_id.$payroll->id ?>">

                </div>
            </div>
        </div>
    </div>
<?php } ?>
