<?php

namespace App\Form;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', StringType::class, ['label' => 'Nom'])
            ->add('description', StringType::class, ['label' => 'Description'])
            ->add('price', FloatType::class, ['label' => 'Prix'])
            ->add('quantity', IntegerType::class, ['label' => 'Quantité'])
            ->add('image', StringType::class, ['label' => 'URL de l\'image'])
            ->add('category')
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
