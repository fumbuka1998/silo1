<?php
/**
 * Created by PhpStorm.
 * User: userx
 * Date: 2/3/20
 * Time: 9:07 PM
 */

$continuation = isset($topic);
?>
<div style="width: 70%" class="modal-dialog">
    <form>
        <div class="modal-content">
            <div class="box box-info">
                <div class="box-header">
                    <div class="col-md-12">
                        <i class="fa fa-comments-o"></i>
                        <h3 class="box-title"><span class="display_main_recipient"><?= $continuation ? $topic->chat_main_recipient() : '' ?></span></h3>
                        <?php
                        if($continuation) {
                            $ccs = $topic->ccs();
                            if (!empty($ccs)) {
                                ?>
                                <h5>Cc:&nbsp;&nbsp;
                                    <?php
                                    foreach ($ccs as $cc) {
                                        ?>
                                        <span><?= $cc->email ?>&nbsp;</span>
                                        <?php
                                    }
                                    ?>
                                </h5>
                                <?php
                            }
                        }
                        ?>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <input type="hidden" name="topic_id" value="<?= $continuation ? $topic->{$topic::DB_TABLE_PK} : '' ?>">
                        <input type="hidden" name="recipient" value="<?= $continuation ? $topic->chat_main_recipient(true) : '' ?>">
                        <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                    </div>
                    <?php
                        if(!$continuation){
                    ?>
                    <div class="input_container">
                        <div class="col-md-12">
                            <div class="col-md-10">
                                <div class="col-sm-2" style="text-align: right">
                                    <label for="recipient">To&nbsp;&nbsp;:  </label>
                                </div>
                                <div class="col-sm-10">
                                    <?= form_dropdown('recipient',$employee_options,'',' class="form-control searchable"') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-10">
                                <div class="col-sm-2" style="text-align: right">
                                    <label for="direct_chat_ccs">Cc&nbsp;&nbsp;:  </label>
                                </div>
                                <div class="col-sm-10">
                                    <?= form_multiselect('direct_chat_ccs[]',$employee_options,[],'class="form-control searchable direct_chat_ccs"')?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                        <div class="col-md-10">
                            <div class="col-sm-2" style="text-align: right">
                                <label for="direct_chat_ccs">Subject&nbsp;&nbsp;:  </label>
                            </div>
                            <div class="col-sm-10">
                                <?= form_dropdown('subject_id',$project_objects_dropdown_options,'','class="form-control searchable"') ?>
                            </div>
                        </div>
                    </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="box-body" id="chat-box">

                </div>
                <div class="box-footer">
                    <div class="col-md-5">
                        <div class="col-sm-2">
                            <label for="topic_attachment"><?= nl2br('Attach
                            File:') ?></label>
                        </div>
                        <div class="col-sm-10">
                            <input type="file" style="width: 85%" name="topic_attachment" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="input-group">
                            <input type="text" class="form-control" name="topic_message" placeholder="Type message...">

                            <div class="input-group-btn">
                                <button type="button" class="btn btn-success send_direct_chat" id="send_direct_chat"><i class="fa fa-send-o"></i>&nbsp;&nbsp;Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
