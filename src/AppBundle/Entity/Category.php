<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Swagger\Annotations AS SWG;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * Class Category.
 * @ORM\Entity(repositoryClass="CategoryRepository")
 * @SWG\Definition(required={"name","type","unit"})
 */
class Category implements \JsonSerializable
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @SWG\Property()
     */
    protected $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @SWG\Property()
     */
    protected $name;
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @SWG\Property()
     */
    protected $description;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @SWG\Property()
     */
    protected $type;
    /**
     * One of gr|ml
     * @var string
     * @ORM\Column(type="string", length=3)
     * @SWG\Property(enum={"gr","ml"})
     */
    protected $unit;
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
     * @Gedmo\Timestampable(on="create")
     * @SWG\Property()
     */
    protected $updatedAt;

    /**
     * Category constructor.
     * @param string $name
     * @param string $type
     * @param string $unit
     * @param string|null $description
     */
    public function __construct($name, $type, $unit, $description = null)
    {
        $this->id = Uuid::uuid4();
        $this->setName($name);
        $this->setType($type);
        $this->setUnit($unit);
        $this->setDescription($description);
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @param string $name
     */
    private function setName($name)
    {
        Assert::stringNotEmpty($name, 'Name is required');
        Assert::range(mb_strlen($name), 3, 255);
        $this->name = $name;
    }

    /**
     * @param string $type
     */
    private function setType($type)
    {
        Assert::stringNotEmpty($type, 'Type is required');
        Assert::range(mb_strlen($type), 3, 255);
        $this->type = $type;
    }

    /**
     * @param string $unit
     */
    private function setUnit($unit)
    {
        Assert::oneOf($unit, ['gr', 'ml']);
        $this->unit = $unit;
    }

    /**
     * @param string $description
     */
    private function setDescription($description)
    {
        if ($description) {
            Assert::range(mb_strlen($description), 3, 1024);
            $this->description = $description;
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'unit' => $this->unit,
            'type' => $this->type,
            'description' => $this->description,
        ];
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
}