<?php


namespace AppBundle\Controller;


use AppBundle\CsvFoodsImporter;
use AppBundle\Entity\Food;
use AppBundle\Exception\EntityNotFoundException;
use AppBundle\Service\CreateFood;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Constraints\Uuid;
use Swagger\Annotations AS SWG;

/**
 * Class FoodsController.
 */
class FoodsController
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * FoodsController constructor.
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    /**
     * @SWG\Get(
     *     path="/foods/{foodId}",
     *     description="Get Food by ID",
     *     operationId="getFoodAction",
     *     @SWG\Parameter(
     *         name="foodId", format="uuid", type="string", required=true, in="path", description="ID of food",
     *     ),
     *     @SWG\Response(response=200, description="Food", @SWG\Schema(ref="#/definitions/Food")),
     * )
     * @param Food $food
     * @return Food
     * @View
     */
    public function getFoodAction(Food $food)
    {
        return $food;
    }

    /**
     * @SWG\Get(
     *     path="/foods",
     *     description="Return foods by filters",
     *     operationId="getFoodsAction",
     *     @SWG\Parameter(
     *         description="Filter foods by LIKE pattern",
     *         type="string",
     *         in="query",
     *         name="like",
     *     ),
     *     @SWG\Response(response=200, description="List of Foods", @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Food"))),
     * )
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @QueryParam(name="like", description="Filter foods by LIKE pattern")
     * @View
     */
    public function getFoodsAction(ParamFetcher $params)
    {
        $repo = $this->doctrine->getRepository('AppBundle:Food');

        if ($like = $params->get('like')) {
            return $repo->findByNameLike($like);
        } else {
            return $repo->findAll();
        }
    }

    /**
     * @SWG\Post(
     *     path="/foods",
     *     operationId="postFoodsAction",
     *     description="Creates new food",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="Food name",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Food"),
     *     ),
     *     @SWG\Parameter(
     *         name="categoryId",
     *         in="body",
     *         format="uuid",
     *         type="string",
     *         description="ID of category",
     *         @SWG\Schema(ref="#/definitions/Food"),
     *     ),
     *     @SWG\Response(response=201, description="Newly created Food", @SWG\Schema(ref="#/definitions/Food") ),
     * )
     * @RequestParam(name="name", description="Food name")
     * @RequestParam(name="categoryId", strict=false, requirements=@Uuid, description="Category to assign")
     * @param ParamFetcher $params
     * @return Food
     * @throws \Exception
     * @View(statusCode=201);
     */
    public function postFoodsAction(ParamFetcher $params)
    {
        $createFood = new CreateFood(
            $this->doctrine->getRepository('AppBundle:Food'),
            $this->doctrine->getRepository('AppBundle:Category')
        );
        try {
            $food = $createFood->execute(
                $params->get('name'),
                $params->get('categoryId')
            );
        } catch (\Exception $e) {
            if ($e instanceof \InvalidArgumentException || $e instanceof EntityNotFoundException) {
                throw new \DomainException($e->getMessage(), $e->getCode(), $e);
            }
            throw $e;
        }

        $em = $this->doctrine->getManager();
        $em->persist($food);
        $em->flush();

        return $food;
    }

    /**
     * @SWG\Post(
     *     path="/foods/import-csv",
     *     operationId="importCsvAction",
     *     description="Allows to import several foods via CSV format",
     *     @SWG\Parameter(
     *         name="delimiter",
     *         description="Symbol which separates one column from another",
     *         enum={";",",","\t"},
     *         in="formData",
     *         default=";",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="skipRows",
     *         type="integer",
     *         in="formData",
     *         description="Count number of rows skip from the beggining of the file",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         type="file",
     *         description="File to import",
     *         required=true
     *     ),
     *     @SWG\Response(response=201, description="Newly created Food", @SWG\Schema(ref="#/definitions/Food") ),
     * )
     * @Post("/foods/import-csv")
     * @RequestParam(name="delimiter", strict=false, requirements="(;|,|\t)", default=";", description="Symbol which separates one column from another")
     * @RequestParam(name="skipRows", strict=false, requirements="\d+", default="0", description="Count number of rows skip from the beggining of the file")
     * @RequestParam(name="file", description="File to import")
     * @param ParamFetcher $params
     * @param Request $request
     * @return array []
     * @throws \Ddeboer\DataImport\Exception\DuplicateHeadersException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Exception
     * @View
     */
    public function postImportCsvAction(ParamFetcher $params, Request $request)
    {
        $delimiter = $params->get('delimiter');
        $skipRows = $params->get('skipRows');

        $file = $request->files->get('file');
        if (!$file instanceof UploadedFile) {
            throw new HttpException(400, 'File not provided or it has invalid format');
        }

        $importer = new CsvFoodsImporter($this->doctrine, $delimiter, $skipRows);

        return $importer->import($file->getRealPath());
    }

    /**
     * @SWG\Put(
     *     path="/foods/{foodId}/aliases/{alias}",
     *     operationId="postFoodAliasesAction",
     *     description="Adds alias for food",
     *     @SWG\Parameter(
     *         name="foodId", format="uuid", type="string", required=true, in="path", description="ID of Food",
     *     ),
     *     @SWG\Parameter(
     *         name="alias", description="", required=true, in="path",
     *     ),
     *     @SWG\Response(response=204)
     * )
     * @param Food $food
     * @param string $alias
     * @return Response
     * @throws \InvalidArgumentException
     * @RequestParam(name="name", description="Food Alias name")
     */
    public function putFoodAliasAction(Food $food, $alias)
    {
        $food->addAlias($alias);
        $this->doctrine->getManager()->flush();

        return new Response();
    }
}