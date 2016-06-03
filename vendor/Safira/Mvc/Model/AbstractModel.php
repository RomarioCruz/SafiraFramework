<?php

namespace Safira\Mvc\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of AbstractModel
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class AbstractModel {

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MODIFICADO_EM", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var smallint
     *
     * @ORM\Column(name="EXCLUIDO", type="smallint", nullable=false, options={"default" = 0})
     */
    private $deleted = 0;

    public function __get($name) {
        $method = 'get' . ucfirst($name);
        if (!method_exists($this, $method)) {
            $className = get_class($this);
            throw new \Exception("O método {$className}::{$method}() não existe na classe {$className}");
        }
        return $this->{$method}();
    }

    public function __set($name, $value) {
        $method = 'set' . ucfirst($name);
        if (!method_exists($this, $method)) {
            $className = get_class($this);
            throw new \Exception("O método {$className}::{$method}() não existe na classe {$className}");
        }
        return $this->{$method}($value);
    }

    final public function getDeleted() {
        return $this->deleted;
    }

    final public function setDeleted($deleted) {
        $this->deleted = $deleted;
    }

    final public function getModified() {
        return $this->modified;
    }

    /**
     * @param \DateTime $modified
     */
    final public function setModified($modified = null) {
        $this->modified = new \DateTime();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    final public function registerModified() {
        $this->setModified();
    }

}
