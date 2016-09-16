<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Food;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @View(statusCode=201);
     */
    public function postFoodsAction(Request $request)
    {
        $food = new Food($request->request->get('name'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($food);
        $em->flush();

        return $food;
    }

    /**
     * @param Food $food
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @View
     */
    public function putFoodAction(Food $food, Request $request)
    {
        if ($name = $request->request->get('name')) {
            $food->changeName($name);
        }
        $this->getDoctrine()->getManager()->flush();

        return $food;
    }
}