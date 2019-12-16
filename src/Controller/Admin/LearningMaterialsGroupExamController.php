<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 15.12.2019
 * Time: 20:50
 */

namespace App\Controller\Admin;


use App\Entity\Admin\LearningMaterialsGroupExam;
use App\Form\Admin\LearningMaterialsGroupExamType;
use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\LearningMaterialsGroupExamRepository;
use App\Repository\Admin\LearningMaterialsGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LearningMaterialsGroupExamController extends AbstractController
{
    /**
     * @Route("learningMaterialsGroupExam", name="learningMaterialsGroupExam")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        //$repository = $this->getDoctrine()->getRepository(Exam::class);
        $groupExam= new LearningMaterialsGroupExam([]);

        $form = $this->createForm(LearningMaterialsGroupExamType::class, $groupExam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $info = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();

            $values = $groupExam->getAllInformation();
            $repositoryExam = new LearningMaterialsGroupExamRepository();
            $repositoryExam->insert($values);

            return $this->redirectToRoute('learningMaterialsGroupExamList');
        }

        return $this->render('learningMaterialsGroupExamAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("learningMaterialsGroupExamList", name="learningMaterialsGroupExamList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function learningMaterialsGroupExamListCreate()
    {
        $groupExamInformation = new LearningMaterialsGroupExamRepository();
        $examInformationRepo = new ExamRepository();
        $learningMaterialsGroupRepo= new LearningMaterialsGroupRepository();

        $id = $groupExamInformation->getQuantity();

        if ($id > 0) {
            $info = true;
            for ($i = 0; $i < $id; $i++) {
                $groupExam = $groupExamInformation->getLearningMaterialsGroupExam($i);

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
    public function editExam(Request $request, LearningMaterialsGroupExam $groupExam)
    {


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
            $exams = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();

            $values = $groupExam->getAllInformation();
            $repositoryExam = new LearningMaterialsGroupExamRepository();
            $repositoryExam->update($values,$groupExamId);
            return $this->redirectToRoute('learningMaterialsGroupExamList');
        }
        return $this->render('learningMaterialsGroupExamAdd.html.twig', [
            'form' => $form->createView(),
            'examInformation' =>$examInfoArray,
        ]);
    }

    /**
     * @param Request $request
     * @Route("deleteLearningMaterialsGroupExam/{id}", name="deleteLearningMaterialsGroupExam")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteLearningMaterialsGroupExam(Request $request)
    {
        $id = $request->attributes->get('id');
        $repo = new LearningMaterialsGroupExamRepository();
        $repo->delete($id);

        //todo: czy jest powiazanie w userexam - data obejrzenia wymaganych materialow przed rozpoczeciem egzaminu
       /* if($isQuestion !=0 or $isUserExam==false){
            $_SESSION['information'][] = array( 'type' => 'error', 'message' => 'The record cannot be deleted, there are links in the database');

        } else {
            $repo->delete($id);
            $_SESSION['information'][] = array( 'type' => 'ok', 'message' => 'Successfully deleted');

        }*/


        return $this->redirectToRoute('learningMaterialsGroupExamList');
    }
}