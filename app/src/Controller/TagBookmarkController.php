<?php
/**
 * TagBookmark controller.
 */

namespace App\Controller;

use App\Entity\TagBookmark;
use App\Repository\TagBookmarkRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TagBookmarkController.
 *
 * @Route("/tagbookmark")
 */
class TagBookmarkController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Repository\TagBookmarkRepository $repository TagBookmark repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route(
     *     "/",
     *     name="tagbookmark_index",
     * )
     */
    public function index(Request $request, TagBookmarkRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            TagBookmark::NUMBER_OF_ITEMS
        );

        return $this->render(
            'tagbookmark/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param \App\Entity\TagBookmark $tagbookmark TagBookmark entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="tagbookmark_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(TagBookmark $tagbookmark): Response
    {
        return $this->render(
            'tagbookmark/view.html.twig',
            ['tagbookmark' => $tagbookmark]
        );
    }
}