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
     * @param Supplier $supplier
     * @return \Symfony\Component\HttpFoundation\Response
     * @View
     */
    public function getSupplierAction(Supplier $supplier)
    {
        return $supplier;
    }

    /**
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