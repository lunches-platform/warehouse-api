<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Brand;
use AppBundle\ValueObject\EntityName;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Swagger\Annotations AS SWG;

/**
 * Class BrandsController.
 */
class BrandsController
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
     *     path="/brands/{brandId}",
     *     description="Get Brand by ID",
     *     operationId="getBrandAction",
     *     @SWG\Parameter(
     *         description="ID of Brand",
     *         format="uuid",
     *         type="string",
     *         in="path",
     *         name="brandId",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Brand",
     *         @SWG\Schema(ref="#/definitions/Brand")
     *     ),
     * )
     * @param Brand $brand
     * @return \Symfony\Component\HttpFoundation\Response
     * @View
     */
    public function getBrandAction(Brand $brand)
    {
        return $brand;
    }

    /**
     * @SWG\Get(
     *     path="/brands",
     *     description="Get all brands registered",
     *     operationId="getBrandsAction",
     *     @SWG\Response(response=200, description="List of Brands", @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Brand"))),
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @View
     */
    public function getBrandsAction()
    {
        $repo = $this->doctrine->getRepository('AppBundle:Brand');

        return $repo->findAll();
    }

    /**
     * @SWG\Post(
     *     path="/brands",
     *     operationId="postBrandsAction",
     *     description="Creates new brand",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="Brand name",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Brand"),
     *     ),
     *     @SWG\Parameter(name="description", in="body", @SWG\Schema(ref="#/definitions/Brand")),
     *     @SWG\Response(response=201, description="Newly created Brand", @SWG\Schema(ref="#/definitions/Brand") ),
     * )
     * @RequestParam(name="name")
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @View(statusCode=201);
     */
    public function postBrandsAction(ParamFetcher $params)
    {
        $name = $params->get('name');
        if ($this->doctrine->getRepository('AppBundle:Brand')->findOneBy(['name.name' => $name])) {
            throw new HttpException(400, 'Brand with specified name is exist already');
        }
        $brand = new Brand(new EntityName($name));

        $em = $this->doctrine->getManager();
        $em->persist($brand);
        $em->flush();

        return $brand;
    }
}