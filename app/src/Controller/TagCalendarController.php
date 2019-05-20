<?php
/**
 * TagCalendar controller.
 */

namespace App\Controller;

use App\Entity\TagCalendar;
use App\Repository\TagCalendarRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TagCalendarController.
 *
 * @Route("/tagcalendar")
 */
class TagCalendarController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Repository\TagCalendarRepository $repository TagCalendar repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route(
     *     "/",
     *     name="tagcalendar_index",
     * )
     */
    public function index(Request $request, TagCalendarRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            TagCalendar::NUMBER_OF_ITEMS
        );

        return $this->render(
            'tagcalendar/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param \App\Entity\TagCalendar $tagcalendar TagCalendar entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="tagcalendar_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(TagCalendar $tagcalendar): Response
    {
        return $this->render(
            'tagcalendar/view.html.twig',
            ['tagcalendar' => $tagcalendar]
        );
    }
}