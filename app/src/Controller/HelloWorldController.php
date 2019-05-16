<?php
/**
 * Hello World controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HelloWorldController.
 */
class HelloWorldController extends AbstractController
{
    /**
     * Index action.
     *
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     * )
     */
    public function index(): Response
    {
        return $this->render(
            'hello/index.html.twig'
        );
    }
}