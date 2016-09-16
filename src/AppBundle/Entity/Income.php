<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JsonSerializable;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * Class Income.
 * @ORM\Entity(repositoryClass="IncomeRepository")
 */
class Income implements JsonSerializable
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="guid")
     */
    protected $id;
    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product")
     */
    protected $product;
    /**
     * @var float
     * @ORM\Column(type="float")
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
     */
    protected $createdAt;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $purchasedAt;
    /**
     * TODO implement Supplier and BasicSupplier
     * @var string
     * @ORM\Column(type="string")
     */
    protected $supplier;

    /**
     * Income constructor.
     * @param Product $product
     * @param $quantity
     * @param Money $price
     * @param $supplier
     * @param \DateTimeImmutable|null $purchasedAt
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Product $product,
        $quantity,
        Money $price,
        $supplier,
        \DateTimeImmutable $purchasedAt = null
    )
    {
        $this->id = Uuid::uuid4();
        $this->product = $product;
        $this->createdAt = new \DateTimeImmutable();
        $this->setPrice($price);
        $this->setPurchasedAt($purchasedAt);
        $this->setQuantity($quantity);
        $this->setSupplier($supplier);
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
     * @param string $supplier
     */
    private function setSupplier($supplier)
    {
        Assert::stringNotEmpty($supplier, 'Supplier is required');
        Assert::range(mb_strlen($supplier), 3, 255);
        $this->supplier = $supplier;
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
            'supplier' => $this->supplier,
        ];
    }
}