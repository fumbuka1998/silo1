<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/12/2017
 * Time: 4:58 PM
 */
if($currency->is_native == 0){
    ?>

    <span class="pull-right">
    <button data-toggle="modal" data-target="#edit_currency_<?= $currency->{$currency::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_currency_<?= $currency->{$currency::DB_TABLE_PK} ?>" class="modal fade" tabindex="-1" role="dialog">
        <?php $this->load->view('finance/settings/currency_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_currency" currency_id="<?= $currency->{$currency::DB_TABLE_PK} ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>

<?php }
