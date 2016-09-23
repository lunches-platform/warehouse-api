<?php


namespace AppBundle\ValueObject;

use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping AS ORM;

/**
 * Class EntityName.
 * @ORM\Embeddable()
 */
class EntityName
{
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * EntityName constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        Assert::stringNotEmpty($name, 'Name is required');
        Assert::range(mb_strlen($name), 3, 255);
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}