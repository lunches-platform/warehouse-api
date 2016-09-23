<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Category;
use AppBundle\ValueObject\EntityName;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Swagger\Annotations AS SWG;

/**
 * Class CategoriesController.
 */
class CategoriesController
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
     *     path="/categories/{categoryId}",
     *     description="Get Category by ID",
     *     operationId="getCategoryAction",
     *     @SWG\Parameter(
     *         description="ID of category",
     *         format="uuid",
     *         type="string",
     *         in="path",
     *         name="categoryId",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Category",
     *         @SWG\Schema(ref="#/definitions/Category")
     *     ),
     * )
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\Response
     * @View
     */
    public function getCategoryAction(Category $category)
    {
        return $category;
    }

    /**
     * @SWG\Get(
     *     path="/categories",
     *     description="Return all categories registered",
     *     operationId="getCategoriesAction",
     *     @SWG\Response(response=200, description="List of Categories", @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Category"))),
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @View
     */
    public function getCategoriesAction()
    {
        $repo = $this->doctrine->getRepository('AppBundle:Category');

        return $repo->findAll();
    }

    /**
     * @SWG\Post(
     *     path="/categories",
     *     operationId="postCategoriesAction",
     *     description="Creates new category",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="Category name",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Category"),
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="body",
     *         description="Category type. It can be treated as 'category of category' or just section",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Category"),
     *     ),
     *     @SWG\Parameter(
     *         name="unit",
     *         in="body",
     *         description="Smallest Unit of the Product.",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Category"),
     *     ),
     *     @SWG\Parameter(name="description", in="body", @SWG\Schema(ref="#/definitions/Category")),
     *     @SWG\Response(response=201, description="Newly created Category", @SWG\Schema(ref="#/definitions/Category") ),
     * )
     *
     * @RequestParam(name="name")
     * @RequestParam(name="type")
     * @RequestParam(name="unit", requirements="(ml|gr)")
     * @RequestParam(name="description", strict=false)
     * 
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * 
     * @View(statusCode=201);
     */
    public function postCategoriesAction(ParamFetcher $params)
    {
        if ($this->doctrine->getRepository('AppBundle:Category')->findOneBy([
            'name.name' => $name = $params->get('name'),
            'type' => $type = $params->get('type'),
        ])) {
            throw new HttpException(400, 'Such category is exist already');
        }
        $category = new Category(new EntityName($name), $type, $params->get('unit'), $params->get('description'));

        $em = $this->doctrine->getManager();
        $em->persist($category);
        $em->flush();

        return $category;
    }
}