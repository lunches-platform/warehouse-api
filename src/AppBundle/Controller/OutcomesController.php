<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Outcome;
use AppBundle\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * Class OutcomesController.
 */
class OutcomesController
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
     *     path="/products/outcomes/{outcomeId}",
     *     description="Get Outcome by ID",
     *     operationId="getOutcomeAction",
     *     @SWG\Parameter(
     *         name="outcomeId", format="uuid", type="string", required=true, in="path", description="ID of Outcome",
     *     ),
     *     @SWG\Response(response=200, description="Outcome", @SWG\Schema(ref="#/definitions/Outcome")),
     * )
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
     * @SWG\Get(
     *     path="/products/{productId}/outcomes",
     *     description="Get outcomes by product",
     *     operationId="getOutcomesAction",
     *     @SWG\Parameter(
     *         name="productId", format="uuid", type="string", required=true, in="path", description="ID of Product",
     *     ),
     *     @SWG\Response(response=200, description="List of Outcomes", @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Outcome"))),
     * )
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @View
     */
    public function getOutcomesAction(Product $product)
    {
        $repo = $this->doctrine->getRepository('AppBundle:Outcome');

        return $repo->findBy([
            'product' => $product,
        ]);
    }

    /**
     * @SWG\Post(
     *     path="/products/{productId}/outcomes",
     *     operationId="postOutcomesAction",
     *     description="Creates new outcome",
     *     @SWG\Parameter(
     *         name="productId", format="uuid", type="string", required=true, in="path", description="ID of Product",
     *     ),
     *     @SWG\Parameter(
     *         name="quantity",
     *         type="float",
     *         required=true,
     *         in="body",
     *         description="Quantity of outcome Product",
     *         @SWG\Schema(ref="#/definitions/Outcome"),
     *     ),
     *     @SWG\Parameter(
     *         name="outcomeAt",
     *         type="dateTime",
     *         required=true,
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/Outcome"),
     *     ),
     *     @SWG\Parameter(
     *         name="warehouseKeeper",
     *         description="Person who gives the Product",
     *         required=true,
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/Outcome"),
     *     ),
     *     @SWG\Parameter(
     *         name="cook",
     *         description="Person who gets the Product",
     *         required=true,
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/Outcome"),
     *     ),
     *     @SWG\Response(response=201, description="Newly created Outcome", @SWG\Schema(ref="#/definitions/Outcome") ),
     * )
     *
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

        $em = $this->doctrine->getManager();
        $em->persist($outcome);
        $em->flush();

        return $outcome;
    }
}