<?php

/**
 * Profile Controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\EmailType;
use App\Form\PassType;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * Class ProfileController.
 *
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * View action.
     *
     * @param \App\Entity\User $user User entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="profile_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(User $user): Response
    {
        if ($user->getId() == $this->getUser()->getId() or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->render(
                'profile/view.html.twig',
                ['user' => $user]
            );
        } else {
            $this->addFlash('warning', 'message.item_not_found');

            return $this->redirectToRoute('profile_view', array('id' => $this->getUser()->getId()));
        }
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\UserRepository            $repository User repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/new",
     *     methods={"GET", "POST"},
     *     name="profile_new",
     * )
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserRepository $repository): Response
    {
        $profile = new User();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        $profile->setPassword(
            $passwordEncoder->encodePassword(
                $profile,
                $form->get('password')->getData()
            )
        );

        $profile->setRoles(['ROLE_USER']);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($profile);

            $this->addFlash('success', 'message.user_created_successfully');

            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            'profile/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit Email.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\User                          $user       User entity
     * @param \App\Repository\UserRepository            $repository User repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/change_email",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="email_edit",
     * )
     */
    public function edit(Request $request, User $user, UserRepository $repository): Response
    {
        if ($user->getId() == $this->getUser()->getId() or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(EmailType::class, $user, ['method' => 'PUT']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $repository->save($user);

                $this->addFlash('success', 'message.updated_successfully');

                return $this->redirectToRoute('profile_view', array('id' => $user->getId()));
            }

            return $this->render(
                'profile/edit_email.html.twig',
                [
                    'form' => $form->createView(),
                    'profile' => $user,
                ]
            );
        } else {
            $this->addFlash('warning', 'message.item_not_found');

            return $this->redirectToRoute('profile_view', array('id' => $this->getUser()->getId()));
        }
    }

    /**
     * Edit Password.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\User                          $user       User entity
     * @param \App\Repository\UserRepository            $repository User repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/change_password",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="password_edit",
     * )
     */
    public function edit_pass(Request $request, User $user,  UserPasswordEncoderInterface $passwordEncoder, UserRepository $repository): Response
    {
        dump($this->getUser());
        if ($user->getId() == $this->getUser()->getId() or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(PassType::class, $user, ['method' => 'PUT']);
            $form->handleRequest($request);

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            if ($form->isSubmitted() && $form->isValid()) {
                $repository->save($user);

                $this->addFlash('success', 'message.updated_successfully');

                return $this->redirectToRoute('profile_view', array('id' => $this->getUser()->getId()));
            }

            return $this->render(
                'profile/edit_password.html.twig',
                [
                    'form' => $form->createView(),
                    'profile' => $user,
                ]
            );
        } else {
            $this->addFlash('warning', 'message.item_not_found');

            return $this->redirectToRoute('profile_view', array('id' => $this->getUser()->getId()));
        }
    }

    /**
     * Edit role.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\User                          $user       User entity
     * @param \App\Repository\UserRepository            $repository User repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/change_role",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="role_edit",
     * )
     */
    public function edit_role(Request $request, User $user,  UserPasswordEncoderInterface $passwordEncoder, UserRepository $repository): Response
    {
        if ($user->getId() !== $this->getUser()->getId()) {
            $form = $this->createForm(FormType::class, $user, ['method' => 'PUT']);
            $form->handleRequest($request);

            if (count($user->getRoles()) == 1) {
                $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $repository->save($user);

                $this->addFlash('success', 'message.updated_successfully');

                return $this->redirectToRoute('admin_user_index');
            }

            return $this->render(
                'profile/edit_role.html.twig',
                [
                    'form' => $form->createView(),
                    'user' => $user,
                ]
            );
        } else {
            $this->addFlash('warning', 'message.item_not_found');

            return $this->redirectToRoute('profile_view', array('id' => $this->getUser()->getId()));
        }
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\User                      $user   User entity
     * @param \App\Repository\UserRepository        $repository User repository
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
     *     name="profile_delete",
     * )
     */
    public function delete(Request $request, User $user, UserRepository $repository): Response
    {
        if ($user->getId() == $this->getUser()->getId() or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(FormType::class, $user, ['method' => 'DELETE']);
            $form->handleRequest($request);

            if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
                $form->submit($request->request->get($form->getName()));
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $repository->delete($user);
                $this->addFlash('success', 'message.deleted_successfully');

                return $this->redirectToRoute('security_login');
            }

            return $this->render(
                'profile/delete.html.twig',
                [
                    'form' => $form->createView(),
                    'user' => $user,
                ]
            );
        } else {
            $this->addFlash('warning', 'message.item_not_found');

            return $this->redirectToRoute('profile_view', array('id' => $this->getUser()->getId()));
        }
    }
}
