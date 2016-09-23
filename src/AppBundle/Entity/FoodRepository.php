<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FoodRepository
 */
class FoodRepository extends EntityRepository
{
    /**
     * @param string $like
     * @return array
     */
    public function findByNameLike($like)
    {
        $dql = 'SELECT f FROM AppBundle\Entity\Food f WHERE f.name.name LIKE :like';

        return $this->_em->createQuery($dql)->setParameter('like', '%'.$like.'%')->getResult();
    }
}
