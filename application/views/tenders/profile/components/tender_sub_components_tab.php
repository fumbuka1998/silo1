<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/27/2018
 * Time: 12:27 AM
 */
?>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#tender_subcomponent_form_<?= $component_id ?>" class="btn btn-xs btn-default">
                                <i class="fa fa-plus"></i> New Sub-Component
                            </button>
                            <div id="tender_subcomponent_form_<?= $component_id ?>" class="modal fade" role="dialog">
                                <?php $this->load->view('tenders/profile/components/tender_sub_component_form',$component_id); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class='row'>
                        <div class="col-xs-12 table-responsive">
                            <table tender_component_id="<?= $component_id ?>" class="table table-bordered table-hover table-striped tender_subcomponents_list">
                                <thead>
                                <tr>
                                    <th>Sub-Component Name</th><th>Lump-sum Price</th><th></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
