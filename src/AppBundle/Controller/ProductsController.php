<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Product;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * Class ProductsController.
 */
class ProductsController extends FOSRestController
{
    /**
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getProductAction(Product $product)
    {
        return $this->handleView(
            $this->view($product, 200)
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function getProductsAction()
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Product');
        return $this->handleView(
            $this->view($repo->findAll())
        );
    }

    /**
     * @RequestParam(name="foodId", description="Food identifier")
     * @RequestParam(name="name", description="Food identifier")
     * @RequestParam(name="brand", description="Food identifier")
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
     */
    public function postProductsAction(ParamFetcher $params)
    {
        $foodId = $params->get('foodId');
        $food = $this->getDoctrine()->getRepository('AppBundle:Food')->find($foodId);
        if (!$food) {
            throw $this->createNotFoundException('Food not found');
        }
        $product = new Product(
            $food,
            $params->get('name'),
            $params->get('brand'),
            $params->get('pcs'),
            $params->get('weight')
        );

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->handleView($this->view($product, 201));
    }
}