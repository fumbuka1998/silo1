<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 6/5/2018
 * Time: 1:38 PM
 */

    if(!empty($purchase_orders)){
        ?>
        <div class="col-xs-12 table-responsive">
    <table  <?php if($print){ ?> style="font-size: 11px" width="100%" border="1" cellspacing="0"  <?php } else { ?> class="table table-bordered table-hover" <?php } ?> >
        <thead>
            <tr>
                <th>Date</th><th>Order No</th><th>Vendor</th><th>Project</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach($purchase_orders as $purchase_order){
                $vendor= $purchase_order->vendor();
            ?>
            <tr>
                <td><?= custom_standard_date($purchase_order->issue_date) ?></td>
                <td><?= $purchase_order->order_number() ?></td>
                <td><?= $vendor->vendor_name ?></td>
                <td><?= $purchase_order->cost_center_name() ?></td>
                <td><?= $purchase_order->status ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
        </div>
<?php
    } else {
        ?>
        <div class='col-xs-12 alert alert-info' style="text-align: center ;">No purchase order found</div>
        <?php
    }
?>

