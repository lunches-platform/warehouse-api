<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Swagger\Annotations AS SWG;
use JsonSerializable;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * Class Income.
 * @ORM\Entity(repositoryClass="IncomeRepository")
 * @SWG\Definition(
 *     required={"product","quantity","price","supplier"},
 *     @SWG\Property(
 *         property="price",
 *         description="Price of the product",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="currency",
 *         description="Currency of the price",
 *         type="string"
 *     )
 * )
 */
class Income implements JsonSerializable
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @SWG\Property()
     */
    protected $id;
    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product")
     * @SWG\Property(ref="#/definitions/Product")
     */
    protected $product;
    /**
     * @var float
     * @ORM\Column(type="float")
     * @SWG\Property()
     */
    protected $quantity;
    /**
     * @var Money
     * @ORM\Embedded(class="Money\Money")
     */
    protected $price;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @SWG\Property()
     */
    protected $createdAt;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @SWG\Property()
     */
    protected $purchasedAt;
    /**
     * @var Supplier
     * @ORM\ManyToOne(targetEntity="Supplier")
     * @SWG\Property(ref="#/definitions/Supplier")
     */
    protected $supplier;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @SWG\Property()
     */
    protected $purchaser;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @SWG\Property()
     */
    protected $warehouseKeeper;

    /**
     * Income constructor.
     * @param Product $product
     * @param $quantity
     * @param Money $price
     * @param Supplier $supplier
     * @param string $warehouseKeeper
     * @param string $purchaser
     * @param \DateTimeImmutable|null $purchasedAt
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Product $product,
        $quantity,
        Money $price,
        Supplier $supplier,
        $warehouseKeeper,
        $purchaser,
        \DateTimeImmutable $purchasedAt = null
    )
    {
        $this->id = Uuid::uuid4()->toString();
        $this->product = $product;
        $this->supplier = $supplier;
        $this->createdAt = new \DateTimeImmutable();
        $this->setPrice($price);
        $this->setPurchasedAt($purchasedAt);
        $this->setQuantity($quantity);
        $this->setWarehouseKeeper($warehouseKeeper);
        $this->setPurchaser($purchaser);
    }

    /**
     * @param float $quantity
     * @throws \InvalidArgumentException
     */
    private function setQuantity($quantity)
    {
        $quantity = (float) $quantity;
        $quantity = round($quantity, 3);

        Assert::range($quantity, 0, 1000);

        if ($this->product->distributesInPcs() && ($quantity - floor($quantity) > 0)) {
            throw new \InvalidArgumentException("Product #{$this->product()->id()} distributes in pcs, so quantity must be integer");
        }

        $this->quantity = $quantity;
    }

    /**
     * @param \DateTimeImmutable $purchasedAt
     * @throws \InvalidArgumentException
     */
    private function setPurchasedAt(\DateTimeImmutable $purchasedAt = null)
    {
        $today = new \DateTimeImmutable();
        $purchasedAt = $purchasedAt instanceof \DateTimeImmutable ? $purchasedAt : $today;

        if ($purchasedAt > $today) {
            throw new \InvalidArgumentException('Purchase date must be in the past');
        }

        $this->purchasedAt = $purchasedAt;
    }

    /**
     * @return Product
     */
    private function product()
    {
        return $this->product;
    }

    /**
     * @param Money $price
     * @throws \InvalidArgumentException
     */
    private function setPrice(Money $price)
    {
        if (!$price->isPositive()) {
            throw new \InvalidArgumentException('Price must be positive');
        }
        $this->price = $price;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => (string) $this->id,
            'productId' => $this->product->id(),
//            'product' => $this->product,
            'quantity' => $this->quantity,
            'price' => [
                'amount' => $this->price->getAmount(),
                'currency' => $this->price->getCurrency(),
            ],
            'purchasedAt' => $this->purchasedAt,
            'supplierId' => $this->supplier->id(),
        ];
    }

    /**
     * @param string $warehouseKeeper
     * @throws \InvalidArgumentException
     */
    private function setWarehouseKeeper($warehouseKeeper)
    {
        Assert::stringNotEmpty($warehouseKeeper);
        if ($warehouseKeeper !== 'WarehouseKeeper') {
            throw new \InvalidArgumentException('WarehouseKeeper is invalid');
        }
        $this->warehouseKeeper = $warehouseKeeper;
    }

    /**
     * @param string $purchaser
     * @throws \InvalidArgumentException
     */
    private function setPurchaser($purchaser)
    {
        Assert::stringNotEmpty($purchaser);
        if ($purchaser !== 'Purchaser') {
            throw new \InvalidArgumentException('Purchaser is invalid');
        }
        $this->purchaser = $purchaser;
    }
}