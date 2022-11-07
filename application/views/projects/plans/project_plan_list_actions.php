<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/30/2018
 * Time: 12:51 PM
 */

    ?>

    <div style="width: 100%">
        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
                Actions
            </button>
            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <a href="#"  data-toggle="modal" data-target="#project_plan_summary_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>"
                        class="btn btn-default btn-xs">
                        <i class="fa fa-eye"></i> Details
                    </a>
                </li>
                <?php
                  if (check_privilege('Project Actions')) { ?>
                      <li>
                          <a href="#"  data-toggle="modal" data-target="#edit_project_plan_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>"
                             class="btn btn-default btn-xs">
                              <i class="fa fa-edit"></i> Edit
                          </a>
                      </li>
                      <li>
                          <a style="color: white" href="#" class="btn btn-danger btn-xs delete_project_plan" project_plan_id="<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>">
                              <i class="fa fa-trash"></i> Delete
                          </a>
                      </li>
                  <?php }
                ?>
               <li>
                    <a  class="btn btn-default btn-xs" target="_blank" href="<?= base_url('projects/preview_project_plan/'.$project_plan->{$project_plan::DB_TABLE_PK}) ?>">
                        <i class="fa fa-file-pdf-o"></i> PDF</a>
                </li>

            </ul>
        </div>
        <div id="edit_project_plan_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
            <?php $this->load->view('projects/plans/project_plan_form'); ?>
        </div>
        <div id="project_plan_summary_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="modal fade project_plan_details" role="dialog">
            <?php $this->load->view('projects/plans/project_plan_details'); ?>
        </div>
    </div>



