<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * Allow to get the configuration of fields in the form
     *
     * @param string $label
     * @param string $placeholder
     * @return array
     */
    private function getConfiguration($label, $placeholder): array
    {
        return array(
            'attr' => array(
                'label' => $label,
                'placeholder' => $placeholder
            ),
        );
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration('Nom du produit', 'Entrer le nom du produit'))
            ->add('description', TextareaType::class, $this->getConfiguration('Nom de la description', 'Entrer le nom de la description'))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix du produit', 'Le prix dub product'))
            ->add('stock', IntegerType::class, $this->getConfiguration('Le stock du produit', 'Entrer la quantité du produit'))
            ->add('photo', FileType::class, ['data_class' => null])
            //->add('cartContent')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
