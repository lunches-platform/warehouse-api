<?php


namespace AppBundle\Service;


use AppBundle\Entity\BrandRepository;
use AppBundle\Entity\FoodRepository;
use AppBundle\Entity\Product;
use AppBundle\ValueObject\EntityName;
use FOS\RestBundle\Request\ParamFetcher;

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
     * @param ParamFetcher $params
     * @return Product
     * @throws \AppBundle\Exception\EntityNotFoundException
     * @throws \InvalidArgumentException
     */
    public function execute(ParamFetcher $params)
    {
        $product = new Product(
            $this->foodRepo->get($params->get('foodId')),
            new EntityName($params->get('name')),
            $this->brandRepo->get($params->get('brandId')),
            $params->get('pcs'),
            $params->get('weight')
        );

        return $product;
    }
}