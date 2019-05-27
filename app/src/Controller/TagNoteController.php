<?php
/**
 * TagNote controller.
 */

namespace App\Controller;

use App\Entity\TagNote;
use App\Repository\TagNoteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TagNoteController.
 *
 * @Route("/tagnote")
 */
class TagNoteController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Repository\TagNoteRepository $repository TagNote repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route(
     *     "/",
     *     name="tagnote_index",
     * )
     */
    public function index(Request $request, TagNoteRepository $repository, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            TagNote::NUMBER_OF_ITEMS
        );

        return $this->render(
            'tagnote/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param \App\Entity\TagNote $tagnote TagNote entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="tagnote_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(TagNote $tagnote): Response
    {
        return $this->render(
            'tagnote/view.html.twig',
            ['tagnote' => $tagnote]
        );
    }
}