<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 9/8/2018
 * Time: 11:47 AM
 */

?>
<div style="width: 100%; height: 90%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Retirements</h4>
        </div>
        <form>
            <div class="modal-body">
                <table imprest_voucher_id="<?= $imprest_voucher_id ?>" class="table table-bordered table-hover retirement_examination_list">
                    <thead>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
            </div>
        </form>
    </div>
</div>
