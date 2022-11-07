<?php
/**
 * Created by PhpStorm.
 * User: stunna
 * Date: 20/11/2017
 * Time: 12:44
 */
?>

<table style="font-size: 10px !important;" cellspacing="0" border="1">
    <thead>
        <tr>
            <th>ORDER ITEM ID</th><th>Material Item Name</th><th>Quantity</th><th>Order ID</th><th>Matches</th><th>GRN Junctions</th><th>GRN ITEMS</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $existed_in_matches = [];
        foreach ($order_material_items as $item){
            if(!in_array($item->{$item::DB_TABLE_PK},$existed_in_matches)) {
                $grn_item_junctions = $item->grn_items_junctions();
                $grn_items = $item->grn_items();
                $matched_items = $item->matched_items();
                ?>
                <tr>
                    <td style="vertical-align: top"><?= $item->{$item::DB_TABLE_PK} ?></td>
                    <td style="vertical-align: top"><?= $item->material_item()->item_name ?></td>
                    <td style="vertical-align: top"><?= $item->quantity ?></td>
                    <td style="vertical-align: top"><?= $item->order_id ?></td>
                    <td style="vertical-align: top">
                        <table style="font-size: 10px !important;" cellspacing="0" width="100%" border="1">
                            <tr>
                                <th>ORDER ITEM ID</th>
                                <th>ITEM NAME</th>
                                <th>Quantity</th>
                                <th>Order ID</th>
                            </tr>
                            <?php
                            foreach ($matched_items as $matched_item) {
                                ?>
                                <tr>
                                    <td><?= $matched_item->{$matched_item::DB_TABLE_PK} ?></td>
                                    <td><?= $item->material_item()->item_name ?></td>
                                    <td><?= $matched_item->quantity ?></td>
                                    <td><?= $matched_item->order_id ?></td>
                                </tr>
                                <?php
                                $existed_in_matches[] = $matched_item->{$matched_item::DB_TABLE_PK};
                            }
                            ?>
                        </table>
                    </td>
                    <td style="vertical-align: top">
                        <table style="font-size: 10px !important;" cellspacing="0" width="100%" border="1">
                            <tr>
                                <th>ID</th>
                                <th>ORDER ITEM ID</th>
                                <th>GRN ITEM ID</th>
                            </tr>
                            <?php
                            foreach ($grn_item_junctions as $junction) {
                                ?>
                                <tr>
                                    <td><?= $junction->{$junction::DB_TABLE_PK} ?></td>
                                    <td><?= $junction->purchase_order_material_item_id ?></td>
                                    <td><?= $junction->goods_received_note_item_id ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                    </td>
                    <td>
                        <table style="font-size: 10px !important;" cellspacing="0" width="100%" border="1">
                            <tr>
                                <th>GRN ITEM ID</th>
                                <th>GRN ID</th>
                                <th>QUANTITY</th>
                            </tr>
                            <?php
                            foreach ($grn_items as $grn_item) {
                                ?>
                                <tr>
                                    <td><?= $grn_item->{$grn_item::DB_TABLE_PK} ?></td>
                                    <td><?= $grn_item->grn_id ?></td>
                                    <td><?= $grn_item->stock_item()->quantity ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                    </td>
                </tr>
                <?php
                $existed_in_matches[] = $item->{$item::DB_TABLE_PK};
            }
        }
    ?>
    </tbody>
</table>
