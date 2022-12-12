<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use phpDocumentor\Reflection\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options ): void
    {

        $builder
            ->add('title',TextType::class,[
                    'label'=>'Titre',
                    'required'=>true,
                    'attr'=> [
                              'placeholder'=>'Saisir le titre'
                             ]
            ])
            ->add('description',TextareaType::class,[
                'label'=>'Description',
                'required'=> true,
               
            ])
            ->add('price',MoneyType::class,[
                        'label'=>'Prix' ,
                        'required'=>true,
            ])

            ->add('category',EntityType::class,[
                'label'=>'Catégorie' ,
                'required'=>true,
                'class'=> Category::class, //permet de lier l input a une entité
                'choice_label' => function ($category) {
                    return $category->getName();},


            ])
            ->add('Submit',SubmitType::class,[
                     'label'=>'Soumettre'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
