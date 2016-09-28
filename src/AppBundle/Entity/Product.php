<?php

namespace AppBundle\Entity;

use AppBundle\ValueObject\EntityName;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Swagger\Annotations AS SWG;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * Class Product.
 * @ORM\Entity(repositoryClass="ProductRepository")
 * @ORM\Table(name="product")
 * @SWG\Definition(required={"food", "name", "brand"})
 */
class Product implements \JsonSerializable
{
    /**
     * @var string
     * @ORM\Column(type="guid")
     * @ORM\Id
     * @SWG\Property()
     */
    protected $id;
    /**
     * @var EntityName
     * @ORM\Column(type="string")
     * @ORM\Embedded(class="\AppBundle\ValueObject\EntityName", columnPrefix=false)
     */
    protected $name;
    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @SWG\Property()
     */
    protected $createdAt;
    /**
     * @var string
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @SWG\Property()
     */
    protected $updatedAt;
    /**
     * @var Brand
     * @ORM\ManyToOne(targetEntity="Brand")
     * @SWG\Property(ref="#/definitions/Brand")
     */
    protected $brand;
    /**
     * @var Food
     * @ORM\ManyToOne(targetEntity="Food")
     * @SWG\Property(ref="#/definitions/Food")
     */
    protected $food;
    /**
     * Either Product distributes in pcs or no
     *
     * @var bool
     * @ORM\Column(type="boolean")
     * @SWG\Property()
     */
    protected $pcs = true;
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @SWG\Property()
     */
    protected $weightPerPcs;

    /**
     * Product constructor.
     * @param Food $food
     * @param EntityName $name
     * @param Brand $brand
     * @param bool $pcs
     * @param int|null $weight
     * @throws \InvalidArgumentException
     */
    public function __construct(Food $food, EntityName $name, Brand $brand, $pcs = true, $weight = null)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->food = $food;
        $this->brand = $brand;
        $this->createdAt = $this->updatedAt = new \DateTimeImmutable();
        $this->name = $name;
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
        $this->name = new EntityName($name);
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
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

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => (string) $this->id,
            'name' => (string) $this->name,
            'brand' => $this->brand,
            'food' => $this->food,
            'pcs' => $this->pcs,
            'weightPerPcs' => $this->weightPerPcs,
        ];
    }
}