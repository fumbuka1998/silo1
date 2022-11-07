<?php
$images_extension = array('gif','jpg','png','jpeg');

if(!empty($topic_convo_logs)){
    foreach($topic_convo_logs as $convo_log){
        $log = unserialize(urldecode($convo_log->log_details));
        $session_id = $this->session->userdata('employee_id');
        $topic = $log['topic'];
        $convo = $log['topic_convo'];
        $date = $convo->created_at;
        $createDate = new DateTime($date);
        $strip_ymd = $createDate->format('H:i:s')
        ?>
        <div class="row" >
            <div class="item <?= $convo->sender == $session_id ? 'pull-right' : 'pull-left' ?> col-md-8" style="background-color: #7adddd">
                <a href="#" class="name">
                    <?= $convo->sender()->full_name() ?>
                    <small class="text-muted pull-right"><i class="fa fa-clock-o"></i><?= $strip_ymd ?></small>
                </a>
                <br/>
                <p class="message">
                    <?= wordwrap($convo->message,100,'<br/>') ?>
                </p>
                <?php if ($topic->has_thumbnail()) { ?>
                    <div class="attachment">
                        <h4>Attachments:</h4>
                        <?php
                        $ext = pathinfo($topic->attachment_name, PATHINFO_EXTENSION);
                        if (in_array($ext, $images_extension)) {
                            ?>
                            <p class="filename">
                                <img class="gallery-items"
                                     src="<?= $topic->thumbnail() ?>"
                                     alt="There is a problem with the attachment" width="100px">
                            </p>
                            <?php
                        } else {
                            ?>
                            <p class="filename">
                                <?= $topic->thumbnail() ?>
                            </p>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                } ?>
            </div>
        </div>
        <hr>
        <?php
    }
} else {
    ?>
    <div class='alert alert-info' style="text-align: center">Type a message to start a chat</div>
    <?
}
?>