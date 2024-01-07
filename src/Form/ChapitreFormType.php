<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Chapitre;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type  ;                
use Symfony\Component\Form\FormBuilderInterface;                                        
 use Symfony\Component\Form\ChoiceList\ChoiceList;
 use Symfony\Component\Form\Extension\Core\Type\HiddenType;
 use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 use Symfony\Component\OptionsResolver\OptionsResolver;
 


 class ChapitreType extends AbstractType
{
     
    public function buildForm(FormBuilderInterface $builder, array $form): void
    {
      
        {
           $builder
           ->add('nom', TextType::class)
     
           ->add('id_cours', EntityType::class,[
            'class' =>Cours::class,
       
        ])
       ;
        }
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapitre::class,
            
        ]);
    }
}