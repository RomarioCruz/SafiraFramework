<?php

namespace app\Models;

use Safira\Mvc\Model\AbstractModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * Atividade
 * 
 * @ORM\Table(name="atividade")
 * @ORM\Entity
 */
class Atividade extends AbstractModel {

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

    /**
     * @ORM\Column(name="dataInicio", type="datetime")
     */
    private $dataInicio;

    /**
     * @ORM\Column(name="dataFim", type="datetime")
     */
    private $dataFim;

    /**
     * @ORM\Column(name="prazo", type="integer")
     */
    private $prazo;

    /**
     * @ORM\OneToOne(targetEntity="app\Models\Projeto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="projeto_id", referencedColumnName="id")
     * })
     */
    private $projeto;

    /**
     * @ORM\OneToOne(targetEntity="app\Models\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;
    
    /**
     * @ORM\OneToOne(targetEntity="app\Models\StatusAtividade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_atividade_id", referencedColumnName="id")
     * })
     */
    private $statusAtividade;

    function getId() {
        return $this->id;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getDataInicio() {
        return $this->dataInicio;
    }

    function getDataFim() {
        return $this->dataFim;
    }

    function getPrazo() {
        return $this->prazo;
    }

    function getUsuario() {
        return $this->usuario;
    }
    
    function getStatusAtividade() {
        return $this->statusAtividade;
    }

    function getProjeto() {
        return $this->projeto;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setDataInicio($dataInicio) {
        $this->dataInicio = $dataInicio;
    }

    function setDataFim($dataFim) {
        $this->dataFim = $dataFim;
    }

    function setPrazo($prazo) {
        $this->prazo = $prazo;
    }

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }
    
    function setStatusAtividade($statusAtividade) {
        $this->statusAtividade = $statusAtividade;
    }

    function setProjeto($projeto) {
        $this->projeto = $projeto;
    }

}
