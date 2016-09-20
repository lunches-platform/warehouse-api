<?php


namespace AppBundle\Entity;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Swagger\Annotations AS SWG;
use Webmozart\Assert\Assert;

/**
 * Class Supplier.
 * @ORM\Entity(repositoryClass="SupplierRepository")
 * @SWG\Definition(required={"name"})
 */
class Supplier implements \JsonSerializable
{
    /**
     * @var string
     * @ORM\Column(type="guid")
     * @ORM\Id
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
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @SWG\Property()
     */
    protected $createdAt;

    /**
     * Whether supplier is confirmed manually or not
     *
     * @var bool
     * @ORM\Column(type="boolean")
     * @SWG\Property()
     */
    protected $confirmed = false;

    /**
     * Supplier constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->id = Uuid::uuid4();
        $this->setName($name);
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @param string $name
     */
    public function changeName($name)
    {
        $this->setName($name);
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
     * @param string $name
     */
    private function setName($name)
    {
        Assert::stringNotEmpty($name, 'Name is required');
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
            'confirmed' => $this->confirmed,
        ];
    }
}