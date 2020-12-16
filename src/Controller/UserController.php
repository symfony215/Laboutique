<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private  $entityManager;

    public function __construct(EntityManagerInterface  $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/compte", name="account")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/compte/modifier_motDePasse", name="account_password")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $old_pwd = $form->get('old_password')->getData();

            if ($encoder->isPasswordValid($user,$old_pwd))
            {
                $new_pwd = $form->get('new_password')->getData();
                $password = $encoder->encodePassword($user,$new_pwd);

                $user->setPassword($password);
                $this->entityManager->flush();
                $this->addFlash('success', 'Mot de passe modifié avec succès!');
            }
            else
            {
                $this->addFlash('error', 'Veuillez entrez le bon mot de passe a modifier!!');

            }
        }

        return $this->render('user/password.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
