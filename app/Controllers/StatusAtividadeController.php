<?php

namespace app\Controllers;

use Safira\Mvc\Controller\AbstractController;

class StatusAtividadeController extends AbstractController {

    public function __construct() {
        parent::__construct();
        $this->titlePage = 'Cadastro de Status da Atividade';

        $this->setFieldsRequired(array(
            'descricao' => 'Descrição'
        ));
        
    }
    
    public function hasPermission() {
        return false;
    }

}
