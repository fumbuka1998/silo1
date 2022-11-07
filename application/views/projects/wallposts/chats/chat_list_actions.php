<?php ?>
<button data-toggle="modal" data-target="#continue_chat_<?= $topic->{$topic::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
    <i class="fa fa-comments-o"></i>
</button>
<div id="continue_chat_<?= $topic->{$topic::DB_TABLE_PK} ?>" class="modal fade chat_form_continuation" role="dialog">
    <?php $this->load->view('projects/wallposts/chats/chat_form');?>
</div>
