<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController.
 *
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\CategoryRepository        $repository Category repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route(
     *     "/",
     *     name="category_index",
     * )
     */
    public function index(Request $request, CategoryRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            Category::NUMBER_OF_ITEMS
        );

        return $this->render(
            'category/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param \App\Entity\Category $category Category entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="category_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Category $category): Response
    {
        return $this->render(
            'category/view.html.twig',
            ['category' => $category]
        );
    }
}