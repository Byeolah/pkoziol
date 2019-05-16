<?php

/**
 * Profile Controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\EmailType;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
        return $this->render(
            'profile/view.html.twig',
            ['user'=>$user]
        );
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\UserRepository        $repository User repository
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

        $profile->setRoles(array('ROLE_USER'));

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
     *     "/{id}/changeemail",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="email_edit",
     * )
     */
    public function edit(Request $request, User $user, UserRepository $repository): Response
    {
        $form = $this->createForm(EmailType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($user);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('profile_view');
        }

        return $this->render(
            'profile/edit_email.html.twig',
            [
                'form' => $form->createView(),
                'task' => $user,
            ]
        );
    }
}