<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 18.11.2019
 * Time: 20:14
 */

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login')
            ->add('password')
            ->add('save', 'submit');
    }
    public function getName()
    {
        return 'login';
    }
    public function configureOptions(OptionsResolver $resolver)
    {
     /*   $resolver->setDefaults([
            'data_class' => User::class,
        ]);*/
    }
}
//php -S 127.0.0.1:8000 -t public