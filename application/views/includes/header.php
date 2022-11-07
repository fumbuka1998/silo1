<?php
$dp_path = $this->session->userdata('dp_path');
$due_invoices = $this->session->userdata('due_invoices');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= isset($title) ? $title : "Electronic Project Manager"?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="image/png" href="<?=  base_url('favicon.png')?>" sizes="16x16">
    <!-- Gantt Chart -->
    <?php
    if(isset($include_gantt_chart)) {
        ?>
        <link rel=stylesheet href="<?= base_url('plugins/ganttChart/platform.css')?>" type="text/css">
        <link rel=stylesheet href="<?= base_url('plugins/ganttChart/libs/jquery/dateField/jquery.dateField.css')?>" type="text/css">

        <link rel=stylesheet href="<?= base_url('plugins/ganttChart/gantt.css')?>" type="text/css">
        <link rel=stylesheet href="<?= base_url('plugins/ganttChart/ganttPrint.css')?>" type="text/css" media="print">
        <?php
    }
    ?>
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css')?>">
    <link rel="stylesheet" href="<?= base_url('plugins/dataTables/dataTables.bootstrap.css')?>">


    <link href="<?= base_url('node_modules/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('node_modules/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('node_modules/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('node_modules/datatables.net-scroller-bs/css/scroller.bootstrap.min.css') ?>" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('plugins/datepicker/datepicker3.css')?>">
    <link rel="stylesheet" href="<?= base_url('plugins/datetimepicker/datetimepicker.css')?>">
    <link rel="stylesheet" href="<?= base_url('plugins/iziToast/css/iziToast.min.css')?>">
    <link rel="stylesheet" href="<?= base_url('plugins/jquery-confirm/jquery-confirm.min.css')?>">
    <link rel="stylesheet" href="<?= base_url('plugins/select2/select2.min.css')?>">

    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('css/AdminLTE.min.css')?>">

    <!--    jmSpinner -->
    <link rel="stylesheet" href="<?= base_url('plugins/jmSpinner/jm.spinner.css')?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('css/font-awesome.css')?>">
    <link rel="stylesheet" href="<?= base_url('css/image_viewer.css')?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?= base_url('css/ionicons.css')?>">

    <link rel="stylesheet" href="<?= base_url('css/epm.css')?>">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue-light fixed sidebar-mini sidebar-collapse">
