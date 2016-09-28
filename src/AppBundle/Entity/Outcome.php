<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Swagger\Annotations AS SWG;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * Class Outcome.
 * @ORM\Entity(repositoryClass="OutcomeRepository")
 * @SWG\Definition(required={"product","quantity"})
 */
class Outcome implements \JsonSerializable
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
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @SWG\Property()
     */
    protected $outcomeAt;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @SWG\Property()
     */
    protected $createdAt;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @SWG\Property()
     */
    protected $cook;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @SWG\Property()
     */
    protected $warehouseKeeper;

    /**
     * Outcome constructor.
     * 
     * @param Product $product
     * @param $quantity
     * @param string $warehouseKeeper
     * @param string $cook
     * @param \DateTimeImmutable|null $outcomeAt
     * 
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Product $product,
        $quantity,
        $warehouseKeeper,
        $cook,
        \DateTimeImmutable $outcomeAt = null
    )
    {
        $this->id = Uuid::uuid4()->toString();
        $this->product = $product;
        $this->createdAt = new \DateTimeImmutable();
        $this->setOutcomeAt($outcomeAt);
        $this->setQuantity($quantity);
        $this->setWarehouseKeeper($warehouseKeeper);
        $this->setCook($cook);
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
     * @param \DateTimeImmutable $outcomeAt
     * @throws \InvalidArgumentException
     */
    private function setOutcomeAt(\DateTimeImmutable $outcomeAt = null)
    {
        $today = new \DateTimeImmutable();
        $outcomeAt = $outcomeAt instanceof \DateTimeImmutable ? $outcomeAt : $today;

        if ($outcomeAt > $today) {
            throw new \InvalidArgumentException('Outcome date must be in the past');
        }

        $this->outcomeAt = $outcomeAt;
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
     * @param string $cook
     * @throws \InvalidArgumentException
     */
    private function setCook($cook)
    {
        Assert::stringNotEmpty($cook);
        if ($cook !== 'Cook') {
            throw new \InvalidArgumentException('Cook is invalid');
        }
        $this->cook = $cook;
    }

    /**
     * @return Product
     */
    private function product()
    {
        return $this->product;
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
            'outcomeAt' => $this->outcomeAt,
        ];
    }
}