<?php

namespace App\Controller\Admin;

use App\Entity\Admin\LearningMaterialsGroup;
use App\Form\Admin\LearningMaterialsGroupType;
use App\Repository\Admin\LearningMaterialRepository;
use App\Repository\Admin\LearningMaterialsGroupExamRepository;
use App\Repository\Admin\LearningMaterialsGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LearningMaterialsGroupController extends AbstractController {

    /**
     * @Route("/learningMaterialsGroup", name="learningMaterialsGroup")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $learningMaterialsGroup = new LearningMaterialsGroup([]);
        $form = $this->createForm(LearningMaterialsGroupType::class, $learningMaterialsGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data[0] = $request->request->get('name_of_group');
            $group = $form->getData();

            $values = $group->getAllInformation();

            $repositoryExam = new LearningMaterialsGroupRepository();
            $repositoryExam->insert($values);

            switch ($_SESSION['role']) {
                case "ROLE_ADMIN":
                    {
                        return $this->redirectToRoute('learningMaterialsGroupList');
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
        return $this->render('learningMaterialsGroupAdd.html.twig', [
            'form' => $form->createView(),
            'role' => $_SESSION['role'],
        ]);
    }

    /**
     * @Route("/learningMaterialsGroupList", name="learningMaterialsGroupList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function learningMaterialsGroupListCreate() {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");

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
        $learningMaterialsGroupRepository= new LearningMaterialsGroupRepository();
        $learningMaterialsGroupsId = $learningMaterialsGroupRepository->getLearningMaterialsGroupId();

        if($learningMaterialsGroupsId!=0){
            $learningMaterialsGroupCount = count($learningMaterialsGroupsId);
        } else {
            $learningMaterialsGroupCount=0;
        }

        if($learningMaterialsGroupCount>0) {
            $info=true;
            for ($i = 0; $i < $learningMaterialsGroupCount; $i++) {
                $learningMaterialsGroup = $learningMaterialsGroupRepository->getLearningMaterialsGroup($learningMaterialsGroupsId[$i]);

                $tplArray[$i] = array(
                    'id' => $i,
                    'name_of_group' => $learningMaterialsGroup['name_of_group'],
                );
            }
        } else {
            $info = false;
            $tplArray = array(
                'id' => "",
                'name_of_group' => "",
            );
        }
        if( isset( $_SESSION['information'] ) && count( $_SESSION['information'] ) > 0  ) {
            $infoDelete = $_SESSION['information'];
        } else {
            $infoDelete = "";
        }
        $_SESSION['information'] = array();
        return $this->render( 'LearningMaterialsGroupList.html.twig', array (
            'data' => $tplArray,
            'information' => $info,
            'infoDelete' => $infoDelete
        ) );
    }
    /**
     * @param Request $request
     * @param LearningMaterialsGroup $learningMaterialsGroup

     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("editLearningMaterialsGroup/{id}", name="editLearningMaterialsGroup")
     */
    public function editLearningMaterialsGroup(Request $request, LearningMaterialsGroup $learningMaterialsGroup) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $learningMaterialsGroupInformation = new LearningMaterialsGroupRepository();
        $groupId = (int)$request->attributes->get('id');
        $infos = $learningMaterialsGroupInformation->getLearningMaterialsGroup($groupId);

        $examInfoArray = array(
            'name_of_group' => $infos['name_of_group'],
        );

        $form = $this->createForm(LearningMaterialsGroupType::class, $learningMaterialsGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $values = $learningMaterialsGroup->getAllInformation();

            $learningMaterialsGroupInformation->update($values,$groupId);
            switch ($_SESSION['role']) {
                case "ROLE_ADMIN":
                    {
                        return $this->redirectToRoute('learningMaterialsGroupList');
                        break;
                    }
                case "ROLE_PROFESSOR":
                    {
                        return $this->redirectToRoute('teacherLearningMaterialsInfo', [
                            'groupId' => $_SESSION['group_id'],
                        ]);
                        break;
                    }
            }
        }
        return $this->render('learningMaterialsGroupAdd.html.twig', [
            'form' => $form->createView(),
            'examInformation' =>$examInfoArray,
            'role' => $_SESSION['role'],

        ]);
    }


    /**
     * @param Request $request
     * @Route("/deleteGroup/{learningMaterialsGroup}", name="deleteGroup")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteGroup(Request $request) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $id = $request->attributes->get('learningMaterialsGroup');
        $repo = new LearningMaterialsGroupRepository();
        $learningMaterialRepo = new LearningMaterialRepository();
        $learningMaterialsGroupExam = new LearningMaterialsGroupExamRepository();

        $isLearningMaterialsGroupExam = $learningMaterialsGroupExam->findByGroupId($id);
        $isMaterial = $learningMaterialRepo->getQuantity($id);

        if($isMaterial or $isLearningMaterialsGroupExam){
            $_SESSION['information'][] = array( 'type' => 'error', 'message' => 'The record cannot be deleted, there are links in the database');
        } else {
            $repo->delete($id);
            $_SESSION['information'][] = array( 'type' => 'ok', 'message' => 'Successfully deleted');
        }
        switch ($_SESSION['role']) {
            case "ROLE_ADMIN" : {
                return $this->redirectToRoute('learningMaterialsGroupList');
                break;
            }
            case "ROLE_TEACHER" : {
                return $this->redirectToRoute('teacherLearningMaterialsInfo', [
                    'groupId' => $id
                ]);
                break;
            }
        }
    }
}
