<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 26/05/2018
 * Time: 04:40
 */
$this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Test Graph
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href=""></a></li>
            <li class="active"></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="col-xs-12">
                            <div class="box-tools pull-right">

                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div id="chart_container" class="col-xs-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th></th><th>Item</th><th>Average Prices</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $sn = 0;
                                        foreach ($material_items as $material_item){
                                            $sn++;
                                            $material_item_id = $material_item->{$material_item::DB_TABLE_PK};
                                            ?>
                                            <tr>
                                                <td><?= $sn ?></td>
                                                <td><?= $material_item->item_name ?></td>
                                                <td>
                                                    <table class="table table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Transaction Date</th><td>Sub Location ID</td><th>Project ID</th><th>Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                                foreach ($material_stocks[$material_item_id] as $material_stock){
                                                                    ?>
                                                                    <tr style="<?= $material_stock->price == 37288 ? 'background-color: red' : '' ?>">
                                                                        <td><?= $material_stock->date_received ?></td>
                                                                        <td><?= $material_stock->sub_location_id ?></td>
                                                                        <td><?= $material_stock->project_id ?></td>
                                                                        <td><?= number_format($material_stock->price) ?></td>
                                                                    </tr>
                                                        <?php
                                                                }
                                                        
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>
