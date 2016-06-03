<?php

namespace Safira\Mvc\Controller;

use Safira\Routing\Router;
use Safira\Session\SessionManager;
use Safira\Auth\AuthManager;
use Safira\Helpers\Util;
use Safira\Message\FlashMessenger;
use Safira\Http\HttpRequest;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Classe AbstractController
 */
abstract class AbstractController {

    /**
     * Variável com o nome da action atual
     * @var string 
     */
    private $action;
    
    /**
     * Variável que possui o objeto da entidade SessionManager
     * 
     * @var \stdClass 
     */
    private $sessionManager;
    
    /**
     * Variável que possui o objeto da entidade Router
     * 
     * @var \stdClass 
     */
    private $router;
    
    /**
     * Variável que possui o objeto da entidade AuthManager
     * 
     * @var \stdClass 
     */
    private $auth;
    
    /**
     * Todas as variáveis que estarão disponíveis na View
     * @var array 
     */
    private $variables = array();
    
    /**
     * Variável que possui o objeto do Service atual
     * 
     * @var \stdClass 
     */
    private $businessService;
    
    /**
     * Variável que possui o objeto do Service a ser definido
     * 
     * @var \stdClass 
     */
    private $serviceLocator;
    
    /**
     * Variável que possui os campos obrigatórios
     * 
     * @var \stdClass 
     */
    private $fieldsRequired;
    
    /**
     * Variável que possui os campos inválidos
     * 
     * @var \stdClass 
     */
    private $fieldsInvalid = array();
    
    /**
     * Variável que possui o objeto do HttpRequest
     * 
     * @var \stdClass 
     */
    private $request;
    
    /**
     * Variável com o template da página a ser exibida 
     * @var string 
     */
    private $template;
    
    /**
     * Quantidade de registros a serem exibidos por página na tela index
     * @var int 
     */
    protected $itemCountPerPage = 10;
    
    /**
     * Variável com o título da página a ser exibida 
     * @var string 
     */
    protected $titlePage;

    /**
     * Construtor do AbstractController
     */
    public function __construct() {
        $this->router = new Router;
        $this->sessionManager = new SessionManager;
        $this->auth = new AuthManager;
        $this->setVariable('controller', $this->getRouter()->getCurrentController());
        $this->setVariable('action', $this->getRouter()->getCurrentAction());
        $this->setTemplate('default');
    }

    /**
     * @return string Representa no nome da classe Model, deste controller
     */
    public function getModelName() {
        return $this->getBusinessService()->getModelName();
    }

    /**
     * Ação Index padrão
     */
    public function indexAction() {
        try {
            $entity = $this->paginate($this->itemCountPerPage);
            $this->setVariable('entity', $entity);
        } catch (Exception $ex) {
            FlashMessenger::addErrorMessage(Util::message('indexError'));
            $this->getRouter()->goToRoot();
        }

        $this->setVariable('scripts', $this->getScriptsArray());
        $this->setVariable('styles', $this->getStylesArray());
        $this->setVariable('pagination', $this->linksPagination(count($entity)));
        $this->setVariable('titlePage', $this->titlePage);

        $this->render('index');
    }

