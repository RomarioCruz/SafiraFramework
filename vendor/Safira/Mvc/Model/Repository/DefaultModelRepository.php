<?php
namespace Sec\Mvc\Model\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of DefaultModelRepository
 */
class DefaultModelRepository extends EntityRepository {
    
    /**
     * 
     * @param array $orderBy
     * @return type
     */
    public function actives(array $orderBy = array()) {
        $qb = $this->createQueryBuilder('m')
                ->where('m.deleted = 0');
        
        foreach ($orderBy as $fieldName => $order) {
            $qb->addOrderBy("m.{$fieldName}", $order);
        }
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * 
     * @param array $orderBy
     * @return type
     */
    public function inactives(array $orderBy = array()) {
        $qb = $this->createQueryBuilder('m')
                ->where('m.deleted = 1');
        
        foreach ($orderBy as $fieldName => $order) {
            $qb->addOrderBy("m.{$fieldName}", $order);
        }
        
        return $qb->getQuery()->getResult();
    }
    
    public function getEmpty() {
        return array();
    }
}