<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Food;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;

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
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @View
     */
    public function getFoodsAction()
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Food');
        return $repo->findAll();
    }

    /**
     * @RequestParam(name="name", description="Food name")
     * @param ParamFetcher $params
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @View(statusCode=201);
     */
    public function postFoodsAction(ParamFetcher $params)
    {
        $food = new Food($params->get('name'));

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