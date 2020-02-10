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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('password', PasswordType::class,  [
                'help' => 'The password must be a string with at least 6 characters.'
            ])
            ->setMethod('POST')
            ->setAction('')
            ->getForm();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();
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
                default:
                    {
                        $choices['Student'] = 'student';
                        break;
                    }
            }

            if (!$user || null === $user->getId()) {
                $form
                    ->add('first_name', TextType::class)
                    ->add('last_name', TextType::class)
                    ->add('email', EmailType::class)
                    ->add('roles', ChoiceType::class, [
                        'choices' => $choices
                    ])
                    ->add('group_of_students', TextType::class, [
                        'attr' => ['id' => 'group_of_students']
                    ]);
                $label = "Add user";
            } else {
                if($_SESSION['role']=="ROLE_ADMIN"){
                    $form
                        ->add('first_name', TextType::class)
                        ->add('last_name', TextType::class)
                        ->add('email', EmailType::class);

                        if($user->getRoles()=="ROLE_STUDENT") {
                         $form
                             ->add('group_of_students', TextType::class, [
                                'attr' => ['id' => 'group_of_students']
                            ]);
                        }
                }
                $label = "Edit user";
            }
            $form->add('save', SubmitType::class, ['label' => $label]);
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
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