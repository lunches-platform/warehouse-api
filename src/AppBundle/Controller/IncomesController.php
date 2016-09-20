<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Income;
use AppBundle\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Money\Currency;
use Money\Money;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Uuid;

/**
 * Class IncomesController.
 */
class IncomesController
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
        $repo = $this->doctrine->getRepository('AppBundle:Income');

        return $repo->findBy([
            'product' => $product,
        ]);
    }

    /**
     * @param Product $product
     * @param ParamFetcher $params
     *
     * @RequestParam(name="purchasedAt", strict=false, description="Date when product was purchased. Can be omitted")
     * @RequestParam(name="supplierId", requirements=@Uuid, description="Shop or supplier where product has been bought")
     * @RequestParam(name="quantity", description="Quantity of product. Float accepted")
     * @RequestParam(name="price", requirements="\d+", description="Price in smallest unit of currency")
     * @RequestParam(name="warehouseKeeper", description="Person who is responsible about warehouse management")
     * @RequestParam(name="purchaser", description="Person who has bought a Product")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
        $purchasedAt = new \DateTimeImmutable($params->get('purchasedAt'));

        $supplier = $this->doctrine->getRepository('AppBundle:Supplier')->find($params->get('supplierId'));
        if (!$supplier) {
            throw new NotFoundHttpException('Supplier not found');
        }

        $warehouseKeeper = $params->get('warehouseKeeper');
        $purchaser = $params->get('purchaser');

        $income = new Income($product, $quantity, $price, $supplier, $warehouseKeeper, $purchaser, $purchasedAt);

        $em = $this->doctrine->getManager();
        $em->persist($income);
        $em->flush();

        return $income;
    }
}