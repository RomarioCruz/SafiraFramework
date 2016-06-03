<?php

namespace app\Controllers;

use Safira\Mvc\Controller\AbstractController;
use Safira\Helpers\Util;
use Safira\Message\FlashMessenger;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AtividadeController extends AbstractController {

    public function __construct() {
        parent::__construct();
        $this->titlePage = 'Cadastro de Atividades';

        $this->setFieldsRequired(array(
            'projeto' => 'Projeto',
            'descricao' => 'Descrição',
            'usuario' => 'Responsável',
            'prazo' => 'Prazo',
            'dataInicio' => 'Data Início',
        ));

        $this->obterResponsaveis();
        $this->obterStatus();
        $this->obterProjetos();
    }

    protected function bind($entity) {
        $objUsuario = $this->getBusinessService()->find($this->getRequest()->getPost('usuario'), 'app\Models\Usuario');
        $objStatusAtividade = $this->getBusinessService()->find($this->getRequest()->getPost('statusAtividade'), 'app\Models\StatusAtividade');
        $objProjeto = $this->getBusinessService()->find($this->getRequest()->getPost('projeto'), 'app\Models\Projeto');

        $dataInicio = Util::convertDate($this->getRequest()->getPost('dataInicio'));

        $entity->setDescricao($this->getRequest()->getPost('descricao'));
        $entity->setDataInicio($dataInicio);
        $entity->setPrazo($this->getRequest()->getPost('prazo'));
        $entity->setUsuario($objUsuario);
        $entity->setStatusAtividade($objStatusAtividade);
        $entity->setProjeto($objProjeto);

        return $entity;
    }
    
    protected function preUpdate($entity) {
        if($entity->getStatusAtividade()->getId() == 4) {
            $dataFim = Util::convertDate("now");
            $entity->setDataFim($dataFim);
        }
    }

    public function obterResponsaveis() {
        $objResponsavel = $this->getServiceLocator('usuario')->obterResponsaveis();
        $this->setVariable('objResponsavel', $objResponsavel);
    }

    public function obterStatus() {
        $objStatus = $this->getServiceLocator('statusAtividade')->obterStatus();
        $this->setVariable('objStatus', $objStatus);
    }

    public function obterProjetos() {
        $objProjetos = $this->getServiceLocator('projeto')->obterProjetos();
        $this->setVariable('objProjetos', $objProjetos);
    }

    public function applyFormFilter($qb) {
        if (is_subclass_of($this->getModelName(), 'Safira\Mvc\Model\AbstractModel')) {
            $qb->andWhere('m.deleted = 0');
        }

        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getPost('projeto')) {
                $qb->andWhere('m.projeto = :idProjeto')->setParameter('idProjeto', $this->getRequest()->getPost('projeto'));
            }

            if ($this->getRequest()->getPost('usuario')) {
                $qb->andWhere('m.usuario = :idUsuario')->setParameter('idUsuario', $this->getRequest()->getPost('usuario'));
            }

            if ($this->getRequest()->getPost('statusAtividade')) {
                $qb->andWhere('m.statusAtividade = :idStatus')->setParameter('idStatus', $this->getRequest()->getPost('statusAtividade'));
            }
        }
    }

    public function relatorioAction() {
        try {
            
            $itemCountPerPage = $this->itemCountPerPage;

            $id = 1;
            if ($this->getRouter()->getIdFromRoute()) {
                $id = $this->getRouter()->getIdFromRoute();
            }

            $qb = $this->getBusinessService()
                    ->getEntityManager()
                    ->createQueryBuilder()
                    ->setFirstResult($itemCountPerPage * ($id - 1))
                    ->setMaxResults($itemCountPerPage);

            $qb->addSelect('m')->from($this->getModelName(), 'm');

            if($this->getAuth()->getUserInfo('perfil') != 1) {
                $qb->andWhere("m.usuario = {$this->getAuth()->getUserInfo('id')}");
            }

            $this->applyFormFilter($qb);

            $entity = new Paginator($qb);

            $this->setVariable('entity', $entity);
        } catch (Exception $ex) {
            FlashMessenger::addErrorMessage(Util::message('indexError'));
            $this->getRouter()->goToRoot();
        }

        $this->setVariable('titlePage', 'Relatório de Atividades');
        $this->setVariable('scripts', $this->getScriptsArray());
        $this->setVariable('styles', $this->getStylesArray());
        $this->setVariable('pagination', $this->linksPagination(count($entity)));

        $this->render('relatorio');
    }
    
    public function controleAtividadeAction() {
        try {
            
            $itemCountPerPage = $this->itemCountPerPage;

            $id = 1;
            if ($this->getRouter()->getIdFromRoute()) {
                $id = $this->getRouter()->getIdFromRoute();
            }

            $qb = $this->getBusinessService()
                    ->getEntityManager()
                    ->createQueryBuilder()
                    ->setFirstResult($itemCountPerPage * ($id - 1))
                    ->setMaxResults($itemCountPerPage);

            $qb->addSelect('m')->from($this->getModelName(), 'm');

            $qb->andWhere("m.usuario = {$this->getAuth()->getUserInfo('id')}");
            $qb->andWhere('m.statusAtividade != 4');//Finalizado

            $this->applyFormFilter($qb);

            $entity = new Paginator($qb);

            $this->setVariable('entity', $entity);
        } catch (Exception $ex) {
            FlashMessenger::addErrorMessage(Util::message('indexError'));
            $this->getRouter()->goToRoot();
        }

        $this->setVariable('titlePage', 'Controle de Atividades');
        $this->setVariable('scripts', $this->getScriptsArray());
        $this->setVariable('styles', $this->getStylesArray());
        $this->setVariable('pagination', $this->linksPagination(count($entity)));

        $this->render('controleAtividade');
    }
    
    public function mudarStatusAction() {
        $idAtividade = $this->getRouter()->getParam('idAtividade');
        $idStatus = $this->getRouter()->getParam('idStatus');
        
        $this->getBusinessService()->mudarStatus($idAtividade, $idStatus);
        
        $this->getRouter()->goToControllerAction('atividade', 'controleAtividade');
    }
    
    public function hasPermission() {
        if($this->getAuth()->getUserInfo('perfil') == 2) { // usuário comum
            if($this->getRouter()->getCurrentAction() == "relatorio" || $this->getRouter()->getCurrentAction() == "controleAtividade"){
                return true;
            }
            
        } else {
            return true;
        }
        
        return false;
        
    }

}
