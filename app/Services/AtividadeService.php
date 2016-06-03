<?php

namespace app\Services;

use Safira\Mvc\Service\AbstractService;
use app\Consts\ModelClasses;
use Safira\Helpers\Util;

class AtividadeService extends AbstractService {

    public function getModelName() {
        return ModelClasses::ATIVIDADE;
    }
    
    public function mudarStatus($idAtividade, $idStatus) {
        $objAtividade = $this->find($idAtividade);
        $objStatusAtividade = $this->find($idStatus, 'app\Models\StatusAtividade');
        
        if($idStatus == 4) {
            $dataFim = Util::convertDate("now");
            $objAtividade->setDataFim($dataFim);
        }
        
        $objAtividade->setStatusAtividade($objStatusAtividade);
        $this->flush();
    }

}
