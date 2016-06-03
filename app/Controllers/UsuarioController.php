<?php

namespace app\Controllers;

use Safira\Mvc\Controller\AbstractController;

class UsuarioController extends AbstractController {

    public function __construct() {
        parent::__construct();
        $this->titlePage = 'Cadastro de Usuários';

        $this->setFieldsRequired(array(
            'login' => 'Login',
            'senha' => 'Senha',
            'nome' => 'Nome',
            'perfil' => 'Perfil'
        ));
        
    }
    
    protected function prePersist($entity) {
        $entity->setSenha(sha1($entity->getSenha()));
        
        return $entity;
    }
    
    protected function preUpdate($entity) {
        $uow = $this->getBusinessService()->getEntityManager()->getUnitOfWork();
        $originalData = $uow->getOriginalEntityData($entity);
        
        if($entity->getSenha() != $originalData['senha']) {
            $entity->setSenha(sha1($entity->getSenha()));
        }

        return $entity;
    }
    
    public function hasPermission() {
        if($this->getAuth()->getUserInfo('perfil') == 1) { // admin
            return true;
        } 
        
        return false;
        
    }

}
