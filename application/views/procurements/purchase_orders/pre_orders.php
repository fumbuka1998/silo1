<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        To Be Ordered
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('procurements')?>"><i class="fa fa-shopping-cart"></i>Procurements</a></li>
        <li class="active">To Be Ordered</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="pre_orders_table" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Approved Date</th><th>Vendor</th><th>Project</th><th>Requisition No.</th><th>Approved By</th><th></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');