<?php

namespace app\Services;

use Safira\Mvc\Service\AbstractService;
use app\Consts\ModelClasses;

class StatusAtividadeService extends AbstractService {

    public function getModelName() {
        return ModelClasses::STATUS_ATIVIDADE;
    }
    
     public function obterStatus() {
        return $this->find(null, 'app\Models\StatusAtividade');
    }

}
