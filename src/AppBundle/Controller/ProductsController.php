<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Product;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Validator\Constraints\Uuid;

/**
 * Class ProductsController.
 */
class ProductsController extends FOSRestController
{
    /**
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     * @View
     */
    public function getProductAction(Product $product)
    {
        return $product;
    }

    /**
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * @QueryParam(name="like", description="Filter products by LIKE pattern")
     * @View
     */
    public function getProductsAction(ParamFetcher $params)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Product');
        if ($like = $params->get('like')) {
            return $repo->findByNameLike($like);
        } else {
            return $repo->findAll();
        }
    }

    /**
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
        $food = $this->getDoctrine()->getRepository('AppBundle:Food')->find($params->get('foodId'));
        if (!$food) {
            throw $this->createNotFoundException('Food not found');
        }
        $brand = $this->getDoctrine()->getRepository('AppBundle:Brand')->find($params->get('brandId'));
        if (!$brand) {
            throw $this->createNotFoundException('Brand not found');
        }
        $product = new Product(
            $food,
            $params->get('name'),
            $brand,
            $params->get('pcs'),
            $params->get('weight')
        );

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $product;
    }
}