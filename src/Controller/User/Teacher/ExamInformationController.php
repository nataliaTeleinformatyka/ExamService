<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 08.12.2019
 * Time: 14:40
 */

namespace App\Controller\User\Teacher;



use App\Entity\Admin\Exam;
use App\Form\Admin\ExamType;
use App\Repository\Admin\AnswerRepository;
use App\Repository\Admin\ExamRepository;
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
                if ($exams['created_by'] == 0){ //todo: Pobieranie id uzytkownika z sesji
                    if ($exams['learning_required'] == 1) {
                        $is_required = true;
                    } else {
                        $is_required = false;
                    }
                    $tplArray[$i] = array(
                        'id' => $i,
                        'name' => $exams['name'],
                        'learning_required' => $is_required,
                        'min_questions' => $exams['min_questions'],
                        'max_attempts' => $exams['max_attempts'],
                        'duration_of_exam' => $exams['duration_of_exam']['date'], //todo: only time!!
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
                    'min_questions' => "",
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
            $is_required = true;
        } else {
            $is_required = false;
        }
        $examInfoArray[0] = array(
            'id' => $examId,
            'name' => $exams['name'],
            'learning_required' => $is_required,
            'min_questions' => $exams['min_questions'],
            'max_attempts' => $exams['max_attempts'],
            'duration_of_exam' => $exams['duration_of_exam']['date'], //todo: only time!!
            'start_date' => $exams['start_date']['date'],
            'end_date' => $exams['end_date']['date'],
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
        $learningMaterialsGroupInformation= new LearningMaterialsGroupRepository();
        $id = $learningMaterialsGroupInformation -> getQuantity();
        if($id>0) {
            for ($i = 0; $i < $id; $i++) {
                $learningMaterialsGroup = $learningMaterialsGroupInformation->getLearningMaterialsGroup($i);
                if ($learningMaterialsGroup['exam_id'] == $examId){
                    $materialsGroupArray[$i] = array(
                        'id' => $i,
                        'name_of_group' => $learningMaterialsGroup['name_of_group'],
                        'exam_id' => $learningMaterialsGroup['exam_id'],
                    );
                }
            }
        } else {
            $materialsGroupArray = array(
                'id' => "",
                'name_of_group' => "",
                'exam_id' => "",

            );
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
                        $users = $userInformation->getUser($i);
                        $userExamArray[$k] = array(
                            'user_id' => $userExam['user_id'],
                            'first_name' => $users['first_name'],
                            'last_name' => $users['last_name'],
                            'class' => $users['class'],
                            'email' => $users['email']
                        );
                        $k++;
                    }
                }
            }
            print_r($userExamArray);
        }
        return $this->render('teacherExamInfo.html.twig', array(
            'data' => $examInfoArray,
            'question_data' => $questionArray,
            'materials_group_data'=> $materialsGroupArray,
            'user_info_data' => $userExamArray
        ));

    }



    /**
     * @param Request $request
     * @Route("/delete/{exam}", name="deleteByTeacher")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteExam(Request $request)
    {
        $id = $request->attributes->get('exam');
        $repo = new ExamRepository();
        $repo->delete($id);
        //todo: redirect to examList nie usuwa zapytania ktore jest jako 1
        //todo: zapytanie czy chce usunac egzamin gdy sa powiazane question i answers
        //todo: nie mozna usunac egzaminu, gdy jest powiazanie userexam, result

        return $this->redirectToRoute('teacherExamList');
    }
}