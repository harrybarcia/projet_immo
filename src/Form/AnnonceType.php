<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description_courte')
            ->add('description_longue')
            ->add('prix')
            ->add('surface')
            ->add('adresse')
            ->add('cp')
            ->add('ville')
            ->add('date_enregistrement')
            ->add('image')
                                            
            ->add('categorie', EntityType::class, [ // cet input a une relation avec une autre entity
                "class" => Categorie::class,        // avec quelle entity
                "choice_label" => "type",          // quelle propriété (quel champ) afficher
                "placeholder" => "Saisir une catégorie"
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
