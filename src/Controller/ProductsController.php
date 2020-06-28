<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductsController extends AbstractController
{
    /**
     * @Route("/products", name="app_products_index")
     * @param ProductRepository $repository
     * @return Response
     */
    public function index(ProductRepository $repository): Response
    {
        $products = $repository->findAll();
        return $this->render('products/index.html.twig', compact('products'));
    }

    /**
     * @Route("/products/{id<\d+>}", name="app_products_show", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return $this->render('products/show.html.twig', compact('product'));
    }
    /**
     * Action to create a new product
     *
     * @Route("/products/new", name="app_products_new", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();

        // Grab the actual connected user
        $user = $this->getUser();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // retrieve the photo uploaded and set its name
            $photo = $form->get('photo')->getData();
            $photoName = md5(uniqid("product__", true)) . '.' . $photo->guessExtension();
            $photo->move($this->getParameter('productPhoto'), $photoName);
            $product->setPhoto($photoName);

            // Save product on the DB
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', "Le produit a été ajouté à la boutique avec succès!");
            return $this->redirectToRoute('app_home');
        }

        return $this->render("products/new.html.twig", [
            "form" => $form->createView(),
            "user" => $user
        ]);
    }

    /**
     * @Route("/products/{id<\d+>}/edit", name="app_products_edit", methods={"GET|POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Product $product
     * @param TranslatorInterface $translator
     * @return  Response
     */
    public function edit(Request $request, EntityManagerInterface $em, Product $product, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        //$product->setPhoto(new File($this->getParameter('productPhoto') . '/' . $product->getPhoto()));

        if($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $photoData = $form->get('photo')->getData();
            $photoName = md5(uniqid("product__", true)) . '.' . $photoData->guessExtension();
            $photoData->move($this->getParameter("productPhoto"), $photoName);
            $product->setPhoto($photoName);

            $em->flush();

            $this->addFlash('success', $translator->trans('edit_message_success'));

            return $this->redirectToRoute('app_products_show', ['id' => $product->getId()]);
        }

        return $this->render('products/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }


    /**
     * Action to remove a product
     *
     * @Route("/products/{id<\d+>}", name="app_products_delete", methods="DELETE")
     * @param Product $product
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Product $product, EntityManagerInterface $em, Request $request, TranslatorInterface $translator):Response
    {
        if ($this->isCsrfTokenValid('product_deletion_' . $product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();
        }

        $this->addFlash('success', $translator->trans('delete_message'));
        return $this->redirectToRoute('app_products_index');
    }

}
