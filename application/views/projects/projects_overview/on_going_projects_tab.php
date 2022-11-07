<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/23/2018
 * Time: 11:51 AM
 */

?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-md-4">
                    <?= form_dropdown('project_name', $on_going_projects_options, '', ' class="form-control searchable" id="on_going_projects_options"') ?>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12" id="on_going_projects_container" >
                        <?php
                            $this->load->view('projects/projects_overview/project_details');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>