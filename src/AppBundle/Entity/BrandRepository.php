<?php

namespace AppBundle\Entity;

use AppBundle\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

/**
 * BrandRepository
 */
class BrandRepository extends EntityRepository
{
    /**
     * @param string $id
     * @return Brand
     * @throws \AppBundle\Exception\EntityNotFoundException
     */
    public function get($id)
    {
        /** @var Brand $brand */
        $brand = $this->find($id);
        if (!$brand) {
            throw new EntityNotFoundException('Brand not found');
        }

        return $brand;
    }
}
