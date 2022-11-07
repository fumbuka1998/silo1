<?php
$topic_id = $topic->{$topic::DB_TABLE_PK};
?>

<span class="pull-left">
    <button data-toggle="modal" data-target="#edit_topic_<?= $topic_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_topic_<?= $topic_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('hse/settings/topics/topic_form');?>
    </div>

    <button class="btn btn-danger btn-xs delete_topic" topic_id = "<?= $topic_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
