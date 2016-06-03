<?php

namespace app\Controllers;

use Safira\Mvc\Controller\AbstractController;

class ProjetoController extends AbstractController {

    public function __construct() {
        parent::__construct();
        $this->titlePage = 'Cadastro de Projetos';

        $this->setFieldsRequired(array(
            'descricao' => 'Descrição'
        ));
        
    }
    
    public function hasPermission() {
        if($this->getAuth()->getUserInfo('perfil') == 1) { // admin
            return true;
        } 
        
        return false;
    }

}
