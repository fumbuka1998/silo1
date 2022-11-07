<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/30/2018
 * Time: 12:56 PM
 */

    if(!empty($requisitions)){
        ?>

        <table <?php if($print){
?>
            width="100%" cellspacing="0" border="1" style="font-size: 11px"
                <?php
        } else {
?>
            class="table table-bordered table-hover"
<?php
        } ?> >
            <thead>
                <tr>
                    <th>SN</th><th>Requisitions</th><th>Requested Amount</th><th></th>
                </tr>
            </thead>
            <tbody>
            <?php
                $sn = 1;
                foreach ($requisitions as $requisition){
                    $orders = $requisition->purchase_orders();
                    $transfers = $requisition->external_transfers();
                    ?>
                    <tr>
                        <td><?= $sn ?></td>
                        <td><?= $print ? $requisition->requisition_number() : anchor(base_url('requisitions/preview_requisition/'.$requisition->{$requisition::DB_TABLE_PK}),$requisition->requisition_number(),' target="_blank" ')  ?></td>
                        <td style="text-align: right"><?= number_format($requisition->total_amount_in_base_currency()) ?></td>
                        <td>
                            <table <?php if($print){
                                ?>
                                width="100%" cellspacing="0" border="1" style="font-size: 11px" class="level_1_borders"
                                <?php
                            } else {
                                ?>
                                class="table table-bordered table-hover"
                                <?php
                            } ?> >
                                <thead>
                                <?php if(!empty($orders)){ ?>
                                    <tr>
                                        <th style="vertical-align: top">ORDERS</th>
                                        <td >
                                            <table  <?php if($print){
                                                ?>
                                                width="100%" cellspacing="0" border="1" style="font-size: 11px" class="level_2_borders"
                                                <?php
                                            } else {
                                                ?>
                                                class="table table-bordered table-hover"
                                                <?php
                                            } ?> >
                                                <?php
                                                    foreach ($orders as $order){
                                                      ?>
                                                        <tr>
                                                            <td><?= $print ? $order->order_number() : anchor(base_url('procurements/preview_purchase_order/'.$order->{$order::DB_TABLE_PK}),$order->order_number(),' target="_blank" ') ?></td>
                                                        </tr>
                                                      <?php
                                                    }
                                                ?>
                                            </table>

                                        </td>
                                    </tr>
                                <?php } ?>
                                    <tr>
                                        <th style="vertical-align: top">CASH</th>
                                    </tr>
                                    <tr>
                                        <th style="vertical-align: top">STORE</th>
                                        <td>
                                            <table  <?php if($print){
                                                ?>
                                                width="100%" cellspacing="0" border="1" style="font-size: 11px" class="level_2_borders"
                                                <?php
                                            } else {
                                                ?>
                                                class="table table-bordered table-hover"
                                                <?php
                                            } ?> >
                                                <?php
                                                    foreach ($transfers as $transfer){
                                                        ?>
                                                        <tr>
                                                            <td><?= $print ? $transfer->transfer_number() : anchor(base_url('inventory/preview_external_material_transfer_sheet/'.$transfer->{$transfer::DB_TABLE_PK}),$transfer->transfer_number(),' target="_blank" ') ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                ?>
                                            </table>
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                        </td>
                    </tr>

                <?php
                    $sn++;
                }
            ?>
            </tbody>
        </table>

        <?php
    } else {
        ?>
        <div class="alert alert-info"> No Requests Found </div>
<?php
    }
?>


