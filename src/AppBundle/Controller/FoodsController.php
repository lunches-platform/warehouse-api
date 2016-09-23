<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Food;
use AppBundle\ValueObject\EntityName;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Uuid;
use Swagger\Annotations AS SWG;

/**
 * Class FoodsController.
 */
class FoodsController
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * FoodsController constructor.
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    /**
     * @SWG\Get(
     *     path="/foods/{foodId}",
     *     description="Get Food by ID",
     *     operationId="getFoodAction",
     *     @SWG\Parameter(
     *         name="foodId", format="uuid", type="string", required=true, in="path", description="ID of food",
     *     ),
     *     @SWG\Response(response=200, description="Food", @SWG\Schema(ref="#/definitions/Food")),
     * )
     * @param Food $food
     * @return \Symfony\Component\HttpFoundation\Response
     * @View
     */
    public function getFoodAction(Food $food)
    {
        return $food;
    }

    /**
     * @SWG\Get(
     *     path="/foods",
     *     description="Return all foods registered",
     *     operationId="getFoodsAction",
     *     @SWG\Response(response=200, description="List of Foods", @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Food"))),
     * )
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @QueryParam(name="like", description="Filter foods by LIKE pattern")
     * @View
     */
    public function getFoodsAction(ParamFetcher $params)
    {
        $repo = $this->doctrine->getRepository('AppBundle:Food');

        if ($like = $params->get('like')) {
            return $repo->findByNameLike($like);
        } else {
            return $repo->findAll();
        }
    }

    /**
     * @SWG\Post(
     *     path="/foods",
     *     operationId="postFoodsAction",
     *     description="Creates new food",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="Food name",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Food"),
     *     ),
     *     @SWG\Parameter(
     *         name="categoryId",
     *         in="body",
     *         format="uuid",
     *         type="string",
     *         description="ID of category",
     *         @SWG\Schema(ref="#/definitions/Food"),
     *     ),
     *     @SWG\Response(response=201, description="Newly created Food", @SWG\Schema(ref="#/definitions/Food") ),
     * )
     * @RequestParam(name="name", description="Food name")
     * @RequestParam(name="categoryId", strict=false, requirements=@Uuid, description="Category to assign")
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @View(statusCode=201);
     */
    public function postFoodsAction(ParamFetcher $params)
    {
        $name = $params->get('name');
        if ($this->doctrine->getRepository('AppBundle:Food')->findOneBy(['name.name' => $name])) {
            throw new HttpException(400, 'Food with specified name is exist already');
        }

        $food = new Food(new EntityName($name));
        if ($categoryId = $params->get('categoryId')) {
            $category = $this->doctrine->getRepository('AppBundle:Category')->find($categoryId);
            if (!$category) {
                throw new NotFoundHttpException('Category not found');
            }
            $food->assignCategory($category);
        }

        $em = $this->doctrine->getManager();
        $em->persist($food);
        $em->flush();

        return $food;
    }
}