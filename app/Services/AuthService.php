<?php

namespace app\Services;

use Safira\Mvc\Service\AbstractService;
use app\Consts\ModelClasses;

class AuthService extends AbstractService {

    public function getModelName() {
        return ModelClasses::AUTH;
    }

}
