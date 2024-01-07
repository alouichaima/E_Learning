<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;                                        
use EasyCorp\Bundle\EasyAdminBundle\Form\Type  ;                
 use Symfony\Component\Form\ChoiceList\ChoiceList;
 use Symfony\Component\Form\Extension\Core\Type\HiddenType;
 use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 use Symfony\Component\OptionsResolver\OptionsResolver;


 class CategorieType extends AbstractType
{
     
    public function buildForm(FormBuilderInterface $builder, array $form): void
    {
      
        {
           $builder
           ->add('nom', TextType::class)
           ->add('discription',TextType::class)
           
        ->add('parent', EntityType::class,[
            'class' => Categorie::class,
       
        ])
       ;
        }
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
            
        ]);
    }
}