<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Price;
use AppBundle\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Money\Currency;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations AS SWG;

/**
 * Class PricesController.
 */
class PricesController
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * PricesController constructor.
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    /**
     * @SWG\Post(
     *     path="/products/{productId}/prices",
     *     operationId="postPricesAction",
     *     description="Commits new Price",
     *     @SWG\Parameter(
     *         name="productId", format="uuid", type="string", required=true, in="path", description="ID of Product",
     *     ),
     *     @SWG\Parameter(
     *         name="timestamp",
     *         type="dateTime", in="body",
     *         description="Actual date and time when Price was obtained",
     *         @SWG\Schema(ref="#/definitions/Price"),
     *     ),
     *     @SWG\Parameter(
     *         name="amount",
     *         in="body", type="integer",
     *         description="Price amount in smallest unit of currency",
     *         @SWG\Schema(ref="#/definitions/Price"),
     *     ),
     *     @SWG\Parameter(
     *         name="currency",
     *         in="body", type="integer",
     *         description="Currency of price",
     *         @SWG\Schema(ref="#/definitions/Price"),
     *     ),
     *     @SWG\Response(response=201, description="Newly committed Price", @SWG\Schema(ref="#/definitions/Price") ),
     * )
     * @param Product $product
     * @param ParamFetcher $params
     *
     * @RequestParam(name="timestamp", requirements=@Assert\DateTime, strict=true, description="Actual date and time when Price was obtained")
     * @RequestParam(name="amount", requirements="\d+", description="Price amount in smallest unit of currency")
     * @RequestParam(name="currency", requirements=@Assert\Currency, description="Currency of price")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Money\InvalidArgumentException
     * @throws \Money\UnknownCurrencyException
     * @throws \InvalidArgumentException
     *
     * @View(statusCode=201);
     */
    public function postPricesAction(Product $product, ParamFetcher $params)
    {
        $price = new Money((int) $params->get('amount'), new Currency($params->get('currency')));
        $timestamp = new \DateTimeImmutable($params->get('timestamp'));

        $productPrice = new Price($product, $price, $timestamp);

        $em = $this->doctrine->getManager();
        $em->persist($productPrice);
        $em->flush();

        return $productPrice;
    }
}