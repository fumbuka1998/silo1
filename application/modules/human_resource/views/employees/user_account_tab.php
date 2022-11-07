<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/9/2016
 * Time: 7:13 PM
 */

$user = $employee->user();
$data['user'] = $user;
?>
<div class="box">
        <form>
            <div id="username_and_password" class="box-header">
                <?php $this->load->view('employees/username_and_password_form',$data); ?>
            </div>
            <div class="box-body">
                <?php
                if(check_privilege('Register Employee')) {
                    ?>
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#user_permissions" data-toggle="tab">User Permissions</a></li>
                            <li><a href="#authorised_approvals" data-toggle="tab">Authorised Approvals</a></li>
                            <li><a href="#confidentiality" data-toggle="tab">Confidentiality</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="user_permissions">
                                <?php $this->load->view('employees/user_permissions_tab'); ?>
                            </div>
                            <!-- /.tab-pane -->
                            <div class=" tab-pane" id="authorised_approvals">
                                <?php $this->load->view('employees/authorised_approvals_tab'); ?>
                            </div>
                            <!-- /.tab-pane -->
                            <div class=" tab-pane" id="confidentiality">
                                <?php $this->load->view('employees/confidentiality_tab'); ?>
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                    </div>
                    <?php  }
                ?>
                <div class="col-xs-12">
                    <button type="button" class="btn btn-sm btn-default pull-right save_employee_credentials">
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>