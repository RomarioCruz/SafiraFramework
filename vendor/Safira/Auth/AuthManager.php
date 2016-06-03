<?php

namespace Safira\Auth;

use Safira\Session\SessionManager;
use Safira\Routing\Router;
use Safira\Config\Config;
use Safira\Helpers\Util;
use Safira\Message\FlashMessenger;

/**
 * Classe de autenticação
 */
class AuthManager {

    /**
     * Variável que possui o objeto da entidade SessionManager
     * 
     * @var \stdClass 
     */
    protected $sessionManager;
    
    /**
     * Variável que possui o objeto da entidade Router
     * 
     * @var \stdClass 
     */
    protected $router;
    
    /**
     * Variável com o nome da tabela do banco que será buscado as informações de autenticação
     * @var string 
     */
    protected $tableName;
    
    /**
     * Variável com o nome da coluna da tabela do banco que terá o username
     * @var string 
     */
    protected $userColumn;
    
    /**
     * Variável com o nome da coluna da tabela do banco que terá o id do usuário
     * @var string 
     */
    protected $idUserColumn;
    
    /**
     * Variável com o nome da coluna da tabela do banco que terá o nome do usuário
     * @var string 
     */
    protected $nameUserColumn;
    
    /**
     * Variável com o nome da coluna da tabela do banco que terá a senha do usuário
     * @var string 
     */
    protected $passColumn;
    
    /**
     * Variável com o username vindo da View
     * @var string 
     */
    protected $user;
    
    /**
     * Variável com a senha vindo da View
     * @var string 
     */
    protected $pass;
    
    /**
     * Variável com o nome do controller que será exibido após a autenticação
     * @var string 
     */
    protected $loginController;
    
    /**
     * Variável com o nome da action que será exibida após a autenticação
     * @var string 
     */
    protected $loginAction;
    
    /**
     * Variável com o nome do controller que será exibido após fazer logoff do sistema
     * @var string 
     */
    protected $logoutController;
    
    /**
     * Variável com o nome da action que será exibida após fazer logoff do sistema
     * @var string 
     */
    protected $logoutAction;
    
    /**
     * Variável que define se o usuário está autenticado ou não
     * @var boolean 
     */
    protected $sessionUserAuth;
    
    /**
     * Dados do usuário após a autenticação
     * @var array 
     */
    protected $sessionUserInfo;
    
    /**
     * Variável que possui o objeto do Service atual
     * 
     * @var \stdClass 
     */
    private $businessService;

    /**
     * Construtor da classe AuthManager
     * @return \Safira\Auth\AuthManager
     */
    public function __construct() {
        $this->sessionManager = new SessionManager;
        $this->router = new Router;

        $config = Config::auth();
        $this->setIdUserColumn($config['idUserColumn']);
        $this->setnameUserColumn($config['nameUserColumn']);
        $this->setUserColumn($config['userColumn']);
        $this->setPassColumn($config['passColumn']);
        $this->setLoginController($config['afterAuth']);
        $this->setLogoutController('auth');
        $this->setSessionUserAuth('userAuth');
        $this->setSessionUserInfo('userInfo');

        return $this;
    }

    public function getSessionManager() {
        return $this->sessionManager;
    }

