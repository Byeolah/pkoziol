<?php
/**
 * TagContact controller.
 */

namespace App\Controller;

use App\Entity\TagContact;
use App\Repository\TagContactRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class TagContactController.
 *
 * @Route("/tagcontact")
 */
class TagContactController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Repository\TagContactRepository $repository TagContact repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route(
     *     "/",
     *     name="tagcontact_index",
     * )
     *
     */
    public function index(Request $request, TagContactRepository $repository, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            TagContact::NUMBER_OF_ITEMS
        );

        return $this->render(
            'tagcontact/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param \App\Entity\TagContact $tagcontact TagContact entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="tagcontact_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     *
     */
    public function view(TagContact $tagcontact): Response
    {

        return $this->render(
            'tagcontact/view.html.twig',
            ['tagcontact' => $tagcontact]
        );
    }
}