<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Supplier;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class SuppliersController.
 */
class SuppliersController extends FOSRestController
{
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
        $repo = $this->getDoctrine()->getRepository('AppBundle:Supplier');

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
        if ($this->getDoctrine()->getRepository('AppBundle:Supplier')->findOneBy(['name' => $name])) {
            throw new HttpException(400, 'Supplier with specified name is exist already');
        }
        $supplier = new Supplier($name);

        $em = $this->getDoctrine()->getManager();
        $em->persist($supplier);
        $em->flush();

        return $supplier;
    }
}