<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 */
class CategoryRepository extends EntityRepository
{
    /**
     * @param Category $category
     * @return bool
     */
    public function exists(Category $category)
    {
        return (bool) $this->findOneBy([
            'name.name' => $category->name(),
            'type' => $category->type(),
        ]);
    }
}
