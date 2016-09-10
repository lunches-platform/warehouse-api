<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * Class Product.
 * @ORM\Entity(repositoryClass="ProductRepository")
 * @ORM\Table(name="product")
 */
class Product
{
    /**
     * @var string
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    protected $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;
    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;
    /**
     * @var string
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;
    /**
     * TODO implement Brand
     * @var string
     * @ORM\Column(type="string")
     */
    protected $brand;
    /**
     * @var Food
     * @ORM\ManyToOne(targetEntity="Food")
     */
    protected $food;
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $pcs = true;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $weightPerPcs;

    /**
     * Product constructor.
     * @param Food $food
     * @param string $name
     * @param string $brand
     * @param bool $pcs
     * @param int|null $weight
     * @throws \InvalidArgumentException
     */
    public function __construct(Food $food, $name, $brand, $pcs = true, $weight = null)
    {
        $this->id = Uuid::uuid4();
        $this->food = $food;
        $this->createdAt = $this->updatedAt = new \DateTimeImmutable();
        $this->setName($name);
        $this->setBrand($brand);
        $this->setPcsAndWeight($pcs, $weight);
    }

    /**
     * @return bool
     */
    public function distributesInPcs()
    {
        return $this->pcs === true;
    }

    /**
     * @param string $name
     */
    public function changeName($name)
    {
        $this->setName($name);
    }

    /**
     * @param string $brand
     */
    public function changeBrand($brand)
    {
        $this->setBrand($brand);
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    private function setName($name)
    {
        Assert::stringNotEmpty($name, 'Name of the product is required');
        Assert::range(mb_strlen($name), 3, 255, 'Name must be withing 3 and 255 characters length');
        $this->name = $name;
    }

    /**
     * @param string $brand
     */
    private function setBrand($brand)
    {
        Assert::stringNotEmpty($brand, 'Brand is required');
        Assert::range(mb_strlen($brand), 3, 255);
        $this->brand = $brand;
    }

    /**
     * @param bool $pcs
     * @param null|int $weight
     * @throws \InvalidArgumentException
     */
    private function setPcsAndWeight($pcs, $weight)
    {
        $pcs = (bool) $pcs;
        $weight = (int) $weight;

        if ($pcs === true && $weight <= 0) {
            throw new \InvalidArgumentException('When product distributes in pcs, weight per pcs must be specified');
        }
        $this->pcs = $pcs;
        if ($weight) {
            Assert::lessThanEq($weight, 100 * 1000); // 100 kg/litre per pcs
            $this->weightPerPcs = $weight;
        }
    }
}