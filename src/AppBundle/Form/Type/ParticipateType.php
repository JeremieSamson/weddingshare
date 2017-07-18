<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipateType extends AbstractType
{
    /**
     * Build the form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, array(
                'class' => 'AppBundle\Entity\Category',
                'label' => 'Catégorie'
            ))
            ->add('name', TextType::class, array(
                "label" => "Nom / Surnom"
            ))
            ->add('file', FileType::class, array(
                "label" => "Fichier"
            ))
        ;
    }

    /**
     * Configure form options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Form\Model\Participate',
                'csrf_protection'   => false
            )
        );
    }

    /**
     * Get the form name
     *
     * @return string
     */
    public function getName()
    {
        return 'form_address';
    }
}