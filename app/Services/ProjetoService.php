<?php

namespace app\Services;

use Safira\Mvc\Service\AbstractService;
use app\Consts\ModelClasses;

class ProjetoService extends AbstractService {

    public function getModelName() {
        return ModelClasses::PROJETO;
    }
    
    public function obterProjetos() {
        return $this->find(null, 'app\Models\Projeto');
    }

}
