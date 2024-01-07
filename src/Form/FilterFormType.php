<?php

namespace App\Form;

use App\Entity\Filter;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;  
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
 

class FilterFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $form): void
    {
      
        {
           $builder
           ->add('mot', TextType::class,[
               'label' => false,
               'required' => false,
               'attr' => [
               'placeholder' => 'Rechercher un cours'
               ]
           ])
           ->add('categorie', EntityType::class,[
               'label' => false,
               'choice_label'=>'nom',
               'required' => false,
               'class' => Categorie::class,
               'expanded' => true,
               'multiple' => true,
             
           ])
           ->add('min', IntegerType::class,[
               'label' => false,
               'required' => false,
               'attr' => [
                   'placeholder' => 'Prix min'
               ]
           ])
           ->add('max', IntegerType::class,[
            'label' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Prix max'
            ]
            ])
            ->add('enregistrer',SubmitType::class);
           
       
        }
    }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => Filter::class,
                
                
            ]);
        }

    



}
