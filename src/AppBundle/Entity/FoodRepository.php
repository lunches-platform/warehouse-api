<?php

namespace AppBundle\Entity;

use AppBundle\Exception\EntityNotFoundException;
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
        $dql = 'SELECT f FROM AppBundle\Entity\Food f JOIN f.aliases a WHERE a.name.name LIKE :like';

        return $this->_em->createQuery($dql)->setParameter('like', '%'.$like.'%')->getResult();
    }

    /**
     * @param string $id
     * @return Food
     * @throws \AppBundle\Exception\EntityNotFoundException
     */
    public function get($id)
    {
        /** @var Food $food */
        $food = $this->find($id);
        if (!$food) {
            throw new EntityNotFoundException('Food not found');
        }

        return $food;
    }
    /**
     * @param Food $food
     * @return bool
     */
    public function exists(Food $food)
    {
        return (bool) $this->findByName($food->name());
    }

    /**
     * @param string $name
     * @return Food
     */
    public function findByName($name)
    {
        return $this->findOneBy(['name.name' => $name]);
    }
}
