
<?php if(count($tax_rate_items)>0){?>

    <table class="table table-bordered table-hover table-striped">
        <thead>
        <tr>
            <th>From</th>
            <th>To</th>
            <th>Rate</th>
            <th>Additional amount</th>

        </tr>
        </thead>

        <?php foreach($tax_rate_items as $tax_rate_item){?>

            <tr>
                <td><?= number_format($tax_rate_item->minimum, 2); ?></td>
                <td><? if($tax_rate_item->maximum == 0){echo 'Infinity';}else{ echo number_format($tax_rate_item->maximum, 2);} ?></td>
                <td><?= $tax_rate_item->rate.' %'; ?></td>
                <td><?= number_format($tax_rate_item->additional_amount,2); ?></td>

            </tr>

        <?php } ?>

    </table>

<?php }else{?>

    <p style="text-align:center;">NO ITEMS FOUND</p>

<?php } ?>