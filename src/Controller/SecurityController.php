<?php

namespace App\Controller;

use App\Entity\LoginAttempt;
use App\Repository\LoginAttemptRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, LoginAttemptRepository $loginAttemptRepository, Request $request): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            $email = $lastUsername;
            $loginAttempt = $loginAttemptRepository->findOneBy(['email' => $email]);

            if (!$loginAttempt) {
                $loginAttempt = new LoginAttempt();
                $loginAttempt->setEmail($email);
            }

            $loginAttempt->setAttempts($loginAttempt->getAttempts() + 1);

            if ($loginAttempt->getAttempts() >= 3) {
                $loginAttempt->setLockedUntil(new \DateTime('+6 hours'));
            }

            $loginAttemptRepository->save($loginAttempt);

            if ($loginAttempt->getLockedUntil() && $loginAttempt->getLockedUntil() > new \DateTime()) {
                $this->addFlash('error', 'Votre compte est temporairement bloqué.');
            } else {
                $this->addFlash('error', 'Plus que ' . (3 - $loginAttempt->getAttempts()) . ' essai(s).');
            }
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/login_check', name: 'app_login_check')]
    public function loginCheck(LoginAttemptRepository $loginAttemptRepository, Request $request): Response
    {
        $email = $request->request->get('_username');
        $loginAttemptRepository->resetAttempts($email);

        return $this->redirectToRoute('app_homepage');
    }
}
