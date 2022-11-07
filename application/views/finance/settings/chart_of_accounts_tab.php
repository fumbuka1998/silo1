<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 3/6/2017
 * Time: 12:06 PM
 */

?>
<div class="row">
	<div class="col-md-12">
		<?php
		foreach($parent_groups as $parent){
			?>
			<div class="col-md-6">
				<div class="box">
					<div class="box-header with-border">
						<div class="form-group">
							<h3><?= ucfirst($parent->group_name) ?></h3>
						</div>
						<div class="col-xs-12">
							<div class="box-tools pull-right">
								<button data-toggle="modal" data-target="#new_account_group_<?= $parent->{$parent::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
									<i class="fa fa-plus"></i> New Group
								</button>
								<div id="new_account_group_<?= $parent->{$parent::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
									<?php $this->load->view('finance/settings/account_group_form',['parent'=>$parent]); ?>
								</div>
								<button data-toggle="modal" data-target="#new_account_<?= $parent->{$parent::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
									<i class="fa fa-plus"></i> New Account
								</button>
								<div id="new_account_<?= $parent->{$parent::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
									<?php
                                        $this->load->view('finance/account_form',['parent'=>$parent,'account' => null]);
                                    ?>
								</div>
							</div>
						</div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12 table-responsive">
								<table class="table table-bordered table-hover">
									<thead>
									<tr>
										<th style="width: 20%"></th><th>Name</th><th style="width: 15%">Code</th><th style="width: 20%"></th>
									</tr>
									</thead>
									<tbody>
									<?php
									$account_natures = $parent->natures($parent->{$parent::DB_TABLE_PK});
									foreach ($account_natures as $account_nature) {
										$account_groups = $account_nature->sub_groups($account_nature->{$account_nature::DB_TABLE_PK});
										?>
										<tr>
											<td colspan="4" style="text-align: center">
												<strong><?= ucfirst($account_nature->group_name) ?></strong></td>
										</tr>
										<?php
										if($account_groups && !$account_nature->has_accounts()) {
											foreach ($account_groups as $group) {
												echo $group->group_items();
											}
										}
										if($account_nature->has_accounts()){
											echo $account_nature->group_items();
										}
									}
									?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</div>




