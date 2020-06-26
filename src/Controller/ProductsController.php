public function index(ProductRepository $repository): Response
    {
        $products = $repository->findAll();
        return $this->render('products/index.html.twig', compact('products'));
    }

    /**
     * @Route("/products/{id<\d+>}", name="app_products_show")
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return $this->render('products/show.html.twig', compact('product'));
    }
    /**
     * Allow to create a new product
     *
     * @Route("/products/new", name="app_products_new")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', "Le produit <strong>{$product->getName()}</strong> a été ajouté à la boutique avec succès!");
            return $this->redirectToRoute('app_products_show', ['id' => $product->getId()]);
        }

        return $this->render("products/new.html.twig", [
            "form" => $form->createView()
        ]);
    }