<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * Class Outcome.
 * @ORM\Entity(repositoryClass="OutcomeRepository")
 */
class Outcome
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
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $outcomeAt;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * Outcome constructor.
     * 
     * @param Product $product
     * @param $quantity
     * @param \DateTimeImmutable|null $outcomeAt
     * 
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Product $product,
        $quantity,
        \DateTimeImmutable $outcomeAt = null
    )
    {
        $this->id = Uuid::uuid4();
        $this->product = $product;
        $this->createdAt = new \DateTimeImmutable();
        $this->setOutcomeAt($outcomeAt);
        $this->setQuantity($quantity);
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
     * @return Product
     */
    private function product()
    {
        return $this->product;
    }
}