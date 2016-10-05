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
     * @param array|string $like
     * @return array
     */
    public function findByNameLike($like)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('f');
        $qb->join('f.aliases', 'a');

        $like = (array) $like;

        $i = 0;
        foreach ($like as $l) {
            $qb->orWhere('a.name.name LIKE ?'.$i);
            $qb->setParameter($i++, '%'.$l.'%');
        }

        return $qb->getQuery()->getResult();
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
