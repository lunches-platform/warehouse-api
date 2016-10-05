<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Swagger\Annotations AS SWG;
use JsonSerializable;
use Money\Money;
use Ramsey\Uuid\Uuid;

/**
 * Class Price.
 * @ORM\Entity(repositoryClass="PriceRepository")
 * @SWG\Definition(
 *     required={"product","price","timestamp"},
 *     @SWG\Property(
 *         property="value",
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
class Price implements JsonSerializable
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
     * @var Money
     * @ORM\Embedded(class="Money\Money")
     */
    protected $price;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @SWG\Property()
     */
    protected $timestamp;

    /**
     * Price constructor.
     * @param Product $product
     * @param Money $price
     * @param \DateTimeImmutable|null $timestamp
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Product $product,
        Money $price,
        \DateTimeImmutable $timestamp
    )
    {
        $this->id = Uuid::uuid4()->toString();
        $this->product = $product;
        $this->setPrice($price);
        $this->timestamp = $timestamp;
        
        $product->refreshLastPrice($this);
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
            'amount' => $this->price->getAmount(),
            'currency' => $this->price->getCurrency(),
            'timestamp' => $this->timestamp,
        ];
    }
}