<?php
/**
 * Created by PhpStorm.
 * User: userx
 * Date: 12/5/19
 * Time: 11:06 AM
 */
$this->load->view('includes/header');

?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Attendance
            <small>Employee Attendances</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li class="active">Attendance</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row" id="">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="attendances_list" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Date</th><th>Time</th><th>Employee</th><th>Type</th><th>Created At</th>
                                </tr>
                                <?php foreach($attendances as $attendance){
                                    ?>
                                    <tr>
                                        <td style="text-align: left"><?= $attendance->date ?></td>
                                        <td style="text-align: left"><?= $attendance->time ?></td>
                                        <td style="text-align: left"><?= $attendance->employee()->full_name() ?></td>
                                        <td style="text-align: left"><?= $attendance->type ?></td>
                                        <td style="text-align: left"><?= $attendance->created_at ?></td>
                                    </tr>
                                    <?php
                                } ?>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');

