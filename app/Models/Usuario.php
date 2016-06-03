<?php

namespace app\Models;

use Safira\Mvc\Model\AbstractModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * Usuario
 * 
 * @ORM\Table(name="usuario")
 * @ORM\Entity
 */
class Usuario extends AbstractModel {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="login", type="string")
     */
    private $login;

    /**
     * @ORM\Column(name="senha", type="string")
     */
    private $senha;

    /**
     * @ORM\Column(name="nome", type="string")
     */
    private $nome;

    /**
     * @ORM\Column(name="perfil", type="smallint")
     */
    private $perfil;

    function getId() {
        return $this->id;
    }

    function getLogin() {
        return $this->login;
    }

    function getSenha() {
        return $this->senha;
    }

    function getNome() {
        return $this->nome;
    }

    function getPerfil() {
        return $this->perfil;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setLogin($login) {
        $this->login = $login;
    }

    function setSenha($senha) {
        $this->senha = $senha;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setPerfil($perfil) {
        $this->perfil = $perfil;
    }

}
