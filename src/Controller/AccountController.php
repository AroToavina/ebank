<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    #[Route('/delete_account', name: 'delete_account', methods: ['POST'])]
    public function deleteAccount(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été supprimé avec succès.');

            return $this->redirectToRoute('app_home');
        }

        $this->addFlash('error', 'Une erreur s\'est produite lors de la suppression de votre compte.');
        return $this->redirectToRoute('app_account');
    }
}
