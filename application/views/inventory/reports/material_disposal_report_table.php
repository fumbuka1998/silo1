<table <?php if ($print) { ?> width="100%" border="1" cellspacing="0" style="font-size: 11px" <?php } else { ?> class="table table-bordered table-hover" <?php } ?>>
    <thead>
        <tr>
            <th>Disposal Date</th>
            <th>Project</th>
            <th>Amount</th>
            <th>Disposed By</th>
            <th>Datetime</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($table_items as $table_item) { ?>
            <tr>
                <td><?= custom_standard_date($table_item['disposal_date']) ?></td>
                <td><?= $table_item['project'] ?></td>
                <td style="text-align: right;"><?= $print ? number_format($table_item['amount'], 2) : anchor(base_url('inventory/preview_material_disposal/' . $table_item['disposal_id']), number_format($table_item['amount'], 2), ' target="_blank"') ?></td>
                <td><?= $table_item['disposed_by'] ?></td>
                <td><?= date('d/m/Y H:i:s', strtotime($table_item['datetime'])) ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>