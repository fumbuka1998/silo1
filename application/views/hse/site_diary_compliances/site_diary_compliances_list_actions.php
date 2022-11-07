<?php
$site_diary_compliance_id = $site_diary_compliance->{$site_diary_compliance::DB_TABLE_PK};
?>
<span class="pull-left">
        <?php
        echo anchor(base_url('hse/preview_site_diary_compliance/'. $site_diary_compliance_id),'<i class="fa fa-print"></i>',  'title="Print" target="_blank" class="btn btn-xs btn-default"');
        ?>
        <button data-toggle="modal" title="Edit" data-target="#edit_site_diary_compliance_<?= $site_diary_compliance_id ?>"
                class="btn btn-default btn-xs">
         <i class="fa fa-edit"></i>
    </button>

    <button class="btn btn-danger btn-xs delete_site_diary_compliance" title="Edit" site_diary_compliance_id = "<?= $site_diary_compliance_id ?>">
        <i class="fa fa-trash"></i>
    </button>
</span>
<div id="edit_site_diary_compliance_<?= $site_diary_compliance_id ?>" class="modal fade site_diary_compliance_form " role="dialog">
    <?php  $this->load->view('hse/site_diary_compliances/site_diary_compliance_form');?>
</div>