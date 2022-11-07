<?php
/**
 * Author: https://github.com/davinder17s
 * Email: davinder17s@gmail.com
 * Repository: https://github.com/davinder17s/codeigniter-middleware
 */

class AdminAuthMiddleware {
    protected $controller;
    protected $ci;
    public $roles = array();

    public function __construct($controller, $ci)
    {
        $this->controller = $controller;
        $this->ci = $ci;
    }

    public function run(){
       $request_url = $this->controller->router->class.'/'.$this->controller->router->method;
       if(!$this->ci->session->userdata('staff_id')){

           redirect(base_url('app/login?url='.$request_url));
       }
    }
}