<?php

namespace app\Controllers;

use Safira\Mvc\Controller\AbstractController;
use Safira\Helpers\Util;
use Safira\Message\FlashMessenger;

class AuthController extends AbstractController {

    public function __construct() {
        parent::__construct();

        $this->setFieldsRequired(array(
            'login' => 'Usuário',
            'senha' => 'Senha'
        ));
    }

    public function indexAction() {
        if (!$this->getSessionManager()->hasSession($this->getAuth()->getSessionUserAuth())) {
            $this->setTemplate(null);
            $this->render('index');
        } else {
            $this->getRouter()->goToRoot();
        }
    }

    public function loginAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                /* @var $entity \app\Models\Auth */
                $entity = $this->getModelInstance();
                $this->bind($entity);

                if ($this->isValid($entity)) {
                    $this->getAuth()
                            ->setUser($entity->getLogin())
                            ->setPass(sha1($entity->getSenha()))
                            ->login();
                } else {
                    FlashMessenger::addErrorMessage(Util::message('fieldInvalid', $this->getFieldsInvalid()));
                    $this->getRouter()->goToCurrentControllerAction();
                }
            } catch (\Exception $e) {
                echo "Code Error: " . $e->getErrorCode() . "<br>";
                echo "Msg Error: " . $e->getPrevious()->getMessage();
            }
        } else {
            $this->getRouter()->goToController('auth');
        }
    }

    public function logoutAction() {
        $this->getAuth()->logout();
    }

}
