<?php
/**
 * Contact controller.
 */

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ContactController.
 *
 * @Route("/contact")
 */
class ContactController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\ContactRepository         $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="contact_index",
     * )
     */
    public function index(Request $request, ContactRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryByAuthor($this->getUser()),
            $request->query->getInt('page', 1),
            Contact::NUMBER_OF_ITEMS
        );

        return $this->render(
            'contact/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param \App\Entity\Contact $contact Contact entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="contact_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     *
     * @IsGranted(
     *     "MANAGE",
     *     subject="contact",
     * )
     */
    public function view(Contact $contact): Response
    {
        return $this->render(
            'contact/view.html.twig',
            ['contact' => $contact]
        );
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\contactRepository         $repository Contact repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/new",
     *     methods={"GET", "POST"},
     *     name="contact_new",
     * )
     */
    public function new(Request $request, ContactRepository $repository): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setAuthor($this->getUser());
            $repository->save($contact);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('contact_index');
        }

        return $this->render(
            'contact/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Contact                       $contact    Contact entity
     * @param \App\Repository\ContactRepository         $repository Contact repository
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
     *     name="contact_edit",
     * )
     *
     * @IsGranted(
     *     "MANAGE",
     *     subject="contact",
     * )
     */
    public function edit(Request $request, Contact $contact, ContactRepository $repository): Response
    {
        $form = $this->createForm(ContactType::class, $contact, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($contact);
            $this->addFlash('success', 'message.updated_successfully');
            return $this->redirectToRoute('contact_index');
        }
        return $this->render(
            'contact/edit.html.twig',
            [
                'form' => $form->createView(),
                'contact' => $contact,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Contact                       $contact    Contact entity
     * @param \App\Repository\ContactRepository         $repository Contact repository
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
     *     name="contact_delete",
     * )
     *
     * @IsGranted(
     *     "MANAGE",
     *     subject="contact",
     * )
     */
    public function delete(Request $request, Contact $contact, ContactRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $contact, ['method' => 'DELETE']);
        $form->handleRequest($request);
        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($contact->getTags() as $tag) {
                $contact->removeTag($tag);
            }
            $repository->delete($contact);
            $this->addFlash('success', 'message.deleted_successfully');
            return $this->redirectToRoute('contact_index');
        }
        return $this->render(
            'contact/delete.html.twig',
            [
                'form' => $form->createView(),
                'contact' => $contact,
            ]
        );
    }

    /**
     * Index Admin action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\ContactRepository        $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/admin",
     *     name="contact_index_admin",
     * )
     */
    public function index_admin(Request $request, ContactRepository $repository, PaginatorInterface $paginator): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $pagination = $paginator->paginate(
                $repository->queryAll(),
                $request->query->getInt('page', 1),
                Contact::NUMBER_OF_ITEMS
            );

            return $this->render(
                'contact/index.html.twig',
                ['pagination' => $pagination]
            );
        }
        else {
            return $this->redirectToRoute('contact_index');
        }
    }
}
