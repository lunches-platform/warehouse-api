<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Supplier;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class SuppliersController.
 */
class SuppliersController
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
     *     path="/suppliers/{supplierId}",
     *     description="Get Supplier by ID",
     *     operationId="getSupplierAction",
     *     @SWG\Parameter(
     *         format="uuid", type="string", in="path", name="supplierId", required=true, description="ID of Supplier",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Supplier",
     *         @SWG\Schema(ref="#/definitions/Supplier")
     *     ),
     * )
     * @param Supplier $supplier
     * @return \Symfony\Component\HttpFoundation\Response
     * @View
     */
    public function getSupplierAction(Supplier $supplier)
    {
        return $supplier;
    }

    /**
     * @SWG\Get(
     *     path="/suppliers",
     *     description="Get all suppliers registered",
     *     operationId="getSuppliersAction",
     *     @SWG\Response(response=200, description="List of Suppliers", @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Supplier"))),
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @View
     */
    public function getSuppliersAction()
    {
        $repo = $this->doctrine->getRepository('AppBundle:Supplier');

        return $repo->findAll();
    }

    /**
     * @SWG\Post(
     *     path="/suppliers",
     *     operationId="postSuppliersAction",
     *     description="Creates new supplier",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="Supplier name",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Supplier"),
     *     ),
     *     @SWG\Parameter(name="description", in="body", @SWG\Schema(ref="#/definitions/Supplier")),
     *     @SWG\Response(response=201, description="Newly created Supplier", @SWG\Schema(ref="#/definitions/Supplier") ),
     * )
     * @RequestParam(name="name")
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @View(statusCode=201);
     */
    public function postSuppliersAction(ParamFetcher $params)
    {
        $name = $params->get('name');
        if ($this->doctrine->getRepository('AppBundle:Supplier')->findOneBy(['name' => $name])) {
            throw new HttpException(400, 'Supplier with specified name is exist already');
        }
        $supplier = new Supplier($name);

        $em = $this->doctrine->getManager();
        $em->persist($supplier);
        $em->flush();

        return $supplier;
    }
}