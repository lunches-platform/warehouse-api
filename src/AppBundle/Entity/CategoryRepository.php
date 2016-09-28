<?php

namespace AppBundle\Entity;

use AppBundle\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 */
class CategoryRepository extends EntityRepository
{
    /**
     * @param string $id
     * @return Category
     * @throws \AppBundle\Exception\EntityNotFoundException
     */
    public function get($id)
    {
        /** @var Category $category */
        $category = $this->find($id);
        if (!$category) {
            throw new EntityNotFoundException('Category not found');
        }

        return $category;
    }
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
