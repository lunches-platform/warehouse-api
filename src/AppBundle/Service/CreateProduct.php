<?php


namespace AppBundle\Service;


use AppBundle\Entity\BrandRepository;
use AppBundle\Entity\FoodRepository;
use AppBundle\Entity\Product;
use AppBundle\ValueObject\EntityName;
use Ramsey\Uuid\Uuid;

/**
 * Class CreateProduct.
 */
class CreateProduct
{
    /**
     * @var FoodRepository
     */
    protected $foodRepo;
    /**
     * @var BrandRepository
     */
    protected $brandRepo;

    /**
     * CreateProduct constructor.
     * @param FoodRepository $foods
     * @param BrandRepository $brands
     */
    public function __construct(FoodRepository $foods, BrandRepository $brands)
    {
        $this->foodRepo = $foods;
        $this->brandRepo = $brands;
    }

    /**
     * @param Uuid $foodId
     * @param string $name
     * @param Uuid $brandId
     * @param bool $pcs
     * @param int $weight
     *
     * @return Product
     * @throws \AppBundle\Exception\EntityNotFoundException
     * @throws \InvalidArgumentException
     */
    public function execute($foodId, $name, $brandId, $pcs, $weight)
    {
        $product = new Product(
            $this->foodRepo->get($foodId),
            new EntityName($name),
            $this->brandRepo->get($brandId),
            $pcs,
            $weight
        );

        return $product;
    }
}