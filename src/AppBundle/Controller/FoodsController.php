<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Food;
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
     */
    public function getFoodAction(Food $food)
    {
        return $this->handleView(
            $this->view($food, 200)
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function getFoodsAction()
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Food');
        return $this->handleView(
            $this->view($repo->findAll())
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function postFoodsAction(Request $request)
    {
        $food = new Food($request->request->get('name'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($food);
        $em->flush();

        return $this->handleView($this->view($food, 201));
    }

    /**
     * @param Food $food
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function putFoodAction(Food $food, Request $request)
    {
        if ($name = $request->request->get('name')) {
            $food->changeName($name);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->handleView($this->view($food, 200));
    }
}