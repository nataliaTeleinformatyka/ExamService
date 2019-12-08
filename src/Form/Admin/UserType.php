<?php
namespace App\Form\Admin;

use App\Entity\Admin\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 18.11.2019
 * Time: 21:02
 */

class UserType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //todo:password min 6 znakow
        $builder
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('email', EmailType::class) //todo: nie moze sie zarejestrowac jesli email juz w bazie
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'Admin' =>'admin',
                    'Teacher' => 'teacher',
                    'Student' => 'student',
                ]])
            ->add('class', TextType::class) //todo:ograniczenie - klasa gdy wczesniej wybrano role student,inaczej niewidoczne
            ->add('save', SubmitType::class, ['label' => 'Add User'])
            ->setMethod('POST')
            ->setAction('')
            ->getForm();
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName() {
        return 'user_add';
    }
}