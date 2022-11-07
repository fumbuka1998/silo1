<?php
$edit = isset($stakeholder);
$modal_heading = $edit ? $stakeholder->stakeholder_name : 'New Stakeholder';
$action = 'stakeholders/save_stakeholder/'.($edit ? $stakeholder->{$stakeholder::DB_TABLE_PK} : '');
?>
<div class="modal-dialog">
	<div class="modal-content">
		<form method="post" action="<?= base_url($action) ?>">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?= $modal_heading ?></h4>
			</div>
			<div class="modal-body">
				<div class='row'>
					<div class="col-xs-12">
						<div class="form-group col-md-6">
							<label title="Client | Supplier | Contractor" for="stakeholder_name" class="control-label">Stakeholder Name</label>
							<input type="text" class="form-control" required name="stakeholder_name" value="<?= $edit ? $stakeholder->stakeholder_name : '' ?>">
						</div>
						<div class="form-group col-md-6">
							<label for="email" class="control-label">Email</label>
							<input type="email" class="form-control" name="email" value="<?= $edit ? $stakeholder->email : '' ?>">
						</div>
						<div class="form-group col-md-6">
							<label for="stakeholder_name" class="control-label">Phone</label>
							<input type="text" class="form-control" name="phone" value="<?= $edit ? $stakeholder->phone : '' ?>">
						</div>
						<div class="form-group col-md-6">
							<label for="alternative_phone" class="control-label">Alternative Phone</label>
							<input type="text" class="form-control" name="alternative_phone" value="<?= $edit ? $stakeholder->alternative_phone : '' ?>">
						</div>
						<?php if(!$edit){ ?>

							<div class="form-group col-md-6">
								<label for="account_opening_balance" class="control-label">Account Opening Balance</label>
								<input type="text" class="form-control number_format" name="account_opening_balance" value="0">
							</div>

						<?php } ?>
						<div class="form-group col-md-6">
							<label for="address" class="control-label">Address</label>
							<textarea name="address" rows="5" class="form-control"><?= $edit ? $stakeholder->address : '' ?></textarea>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn btn-default btn-sm">Save</button>
			</div>
		</form>
	</div>
</div>
