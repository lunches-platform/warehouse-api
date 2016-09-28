<?php

namespace AppBundle;
use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryRepository;
use AppBundle\Exception\DuplicateEntityException;
use AppBundle\Exception\EntityNotFoundException;
use AppBundle\Service\CreateCategory;
use AppBundle\Service\CreateFood;
use Ddeboer\DataImport\Reader\CsvReader;
use Webmozart\Assert\Assert;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Class CsvFoodsImporter.
 */
class CsvFoodsImporter
{
    /** @var Registry */
    private $doctrine;

    /** @var \Doctrine\Common\Persistence\ObjectManager */
    private $em;

    /** @var  CategoryRepository */
    private $categoryRepo;

    /** @var CreateCategory  */
    private $createCategory;

    /** @var CreateFood  */
    private $createFood;

    private $delimiter = ';';
    private $skipRows = 0;

    private static $allowedDelimiters = [
        ';', ',', '\t',
    ];

    private $errors = [];

    /**
     * CsvFoodsImporter constructor.
     *
     * @param Registry $doctrine
     * @param string $delimiter
     * @param int $skipRows
     * @throws \InvalidArgumentException
     */
    public function __construct(Registry $doctrine, $delimiter = ';', $skipRows = 0)
    {
        Assert::oneOf($delimiter, self::$allowedDelimiters, 'CSV importer does not support specified columns delimiter');

        // csv options
        $this->delimiter = $delimiter;
        $this->skipRows = (int) $skipRows;

        // doctrine
        $this->doctrine = $doctrine;
        $this->em = $doctrine->getManager();

        $this->categoryRepo = $this->doctrine->getRepository('AppBundle:Category');
        $foodRepo = $this->doctrine->getRepository('AppBundle:Food');

        // services
        $this->createFood = new CreateFood($foodRepo, $this->categoryRepo);
        $this->createCategory = new CreateCategory($this->categoryRepo);
    }

    /**
     * @param string $fileName
     * @return array
     * @throws \Ddeboer\DataImport\Exception\DuplicateHeadersException
     * @throws \Exception
     */
    public function import($fileName)
    {
        $reader = new CsvReader(new \SplFileObject($fileName), $this->delimiter);

        $i = $imported = $skipped = 0;
        $this->errors = [];
        foreach ($reader as $row) {
            $i++;
            if ($i <= $this->skipRows) {
                continue;
            }
            if (count($row) !== 4) {
                $skipped++;
                if ($skipped >= 10) {
                    $this->addError('Count of skipped errors is high, file reading stopped. Probably you have specified invalid CSV rows delimiter, default is"'.$this->delimiter.'"');
                    break;
                }
                continue;
            }

            $result = $this->importRow($row);
            if ($result === true) {
                $imported++;
            }
        }

        return [
            'read' => $i - $this->skipRows,
            'imported' => $imported,
            'errors' => $this->errors,
        ];
    }

    /**
     * @param array $row
     * @return bool
     * @throws \Exception
     */
    private function importRow(array $row)
    {
        list ($type, $category, $food, $unit) = $row;

        try {
            $food = $this->getFood($food, $this->getCategory($type, $category, $unit)->id());

            $this->em->persist($food);
            $this->em->flush();
        } catch (\Exception $e) {
            if (
                $e instanceof \InvalidArgumentException ||
                $e instanceof DuplicateEntityException ||
                $e instanceof EntityNotFoundException
            ) {
                $this->addError($e->getMessage(), $row);
                return false;
            } else {
                throw $e;
            }
        }

        return true;
    }

    /**
     * @param string $name
     * @param string $categoryId
     * @return Entity\Food
     * @throws \AppBundle\Exception\EntityNotFoundException
     * @throws \AppBundle\Exception\DuplicateEntityException
     */
    private function getFood($name, $categoryId)
    {
        return $this->createFood->execute($name, $categoryId);
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $unit
     *
     * @return Category
     * @throws \AppBundle\Exception\DuplicateEntityException
     */
    private function getCategory($type, $name, $unit)
    {
        $category = $this->categoryRepo->findByNameAndType($name, $type);
        if ($category) {
            return $category;
        }

        $category = $this->createCategory->execute($name, $type, $unit);
        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }

    /**
     * @param string $msg
     * @param null|array  $row
     */
    private function addError($msg, array $row = [])
    {
        if ($row) {
            $msg .= ', row: '.implode($this->delimiter, $row);
        }
        $this->errors[] = $msg;
    }
}
