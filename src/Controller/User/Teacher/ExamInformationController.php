<?php

namespace App\Controller\User\Teacher;

use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\LearningMaterialsGroupExamRepository;
use App\Repository\Admin\LearningMaterialsGroupRepository;
use App\Repository\Admin\QuestionRepository;
use App\Repository\Admin\UserExamRepository;
use App\Repository\Admin\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExamInformationController extends AbstractController
{
    /**
     * @Route("teacherExamList", name="teacherExamList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function examListCreate() {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        switch ($_SESSION['role']) {
            case "ROLE_ADMIN":
                {
                    return $this->redirectToRoute('examList');
                    break;
                }
            case "ROLE_STUDENT":
                {
                    return $this->redirectToRoute('studentHomepage');
                    break;
                }
        }

        $_SESSION['exam_id'] = "";
        $examRepository = new ExamRepository();
        $examsId = $examRepository->getIdExams();

        if($examsId!=0){
            $examsCount = count($examsId);
        } else {
            $examsCount=0;
        }

        if ($examsCount > 0) {
            for ($i = 0; $i < $examsCount; $i++) {
                $exams = $examRepository->getExam($examsId[$i]);
                if ($exams['created_by'] == $_SESSION['user_id'] or $exams['created_by'] == -1){
                    if ($exams['learning_required'] == 1) {
                        $is_required = "true";
                    } else {
                        $is_required = "false";
                    }

                    $tplArray[$i] = array(
                        'id' => $exams['exam_id'],
                        'name' => $exams['name'],
                        'learning_required' => $is_required,
                        'max_questions' => $exams['max_questions'],
                        'max_attempts' => $exams['max_attempts'],
                        'duration_of_exam' => $exams['duration_of_exam'], //$accessTime." minut",
                        'start_date' => $exams['start_date']['date'],
                        'end_date' => $exams['end_date']['date'],
                        'additional_information' => $exams['additional_information']
                    );
                }
            }
        } else {
                $tplArray = array(
                    'id' => "",
                    'name' => "",
                    'learning_required' => "",
                    'max_questions' => "",
                    'max_attempts' => "",
                    'duration_of_exam' => "",
                    'start_date' => "",
                    'end_date' => "",
                    'additional_information' => ""
                );
            }
        return $this->render('teacherExamList.html.twig', array(
            'data' => $tplArray
        ));

    }

    /**
     * @Route("teacherExamInfo/{exam}", name="teacherExamInfo")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function examInfoCreate(Request $request) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        switch ($_SESSION['role']) {
            case "ROLE_ADMIN":
                {
                    return $this->redirectToRoute('examList');
                    break;
                }
            case "ROLE_STUDENT":
                {
                    return $this->redirectToRoute('studentHomepage');
                    break;
                }
        }

        $examInformation = new ExamRepository();
        $examId = $request->attributes->get('exam');
        $_SESSION['exam_id'] = $examId;
        $_SESSION['question_id']="";
        $exams = $examInformation->getExam($examId);
        $existQuestions = false;
        if ($exams['learning_required'] == 1) {
            $is_required = "Tak";
        } else {
            $is_required = "Nie";
        }
        $startDate = date("Y-m-d",strtotime( $exams['start_date']['date']));
        $endDate = date("Y-m-d",strtotime( $exams['end_date']['date']));
        if(date("Y",strtotime( $exams['start_date']['date'])) <'2020')
            $startDate=" - ";
        if(date("Y",strtotime( $exams['end_date']['date'])) <'2020')
            $endDate=" - ";

            $examInfoArray[0] = array(
            'id' => $examId,
            'name' => $exams['name'],
            'learning_required' => $is_required,
            'max_questions' => $exams['max_questions'],
            'max_attempts' => $exams['max_attempts'],
            'duration_of_exam' => $exams['duration_of_exam'],
            'percentage_passed_exam' => $exams['percentage_passed_exam'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'additional_information' => $exams['additional_information']
        );

        $questionRepository= new QuestionRepository();
        $questionsId = $questionRepository->getIdQuestions($examId);
        if($questionsId!=0){
            $questionsCount = count($questionsId);
        } else {
            $questionsCount=0;
        }
        if($questionsCount>0) {
            $existQuestions = true;
            for ($i = 0; $i < $questionsCount; $i++) {
                $questions = $questionRepository->getQuestion($examId,$questionsId[$i]);
                $questionArray[$i] = array(
                    'id' => $questions['id'],
                    'exam_id' => $questions['exam_id'],
                    'content' => $questions['content'],
                );
            }
        } else {
            $questionArray = array(
                'id' => '',
                'exam_id' => '',
                'content' => '',
            );
        }

        $learningMaterialsGroupExamInformation = new LearningMaterialsGroupExamRepository();
        $learningMaterialsGroupInformation= new LearningMaterialsGroupRepository();

        $learningMaterialsGroupExamsId = $learningMaterialsGroupExamInformation->getIdLearningMaterialsGroupExams();
        if($learningMaterialsGroupExamsId!=0){
            $learningMaterialsGroupExamsCount = count($learningMaterialsGroupExamsId);
        } else {
            $learningMaterialsGroupExamsCount=0;
        }

        if($learningMaterialsGroupExamsCount>0) {
            $informationMaterialGroupExam = false;
            $amount=0;

            for ($i = 0; $i < $learningMaterialsGroupExamsCount; $i++) {
                $learningMaterialsGroupExam = $learningMaterialsGroupExamInformation->getLearningMaterialsGroupExam($learningMaterialsGroupExamsId[$i]);
                if ($learningMaterialsGroupExam['exam_id'] == $examId){
                    $informationMaterialGroupExam = true;
                    $learning_materials_group_id = $learningMaterialsGroupExam['learning_materials_group_id'];

                    $learningMaterialsGroup = $learningMaterialsGroupInformation->getLearningMaterialsGroup($learning_materials_group_id);
                    $materialsGroupArray[$amount] = array(
                        'id' => $learningMaterialsGroup['learning_materials_groups_id'],
                        'name_of_group' => $learningMaterialsGroup['name_of_group'],
                    );
                    $amount++;
                } else {
                    if($i==$learningMaterialsGroupExamsCount-1 and $informationMaterialGroupExam==false){
                        $materialsGroupArray[] = array(
                            'id' => '',
                            'name_of_group' => '',
                        );
                    }
                }
            }
        } else {
            $materialsGroupArray[] = array(
                'id' => '',
                'name_of_group' => '',
            );
        }

        $userInformation= new UserRepository();
        $usersId = $userInformation->getIdUsers();
        if($usersId!=0){
            $usersCount = count($usersId);
        } else {
            $usersCount=0;
        }
        if ($usersCount > 0) {
            $userExamInformation = new UserExamRepository();
            $userExamsId = $userExamInformation->getIdUserExams();
            if($userExamsId!=0){
                $userExamsCount = count($userExamsId);
            } else {
                $userExamsCount=0;
            }

            if ($userExamsCount > 0) {
                $informationUserExam = false;
            print_r($userExamsCount);
                $k =0;
                for ($i = 0; $i < $userExamsCount; $i++) {
                    $userExam = $userExamInformation->getUserExam($userExamsId[$i]);
                    if($userExam['exam_id'] == $examId) {
                        $informationUserExam = true;
                        $users = $userInformation->getUser($userExam['user_id']);
                        print_r($userExam['user_id']);
                        $userExamArray[$k] = array(
                            'user_exam_id' => $userExam['user_exam_id'],
                            'user_id' => $userExam['user_id'],
                            'first_name' => $users['first_name'],
                            'last_name' => $users['last_name'],
                            'group_of_students' => $users['group_of_students'],
                            'email' => $users['email']
                        );
                        $k++;
                    } else {
                        if($i==$userExamsCount-1 and $informationUserExam == false){
                            print_r("PPPP");
                            $userExamArray[] = array(
                                'user_exam_id' => '',
                                'user_id' => '',
                                'first_name' => '',
                                'last_name' => '',
                                'group_of_students' => '',
                                'email' => ''
                            );
                        }
                    }
                }
            } else {
                $userExamArray[] = array(
                    'user_exam_id' => '',
                    'user_id' => '',
                    'first_name' => '',
                    'last_name' => '',
                    'group_of_students' => '',
                    'email' => ''
                );
            }
        } else {
            $userExamArray[] = array(
                'user_exam_id' => '',
                'user_id' => '',
                'first_name' => '',
                'last_name' => '',
                'group_of_students' => '',
                'email' => ''
            );
        }
        print_r($userExamArray);
        return $this->render('teacherExamInfo.html.twig', array(
            'data' => $examInfoArray,
            'information_question' => $existQuestions,
            'question_data' => $questionArray,
            'materials_group_data'=> $materialsGroupArray,
            'user_info_data' => $userExamArray,
            'exam_id' => $examId,
            'informationMaterialGroupExam' => $informationMaterialGroupExam,
            'informationUserExam' => $informationUserExam,
        ));

    }
}