<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Carrier;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em=$em;
    }
    /**
     * @Route("/order", name="order")
     */
    public function index(Cart $cart): Response
    {
        if(!$this->getUser()->getAdresses()->getValues()){
            return $this->redirectToRoute('account_adress_add');
        }
        $form=$this->createForm(OrderType::class, null, [
            'user'=>$this->getUser()
        ]);
       
        return $this->render('order/index.html.twig',[
            'form'=>$form->createView(),
            'cart'=>$cart->getFull()
        ]);
    }

    /**
     * @Route("/order/recapitulatif", name="order_recap", methods={"POST"})
     */
    public function add(Request $request, Cart $cart): Response
    {
        $form=$this->createForm(OrderType::class, null, [
            'user'=>$this->getUser()
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $date = new \DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('adresses')->getData();

            $deliveryContent =  $delivery->getFirstname().' '.$delivery->getLastname();
            $deliveryContent.='<br>'.$delivery->getPhone();
            if($delivery->getCompany()){
                $deliveryContent.='<br>'.$delivery->getCompany();
            }
            $deliveryContent.='<br>'.$delivery->getAdress();
            $deliveryContent.='<br>'.$delivery->getPostal().' '.$delivery->getCity();
            $deliveryContent.='<br>'.$delivery->getCountry();

            $order =new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($deliveryContent);
            $order->setIsPaid(0);
            $this->em->persist($order);

            foreach($cart->getFull() as $product){
                $orderDetails=new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice()*$product['quantity']);
                
                $this->em->persist($orderDetails);                
            }
            $this->em->flush();    
            return $this->render('order/add.html.twig',[
                'cart'=>$cart->getFull(),
                'carrier'=>$carriers,
                'delivery'=>$deliveryContent
            ]);
        }
        return $this->redirectToRoute('cart');
    }
}
