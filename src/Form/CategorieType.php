<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', TextType::class,[
                "required"=>false,
                "label"=>"Nom de la catégorie",
                "attr"=>["placeholder"=>"saisir le nom de la catégorie"
                ]
            ])
            ->add('propriete', TextType::class,[
                "required"=>false,
                "label"=>"Statut de la propriété",
                "attr"=>["placeholder"=>"saisir le statut de la propriété"
                ]
            ])
        ;
    }

    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
