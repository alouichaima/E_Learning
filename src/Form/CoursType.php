<?php

namespace App\Form;

use App\Entity\Cours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Enseignant;
use App\Entity\Apprenant;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Categorie;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre_cours')
            ->add('image',FileType::class,array('data_class'=> null, 'label' => 'Image','label' => false ))
            ->add('video',FileType::class,array('data_class'=> null, 'label' => 'Video','label' => false ))
            ->add('id_enseignant',EntityType::class, [
                'class'=> Enseignant::class,
                'choice_label'=>'nom',
                'expanded'=>false,
                'multiple'=>false
            ])
            ->add('id_categorie',EntityType::class, [
                'class'=> Categorie::class,
                'choice_label'=>'nom',
                'expanded'=>false,
                'multiple'=>false
            ])
            ->add('apprenant',CollectionType::class, [
                'entry_type'=> ChoiceType::class
            ])
            ->add('prix')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
