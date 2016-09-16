<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Food;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class FoodsController.
 */
class FoodsController extends FOSRestController
{
    /**
     * @param Food $food
     * @return \Symfony\Component\HttpFoundation\Response
     * @View
     */
    public function getFoodAction(Food $food)
    {
        return $food;
    }

    /**
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @QueryParam(name="like", description="Filter foods by LIKE pattern")
     * @View
     */
    public function getFoodsAction(ParamFetcher $params)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Food');

        if ($like = $params->get('like')) {
            return $repo->findByNameLike($like);
        } else {
            return $repo->findAll();
        }
    }

    /**
     * @RequestParam(name="name", description="Food name")
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @View(statusCode=201);
     */
    public function postFoodsAction(ParamFetcher $params)
    {
        $name = $params->get('name');
        if ($this->getDoctrine()->getRepository('AppBundle:Food')->findOneBy(['name' => $name])) {
            throw new HttpException(400, 'Food with specified name is exist already');
        }
        $food = new Food($name);

        $em = $this->getDoctrine()->getManager();
        $em->persist($food);
        $em->flush();

        return $food;
    }

    /**
     * @RequestParam(name="name", description="Food name")
     * @param Food $food
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @View
     */
    public function putFoodAction(Food $food, ParamFetcher $params)
    {
        if ($name = $params->get('name')) {
            $food->changeName($name);
        }
        $this->getDoctrine()->getManager()->flush();

        return $food;
    }
}