    public function getRouter() {
        return $this->router;
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function getUserColumn() {
        return $this->userColumn;
    }

    public function getPassColumn() {
        return $this->passColumn;
    }

    public function getIdUserColumn() {
        return $this->idUserColumn;
    }

    public function setIdUserColumn($idUserColumn) {
        $this->idUserColumn = $idUserColumn;
    }

    public function getNameUserColumn() {
        return $this->nameUserColumn;
    }

    public function setNameUserColumn($nameUserColumn) {
        $this->nameUserColumn = $nameUserColumn;
    }

    public function getSessionUserAuth() {
        return $this->sessionUserAuth;
    }

    public function getSessionUserInfo() {
        return $this->sessionUserInfo;
    }

    public function setSessionUserAuth($sessionUserAuth) {
        $this->sessionUserAuth = $sessionUserAuth;
    }

    public function setSessionUserInfo($sessionUserInfo) {
        $this->sessionUserInfo = $sessionUserInfo;
    }

    public function getUser() {
        return $this->user;
    }

    public function getPass() {
        return $this->pass;
    }

    public function getLoginController() {
        return $this->loginController;
    }

    public function getLoginAction() {
        return $this->loginAction;
    }

    public function getLogoutController() {
        return $this->logoutController;
    }

    public function getLogoutAction() {
        return $this->logoutAction;
    }

    public function setTableName($tableName) {
        $this->tableName = $tableName;
        return $this;
    }

    public function setUserColumn($userColumn) {
        $this->userColumn = $userColumn;
        return $this;
    }

    public function setPassColumn($passColumn) {
        $this->passColumn = $passColumn;
        return $this;
    }

    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    public function setPass($pass) {
        $this->pass = $pass;
        return $this;
    }

    public function setLoginController($controller) {
        $this->loginController = $controller;
        return $this;
    }

    public function setLogoutController($controller) {
        $this->logoutController = $controller;
        return $this;
    }

    public function login() {
        $criteria = array($this->getUserColumn() => $this->getUser(), $this->getPassColumn() => $this->getPass());
        $user = $this->getBusinessService()->getEntityManager()->getRepository($this->getBusinessService()->getModelName())->findBy($criteria);

        if (count($user) == 1) {
            $objUser = array_shift($user);
            if ($objUser->getDeleted() == 0) {
                $idUserColumn = $this->getIdUserColumn();
                $nameUserColumn = $this->getNameUserColumn();
                $userColumn = $this->getUserColumn();

                $dados = array(
                    $this->getIdUserColumn() => $objUser->$idUserColumn,
                    $this->getUserColumn() => $objUser->$userColumn,
                    $this->getNameUserColumn() => $objUser->$nameUserColumn,
                    'perfil' => $objUser->perfil
                );

                $this->getSessionManager()->setSession($this->getSessionUserAuth(), true)
                        ->setSession($this->getSessionUserInfo(), $dados);

//                FlashMessenger::addSuccessMessage(Util::message('authSuccess'));
                $this->getRouter()->goToRoot();
            } else {
                FlashMessenger::addErrorMessage(Util::message('userInactive'));
                $this->getRouter()->goToController($this->getLogoutController());
            }
        } else {
            FlashMessenger::addErrorMessage(Util::message('authInvalid'));
            $this->getRouter()->goToController($this->getLogoutController());
        }

        return $this;
    }

    public function logout() {
        $this->getSessionManager()->destroySession($this->getSessionUserAuth())->destroySession($this->getSessionUserInfo());
        FlashMessenger::addSuccessMessage(Util::message('logoutSuccess'));
        $this->getRouter()->goToController($this->getLogoutController());

        return $this;
    }

    public function checkLogin($action) {
        switch ($action) {
            case "boolean":
                if (!$this->getSessionManager()->hasSession($this->getSessionUserAuth())) {
                    return false;
                } else {
                    return true;
                }
                break;
            case "redirect":
                if (!$this->getSessionManager()->hasSession($this->getSessionUserAuth())) {
                    if ($this->getRouter()->getCurrentController() != $this->getLoginController()) {
                        $this->getRouter()->goToController($this->getLoginController());
                    }
                }
                break;
            case "stop":
                if (!$this->getSessionManager()->hasSession($this->getSessionUserAuth())) {
                    exit();
                }
                break;
        }
    }

    public function hasUserAuth() {
        return $this->getSessionManager()->getSession($this->getSessionUserAuth());
    }

    public function getUserId() {
        return $this->getSessionManager()->getSession($this->getSessionUserInfo())[$this->getIdUserColumn()];
    }

    public function getUserInfo($key = null) {
        $data = $this->getSessionManager()->getSession($this->getSessionUserInfo());

        if ($key) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * Retorna o objeto do Service do controller atual
     * @return \Safira\Mvc\Service\AbstractService
     */
    protected function getBusinessService() {
        if ($this->businessService == null) {
            $serviceName = "\app\Services\\" . ucfirst($this->getRouter()->getCurrentController()) . "Service";
            $this->businessService = new $serviceName;
        }

        return $this->businessService;
    }

}
