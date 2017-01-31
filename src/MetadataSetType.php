<?php

namespace RedIRIS\MetadataCenter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MetadataSetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre',
                'attr' => [
                    'placeholder' => 'abc-defg',
                ],
                'constraints' => [ 
                    new Assert\NotBlank(), new Assert\Regex(['pattern' => '/^[-_\w]+$/'])
                ],
            ])
            ->add('url', UrlType::class, [
                'attr' => [
                    'placeholder' => 'https://url',
                ],
                'constraints' => [ new Assert\NotBlank() ],
            ])
            ->add('filter', TextType::class, [
                'label' => 'Filtro',
                'empty_data' => 'md:EntityDescriptor[md:IDPSSODescriptor]',
                'constraints' => [ new Assert\NotBlank() ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => \RedIRIS\MetadataCenter\MetadataSet::class,
        ));
    }
}
