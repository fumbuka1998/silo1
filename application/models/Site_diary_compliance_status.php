<?php
class Site_diary_compliance_status extends MY_Model {
    const DB_TABLE = 'site_diary_compliance_statuses';
    const DB_TABLE_PK = 'id';
    public $site_diary_id;
    public $description;
    public $comments;

    public function site_diary(){
        $this->load->model('site_diary_compliance');
        $site_diary = new Site_diary_compliance();
        $site_diary->load($this->site_diary_id);
        return $site_diary;
    }
}