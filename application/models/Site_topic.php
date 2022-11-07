<?php
class Site_topic extends MY_Model {
    const DB_TABLE = 'site_topics';
    const DB_TABLE_PK = 'id';
    public $name;
    public $description;

    public function dropdown_options(){
        $topics = $this->get();
        $options[''] = '&nbsp;';
        foreach ($topics as $topic){
            $options[$topic->{$topic::DB_TABLE_PK}] = $topic->name;
        }
        return $options;

    }

    public function topics_list($limit, $start, $keyword, $order){
        $order_string = dataTable_order_string(['name'],$order,'name');

        $where = '';
        if($keyword != ''){
            $where = ' name LIKE "%'.$keyword.'%" ';
        }

        $topics = $this->get($limit, $start, $where,$order_string);
        $rows = array();
        foreach ($topics as $topic) {
            $data['topic'] = $topic;
            $rows[] = array(
                $topic->name,
                $topic->description,
                $this->load->view('hse/settings/topics/topics_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$this->count_rows();
        return json_encode($data);

    }
}
