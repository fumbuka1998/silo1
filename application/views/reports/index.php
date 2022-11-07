<?php $this->load->view('includes/header');?>
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Reports
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
                <li class="active">Reports</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua-gradient">
                                        <div class="inner">
                                            <h3><i class="fa fa-product-hunt"></i></h3>

                                            <p>Projects Summary</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-product-hunt"></i>
                                        </div>
                                        <a href="<?= base_url('reports/project_summary') ?>" class="small-box-footer">Projects Summary <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                                <!-- ./col -->
                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua-active">
                                        <div class="inner">
                                            <h3><i class="fa fa-barcode"></i></h3>

                                            <p>Projects Material Status</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-barcode"></i>
                                        </div>
                                        <a href="<?= base_url('reports/project_material_status') ?>" class="small-box-footer">Projects Material Status <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                                <!-- ./col -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>