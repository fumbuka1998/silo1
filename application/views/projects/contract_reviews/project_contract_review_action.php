<?php if($project_contract_review->created_by == $this->session->userdata('employee_id')){ ?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_project_contract_review_<?= $project_contract_review->{$project_contract_review::DB_TABLE_PK} ?>" class="btn btn-default btn-xs" value="<= ?>">
        <i class="fa fa-edit"></i>   Edit
    </button>
    <div id="edit_project_contract_review_<?= $project_contract_review->{$project_contract_review::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
        <?php $this->load->view('projects/contract_reviews/project_contract_review_form'); ?>
    </div>
    <button project_contract_review_id="<?= $project_contract_review->{$project_contract_review::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs btn-xs delete_project_contract_review ">
        Delete
    </button>
</span>
<?php } ?>