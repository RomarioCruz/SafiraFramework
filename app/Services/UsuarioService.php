<?php

namespace app\Services;

use Safira\Mvc\Service\AbstractService;
use app\Consts\ModelClasses;

class UsuarioService extends AbstractService {

    public function getModelName() {
        return ModelClasses::USUARIO;
    }

    public function obterResponsaveis() {
        return $this->find(null, 'app\Models\Usuario');
    }

}
