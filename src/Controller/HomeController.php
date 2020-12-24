<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use App\Service\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    /**
     * @Route("/{page<\d+>?1}", name="app_home")
     * @param $page
     * @param Paginator $paginator
     * @return Response
     */
    public function home($page, Paginator $paginator): Response
    {
        $paginator
            ->setClass(Post::class)
            ->setOrder(['publishedAt' => 'DESC'])
            ->setCriteria(['published' => true, 'featured' => true])
            ->setMethod('findPostsBy')
            ->setLimit(10)
            ->setPage($page)
            ;

        return $this->render('home/index.html.twig', [
            'posts' => $paginator->getData(),
            'paginator' => $paginator
        ]);
    }

    /**
     * @Route("/typo", name="typo", methods={"GET"})
     * @return Response
     */
    public function typo(): Response
    {
        return $this->render('home/typo.html.twig');
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param UserAuthenticator $authenticator
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $profile = new Profile();
            $user->setProfile($profile);
            $user->setRoles(["ROLE_USER"]);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('home/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('app_home');
         }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('home/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
