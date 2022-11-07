<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 4/24/2017
 * Time: 5:06 PM
 */

?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_project_category" class="btn btn-default btn-xs">
                    New Category
                </button>
                <div id="new_project_category" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('projects/settings/project_category_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="project_categories_list" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Category Name</th><th>Description</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
