<?php

namespace app\Consts;

/**
 * Interface que contém o nome dos Models, como constantes, para ser utilizada
 * durante a codificação para centralizar os pontos de modificação
 *
 */
interface ModelClasses {

    const AUTH = 'app\Models\Auth';
    const ATIVIDADE = 'app\Models\Atividade';
    const STATUS_ATIVIDADE = 'app\Models\StatusAtividade';
    const PROJETO = 'app\Models\Projeto';
    const USUARIO = 'app\Models\Usuario';

}
