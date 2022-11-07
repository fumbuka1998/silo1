

    <span class="pull-right">
    <button data-toggle="modal" data-target="#edit_cost_center_<?= $cost_center->{$cost_center::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_cost_center_<?= $cost_center->{$cost_center::DB_TABLE_PK} ?>" class="modal fade" tabindex="-1" role="dialog">
        <?php $this->load->view('finance/settings/cost_center_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_cost_center_button" cost_center_id="<?= $cost_center->{$cost_center::DB_TABLE_PK} ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
