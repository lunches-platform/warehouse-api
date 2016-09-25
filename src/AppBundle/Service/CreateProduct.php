<?php


namespace AppBundle\Service;


use AppBundle\Entity\Brand;
use AppBundle\Entity\BrandRepository;
use AppBundle\Entity\Food;
use AppBundle\Entity\FoodRepository;
use AppBundle\Entity\Product;
use AppBundle\ValueObject\EntityName;
use FOS\RestBundle\Request\ParamFetcher;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \InvalidArgumentException
     */
    public function execute(ParamFetcher $params)
    {
        $product = new Product(
            $this->getFood($params->get('foodId')),
            new EntityName($params->get('name')),
            $this->getBrand($params->get('brandId')),
            $params->get('pcs'),
            $params->get('weight')
        );

        return $product;
    }

    /**
     * @param Uuid $foodId
     * @return Food
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getFood($foodId)
    {
        $food = $this->foodRepo->find($foodId);
        if (!$food) {
            throw new NotFoundHttpException('Food not found');
        }

        return $food;
    }

    /**
     * @param Uuid $brandId
     * @return Brand
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getBrand($brandId)
    {
        $brand = $this->brandRepo->find($brandId);
        if (!$brand) {
            throw new NotFoundHttpException('Brand not found');
        }

        return $brand;
    }
}