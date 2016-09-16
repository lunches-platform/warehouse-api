<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Outcome;
use AppBundle\Entity\Product;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\NoRoute;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * Class OutcomesController.
 */
class OutcomesController extends FOSRestController
{
    /**
     * @param Outcome $outcome
     * @return \Symfony\Component\HttpFoundation\Response
     * @NoRoute
     * @Get("/products/{productId}/outcomes/{outcome}")
     * @View
     */
    public function getOutcomeAction(Outcome $outcome)
    {
        return $outcome;
    }

    /**
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @View
     */
    public function getOutcomesAction(Product $product)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Outcome');

        return $repo->findBy([
            'product' => $product,
        ]);
    }

    /**
     * @param Product $product
     * @param ParamFetcher $params
     *
     * @RequestParam(name="outcomeAt", strict=false, description="Date when outcome was committed. Can be omitted")
     * @RequestParam(name="quantity", description="Quantity of product. Float accepted")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Money\InvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Money\UnknownCurrencyException
     * 
     * @View(statusCode=201)
     */
    public function postOutcomesAction(Product $product, ParamFetcher $params)
    {
        $quantity = $params->get('quantity');
        $outcomeAt = new \DateTimeImmutable($params->get('outcomeAt'));

        $outcome = new Outcome($product, $quantity, $outcomeAt);

        $em = $this->getDoctrine()->getManager();
        $em->persist($outcome);
        $em->flush();

        return $outcome;
    }
}