    /**
     * Ação de Adição (Insert) padrão
     */
    public function addAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            try {
                $entity = $this->getModelInstance();
                $this->bind($entity);

                if ($this->isValid($entity)) {
                    $this->prePersist($entity);

                    $this->getBusinessService()->create($entity);

                    $this->posPersist($entity);

                    FlashMessenger::addSuccessMessage(Util::message('insertSuccess'));
                    $this->getRouter()->goToController($this->getRouter()->getCurrentController());
                } else {
                    FlashMessenger::addErrorMessage(Util::message('fieldInvalid', $this->getFieldsInvalid()));
                    $this->getRouter()->goToCurrentControllerAction();
                }
            } catch (\Exception $exc) {
                FlashMessenger::addErrorMessage(Util::message('insertError'));
                $this->getRouter()->goToCurrentControllerAction();
            }
        } else {
            $this->setVariable('scripts', $this->getScriptsArray());
            $this->setVariable('styles', $this->getStylesArray());
            $this->setVariable('titlePage', $this->titlePage);

            $this->render('add');
        }
    }

    /**
     * Ação de alteração (Update) padrão
     */
    public function editAction() {
        $id = $this->getRouter()->getIdFromRoute();

        if ($id) {
            $entity = $this->getBusinessService()->find($id);
            if ($entity) {
                $request = $this->getRequest();
                if ($request->isPost()) {
                    try {
                        $this->bind($entity);

                        if ($this->isValid($entity)) {
                            $this->preUpdate($entity);

                            $this->getBusinessService()->update($entity);

                            $this->posUpdate($entity);

                            FlashMessenger::addSuccessMessage(Util::message('updateSuccess'));
                            $this->getRouter()->goToController($this->getRouter()->getCurrentController());
                        } else {
                            FlashMessenger::addErrorMessage(Util::message('fieldInvalid', $this->getFieldsInvalid()));
                            $this->getRouter()->goToCurrentControllerAction();
                        }
                    } catch (Exception $exc) {
                        FlashMessenger::addErrorMessage(Util::message('updateError'));
                        $this->getRouter()->goToCurrentControllerAction();
                    }
                } else {
                    $this->setVariable('entity', $entity);
                    $this->setVariable('scripts', $this->getScriptsArray());
                    $this->setVariable('styles', $this->getStylesArray());
                    $this->setVariable('titlePage', $this->titlePage);

                    $this->render('edit');
                }
            } else {
                FlashMessenger::addErrorMessage(Util::message('registerNotFound'));
                $this->getRouter()->goToController($this->getRouter()->getCurrentController());
            }
        } else {
            $this->getRouter()->goToController($this->getRouter()->getCurrentController());
        }
    }

    /**
     * Ação de exclusão (Delete) padrão
     */
    public function deleteAction() {
        $id = $this->getRouter()->getIdFromRoute();

        if ($id) {
            $entity = $this->getBusinessService()->find($id);
            if ($entity) {
                $request = $this->getRequest();
                if ($request->isPost()) {
                    try {
                        if ($this->isValid($entity)) {
                            $this->preDelete($entity);

                            $this->getBusinessService()->delete($entity);

                            $this->posDelete($entity);

                            FlashMessenger::addSuccessMessage(Util::message('deleteSuccess'));
                            $this->getRouter()->goToController($this->getRouter()->getCurrentController());
                        } else {
                            FlashMessenger::addErrorMessage(Util::message('fieldInvalid', $this->getFieldsInvalid()));
                            $this->getRouter()->goToCurrentControllerAction();
                        }
                    } catch (Exception $exc) {
                        FlashMessenger::addErrorMessage(Util::message('deleteError'));
                        $this->getRouter()->goToCurrentControllerAction();
                    }
                } else {
                    $this->setVariable('entity', $entity);
                    $this->setVariable('scripts', $this->getScriptsArray());
                    $this->setVariable('styles', $this->getStylesArray());
                    $this->setVariable('titlePage', $this->titlePage);

                    $this->render('delete');
                }
            } else {
                FlashMessenger::addErrorMessage(Util::message('registerNotFound'));
                $this->getRouter()->goToController($this->getRouter()->getCurrentController());
            }
        } else {
            $this->getRouter()->goToController($this->getRouter()->getCurrentController());
        }
    }

    /**
     * Renderiza a view
     * @param string $action
     * Exemplo: index, add, edit, delete
     */
    protected function render($action) {
        if($this->hasPermission()) {
            $this->action = $action;

            if ($this->hasTemplate()) {
                if (count($this->variables) > 0) {
                    extract($this->variables, EXTR_OVERWRITE);
                }
                include_once "../app/Views/Templates/{$this->getTemplate()}.phtml";
            } else {
                $this->content();
            }
        } else {
            FlashMessenger::addErrorMessage(Util::message('noPermission'));
            $this->getRouter()->goToRoot();
        }
    }
    
    /**
     * Complemento do render() que carrega a view caso não tenha um template definido
     */
    public function content() {
        $className = str_replace('app\\Controllers\\', '', get_class($this));
        $singleClassName = preg_replace('/Controller/', '', $className, 1);

        if (count($this->variables) > 0) {
            extract($this->variables, EXTR_OVERWRITE);
        }

        include_once '../app/Views/' . $singleClassName . '/' . $this->action . '.phtml';
    }
    
    /**
     * Verifica se o usuário tem acesso ao controler ou action atual
     * @return boolean
     */
    public function hasPermission() {
        return true;
    }

    /**
     * Popula o objeto da entidade com os dados da View
     * @param Model $entity
     * @return Model $entity
     */
    protected function bind($entity) {
        $attr = new \ReflectionClass($this->getModelName());
        $props = $attr->getProperties();

        $columnId = null;
        foreach ($props as $prop) {
            if (strpos($prop->getDocComment(), "@ORM\Id")) {
                $columnId = $prop->name;
            }
        }

        $request = $this->getRequest();
        $nameAttr = array_keys($attr->getDefaultProperties());
        foreach ($nameAttr as $name) {
            if ($name != $columnId) {
                $method = "set" . ucfirst($name);
                if (isset($request->getPost()[$name])) {
                    $entity->$method($request->getPost($name));
                }
            }
        }

        return $entity;
    }

    /**
     * Valida os dados da View
     * @param Model $entity
     * @return boolean
     */
    protected function isValid($entity) {
        if ($this->getFieldsRequired()) {
            foreach ($this->getFieldsRequired() as $key => $value) {
                $method = "get" . ucfirst($key);

                if ($entity->$method() == "" || $entity->$method() == null) {
                    $this->setFieldsInvalid($value);
                }
            }
        }

        if ($this->getFieldsInvalid()) {
            return false;
        }

        return true;
    }

    /**
     * Monta a consulta para exibição no indexAction
     * @param int $itemCountPerPage: número de registros por páginas, default 10
     * @return Object $paginator
     */
    public function paginate($itemCountPerPage) {
        if ($this->getRouter()->getCurrentController() != "index") {
            $this->itemCountPerPage = $itemCountPerPage;

            $id = 1;
            if ($this->getRouter()->getIdFromRoute()) {
                $id = $this->getRouter()->getIdFromRoute();
            }

            $qb = $this->getBusinessService()
                    ->getEntityManager()
                    ->createQueryBuilder()
                    ->setFirstResult($itemCountPerPage * ($id - 1))
                    ->setMaxResults($itemCountPerPage);

            $qb->addSelect('m')->from($this->getModelName(), 'm');

            $this->applyFormFilter($qb);
            $this->addCustomQuery($qb);

            $paginator = new Paginator($qb);

            return $paginator;
        }
    }

    /**
     * Modifica o objeto query builder, aplicando os filtros provinientes do
     * formulário.
     * 
     * @param \Doctrine\ORM\QueryBuilder $qb
     */
    public function applyFormFilter($qb) {
        if (is_subclass_of($this->getModelName(), 'Safira\Mvc\Model\AbstractModel')) {
            $qb->andWhere('m.deleted = 0');
        }
    }
    
    /**
     * Hook para adição de filtros (where) no QueryBuilder
     * @param \Doctrine\ORM\QueryBuilder $qb
     */
    public function addCustomQuery($qb) {
        
    }

    /**
     * Monta os links da paginação que será exibida no indexAction
     * @param int $countEntity (número de registros que será exibido)
     * @return string
     */
    public function linksPagination($countEntity) {
        $item = ceil($countEntity / $this->itemCountPerPage);
        if($item > 1) {
           $page = $this->getRouter()->getIdFromRoute();
            if(!$page) {
                $page = 1;
            }
            $pagePrevious = $page - 1;
            $pageNext = $page + 1;

            $nav = "";
            $nav .= "<nav class='paginacao'>";
            $nav .= "<ul class='pagination'>";
            if($page == 1) {
                $nav .= "<li class='disabled'><a aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>";
            } else {
                $nav .= "<li><a href='/{$this->getRouter()->getCurrentController()}/{$this->getRouter()->getCurrentAction()}/{$pagePrevious}' aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>";
            }
            for ($i = 1; $i <= $item; $i++) {
                if($page == $i) {
                    $nav .= "<li class='active'><a href='/{$this->getRouter()->getCurrentController()}/{$this->getRouter()->getCurrentAction()}/{$i}'>{$i} <span class='sr-only'>(current)</span></a></li>";
                } else {
                    $nav .= "<li><a href='/{$this->getRouter()->getCurrentController()}/{$this->getRouter()->getCurrentAction()}/{$i}'>{$i} <span class='sr-only'>(current)</span></a></li>";
                }
            }
            if($page == $item) {
                $nav .= "<li class='disabled'><a aria-label='Next'><span aria-hidden='true'>»</span></a></li>";
            } else {
                $nav .= "<li><a href='/{$this->getRouter()->getCurrentController()}/{$this->getRouter()->getCurrentAction()}/{$pageNext}' aria-label='Next'><span aria-hidden='true'>»</span></a></li>";
            }
            $nav .= "</ul>";
            $nav .= "</nav>";

            return $nav;
        } else {
            return ""; 
        }
        
    }

    /**
     * @return string Representa no nome da classe Model, daquele controller
     */
    protected function getModelInstance() {
        $modelName = $this->getModelName();
        return new $modelName;
    }

    /**
     * Define as variáveis que serão usadas na View
     *
     * @param  string $name
     * @param  mixed $value
     */
    protected function setVariable($name, $value) {
        $this->variables[(string) $name] = $value;
    }

    /**
     * Retorna o objeto da entidade Router
     * @return \Safira\Routing\Router
     */
    protected function getRouter() {
        return $this->router;
    }

    /**
     * Retorna o objeto da entidade SessionManager
     * @return \Safira\Session\SessionManager
     */
    protected function getSessionManager() {
        return $this->sessionManager;
    }

    /**
     * Retorna o objeto da entidade AuthManager
     * @return \Safira\Auth\AuthManager
     */
    protected function getAuth() {
        return $this->auth;
    }

    /**
     * 
     * @return \Safira\Http\HttpRequest
     */
    public function getRequest() {
        if (!$this->request) {
            $this->request = new HttpRequest();
        }

        return $this->request;
    }

    /**
     * 
     * @return \Safira\Mvc\Service\AbstractService
     */
    protected function getBusinessService() {
        if ($this->businessService == null) {
            $serviceName = "\app\Services\\" . ucfirst($this->getRouter()->getCurrentController()) . "Service";
            $this->businessService = new $serviceName;
        }

        return $this->businessService;
    }

    /**
     * Retorna o objeto do Service do controller passado por parâmetro
     * @param string $serviceName
     * @return Object Service
     */
    protected function getServiceLocator($serviceName) {
        $serviceName = "\app\Services\\" . ucfirst($serviceName) . "Service";
        $this->serviceLocator = new $serviceName;

        return $this->serviceLocator;
    }

    /**
     * Hook para realização de verificaçãoes de regras de negócio ANTES de
     * INSERIR o novo objeto
     * @param Model $entity
     */
    protected function prePersist($entity) {
        
    }

    /**
     * Hook para realização de verificaçãoes de regras de negócio DEPOIS de
     * INSERIR o novo objeto
     * @param Model $entity
     */
    protected function posPersist($entity) {
        
    }

    /**
     * Hook para realização de verificaçãoes de regras de negócio ANTES de
     * ATUALIZAR o objeto
     * @param Model $entity
     */
    protected function preUpdate($entity) {
        
    }

    /**
     * Hook para realização de verificaçãoes de regras de negócio DEPOIS de
     * ATUALIZAR o objeto
     * @param Model $entity
     */
    protected function posUpdate($entity) {
        
    }

    /**
     * Hook para realização de verificaçãoes de regras de negócio ANTES de
     * EXCLUIR o objeto
     * @param Model $entity
     */
    protected function preDelete($entity) {
        
    }

    /**
     * Hook para realização de verificaçãoes de regras de negócio DEPOIS de
     * EXCLUIR o objeto
     * @param Model $entity
     */
    protected function posDelete($entity) {
        
    }

    /**
     * Este método retorna os javascripts que deverão estar nas páginas das ações
     * padrão ('index', 'add', 'edit' e 'delete') 
     * @return array
     */
    protected function getScriptsArray() {
        return array();
    }

    /**
     * Este método retorna as folhas de estilo (CSS) que deverão estar nas 
     * páginas das ações padrão ('index', 'add', 'edit' e 'delete') 
     * @return array
     */
    protected function getStylesArray() {
        return array();
    }

    /**
     * Retorna o template que será usuado
     * @return string
     */
    function getTemplate() {
        return $this->template;
    }

    /**
     * Define o template que será usuado
     * @param string $template
     */
    protected function setTemplate($template) {
        $this->template = $template;
    }

    /**
     * Verifica se há um template definido para a ação
     * @return boolean
     */
    protected function hasTemplate() {
        if (!is_null($this->getTemplate()) && file_exists("../app/Views/Templates/{$this->getTemplate()}.phtml")) {
            return true;
        }

        return false;
    }

    /**
     * Retorna quais campos são obrigatórios no formulário
     * @return array
     */
    protected function getFieldsRequired() {
        return $this->fieldsRequired;
    }

    /**
     * Define quais campos são obrigatórios no formulário
     * @param array $fieldsRequired
     */
    protected function setFieldsRequired($fieldsRequired) {
        $this->fieldsRequired = $fieldsRequired;
    }

    /**
     * Retorna quais campos estão inválidos no formulário
     * @return array
     */
    protected function getFieldsInvalid() {
        return $this->fieldsInvalid;
    }

    /**
     * Define quais campos estão inválidos no formulário
     * @return array
     */
    private function setFieldsInvalid($fieldsInvalid) {
        $this->fieldsInvalid[] = $fieldsInvalid;
    }

    /**
     * Retorna o título da página a ser exibida
     * @return string
     */
    function getTitlePage() {
        return $this->titlePage;
    }

    /**
     * Define o título da página a ser exibida
     * @param string $titlePage
     */
    function setTitlePage($titlePage) {
        $this->titlePage = $titlePage;
    }

}
