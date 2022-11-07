<?php
    $edit = isset($hired_equipment_receipt);
    $asset_group_options = asset_group_dropdown_options();
    $vendor_options = isset($vendor_options) ? $vendor_options : vendor_dropdown_options();
    $currency_options = isset($currency_options) ? $currency_options : currency_dropdown_options();

?>
<div class="modal-dialog" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Equipment Receipt Form</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>

                <div class="col-xs-12">
                    <div class="form-group col-md-3">


                        <label for="request_date" class="control-label">Issue Date</label>

                        <input type="hidden" name="equipment_receipt_id" value="<?= $edit ? $hired_equipment_receipt->{$hired_equipment_receipt::DB_TABLE_PK} : '' ?>">
                        <input type="text" class="form-control datepicker" required name="issue_date" value="<?= $edit ? $hired_equipment_receipt->receipt_date : '' ?>">
                    </div>
                   
                  
                     <div class="form-group col-xs-3">
                            <label for="vendor_id" class="control-label">Vendor</label>
                            <?= form_dropdown('vendor_id',$vendor_options, $edit ? $hired_equipment_receipt->vendor_id : '',' class="form-control searchable vendor_id"') ?>
                     </div>

                    <div class="form-group col-md-3 ">
                        <label for="rate" class="control-label">Currency</label>
                        <?= form_dropdown('currency_id',$currency_options,'',' class="form-control"') ?>
                    </div>
                </div>

                <div class="col-xs-12 table-responsive">
                    <table class="table table-hover">
                        <thead>
                      
                        <tr style="display: none;" class="row_template">
                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="asset_group_id" class="control-label">Equipment Group</label>
                                    <?= form_dropdown('asset_group_id',$asset_group_options,'',' class="form-control"') ?>
                                    
                                </div>

                            </td>
                            <td>
                             
                                <div class="form-group col-xs-6">
                                    <label for="equipment_code" class="control-label">Equipment Code</label>
                                    <input type="text" name="equipment_code" class="form-control" >
                                </div>

                                <div class="form-group col-md-6">

                                        <?php  $rate_options=['daily'=>'Daily','hourly'=>'Hourly'];?>

                                        <?php
                                            echo form_label('Rate Mode','rate_mode');
                                            echo form_dropdown('rate_mode', $rate_options,'', " class = ' form-control' required ");
                                        ?>

                                </div>

                              
                            </td>
                            <td>
                                <div class="form-group col-xs-12 ">
                                    <label for="rate" class="control-label">Rate</label>
                                    <input type="text" class="form-control money" name="rate" value="" required>
                                </div>

                            </td>
                           
                            <td>
                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>

                        </thead>
                        <tbody>
                        <?php if(!$edit){ ?>
                        <tr>
                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="asset_group_id" class="control-label">Equipment Group</label>
                                    <?= form_dropdown('asset_group_id',$asset_group_options,'',' class="form-control searchable"') ?>
                                    
                                </div>

                               
                            </td>
                            <td>
                              
                                <div class="form-group col-xs-6">
                                    <label for="equipment_code" class="control-label">Equipment Code</label>
                                    <input type="text" name="equipment_code" class="form-control" >
                                </div>


                                <div class="form-group col-md-6">

                                        <?php  $rate_options=['daily'=>'Daily','hourly'=>'Hourly'];?>

                                        <?php
                                            echo form_label('Rate Mode','rate_mode');
                                            echo form_dropdown('rate_mode', $rate_options,'', " class = ' form-control searchable' required ");
                                        ?>

                                </div>

                               

                            </td>

                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="rate" class="control-label">Rate</label>
                                    <input type="text" class="form-control money" name="rate" value="" required>
                                </div>

                                
                            </td>
                            
                            <td>
                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>
                        <?php } else {

                            $hired_equipments = $hired_equipment_receipt->hired_equipments();

                            foreach ($hired_equipments as $hired_equipment) {?>
                                <tr>
                                    <td>
                                        <div class="form-group col-xs-12">
                                            <label for="asset_group_id" class="control-label">Equipment Group</label>
                                            <?= form_dropdown('asset_group_id',$asset_group_options,$hired_equipment->asset_group_id,' class="form-control searchable"') ?>
                                            
                                        </div>
 
                                    </td>
                                    <td>
                                        <div class="form-group col-xs-6">
                                            <label for="equipment_code" class="control-label">Equipment Code</label>
                                           
                                             <input type="text" name="equipment_code" value="<?= $hired_equipment->equipment_code ?>" class="form-control">
                                                
                                        </div>

                                        <div class="form-group col-md-6">

                                        <?php  $rate_options=['daily'=>'Daily','hourly'=>'Hourly'];?>

                                        <?php
                                            echo form_label('Rate Mode','rate_mode');
                                            echo form_dropdown('rate_mode', $rate_options,$hired_equipment->rate_mode," class = ' form-control searchable' required ");
                                        ?>

                                       </div>

                                      
                                    </td>
                                    <td>
                                        <div class="form-group col-xs-12">
                                            <label for="rate" class="control-label">Rate</label>
                                            <input type="text" class="form-control number_format" name="rate" value="<?=  $hired_equipment->rate  ?>" required>
                                        </div>

                                       
                                    </td>
                                   
                                    <td>

                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                        <?php
                            }

                    
                        }
                        ?>
                        </tbody>
                        <tfoot>
                       
                        <tr>
                            <td style="text-align: right" colspan="6">
                                <button type="button" class="btn btn-default btn-xs row_adder">
                                    Add Row
                                </button>
                                &nbsp;&nbsp;
                               
                            </td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-xs-12">
                    <div class="form-group">
                        <label for="comments" class="control-label">Comments</label>
                        <textarea name="comments" class="form-control"><?= $edit ? $hired_equipment_receipt->comments : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_equipment_receipt">Submit</button>
        </div>
        </form>
    </div>
</div>