<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthController extends AbstractController
{
    /**
     * @var EntityManagerInterface $manager
     */
    private $manager;

    public function __construct(UserRepository $repository, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    /**
     * Allow to show and manage login form
     *
     * @Route("/login", name="app_auth_login")
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $last_email = $utils->getLastUsername();
        return $this->render('auth/login.html.twig', [
            'hasError' => $error !== null,
            'lastEmail' => $last_email

        ]);
    }

    /**
     * Allow to logout user
     *
     * @Route("/logout", name="app_auth_logout")
     * @return void
     */
    public function logout(): void
    {
        //nothing for now: Symfony do all the job
    }

    /**
     * Allow to register a user
     *
     * @Route("/register", name="app_auth_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            //$user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $user->setRoles(["ROLE_USER"]);
            $this->manager->persist($user);
            $this->manager->flush();
            $this->addFlash('success', 'Votre compte a bien été crée. Vous pouvez désormais vous connecter à votre compte.');

            return  $this->redirectToRoute('app_auth_login');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Allow to update user profile infos
     *
     * @Route("/auth/profile", name="app_auth_profile")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager, TranslatorInterface $translator, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser(); // the current connected user
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$newPassword = $encoder->encodePassword($this->getUser(), $form->get('password')->getData());
            //$user->setPassword($newPassword);
            $manager->flush();
            $this->addFlash('success', $translator->trans('profile_update_success'));
        }

        return $this->render("auth/profile.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * Allows you to update the password of the currently logged in user
     *
     * @Route("/auth/password-update", name="app_auth__password_update")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager, TranslatorInterface $translator): Response
    {
        // get the current user
        $user = $this->getUser();
        $passwordUpdate = new PasswordUpdate();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // check if the old password matches the password typed in
            if(!$encoder->isPasswordValid($user, $passwordUpdate->getOldPassword())) {
                // Error handling and add an error to the oldPassword field
                $oldPassword = $form->get('oldPassword');
                $oldPassword->addError(new FormError($translator->trans('old_password_error')));
            } else {
                // Save the user's new password
                $newPassword = $passwordUpdate->getNewPassword();
                $newPasswordHashed = $encoder->encodePassword($user, $newPassword);
                $user->setPassword($newPasswordHashed);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', $translator->trans('password_updated_success'));
            }
        }

        return $this->render('auth/password/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
