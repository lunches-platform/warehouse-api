<?php


namespace AppBundle\Entity;

use AppBundle\ValueObject\EntityName;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Swagger\Annotations AS SWG;
use Webmozart\Assert\Assert;

/**
 * Class Food.
 * @ORM\Entity(repositoryClass="FoodRepository")
 * @SWG\Definition(required={"name"})
 */
class Food implements \JsonSerializable
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
     * @ORM\Embedded(class="\AppBundle\ValueObject\EntityName", columnPrefix=false)
     * @SWG\Property()
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
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category")
     * @SWG\Property(ref="#/definitions/Category")
     */
    protected $category;

    /**
     * Food constructor.
     * @param EntityName $name
     */
    public function __construct(EntityName $name)
    {
        $this->id = Uuid::uuid4();
        $this->name = $name;
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
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => (string) $this->id,
            'name' => (string) $this->name,
            'category' => null === $this->category ? null : $this->category->name(),
        ];
    }
}