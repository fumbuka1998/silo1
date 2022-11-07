<div class="modal-dialog" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h4 class="modal-title">Equipment Receipt</h4>
        </div>

            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-6">

                        <p>Receipt Date: <?= custom_standard_date($hired_equipment_receipt->receipt_date); ?></p>

                    </div>
                    <div class="col-xs-6">


                        <p>Vendor:  <?= $hired_equipment_receipt->vendor()->vendor_name; ?></p>

                    </div>

                </div>
                <div class='row'>
                    <div class="col-xs-12">

                            <?php $hired_equipments=$hired_equipment_receipt->hired_equipments()?>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                <tr>
                                <th>Equipment Code</th><th>Equipment Group</th><th>Rate</th><th>Rate Mode</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($hired_equipments as $hired_equipment){?>

                                    <tr>
                                        <td><?= $hired_equipment->equipment_code;?></td>
                                        <td><?= $hired_equipment->equipment_group()->group_name;?></td>
                                        <td style="text-align:right;"><?= number_format($hired_equipment->rate);?></td>
                                        <td><?= $hired_equipment->rate_mode;?></td>
                                    </tr>
                                <?php }?>

                                </tbody>

                            </table>
                    </div>
                </div>

            </div>
            
            <div class="modal-footer">
                <div class="row">
                    <div class="col-xs-12 text-center">

                        <p>Comments: <?= $hired_equipment_receipt->comments; ?></p>

                    </div>
                </div>
            </div>

    </div>
</div>