<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Brand;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class BrandsController.
 */
class BrandsController extends FOSRestController
{
    /**
     * @param Brand $brand
     * @return \Symfony\Component\HttpFoundation\Response
     * @View
     */
    public function getBrandAction(Brand $brand)
    {
        return $brand;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @View
     */
    public function getBrandsAction()
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Brand');

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
    public function postBrandsAction(ParamFetcher $params)
    {
        $name = $params->get('name');
        if ($this->getDoctrine()->getRepository('AppBundle:Brand')->findOneBy(['name' => $name])) {
            throw new HttpException(400, 'Brand with specified name is exist already');
        }
        $brand = new Brand($name);

        $em = $this->getDoctrine()->getManager();
        $em->persist($brand);
        $em->flush();

        return $brand;
    }
}