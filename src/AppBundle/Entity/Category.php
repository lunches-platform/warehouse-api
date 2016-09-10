<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Category.
 * @ORM\Entity
 */
class Category
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="guid")
     */
    protected $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $type;
    /**
     * One of gr|ml
     * @var string
     * @ORM\Column(type="string", length=3)
     */
    protected $unit;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $updatedAt;
}