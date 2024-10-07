<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class FundController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function account()
    {
        $user = $this->getUser();

        return $this->render('account/index.html.twig', [
            'balance' => $user->getBalance(),
        ]);
    }

    #[Route('/account/add', name: 'add_funds', methods: ['POST'])]
    public function addFunds(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $amount = (float) $request->request->get('amount');

        if ($amount > 0) {
            $user->addToBalance($amount);
            $em->flush();

            $this->addFlash('success', 'Fonds ajoutés avec succès.');
        }

        return $this->redirectToRoute('app_account');
    }

    #[Route('/account/withdraw', name: 'withdraw_funds', methods: ['POST'])]
    public function withdrawFunds(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $amount = (float) $request->request->get('amount');

        if ($amount > 0 && $user->getBalance() >= $amount) {
            $user->subtractFromBalance($amount);
            $em->flush();

            $this->addFlash('success', 'Fonds retirés avec succès.');
        } else {
            $this->addFlash('error', 'Montant insuffisant.');
        }

        return $this->redirectToRoute('app_account');
    }
}
