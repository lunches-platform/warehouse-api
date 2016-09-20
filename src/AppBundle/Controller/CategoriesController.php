<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class CategoriesController.
 */
class CategoriesController
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
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\Response
     * @View
     */
    public function getCategoryAction(Category $category)
    {
        return $category;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @View
     */
    public function getCategoriesAction()
    {
        $repo = $this->doctrine->getRepository('AppBundle:Category');

        return $repo->findAll();
    }

    /**
     * @RequestParam(name="name")
     * @RequestParam(name="type")
     * @RequestParam(name="unit", requirements="(ml|gr)")
     * @RequestParam(name="description", strict=false)
     * 
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * 
     * @View(statusCode=201);
     */
    public function postCategoriesAction(ParamFetcher $params)
    {
        if ($this->doctrine->getRepository('AppBundle:Category')->findOneBy([
            'name' => $name = $params->get('name'),
            'type' => $type = $params->get('type'),
        ])) {
            throw new HttpException(400, 'Such category is exist already');
        }
        $category = new Category($name, $type, $params->get('unit'), $params->get('description'));

        $em = $this->doctrine->getManager();
        $em->persist($category);
        $em->flush();

        return $category;
    }
}