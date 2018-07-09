<?php
// src/Form/ProductType.php
namespace App\Form;

use App\Entity\Emaillist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('list_name', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'width: 70%;'], // for input
                'label_attr' => ['class' => ' col-form-label col-form-label-sm'], // for label

            ])
            ->add('file', FileType::class, [
                'attr' => ['class' => 'custom-file-input', 'id'=>'inputGroupFile02'],  // for input
            ] )// ...
            ->add('save', SubmitType::class, ['label' => 'Save List',
                'attr' => ['class' => 'btn btn-success'],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Emaillist::class,
        ));
    }
}