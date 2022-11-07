<?php
/**
 * Created by PhpStorm.
 * User: userx
 * Date: 2/4/20
 * Time: 9:59 AM
 */

class Topic_subject extends MY_Model{
    const DB_TABLE = 'topic_subjects';
    const DB_TABLE_PK = 'id';

    public $subject_type;
    public $activity_id;
    public $task_id;
    public $topic_id;

}
?>