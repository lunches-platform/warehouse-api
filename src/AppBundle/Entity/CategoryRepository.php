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
        return (bool) $this->findByNameAndType(
            $category->name(),
            $category->type()
        );
    }

    /**
     * @param string $name
     * @param string $type
     * @return Category
     */
    public function findByNameAndType($name, $type)
    {
        /** @var Category $category */
        $category = $this->findOneBy([
            'name.name' => $name,
            'type' => $type,
        ]);

        return $category;
    }
}
