<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Outcome;
use AppBundle\Entity\Product;
use FOS\RestBundle\Controller\Annotations\Get;
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
     * @Get("/products/outcomes/{outcome}")
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
     * @RequestParam(name="warehouseKeeper", description="Person who gives the Product")
     * @RequestParam(name="cook", description="Person who gets the Product")
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
        $warehouseKeeper = $params->get('warehouseKeeper');
        $cook = $params->get('cook');

        $outcome = new Outcome($product, $quantity, $warehouseKeeper, $cook, $outcomeAt);

        $em = $this->getDoctrine()->getManager();
        $em->persist($outcome);
        $em->flush();

        return $outcome;
    }
}