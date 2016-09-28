<?php


namespace AppBundle\Service;


use AppBundle\Entity\BrandRepository;
use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryRepository;
use AppBundle\Entity\FoodRepository;
use AppBundle\Entity\Product;
use AppBundle\Exception\DuplicateEntityException;
use AppBundle\ValueObject\EntityName;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * Class CreateCategory.
 */
class CreateCategory
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
     * @param CategoryRepository $categoryRepo
     */
    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $unit
     * @param string $description
     * @return Category
     * @throws \AppBundle\Exception\DuplicateEntityException
     */
    public function execute($name, $type, $unit, $description)
    {
        $category = new Category(new EntityName($name), $type, $unit, $description);

        if ($this->categoryRepo->exists($category)) {
            throw new DuplicateEntityException('Such category is exist already');
        }

        return $category;
    }
}