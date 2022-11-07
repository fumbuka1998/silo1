<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/26/2018
 * Time: 11:49 PM
 */
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">

                        <input id="component_keyword" type="text" placeholder="Search..">
                        <button data-toggle="modal" data-target="#new_component" class="btn btn-default btn-xs">
                            <i class="fa fa-plus"></i> New Component
                        </button>
                        <div id="new_component" class="modal fade" tabindex="-1" role="dialog">
                            <?php $this->load->view('tenders/profile/components/component_form'); ?>
                        </div>

                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <div class="row">
                            <div class="col-xs-12" id="components_container" tender_id="<?= $tender->{$tender::DB_TABLE_PK} ?>">
                                <?php
                                    $this->load->view('tenders/profile/components/component_list',['tender_components' => $tender->get_components()]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
