<?php
$images_extension = array('gif','jpg','png','jpeg');
if(!empty($project_topics)){
    foreach($project_topics as $index=>$project_topic){
        $convos = $project_topic->conversations('CAPTION');
        $convo = array_shift($convos);
        $time_set = set_duration($project_topic->updated_at);
        ?>

    <div class="box box-widget">
        <div class="box-header with-border">
            <div class="user-block">
                <script>
                    function <?= 'time_sent'.$project_topic->id?>() {
                        $.alert({
                            title: '',
                            content: "<?= "<b>Date: </b>".explode(' ',$project_topic->created_at)[0] ?> <br/> <?= "<b>Time: </b>".explode(' ',$project_topic->created_at)[1] ?> ",
                        });
                    }
                </script>
                <img class="profile-user-img img-responsive img-circle" src="<?= session_user_avatr() ?>" alt="User profile picture">
                <span class="username"><a href="#"><?= $project_topic->creator()->full_name() ?></a></span>
                <span class="description">Shared publicly - <a href="#" onclick="<?= 'time_sent'.$project_topic->id ?>()" class="time mr-1"><i class="fa fa-clock-o"></i><small><?= ' '.$time_set ?></small></a></span>
            </div>
            <!-- /.user-block -->
            <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
            <!-- /.box-tools -->
        </div>
        <!-- /.box-header -->
        <div class="box-body">

            <h5 class="box-title"><?= strtoupper(wordwrap($project_topic->subject,60,'<br/>')) ?></h5>
            <?php
            if($project_topic->has_thumbnail()){
                $ext = pathinfo($project_topic->attachment_name, PATHINFO_EXTENSION);
                if(in_array($ext,$images_extension)){
                ?>
                <img class="img-responsive pad" src="<?= $project_topic->thumbnail() ?>" alt="There is a problem with the attachment">
            <?php  }
            } ?>

            <p><?= wordwrap($convo->message,100,'<br/>')?></p>
            <?php
                if($project_topic->has_thumbnail()) {
                    $ext = pathinfo($project_topic->attachment_name, PATHINFO_EXTENSION);
                    if (!in_array($ext, $images_extension)) {
                        ?>
                        <div class="attachment-block clearfix">
                            <?= $project_topic->thumbnail() ?>
                        </div>
                        <?php
                    }
                }
            ?>
            <span class="pull-right text-muted"> <?= count($project_topic->conversations('COMMENT')) ?> comments</span>
        </div>
        <div class="box-footer box-comments">
            <?php
            $topic_comments = $project_topic->conversations('COMMENT');
            if(!empty($topic_comments)) {
                foreach ($topic_comments as $comment) {
                    ?>
                    <div class="box-comment">
                        <img class="profile-user-img img-responsive img-circle" src="<?= session_user_avatr() ?>"
                             alt="User profile picture">
                        <div class="comment-text">
                          <span class="username">
                            <?= $comment->sender()->full_name() ?>
                            <span class="text-muted pull-right"><?= set_duration($comment->created_at) ?></span>
                          </span>
                            <?= wordwrap($comment->message, 100, '<br/>') ?>
                        </div>
                    </div>
                <?php }
            } else {
                ?>
                <div class='alert alert' style="text-align: center">No comments currently</div>
                <?php
            } ?>
        </div>
        <div class="box-footer">
            <form id="comment_form_<?= $project_topic->id ?>">
                <img class="img-responsive img-circle img-sm" src="<?= session_user_avatr() ?>" alt="Alt Text">
                <div class="img-push">
                    <div class="input-group">
                        <input type="text" name="comment_message" placeholder="Type Message ..." class="form-control">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary btn-flat comment_post" id="comment_post_<?= $project_topic->id ?>"
                                    project_id = "<?= $project_id ?>"
                                    topic_id="<?= $project_topic->id ?>">
                                <i class="fa fa-plus"></i></button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
    }
} else {
    ?>
    <div class='alert alert-info' style="text-align: center">No Project Posts</div>
    <?
}
?>