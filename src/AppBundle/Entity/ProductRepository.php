<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository
 */
class ProductRepository extends EntityRepository
{
    /**
     * @param string $like
     * @return array
     */
    public function findByNameLike($like)
    {
        $dql = 'SELECT p FROM AppBundle\Entity\Product p WHERE p.name LIKE :like';

        return $this->_em->createQuery($dql)->setParameter('like', '%'.$like.'%')->getResult();
    }
}
