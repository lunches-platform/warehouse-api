<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Uuid;
use Swagger\Annotations AS SWG;

/**
 * Class ProductsController.
 */
class ProductsController
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
     *     path="/products/{productId}",
     *     description="Get Product by ID",
     *     operationId="getProductAction",
     *     @SWG\Parameter(
     *         name="productId", format="uuid", type="string", required=true, in="path", description="ID of product",
     *     ),
     *     @SWG\Response(response=200, description="Product", @SWG\Schema(ref="#/definitions/Product")),
     * )
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     * @View
     */
    public function getProductAction(Product $product)
    {
        return $product;
    }

    /**
     * @SWG\Get(
     *     path="/products",
     *     description="Return products by filter conditions",
     *     operationId="getProductsAction",
     *     @SWG\Parameter(
     *         name="like",
     *         in="query",
     *         description="Pattern of product name",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Response(response=200, description="List of Products", @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Product"))),
     * )
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * @QueryParam(name="like", description="Filter products by LIKE pattern")
     * @View
     */
    public function getProductsAction(ParamFetcher $params)
    {
        $repo = $this->doctrine->getRepository('AppBundle:Product');
        if ($like = $params->get('like')) {
            return $repo->findByNameLike($like);
        } else {
            return $repo->findAll();
        }
    }

    /**
     * @SWG\Post(
     *     path="/products",
     *     operationId="postProductsAction",
     *     description="Creates new product",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         required=true,
     *         description="Product name",
     *         @SWG\Schema(ref="#/definitions/Product"),
     *     ),
     *     @SWG\Parameter(
     *         name="foodId",
     *         required=true,
     *         in="body", format="uuid", type="string",
     *         description="ID of Food",
     *         @SWG\Schema(ref="#/definitions/Product"),
     *     ),
     *     @SWG\Parameter(
     *         name="brandId",
     *         required=true,
     *         in="body", format="uuid", type="string",
     *         description="ID of Brand",
     *         @SWG\Schema(ref="#/definitions/Product"),
     *     ),
     *     @SWG\Parameter(
     *         name="pcs",
     *         required=true,
     *         in="body", type="boolean",
     *         description="Either Product distributes in pcs or no",
     *         enum={"0","1"},
     *         @SWG\Schema(ref="#/definitions/Product"),
     *     ),
     *     @SWG\Parameter(
     *         name="wight",
     *         in="body", type="integer",
     *         description="Product weight in corresponding unit",
     *         @SWG\Schema(ref="#/definitions/Product"),
     *     ),
     *     @SWG\Response(response=201, description="Newly created Product", @SWG\Schema(ref="#/definitions/Product") ),
     * )
     * @RequestParam(name="foodId", requirements=@Uuid, description="Food of Product")
     * @RequestParam(name="name", description="Product name")
     * @RequestParam(name="brandId", requirements=@Uuid, description="Product brand")
     * @RequestParam(name="pcs", requirements="(0|1)", description="Either Product distributes in pcs or no")
     * @RequestParam(name="weight", requirements="\d+",  strict=false, description="Product weight in corresponding unit")
     * 
     * @param ParamFetcher $params
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \LogicException
     *
     * @View(statusCode=201)
     */
    public function postProductsAction(ParamFetcher $params)
    {
        $food = $this->doctrine->getRepository('AppBundle:Food')->find($params->get('foodId'));
        if (!$food) {
            throw new NotFoundHttpException('Food not found');
        }
        $brand = $this->doctrine->getRepository('AppBundle:Brand')->find($params->get('brandId'));
        if (!$brand) {
            throw new NotFoundHttpException('Brand not found');
        }
        $product = new Product(
            $food,
            $params->get('name'),
            $brand,
            $params->get('pcs'),
            $params->get('weight')
        );

        $em = $this->doctrine->getManager();
        $em->persist($product);
        $em->flush();

        return $product;
    }
}