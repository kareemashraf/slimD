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
            ->add('list_name', TextType::class, array('label' => 'List Name : '))
            ->add('file', FileType::class, array('label' => 'List (CSV file)'))// ...
            ->add('save', SubmitType::class, array('label' => 'Save List'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Emaillist::class,
        ));
    }
}