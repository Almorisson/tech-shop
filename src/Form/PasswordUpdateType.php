<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateType extends AbstractType
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
            ->add('oldPassword', PasswordType::class, $this->getConfiguration('Ancien mot de passe', 'Entrer votre mot mot de passe actuel'))
            ->add('newPassword', PasswordType::class, $this->getConfiguration('Nouveau mot de passe', 'Entrer votre nouveau mot de passe'))
            ->add('confirmPassword', PasswordType::class, $this->getConfiguration('Confirmation du nopuveau mot de passe', 'Enter le mot de passe de confirmation du nouveau mot de passe'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
