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
        switch ($_SESSION['role']) {
            case "ROLE_PROFESSOR" :
                {
                    $choices['Student'] = 'student';
                    break;
                }
            case "ROLE_ADMIN" :
                {
                    $choices['Admin'] = 'admin';
                    $choices['Professor'] = 'professor';
                    $choices['Student'] = 'student';
                    break;
                }
            default:{
                $choices['Student'] = 'student';
                break;
            }
        }
        $builder
            ->add('password', PasswordType::class,  [
                'help' => 'The password must be a string with at least 6 characters.'
            ])
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class,[
                'choices'  => $choices
            ])
            ->add('group_of_students', TextType::class, [
                'attr' => ['id' => 'group_of_students'],
            ])
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
        return 'add_user';
    }
}