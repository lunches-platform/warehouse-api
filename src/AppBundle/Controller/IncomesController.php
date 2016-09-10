<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Income;
use AppBundle\Entity\Product;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\NoRoute;
use FOS\RestBundle\Controller\Annotations\RequestParam;
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
     * TODO does not work, fix it
     * @NoRoute
     * @Get("/products/{productId}/incomes/{income}")
     */
    public function getIncomeAction(Income $income)
    {
        return $this->handleView(
            $this->view($income, 200)
        );
    }

    /**
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function getIncomesAction(Product $product)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Income');

        return $this->handleView(
            $this->view($repo->findBy([
                'product' => $product,
            ]))
        );
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
     */
    public function postIncomesAction(Product $product, ParamFetcher $params)
    {
        // TODO make price field required and forbid using negative
        $price = new Money((int) $params->get('price'), new Currency('UAH'));
        $quantity = $params->get('quantity');
        $supplier = $params->get('supplier');
        $purchasedAt = new \DateTimeImmutable($params->get('purchasedAt'));

        $income = new Income($product, $quantity, $price, $supplier, $purchasedAt);

        $em = $this->getDoctrine()->getManager();
        $em->persist($income);
        $em->flush();

        return $this->handleView($this->view($income));
    }
}