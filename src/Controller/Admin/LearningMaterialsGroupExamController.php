<?php

namespace App\Controller\Admin;

use App\Entity\Admin\LearningMaterialsGroupExam;
use App\Form\Admin\LearningMaterialsGroupExamType;
use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\LearningMaterialsGroupExamRepository;
use App\Repository\Admin\LearningMaterialsGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LearningMaterialsGroupExamController extends AbstractController
{
    /**
     * @Route("learningMaterialsGroupExam", name="learningMaterialsGroupExam")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request) {
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $learningMaterialsGroupExam= new LearningMaterialsGroupExam([]);
        $learningMaterialsGroupRepository = new LearningMaterialsGroupRepository();
        $examRepository = new ExamRepository();
        $examsId = $examRepository->getIdExams();
        $learningMaterialsGroupsId = $learningMaterialsGroupRepository->getLearningMaterialsGroupId();

        if($examsId==0 or $learningMaterialsGroupsId==0) {
            $_SESSION['information'][] = array( 'type' => 'error', 'message' => ' There are no exams or learning materials groups to assign');
        } else {
            $form = $this->createForm(LearningMaterialsGroupExamType::class, $learningMaterialsGroupExam);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $values = $learningMaterialsGroupExam->getAllInformation();
                $repositoryExam = new LearningMaterialsGroupExamRepository();
                $repositoryExam->insert($values);

                switch ($_SESSION['role']) {
                    case "ROLE_ADMIN":
                        {
                            return $this->redirectToRoute('learningMaterialsGroupExamList');
                            break;
                        }
                    case "ROLE_PROFESSOR":
                        {
                            return $this->redirectToRoute('teacherExamInfo', [
                                'exam' => $_SESSION['exam_id'],
                            ]);
                            break;
                        }
                }
            }
            return $this->render('learningMaterialsGroupExamAdd.html.twig', [
                'form' => $form->createView(),
                'role' => $_SESSION['role'],
            ]);
        }
        return $this->redirectToRoute('learningMaterialsGroupExamList');
    }

    /**
     * @Route("learningMaterialsGroupExamList", name="learningMaterialsGroupExamList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function learningMaterialsGroupExamListCreate() {
        switch ($_SESSION['role']) {
            case "ROLE_STUDENT":
                {
                    return $this->redirectToRoute('studentHomepage');
                    break;
                }
            case "ROLE_PROFESSOR":
                {
                    return $this->redirectToRoute('teacherExamList');
                    break;
                }
        }

        $groupExamRepository = new LearningMaterialsGroupExamRepository();
        $examInformationRepo = new ExamRepository();
        $learningMaterialsGroupRepo= new LearningMaterialsGroupRepository();

        $groupExamsId = $groupExamRepository->getIdLearningMaterialsGroupExams();
        if($groupExamsId!=0){
            $groupExamsCount = count($groupExamsId);
        } else {
            $groupExamsCount=0;
        }

        if ($groupExamsCount > 0) {
            $info = true;
            for ($i = 0; $i < $groupExamsCount; $i++) {
                $groupExam = $groupExamRepository->getLearningMaterialsGroupExam($groupExamsId[$i]);

                $groupInfo = $learningMaterialsGroupRepo->getLearningMaterialsGroup($groupExam['learning_materials_group_id']);
                $examInfo=$examInformationRepo->getExam($groupExam['exam_id']);

                $tplArray[$i] = array(
                    'id' => $groupExam['id'],
                    'learning_materials_group_id' => $groupExam['learning_materials_group_id'],
                    'learning_materials_group_name' => $groupInfo['name_of_group'],
                    'exam_id' => $groupExam['exam_id'],
                    'exam_name' => $examInfo['name']

                );
            }
        } else {
            $info = false;
            $tplArray = array(
                'id' => "",
                'learning_materials_group_id' => "",
                'learning_materials_group_name' => "",
                'exam_id' => "",
                'exam_name' => ""
            );
        }
        if( isset( $_SESSION['information'] ) && count( $_SESSION['information'] ) > 0  ) {
            $infoDelete = $_SESSION['information'];
        } else {
            $infoDelete = "";
        }
        $_SESSION['information'] = array();

        return $this->render('learningMaterialsGroupExamList.html.twig', array(
            'data' => $tplArray,
            'information' => $info,
            'infoDelete' => $infoDelete
        ));
    }
    /**
     * @param Request $request
     * @param LearningMaterialsGroupExam $groupExam

     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("editLearningMaterialsGroupExam/{id}", name="editLearningMaterialsGroupExam")
     */
    public function editLearningMaterialsGroupExam(Request $request, LearningMaterialsGroupExam $groupExam) {
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $groupExamInformation = new LearningMaterialsGroupExamRepository();
        $groupExamId = (int)$request->attributes->get('id');
        $groupExams = $groupExamInformation->getLearningMaterialsGroupExam($groupExamId);

        $examInfoArray = array(
            'learning_materials_group_id' => $groupExams['learning_materials_group_id'],
            'exam_id' => $groupExams['exam_id'],
        );

        $form = $this->createForm(LearningMaterialsGroupExamType::class, $groupExam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $values = $groupExam->getAllInformation();
            $repositoryExam = new LearningMaterialsGroupExamRepository();
            $repositoryExam->update($values,$groupExamId);
            switch ($_SESSION['role']) {
                case "ROLE_ADMIN":
                    {
                        return $this->redirectToRoute('learningMaterialsGroupExamList');
                        break;
                    }
                case "ROLE_PROFESSOR":
                    {
                        return $this->redirectToRoute('teacherExamInfo', [
                            'exam' => $_SESSION['exam_id'],
                        ]);
                        break;
                    }
            }
        }
        return $this->render('learningMaterialsGroupExamAdd.html.twig', [
            'form' => $form->createView(),
            'examInformation' =>$examInfoArray,
            'role' => $_SESSION['role'],
        ]);
    }

    /**
     * @param Request $request
     * @Route("deleteLearningMaterialsGroupExam/{id}", name="deleteLearningMaterialsGroupExam")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteLearningMaterialsGroupExam(Request $request) {
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');
        $id = $request->attributes->get('id');
        $repo = new LearningMaterialsGroupExamRepository();
        $repo->delete($id);
        switch ($_SESSION['role']) {
            case "ROLE_ADMIN":
                {
                    return $this->redirectToRoute('learningMaterialsGroupExamList');
                    break;
                }
            case "ROLE_PROFESSOR":
                {
                    return $this->redirectToRoute('teacherExamInfo', [
                        'exam' => $_SESSION['exam_id'],
                    ]);
                    break;
                }
        }
    }
}