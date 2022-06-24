<?php

namespace App\Controller;

use App\Model\ChangePassword;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CompteController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em
    ) {}

    #[Route('/mon-compte', name: 'app_moncompte')]
    public function modifierPassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $changePassword = new ChangePassword();
        $user = $this->getUser();

        $form = $this->createForm('App\Form\ModifyPasswordType', $changePassword);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $password = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user,$password);
            $user->setPassword($hashedPassword);

            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('password_changed','Votre mot de passe a bien été modifié');
        }

        return $this->renderForm('compte/mon-compte.html.twig', [
            'form' => $form,
        ]);
    }
}
