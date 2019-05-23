<?php
/**
 * Calendar controller.
 */

namespace App\Controller;

use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CalendarController.
 *
 * @Route("/calendar")
 */
class CalendarController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\CalendarRepository        $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="calendar_index",
     * )
     */
    public function index(Request $request, CalendarRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryByAuthor($this->getUser()),
            $request->query->getInt('page', 1),
            Calendar::NUMBER_OF_ITEMS
        );

        return $this->render(
            'calendar/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param \App\Entity\Calendar $calendar Calendar entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="calendar_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Calendar $calendar): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') or $calendar->getAuthor() == $this->getUser()) {
            return $this->render(
                'calendar/view.html.twig',
                ['calendar' => $calendar]
            );
        }

        $this->addFlash('warning', 'message.item_not_found');

        return $this->redirectToRoute('calendar_index');
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\CalendarRepository        $repository Calendar repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/new",
     *     methods={"GET", "POST"},
     *     name="calendar_new",
     * )
     */
    public function new(Request $request, CalendarRepository $repository): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calendar->setAuthor($this->getUser());
            $repository->save($calendar);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('calendar_index');
        }

        return $this->render(
            'calendar/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Calendar                      $calendar   Calendar entity
     * @param \App\Repository\CalendarRepository        $repository Calendar repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="calendar_edit",
     * )
     */
    public function edit(Request $request, Calendar $calendar, CalendarRepository $repository): Response
    {
        if ($calendar->getAuthor() == $this->getUser() or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(CalendarType::class, $calendar, ['method' => 'PUT']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $repository->save($calendar);

                $this->addFlash('success', 'message.updated_successfully');

                return $this->redirectToRoute('calendar_index');
            }

            return $this->render(
                'calendar/edit.html.twig',
                [
                    'form' => $form->createView(),
                    'calendar' => $calendar,
                ]
            );
        } else {
            $this->addFlash('warning', 'message.item_not_found');

            return $this->redirectToRoute('calendar_index');
        }
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Calendar                      $calendar   Calendar entity
     * @param \App\Repository\CalendarRepository        $repository Calendar repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="calendar_delete",
     * )
     */
    public function delete(Request $request, Calendar $calendar, CalendarRepository $repository): Response
    {
        if ($calendar->getAuthor() == $this->getUser() or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(FormType::class, $calendar, ['method' => 'DELETE']);
            $form->handleRequest($request);

            if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
                $form->submit($request->request->get($form->getName()));
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $repository->delete($calendar);
                $this->addFlash('success', 'message.deleted_successfully');

                return $this->redirectToRoute('calendar_index');
            }

            return $this->render(
                'calendar/delete.html.twig',
                [
                    'form' => $form->createView(),
                    'calendar' => $calendar,
                ]
            );
        } else {
            $this->addFlash('warning', 'message.item_not_found');

            return $this->redirectToRoute('calendar_index');
        }
    }

    /**
     * Index Admin action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\CalendarRepository        $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/admin",
     *     name="calendar_index_admin",
     * )
     */
    public function index_admin(Request $request, CalendarRepository $repository, PaginatorInterface $paginator): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $pagination = $paginator->paginate(
                $repository->queryAll(),
                $request->query->getInt('page', 1),
                Calendar::NUMBER_OF_ITEMS
            );

            return $this->render(
                'calendar/index.html.twig',
                ['pagination' => $pagination]
            );
        }
        else {
            return $this->redirectToRoute('calendar_index');
        }
    }
}
