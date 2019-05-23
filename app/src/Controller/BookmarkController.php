<?php
/**
 * Bookmark controller.
 */

namespace App\Controller;

use App\Entity\Bookmark;
use App\Form\BookmarkType;
use App\Repository\BookmarkRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BookmarkController.
 *
 * @Route("/bookmark")
 */
class BookmarkController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\BookmarkRepository        $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="bookmark_index",
     * )
     */
    public function index(Request $request, BookmarkRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryByAuthor($this->getUser()),
            $request->query->getInt('page', 1),
            Bookmark::NUMBER_OF_ITEMS
        );

        return $this->render(
            'bookmark/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param \App\Entity\Bookmark $bookmark Bookmark entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="bookmark_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Bookmark $bookmark): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') or $bookmark->getAuthor() == $this->getUser()) {
            return $this->render(
                'bookmark/view.html.twig',
                ['bookmark' => $bookmark]
            );
        }

        $this->addFlash('warning', 'message.item_not_found');

        return $this->redirectToRoute('bookmark_index');
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\BookmarkRepository        $repository Bookmark repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/new",
     *     methods={"GET", "POST"},
     *     name="bookmark_new",
     * )
     */
    public function new(Request $request, BookmarkRepository $repository): Response
    {
        $bookmark = new Bookmark();
        $form = $this->createForm(BookmarkType::class, $bookmark);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookmark->setAuthor($this->getUser());
            $repository->save($bookmark);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('bookmark_index');
        }

        return $this->render(
            'bookmark/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Bookmark                      $bookmark   Bookmark entity
     * @param \App\Repository\BookmarkRepository        $repository Bookmark repository
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
     *     name="bookmark_edit",
     * )
     */
    public function edit(Request $request, Bookmark $bookmark, BookmarkRepository $repository): Response
    {
        if ($bookmark->getAuthor() == $this->getUser() or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(BookmarkType::class, $bookmark, ['method' => 'PUT']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $repository->save($bookmark);

                $this->addFlash('success', 'message.updated_successfully');

                return $this->redirectToRoute('bookmark_index');
            }

            return $this->render(
                'bookmark/edit.html.twig',
                [
                    'form' => $form->createView(),
                    'bookmark' => $bookmark,
                ]
            );
        } else {
            $this->addFlash('warning', 'message.item_not_found');

            return $this->redirectToRoute('bookmark_index');
        }
    }


    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Bookmark                      $bookmark   Bookmark entity
     * @param \App\Repository\BookmarkRepository        $repository Bookmark repository
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
     *     name="bookmark_delete",
     * )
     */
    public function delete(Request $request, Bookmark $bookmark, BookmarkRepository $repository): Response
    {
        if ($bookmark->getAuthor() == $this->getUser() or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(FormType::class, $bookmark, ['method' => 'DELETE']);
            $form->handleRequest($request);

            if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
                $form->submit($request->request->get($form->getName()));
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $repository->delete($bookmark);
                $this->addFlash('success', 'message.deleted_successfully');

                return $this->redirectToRoute('bookmark_index');
            }

            return $this->render(
                'bookmark/delete.html.twig',
                [
                    'form' => $form->createView(),
                    'bookmark' => $bookmark,
                ]
            );
        } else{
            $this->addFlash('warning', 'message.item_not_found');

            return $this->redirectToRoute('bookmark_index');
        }
    }


    /**
     * Index Admin action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\BookmarkRepository        $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/admin",
     *     name="bookmark_index_admin",
     * )
     */
    public function index_admin(Request $request, BookmarkRepository $repository, PaginatorInterface $paginator): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $pagination = $paginator->paginate(
                $repository->queryAll(),
                $request->query->getInt('page', 1),
                Bookmark::NUMBER_OF_ITEMS
            );

            return $this->render(
                'bookmark/index.html.twig',
                ['pagination' => $pagination]
            );
        }
        else {
            return $this->redirectToRoute('bookmark_index');
        }
    }
}
