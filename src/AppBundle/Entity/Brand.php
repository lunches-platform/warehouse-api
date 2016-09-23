<?php


namespace AppBundle\Entity;

use AppBundle\ValueObject\EntityName;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Swagger\Annotations AS SWG;

/**
 * Class Brand.
 * @ORM\Entity(repositoryClass="BrandRepository")
 * @SWG\Definition(required={"name"})
 */
class Brand implements \JsonSerializable
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
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @SWG\Property()
     */
    protected $createdAt;

    /**
     * Whether brand is manually confirmed or not
     * 
     * @var bool
     * @ORM\Column(type="boolean")
     * @SWG\Property()
     */
    protected $confirmed = false;

    /**
     * Brand constructor.
     * @param EntityName $name
     */
    public function __construct(EntityName $name)
    {
        $this->id = Uuid::uuid4();
        $this->name = $name;
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @param string $newName
     */
    public function changeName($newName)
    {
        $this->name = new EntityName($newName);
    }

    public function confirm()
    {
        $this->confirmed = true;
    }

    /**
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->confirmed;
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
            'confirmed' => $this->confirmed,
        ];
    }
}