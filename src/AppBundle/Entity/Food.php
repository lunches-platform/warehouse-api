<?php


namespace AppBundle\Entity;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Webmozart\Assert\Assert;

/**
 * Class Food.
 * @ORM\Entity(repositoryClass="FoodRepository")
 */
class Food implements \JsonSerializable
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
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category")
     */
    protected $category;

    /**
     * Food constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->id = Uuid::uuid4();
        $this->setName($name);
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @param Category $category
     */
    public function assignCategory(Category $category)
    {
        Assert::null($this->category);
        $this->category = $category;
    }

    /**
     * @param string $name
     */
    public function changeName($name)
    {
        $this->setName($name);
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
        Assert::stringNotEmpty($name, 'Name of the Food is required');
        Assert::range(mb_strlen($name), 3, 255);
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'category' => null === $this->category ? null : $this->category->name(),
        ];
    }
}