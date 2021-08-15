<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Adress;
use App\Form\AdressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAdressController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em=$em;
    }
    /**
     * @Route("/account/adress", name="account_adress")
     */
    public function index(): Response
    {
        return $this->render('account/adress.html.twig');
    }

    /**
     * @Route("/account/adress/add", name="account_adress_add")
     */
    public function add(Cart $cart,Request $request): Response
    {
        $adress= new Adress();
        $form=$this->createForm(AdressType::class, $adress);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $adress->setUser($this->getUser());
            $this->em->persist($adress);
            $this->em->flush();
            if($cart->get()){
                return $this->redirectToRoute('order');
            }else{
                return $this->redirectToRoute('account_adress');
            }            
        }

        return $this->render('account/adress_form.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/adress/edit/{id}", name="account_adress_edit")
     */
    public function edit(Request $request,$id): Response
    {
        $adress= $this->em->getRepository(Adress::class)->findOneById($id);
        if(!$adress|| $adress->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_adress');
        }
        $form=$this->createForm(AdressType::class, $adress);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->flush();
            return $this->redirectToRoute('account_adress');
        }

        return $this->render('account/adress_form.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/adress/delete/{id}", name="account_adress_delete")
     */
    public function delete($id): Response
    {
        $adress= $this->em->getRepository(Adress::class)->findOneById($id);
        if($adress && $adress->getUser() == $this->getUser()){
            $this->em->remove($adress);
            $this->em->flush();
        }

        return $this->redirectToRoute('account_adress');
    }
}
