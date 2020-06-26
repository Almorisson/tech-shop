<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    /**
     * @var UserRepository $repository
     */
    private $repository;
    /**
     * @var EntityManagerInterface $manager
     */
    private $manager;

    public function __construct(UserRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
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
     * @Route("/logout", name="account_logout")
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
            $this->manager->persist($user);
            $this->manager->flush();
            $this->addFlash('success', 'Votre compte a bien été crée. Vous pouvez désormais vous connecter à votre compte.');

            return  $this->redirectToRoute('app_auth_login');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
