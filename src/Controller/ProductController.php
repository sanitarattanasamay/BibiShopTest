<?php

namespace App\Controller;

use App\Classes\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em=$em;
    }

    /**
     * @Route("/product", name="products")
     */
    public function index(Request $request): Response
    {
        $products=$this->em->getRepository(Product::class)->findAll();
        $search= new Search();
        $form = $this->createForm(SearchType::class,$search);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // $search=$form->getData();
            // dd($search);
            $products=$this->em->getRepository(Product::class)->findWithSearch($search);
        }
        return $this->render('product/index.html.twig',[
            'products'=>$products,
            'form' =>$form->createView()
        ]);
    }

    /**
     * @Route("/product/{slug}", name="product")
     */
    public function show($slug): Response
    {
        $product=$this->em->getRepository(Product::class)->findOneBySlug($slug);        

        if(!$product)
        {
            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig',[
            'product'=>$product
        ]);
    }
}
