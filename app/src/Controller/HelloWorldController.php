<?php
/**
 * Hello World controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

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
     *     name="base_index",
     * )
     */
    public function index(): Response
    {
        return $this->render(
            'hello/index.html.twig'
        );

    }
}
