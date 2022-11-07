<?php
if (!empty($table_items)) {

?>

    <table <?php if ($print) { ?> style="font-size: 10px" width="100%" border="1" cellspacing="0" <?php } ?> class="table table-bordered table-hover">
        <thead>
            <tr>
                <th></th>
                <th>Equipment</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $bg = '#efefef';
            $total_amount = $total_vol = $sn = 0;
            foreach ($table_items as $table_item) {
                $total_vol += $table_item['consumption'];
                $total_amount += $amount = $table_item['consumption'] * $table_item['rate'];
            ?>
                <tr style="background: <?= $bg ?>">
                    <td style="text-align: center"><?= ++$sn ?></td>
                    <td><?= $table_item['name'] ?></td>
                    <td style="text-align: center">Lts</td>
                    <td style="text-align: right"><?= $table_item['consumption'] ?></td>
                    <td style="text-align: right"><?= number_format($table_item['rate'], 2) ?></td>
                    <td style="text-align: right"><?= 'TSH ' . number_format($amount, 2) ?></td>
                </tr>
            <?php
                $bg = $bg == '#efefef' ? '#ffffff' : '#efefef';
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td style="font-weight: bold" colspan="2">TOTAL</td>
                <td style="text-align: center">Lts</td>
                <td style="font-weight: bold; text-align: right"><?= number_format($total_vol) ?></td>
                <td></td>
                <td style="font-weight: bold; text-align: right"><?= number_format($total_amount, 2) ?></td>
            </tr>
        </tfoot>
    </table>
<?php

}
