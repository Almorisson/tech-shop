<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Produit'
            ])
            ->add('description', FloatType::class, [
                'label' => 'Description'
            ])
            ->add('price' IntegerType::class, [
                'label' => 'Prix'
            ])
            ->add('stock' TextType::class, [
                'label' => 'NombreStock'
            ])
            ->add('photo' TextType::class, [
                'label' => 'Photo'
            ])
            ->add('cartContent' TextType::class, [
                'label' => 'Panier'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
