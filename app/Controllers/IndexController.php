<?php

namespace app\Controllers;

use Safira\Mvc\Controller\AbstractController;

class IndexController extends AbstractController {
    
    public function __construct() {
        parent::__construct();
        $this->titlePage = 'Home';
    }

}
