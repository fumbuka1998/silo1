<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Employees
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('human_resource/human_resources')?>"><i class="fa fa-users"></i>Human Resources</a></li>
        <li class="active">Employees</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                     <li class="active"><a href="#existing_contracts" data-toggle="tab">Contract Employees</a></li>
                     <li><a href="#incomplete_contracts" data-toggle="tab">Incomplete Contract Employees</a></li>
                     <li><a href="#expired_contracts" data-toggle="tab">Expired Contract Employees</a></li>
                     <li><a href="#closed_contracts" data-toggle="tab">Closed Contract Employees</a></li>
                     <li><a href="#non_contracts" data-toggle="tab">Non Contract Employees</a></li>
                   
                </ul>
                <div class="tab-content">

                    <div class="active tab-pane" id="existing_contracts">

                        <?php $this->load->view('contracts/contract_employee_list');?>

                    </div>

                    <div class=" tab-pane" id="incomplete_contracts">
                        <?php $this->load->view('contracts/incomplete_contract_employee_list');?>
                    </div>

                     <div class=" tab-pane" id="expired_contracts">
                        <?php $this->load->view('contracts/expired_contract_employee_list');?>
                    </div>

                     <div class=" tab-pane" id="closed_contracts">
                        <?php $this->load->view('contracts/closed_contract_employee_list');?>
                    </div>

                     <div class=" tab-pane" id="non_contracts">
                        <?php $this->load->view('contracts/non_contract_employee_list');?>
                    </div>
                </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');?>