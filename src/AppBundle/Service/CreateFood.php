<?php


namespace AppBundle\Service;

use AppBundle\Entity\CategoryRepository;
use AppBundle\Entity\Food;
use AppBundle\Entity\FoodRepository;
use AppBundle\Exception\DuplicateEntityException;
use AppBundle\ValueObject\EntityName;

/**
 * Class CreateFood.
 */
class CreateFood
{
    /**
     * @var FoodRepository
     */
    protected $foodRepo;
    /**
     * @var CategoryRepository
     */
    protected $categoryRepo;

    /**
     * CreateFood constructor.
     * @param FoodRepository $foodRepo
     * @param CategoryRepository $categoryRepo
     */
    public function __construct(FoodRepository $foodRepo, CategoryRepository $categoryRepo)
    {
        $this->foodRepo = $foodRepo;
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * @param string $name
     * @param string|null $categoryId
     * @return Food
     * @throws \AppBundle\Exception\EntityNotFoundException
     * @throws \AppBundle\Exception\DuplicateEntityException
     */
    public function execute($name, $categoryId = null)
    {
        $food = new Food(new EntityName($name));

        if ($this->foodRepo->exists($food)) {
            throw new DuplicateEntityException('Food with specified name is exist already');
        }

        if ($categoryId) {
            $category = $this->categoryRepo->get($categoryId);
            $food->assignCategory($category);
        }

        return $food;
    }
}