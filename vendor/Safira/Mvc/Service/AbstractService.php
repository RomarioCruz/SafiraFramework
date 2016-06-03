<?php

namespace Safira\Mvc\Service;

abstract class AbstractService {

    /**
     * Variável que possui o objeto do EntityManager do Doctrine
     * 
     * @var \stdClass 
     */
    private $entityManager;

    /**
     * Construtor do AbstractService
     */
    public function __construct() {
        $entityManager = include '../config/database.php';
        $this->setEntityManager($entityManager);
    }

    /**
     * Insere os dados da entidade no banco de dados
     * @param Model $entity
     * @param boolean $flush
     * @throws \Exception
     */
    public function create($entity, $flush = true) {
        try {
            $this->getEntityManager()->persist($entity);

            if ($flush) {
                $this->flush();
            }
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $code = $exc->getCode();

            throw new \Exception($message, $code, $exc);
        }
    }

    /**
     * Atualiza os dados da entidade no banco de dados
     * @param Model $entity
     */
    public function update($entity) {
        $this->flush();
    }

    /**
     * Remove (logicamente ou não) os dados da entidade no banco de dados
     * @param Model $entity
     * @param boolean $flush
     */
    public function delete($entity, $flush = true) {
        if ($entity instanceof \Safira\Mvc\Model\AbstractModel) {
            $entity->setDeleted(true);
        } else {
            $this->getEntityManager()->remove($entity);
        }

        if ($flush) {
            $this->flush();
        }
    }

    /**
     * Busca os dados da entidade no banco de dados
     * @param int $id
     * @param Model $model
     * @return Model
     */
    public function find($id = null, $model = null) {
        if(!$model){
            $model = $this->getModelName();
        }
        
        if (is_null($id)) {
                $register = $this->getEntityManager()->getRepository($model)->findBy(array('deleted' => 0));
        } else {
            $register = $this->getEntityManager()->getRepository($model)->find($id);
        }

        $this->getEntityManager()->getConnection()->close();
        
        return $register;
    }

    /**
     * Commita as operações no banco de dados
     * @throws \Exception
     */
    public function flush() {
        try {
            $this->getEntityManager()->flush();
        } catch (\Exception $exc) {
            $msg = "Não foi possível salvar as informações. Tente mais tarde.";
            $msg.= $exc->getMessage();
            throw new \Exception($msg, $exc->getCode(), $exc);
        }
    }

    /**
     * Executa um merge entre os dados do objeto
     * @param Model $entity
     * @return Model $entity
     */
    public function merge($entity) {
        return $this->getEntityManager()->merge($entity);
    }

    /**
     * Retorna os dados do objeto para a forma inicial
     * @param Model $entity
     */
    public function refresh($entity) {
        $this->getEntityManager()->refresh($entity);
    }

    /**
     * Limpa os dados do objeto
     */
    public function clear() {
        $this->getEntityManager()->clear($this->getModelName());
    }

    public function contains($entity) {
        return $this->getEntityManager()->contains($entity);
    }

    public function detach($entity) {
        $this->getEntityManager()->detach($entity);
    }

    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->entityManager;
    }

    private function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * Retorna uma instância do Model utilizado
     */
    public function getModelInstance() {
        return $this->getBusinessService()->getModelInstance();
    }

    /**
     * @return string Representa no nome da classe Model, daquele controller
     */
    abstract public function getModelName();
}
