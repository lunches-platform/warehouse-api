<?php


namespace AppBundle\Entity;

use AppBundle\ValueObject\EntityName;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Swagger\Annotations AS SWG;

/**
 * Class FoodAlias.
 * @ORM\Entity()
 * @SWG\Definition(required={"name"})
 */
class FoodAlias implements \JsonSerializable
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
     * @SWG\Property(type="string")
     * @ORM\Embedded(class="\AppBundle\ValueObject\EntityName", columnPrefix=false)
     */
    protected $name;

    /**
     * @var Food
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Food", inversedBy="aliases")
     */
    protected $food;

    /**
     * Brand constructor.
     * @param Food $food
     * @param EntityName $name
     */
    public function __construct(Food $food, EntityName $name)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->food = $food;
        $this->name = $name;
    }

    /**
     * @param FoodAlias $alias
     * @return bool
     */
    public function equals(FoodAlias $alias)
    {
        return $this->name == $alias->name;
    }

    /**
     * @param string $newName
     */
    public function changeName($newName)
    {
        $this->name = new EntityName($newName);
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
        return (string) $this->name;
    }
}