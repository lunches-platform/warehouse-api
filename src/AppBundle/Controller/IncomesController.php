<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Income;
use AppBundle\Entity\Product;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Money\Currency;
use Money\Money;

/**
 * Class IncomesController.
 */
class IncomesController extends FOSRestController
{
    /**
     * @param Income $income
     * @return \Symfony\Component\HttpFoundation\Response
     * @Get("/products/incomes/{income}")
     * @View
     */
    public function getIncomeAction(Income $income)
    {
        return $income;
    }

    /**
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @View
     */
    public function getIncomesAction(Product $product)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Income');

        return $repo->findBy([
            'product' => $product,
        ]);
    }

    /**
     * @param Product $product
     * @param ParamFetcher $params
     *
     * @RequestParam(name="purchasedAt", strict=false, description="Date when product was purchased. Can be omitted")
     * @RequestParam(name="supplier", description="Shop or supplier where product has been bought")
     * @RequestParam(name="quantity", description="Quantity of product. Float accepted")
     * @RequestParam(name="price", requirements="\d+", description="Price in smallest unit of currency")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Money\InvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Money\UnknownCurrencyException
     *
     * @View(statusCode=201);
     */
    public function postIncomesAction(Product $product, ParamFetcher $params)
    {
        $price = new Money((int) $params->get('price'), new Currency('UAH'));
        $quantity = $params->get('quantity');
        $supplier = $params->get('supplier');
        $purchasedAt = new \DateTimeImmutable($params->get('purchasedAt'));

        $income = new Income($product, $quantity, $price, $supplier, $purchasedAt);

        $em = $this->getDoctrine()->getManager();
        $em->persist($income);
        $em->flush();

        return $income;
    }
}