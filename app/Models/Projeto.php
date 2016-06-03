<?php

namespace app\Models;

use Safira\Mvc\Model\AbstractModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * Projeto
 * 
 * @ORM\Table(name="projeto")
 * @ORM\Entity
 */
class Projeto extends AbstractModel {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="descricao", type="string")
     */
    private $descricao;

    
    function getId() {
        return $this->id;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

}
