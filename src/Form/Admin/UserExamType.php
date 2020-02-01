<?php

namespace App\Form\Admin;

use App\Entity\Admin\UserExam;
use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserExamType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    //todo nie mozna edytowac !
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $user = new UserRepository();

        $idUsers = $user->getIdUsers();
        if($idUsers==0){
            $amount =0;
        } else {
            $amount = count($idUsers);
        }
        for ($i = 0; $i < $amount; $i++) {
            $values = $user->getUser($idUsers[$i]);
            if ($values['role'] == "ROLE_STUDENT") {
                $userInfo[$i . " - " . $values['first_name'] . " " . $values['last_name']] = $values['id'];
            }
        }

        $exams= new ExamRepository();
        $examsId = $exams->getIdExams();
        if($examsId==0){
            $examsAmount =0;
        } else
            $examsAmount=count($examsId);

        for($j=0;$j<$examsAmount;$j++) {
            $valuesExam = $exams->getExam($examsId[$j]);
            $name = $valuesExam['name'];
            $examInfo[$j." - ".$name] = $valuesExam['exam_id'];
        }

        $builder
            ->add('user_id', ChoiceType::class,
                [
                    'choices'  => $userInfo ])
            ->add('exam_id', ChoiceType::class,
                [
                    'choices'  => $examInfo])
            ->add('save', SubmitType::class, ['label' => 'Add userExam'])
            ->setMethod('POST')
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => UserExam::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName() {
        return 'user_exam_add';
    }
}