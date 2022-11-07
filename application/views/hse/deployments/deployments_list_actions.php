<?php
$deployment_id = $deployment->{$deployment::DB_TABLE_PK};
?>

<span class="pull-left">
        <?php
        echo anchor(base_url('hse/preview_deployment/'. $deployment_id),'<i class="fa fa-print"></i>',  'title="Print" target="_blank" class="btn btn-xs btn-default"');
        ?>
        <button data-toggle="modal" title="Attach Files" data-target="#deployment_attachment_<?= $deployment_id ?>"
                class="btn btn-default btn-xs">
        <i class="fa fa-paperclip"></i>
       </button>
  <?php if(!$deployment->deployment_attachments()) {?>
    <a  href="<?= base_url('hse/deployment_form/'.$deployment_id)?>" title="Edit Deployment" class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i>
    </a>

    <button class="btn btn-danger btn-xs delete_deployment" title="Delete Deployment" deployment_id = "<?= $deployment_id ?>">
        <i class="fa fa-trash"></i>
    </button>
    <?php } ?>
</span>
<div id="deployment_attachment_<?= $deployment_id ?>" class="modal fade" role="dialog">
    <?php $this->load->view('hse/deployments/attachments/attachment_form');?>
</div>