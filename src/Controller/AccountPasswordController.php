<?php

namespace App\Controller;

use App\Form\EditPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{
    private $entityManager;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/account/edit_password", name="account_password")
     */
    public function index(Request $request,UserPasswordHasherInterface $passwordHasher): Response
    {
        $message=null;
        $user=$this->getUser();
        $form = $this->createForm(EditPasswordType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $old_pwd=$form->get('old_password')->getData();
            if($passwordHasher->isPasswordValid($user,$old_pwd)){
                $new_pwd = $form->get('new_password')->getData();
                $user->setPassword($passwordHasher->hashPassword(
                    $user,
                    $new_pwd
                ));

                $this->entityManager->persist($user);
                $this->entityManager->flush();  
                $message='Mot de passe modifié';
            }else{
                $message = 'Le mot de passe ne correspond pas au mot de passe actuel';
            }
        }
        return $this->render('account/password.html.twig',[
            'form'=>$form->createView(),
            'message'=>$message
        ]);
    }
}
