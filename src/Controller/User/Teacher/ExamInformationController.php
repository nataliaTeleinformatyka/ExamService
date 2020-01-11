<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 08.12.2019
 * Time: 14:40
 */

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
    public function examListCreate()
    {
        $examInformation = new ExamRepository();
        $id = $examInformation->getQuantity();
        if ($id > 0) {
            for ($i = 0; $i < $id; $i++) {
                $exams = $examInformation->getExam($i);
                if ($exams['created_by'] == 0/*$_SESSION['user_id']*/){ //todo: Pobieranie id uzytkownika z sesji
                    if ($exams['learning_required'] == 1) {
                        $is_required = true;
                    } else {
                        $is_required = false;
                    }
                    $durationOfExam = $exams['duration_of_exam'];
                    $accessTime = date("H",strtotime($durationOfExam['date']))*60 + date("i",strtotime($durationOfExam['date']));

                    $tplArray[$i] = array(
                        'id' => $i,
                        'name' => $exams['name'],
                        'learning_required' => $is_required,
                        'max_questions' => $exams['max_questions'],
                        'max_attempts' => $exams['max_attempts'],
                        'duration_of_exam' => $accessTime." minut",
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
    public function examInfoCreate(Request $request)
    {
        $examInformation = new ExamRepository();
        $examId = $request->attributes->get('exam');
        $exams = $examInformation->getExam($examId);
        if ($exams['learning_required'] == 1) {
            $is_required = "Tak";
        } else {
            $is_required = "Nie";
        }

        $examInfoArray[0] = array(
            'id' => $examId,
            'name' => $exams['name'],
            'learning_required' => $is_required,
            'max_questions' => $exams['max_questions'],
            'max_attempts' => $exams['max_attempts'],
            'duration_of_exam' => date("H",strtotime($exams['duration_of_exam']['date']))*60 + date("i",strtotime($exams['duration_of_exam']['date']))." minut",
            'start_date' => date("Y-m-d",strtotime( $exams['start_date']['date'])),
            'end_date' => date("Y-m-d",strtotime( $exams['end_date']['date'])),
            'additional_information' => $exams['additional_information']
        );

        $questionInformation= new QuestionRepository();
        $idQuestion = $questionInformation -> getQuantity($examId);
        if($idQuestion>0) {
            for ($i = 0; $i < $idQuestion; $i++) {
                $questions = $questionInformation->getQuestion($examId,$i);
                $questionArray[$i] = array(
                    'id' => $i,
                    'exam_id' => $questions['exam_id'],
                    'content' => $questions['content'],
                );
            }
        } else {
            $questionArray = array(
                'id' => 0,
                'exam_id' => 0,
                'content' => 0,
            );
        }

        $learningMaterialsGroupExamInformation = new LearningMaterialsGroupExamRepository();
        $learningMaterialsGroupInformation= new LearningMaterialsGroupRepository();
        $id = $learningMaterialsGroupExamInformation->getQuantity();
        if($id>0) {
            $informationMaterialGroupExam = false;
            for ($i = 0; $i < $id; $i++) {
                $amount=0;
                $learningMaterialsGroupExam = $learningMaterialsGroupExamInformation->getLearningMaterialsGroupExam($i);
                if ($learningMaterialsGroupExam['exam_id'] == $examId){
                    $informationMaterialGroupExam = true;
                    $learning_materials_group_id = $learningMaterialsGroupExam['learning_materials_group_id'];
                    $exam_id = $learningMaterialsGroupExam['exam_id'];

                    $learningMaterialsGroup = $learningMaterialsGroupInformation->getLearningMaterialsGroup($learning_materials_group_id);
                    $materialsGroupArray[$amount] = array(
                        'id' => $learningMaterialsGroup['learning_materials_groups_id'],
                        'name_of_group' => $learningMaterialsGroup['name_of_group'],
                    );
                    $amount++;
                }
            }
        } else {
                $learning_materials_group_id = "";
                $exam_id = "";
        }

        $userInformation= new UserRepository();
        $userId = $userInformation -> getQuantity();
        if ($userId > 0) {
            $userExamInformation = new UserExamRepository();
            $userExamId = $userExamInformation -> getQuantity();
            if ($userExamId > 0) {
                $k =0;
                for ($i = 0; $i < $userExamId; $i++) {
                    $userExam = $userExamInformation->getUserExam($i);
                    if($userExam['exam_id'] == $examId) {
                        $informationUserExam = true;
                        $users = $userInformation->getUser($userExam['user_id']);
                        print_r($users);
                        $userExamArray[$k] = array(
                            'user_exam_id' => $userExamId,
                            'user_id' => $userExam['user_id'],
                            'first_name' => $users['first_name'],
                            'last_name' => $users['last_name'],
                            'group_of_students' => $users['group_of_students'],
                            'email' => $users['email']
                        );
                        $k++;
                    } else {
                        $informationUserExam = false;
                    }
                }
            }
        }
        return $this->render('teacherExamInfo.html.twig', array(
            'data' => $examInfoArray,
            'question_data' => $questionArray,
            'materials_group_data'=> $materialsGroupArray,
            'user_info_data' => $userExamArray,
            'exam_id' => $exam_id,
            'informationMaterialGroupExam' => $informationMaterialGroupExam,
            'informationUserExam' => $informationUserExam,
        ));

    }
}