<!-- Site wrapper -->
<div class="wrapper">
    <div id="spinner_div"></div>
    <header class="main-header">
        <!-- Logo -->
        <a href="<?= base_url()?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini">e<b>PM</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><img height="50px" src="<?php echo base_url("images/logo.png");?>" /></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?= $dp_path ?>" class="user-image" alt="User Image">
                            <span class="hidden-xs"><?= $this->session->userdata("employee_name") ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?= $dp_path ?>" class="img-circle" alt="User Image">
                                <p>
                                    <?= $this->session->userdata("employee_name") ?> - <?= $this->session->userdata('department_name')?>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?= base_url('human_resource/human_resources/employee_profile/'.$this->session->userdata('employee_id')) ?>" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?= base_url('app/logout')?>" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <!-- <li>
                            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                        </li>-->
                </ul>
            </div>
        </nav>
    </header>

    <!-- =============================================== -->

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?= $dp_path ?>" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><?= $this->session->userdata("employee_name") ?></p>
                </div>
            </div>
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <?php if(1<0){ ?>
                <li>
                    <a href="<?= base_url()?>">
                        <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                    </a>
                </li>
                <?php } ?>
                <li>
                    <a href="<?= base_url('timeline')?>">
                        <i class="fa fa-calendar-times-o"></i> <span>Timeline</span>
                    </a>
                </li>
                <?php if(check_permission('Clients')){ ?>
                    <li>
                        <a href="<?= base_url('Stakeholders')?>">
                            <i class="fa fa-handshake-o"></i> <span  title="Clients / Suppliers / Contractors">Stakeholders</span>
                        </a>
                    </li>
                <?php } if(check_permission('Tenders')){ ?>
                <!---
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-files-o"></i>
                        <span>Tenders </span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="<?= /*base_url('tenders')*/ '' ?>"><i class="glyphicon glyphicon-dashboard"></i> Tenders Dashboard</a></li>
                        <li><a href="<?= /*base_url('tenders/tenders_list')*/ '' ?>"><i class="fa fa-list"></i> Tenders List</a></li>
                        <li><a href="<?= /*base_url('tenders/settings')*/ '' ?>"><i class="fa fa-cog"></i> Tender Settings</a></li>
                    </ul>
                </li>
                --->
                <?php }
                if(check_permission('Projects') || $this->session->userdata('has_project')){
                    ?>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-product-hunt"></i>
                        <span>Projects </span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if(check_permission('Projects') || $this->session->userdata('has_project')){ ?>
                            <li><a href="<?= base_url('projects/projects_list')?>"><i class="fa fa-list"></i> Projects List</a></li>
                        <?php }
                            if(check_permission('Executive Reports')){
                            ?>
                                <li><a href="<?= base_url('projects/projects_overview')?>"><i class="glyphicon glyphicon-hourglass"></i> Projects Overview</a></li>
                        <?php
                            }
                        ?>
                        <?php if(check_privilege('Projects Settings')){ ?><li><a href="<?= base_url('projects/settings')?>"><i class="fa fa-cog"></i> Settings</a></li><?php } ?>
                    </ul>
                </li>
                <li>
                    <a href="<?= base_url('projects/services')?>">
                        <i class="fa fa-cogs"></i> <span  title="Maintainance / Provided Services">Services</span>
                    </a>
                </li>
                    <?php if(1<0){ ?>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-warning"></i>
                        <span>HSE</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-history"></i> <span>Inspections</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li class="treeview">
                                <?php
                                $categories = hse_inspection_categories();
                                foreach($categories as $category){
                                switch ($category->name){
                                case "Deployment":
                                    ?>
                                    <li><a href="<?= base_url('hse/deployment') ?>"> <?= ucfirst($category->name) ?></a></li>
                                    <?php
                                    break;
                                case "Site":
                                    ?>
                                    <li><a href="<?= base_url('hse/inspection/'.$category->id) ?>"> <?= ucfirst($category->name) ?></a></li>
                                    <?php
                                    break;
                                case "First Aid Kit Check":
                                    ?>
                                    <li><a href="<?= base_url('hse/inspection/'.$category->id) ?>"> <?= ucfirst($category->name) ?></a></li>
                                    <?php
                                    break;
                                case "Toolbox Talk Register":
                                    ?>
                                    <li><a href="<?= base_url('hse/talk_register')?>"> <?= ucfirst($category->name) ?></a></li>
                                    <?php
                                    break;
                                case "Site Diary Compliance":
                                ?>
                                    <li><a href="<?= base_url('hse/site_diary_compliance')?>"> <?= ucfirst($category->name) ?></a></li>
                                <?php
                                    break;
                                ?>
                                <?php }
                                } ?>
                                </li>
                            </ul>
                        </li>
                        <li><a href="<?= base_url('hse/job_card') ?>"><i class="fa fa-hourglass-start"></i> Job Cards</a></li>
                        <li><a href="<?= base_url('hse/incident') ?>"><i class="fa fa-info-circle"></i> Incidents</a></li>
<!--                        <li><a href="--><?//= base_url() ?><!--"><i class="fa fa-retweet"></i> Claims</a></li>-->
                        <li><a href="<?= base_url('hse/job_card_reports') ?>"><i class="fa fa-pie-chart"></i> Reports</a></li>
                        <li><a href="<?= base_url('hse/settings') ?>"><i class="fa fa-cog"></i> Settings</a></li>
                    </ul>
                </li>
                        <?php } ?>
                <?php } if(check_permission('Requisitions')){ ?>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-clipboard"></i> <span>Requisitions</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li class="treeview">
                            <li><a href="<?= base_url('requisitions/requisitions_list')?>"> Requisitions List</a></li>
                            <li><a href="<?= base_url('requisitions/enquiries_list')?>"> Enquiries List</a></li>
                            </li>
                        </ul>
                    </li>
                <?php } if(check_permission('Procurements')){ ?>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-shopping-cart"></i>
                            <span>Procurements </span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?= base_url('procurements/pre_orders')?>"><i class="fa fa-clipboard"></i> To Be Ordered</a></li>
                            <?php if(check_privilege('Purchase Orders')){ ?><li><a href="<?= base_url('procurements/purchase_orders') ?>"><i class="fa fa-credit-card"></i> Purchase Orders</a></li><?php } ?>
                            <?php if(check_privilege('GRNs')){ ?><li><a href="<?= base_url('procurements/purchase_orders_grns')?>"><i class="fa fa-truck"></i> GRNs</a></li><?php } ?>
                            <?php if(check_privilege('Payment Request')){ ?><li><a href="<?= base_url('procurements/order_payment_requests')?>"><i class="fa fa-envelope"></i> Payment Requests</a></li><?php } ?>
							<li><a href="<?= base_url('Stakeholders')?>"><i class="fa fa-user-circle"></i>Vendors</a></li>
							<li><a href="<?= base_url('finance/imprests')?>"><i class="fa fa-asterisk"></i> Imprests</a></li>
                        </ul>
                    </li>
                <?php } if(check_permission('Inventory')){ ?>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-barcode"></i>
                            <span>Inventory </span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?= base_url('inventory')?>"><i class="glyphicon glyphicon-dashboard"></i> Inventory</a></li>
                            <li><a href="<?= base_url('inventory/locations')?>"><i class="fa fa-building-o"></i> Locations</a></li>
                            <li><a href="<?= base_url('inventory/material_items')?>"><i class="fa fa-trademark"></i> Material Items</a></li>
                            <?php if(check_privilege('Inventory Reports')){ ?>
                                <li class="treeview">
                                    <a href="#">
                                        <i class="fa fa-bar-chart"></i> <span>Reports</span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li class="treeview">
                                        <li><a href="<?= base_url('inventory/inventory_reports/material_item_availability')?>"> Material Availability</a></li>
                                        <li><a href="<?= base_url('inventory/inventory_reports/inventory_sales')?>"> Inventory Sales</a></li>
                                        <li><a href="<?= base_url('inventory/inventory_reports/cost_center_assignements')?>"> Cost Center Assingment</a></li>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <li><?php if(check_privilege('Inventory Settings')){ ?><a href="<?= base_url('inventory/settings')?>"><i class="fa fa-cog"></i> Settings</a><?php } ?></li>
                        </ul>
                    </li>

                    <?php } if(check_permission('Assets')){ ?>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-taxi"></i>
                            <span> Assets </span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa fa-list"></i> <span>Asset List</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="treeview">
                                        <a href="#">
                                            <i class="fa fa-truck"></i> <span>Hired Assets</span>
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </a>
                                        <ul class="treeview-menu">
                                            <li><a href="<?= base_url('assets/hired_assets/clients')?>"> Hired To Clients</a></li>
                                            <li><a href="<?= base_url('assets/hired_assets/suppliers')?>"> Hired From Suppliers</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <?php if(check_privilege('Assets Reports')){ ?>
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa fa-bar-chart"></i> <span>Reports</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="treeview">
                                        <li><a href="<?= base_url('assets/reports/asset_item_availability')?>"> Asset Item Availability</a></li>
                                    </li>
                                </ul>
                            </li>
                            <?php }
                            if(check_privilege('Assets Settings')){ ?>
                            <li><a href="<?= base_url('assets/settings')?>"><i class="fa fa-cog"></i> Settings</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } if(check_permission('Finance')){ ?>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-money"></i>
                            <span>Finance </span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
							<?php if(check_privilege('Approved Payments')){ ?><li><a href="<?= base_url('finance/transactions')?>"><i class="fa fa-shopping-bag"></i> Transactions</a></li><?php } ?>
							<?php if(check_privilege('Accounts')){ ?><li><a href="<?=base_url('finance/accounts/bank') ?>"><i class="fa fa-bank"></i>Bank Accounts</a></li><?php } ?>
							<?php if(check_privilege('Accounts')){ ?><li><a href="<?=base_url('finance/accounts/cash_in_hand') ?>"><i class="fa fa-bitcoin"></i>Cash Accounts</a></li><?php } ?>
							<?php if(check_privilege('Accounts')){ ?><li><a href="<?=base_url('finance/accounts/ledger') ?>"><i class="fa fa-book"></i>Ledger Accounts</a></li><?php } ?>
							<?php if(check_privilege('Accounts')){ ?><li><a href="<?=base_url('finance/accounts/payable') ?>"><i class="fa fa-briefcase"></i>Accounts Payable</a></li><?php } ?>
							<?php if(check_permission('Finance')){ ?><li><a href="<?=base_url('finance/accounts/receivable') ?>"><i class="fa fa-envelope-open-o"></i>Accounts Receivable</a></li><?php } ?>
							<?php if(check_permission('Finance')){ ?><li><a href="<?=base_url('finance/invoices/sales') ?>"><i class="fa fa-envelope-o"></i>Sales Invoices</a></li><?php } ?>
							<?php if(check_permission('Finance')){ ?><li><a href="<?=base_url('finance/invoices/purchases') ?>"><i class="fa fa-bell-o"></i>Purchases Invoices</a></li><?php } ?>
							<?php if(check_privilege('Payroll Payments')){ ?><li><a href="<?= base_url('/human_resource/human_resources/employee_loan_list')?>"><i class="fa fa-calendar"></i> Payroll </a></li><?php } ?>
                            <?php if(check_privilege('Make Payment')){ ?><li><a href="<?= base_url('finance/reports')?>"><i class="fa fa-pie-chart"></i> Reports </a><?php } ?>
                            <?php if(check_privilege('Finance Settings')){ ?><li><a href="<?= base_url('finance/settings')?>"><i class="fa fa-cog"></i> Settings</a></li><?php } ?>
                        </ul>
                    </li>
                <?php } if(check_permission('Human Resources')){ ?>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Human Resources </span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?= base_url('human_resource/human_resources')?>"><i class="glyphicon glyphicon-dashboard"></i> Human Resources</a></li>
                            <?php if(check_privilege('Employee List')){ ?><li><a href="<?= base_url('human_resource/human_resources/employees_lists')?>"><i class="ion ion-person-stalker"></i> Employees List</a></li><?php } ?>
                            <?php if(check_privilege('Payroll')){ ?><li><a href="<?= base_url('human_resource/human_resources/payroll')?>"><i class="fa fa-money"></i> Payroll</a></li><?php } ?>
                            <?php if(check_privilege('Human Resource Settings')){ ?><li><a href="<?= base_url('human_resource/human_resources/settings')?>"><i class="fa fa-cog"></i> Settings</a></li><?php } ?>
                        </ul>
                    </li>
                <?php } if(check_permission('Executive Reports')){ ?>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-pie-chart"></i>
                            <span>Reports </span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?= base_url('reports')?>"><i class="fa fa-dashboard"></i> Reports</a></li>
                            <li><a href="<?= base_url('reports/project_performance_report')?>"><i class="fa fa-line-chart"></i> Project Performance Status</a></li>
                            <li><a href="<?= base_url('reports/project_inventory_position')?>"><i class="fa fa-product-hunt"></i> Project Inventory Position</a></li>
                            <li><a href="<?= base_url('reports/project_inventory_movement')?>"><i class="fa fa-exchange"></i> Project Inventory Movement</a></li>
                            <li><a href="<?= base_url('reports/project_financial_status')?>"><i class="fa fa-line-chart"></i> Project Financial Status</a></li>
                            <li><a href="<?= base_url('reports/vendors_overall_balance')?>"><i class="fa fa-yen"></i> Vendors Overall Balance</a></li>
                            <li><a href="<?= base_url('reports/services')?>"><i class="fa fa-puzzle-piece"></i> Services Report</a></li>
                            <li><a href="<?= base_url('reports/cash_flow')?>"><i class="fa fa-refresh"></i> Cash-Flow Report</a></li>
                            <!--<li><a href="<?/*= base_url('procurements/purchase_order_status')*/?>"><i class="fa fa-credit-card"></i> Purchase Order Status</a></li>
                            -->
                            <!--<li><a href="<?/*= base_url('reports/requests_vs_payments') */?>"><i class="fa fa-clipboard"></i> Requests Vs Payments</a></li>-->
                        </ul>
                    </li>
                <?php } if(check_permission('Administrative Actions')){ ?>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-info-circle"></i>
                            <span>Administrative Actions </span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?= base_url('administrative_actions')?>"><i class="fa fa-dashboard"></i> Administrative Actions</a></li>
                            <li><a href="<?= base_url('administrative_actions/company_details')?>"><i class="fa fa-info-circle"></i> Company Details</a></li>
                            <li><a href="<?= base_url('administrative_actions/approval_settings')?>"><i class="fa fa-check-circle"></i> Approval Settings</a></li><!--
                            <li><a href="<?/*= base_url('administrative_actions/audit_trail')*/?>"><i class="fa fa-search-plus"></i> Audit Trail</a></li>-->
                        </ul>
                    </li>
                <?php } ?>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-support"></i>
                        <span>Help </span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="<?= base_url('support/tickets')?>"><i class="fa fa-ticket"></i> Tickets</a></li>
                    </ul>
                </li>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- =============================================== -->

    <!-- Content Wrapper. Contains page content -->
    <div id="content-wrapper" class="content-wrapper">

<?php

    if(isset($due_invoices) && is_array($due_invoices)) {
        foreach ($due_invoices as $invoice) {
            $number_of_days = intval((time() - strtotime($invoice->invoice_date)) / (60 * 60 * 24));
            ?>
            <div class="alert alert-warning">
                You have <?= in_array($number_of_days,array(8,11,18)) ? 'an ' : 'a ' ?> <?= $number_of_days ?>-day overdue payment to be settled within <?= 21 - $number_of_days ?> days, kindly pay your subscription to prevent an offline system.
            </div>
            <?php
        }
    }

?>

