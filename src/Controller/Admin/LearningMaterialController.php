<?php

namespace App\Controller\Admin;

use App\Entity\Admin\LearningMaterial;
use App\Form\Admin\LearningMaterialEditType;
use App\Form\Admin\LearningMaterialType;
use App\Repository\Admin\LearningMaterialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LearningMaterialController  extends AbstractController {

    /**
     * @Route("/learningMaterial/{learningMaterialsGroupId}", name="learningMaterial")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request) {
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $learningMaterial = new LearningMaterial([]);
        $repositoryMaterial = new LearningMaterialRepository();
        $learningMaterialsGroupId = $request->attributes->get('learningMaterialsGroupId');

        $form = $this->createForm(LearningMaterialType::class, $learningMaterial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $material = $form->getData();
            $learningMaterialsGroupId = $request->attributes->get('learningMaterialsGroupId');
            $values = $material->getAllInformation();

            $file = $form['attachment']->getData();
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename.'-'.uniqid().'.'.$file->guessExtension();

            $repositoryMaterial->insert($learningMaterialsGroupId,$values,$file,$newFilename);

            switch ($_SESSION['role']) {
                case "ROLE_ADMIN":
                    {
                        return $this->redirectToRoute('learningMaterialList',[
                            "learningMaterialsGroupId" => $learningMaterialsGroupId
                        ]);
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
        return $this->render('learningMaterialAdd.html.twig', [
            'form' => $form->createView(),
            'learningMaterialsGroupId' => $learningMaterialsGroupId,
            'role' => $_SESSION['role'],
        ]);
    }

    /**
     * @Route("/learningMaterialList/{learningMaterialsGroupId}", name="learningMaterialList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function learningMaterialListCreate(Request $request) {
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

        $learningMaterialRepository= new LearningMaterialRepository();

        $learningMaterialsGroupId = $request->attributes->get('learningMaterialsGroupId');
        $_SESSION['group_id'] = "";
        $learningMaterialsId = $learningMaterialRepository->getIdLearningMaterials($learningMaterialsGroupId);

        if($learningMaterialsId!=0){
            $learningMaterialsCount = count($learningMaterialsId);
        } else {
            $learningMaterialsCount=0;
        }

        if($learningMaterialsCount>0) {
            $info = true;
            for ($i = 0; $i < $learningMaterialsCount; $i++) {
                $learningMaterial = $learningMaterialRepository->getLearningMaterial($learningMaterialsGroupId,$learningMaterialsId[$i]);

                if($learningMaterial['is_required'] == true) {
                    $is_required = "true";
                } else {
                    $is_required="false";
                }
                $tplArray[$i] = array(
                    'id' => $learningMaterial['id'],
                    'learning_materials_group_id' => $learningMaterial['learning_materials_group_id'],
                    'name' => $learningMaterial['name'],
                    'name_of_content' => $learningMaterial['name_of_content'],
                    'is_required' => $is_required
                );
            }
        } else {
            $info=false;
            $tplArray = array(
                'id' => 0,
                'learning_materials_group_id' => 0,
                'name' => 0,
                'name_of_content' => 0,
                'is_required' => 0

            );
        }
        return $this->render( 'learningMaterialList.html.twig', array (
            'data' => $tplArray,
            'learningMaterialsGroupId' => $learningMaterialsGroupId,
            'information' => $info
        ));
    }

    /**
     * @Route("/learningMaterialDownload/{learningMaterialsGroupId}/{learningMaterialId}", name="learningMaterialDownload")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function learningMaterialDownload(Request $request) {
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $learningMaterialRepository= new LearningMaterialRepository();

        $learningMaterialsGroupId = $request->attributes->get('learningMaterialsGroupId');
        $learningMaterialId = $request->attributes->get('learningMaterialId');

        $learningMaterialInfo = $learningMaterialRepository->getLearningMaterial($learningMaterialsGroupId,$learningMaterialId);
        $filename = $learningMaterialInfo['name_of_content'];
        $learningMaterialRepository->get_file($filename);

        switch ($_SESSION['role']) {
            case "ROLE_ADMIN":
                {
                    return $this->redirectToRoute('learningMaterialList',[
                        "learningMaterialsGroupId" => $learningMaterialsGroupId
                    ]);
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
        return $this->redirectToRoute('learningMaterialList',[
            "learningMaterialsGroupId" => $learningMaterialsGroupId
        ]);
    }

    /**
     * @param Request $request
     * @param LearningMaterial $learningMaterial
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("editLearningMaterial/{groupId}/{id}", name="editLearningMaterial")
     */
    public function editLearningMaterial(Request $request, LearningMaterial $learningMaterial) {
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $learningMaterialRepository = new LearningMaterialRepository();
        $materialId = (int)$request->attributes->get('id');
        $materialGroupId = (int)$request->attributes->get('groupId');

        $_SESSION['group_id'] = $materialGroupId;
        $materials = $learningMaterialRepository->getLearningMaterial($materialGroupId, $materialId);

        $materialInfoArray = array(
            'name' => $materials['name'],
            'is_required' => $materials['is_required'],
        );

        $form = $this->createForm(LearningMaterialEditType::class, $learningMaterial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $values = $learningMaterial->getAllInformation();
            $learningMaterialRepository->update($values,$materialGroupId,$materialId);

            switch ($_SESSION['role']) {
                case "ROLE_ADMIN":
                    {
                        return $this->redirectToRoute('learningMaterialList',[
                            'learningMaterialsGroupId' => $materialGroupId,
                        ]);                        break;
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
        return $this->render('learningMaterialAdd.html.twig', [
            'form' => $form->createView(),
            'learningMaterialInformation' =>$materialInfoArray,
            'role' => $_SESSION['role'],
        ]);
    }

    /**
     * @param Request $request
     * @Route("/deleteMaterial/{learningMaterialsGroupId}/{learningMaterial}", name="deleteMaterial")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteLearningMaterial(Request $request) {
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $learningMaterialId = $request->attributes->get('learningMaterial');
        $learningMaterialsGroupId = $request->attributes->get('learningMaterialsGroupId');

        $repo = new LearningMaterialRepository();
        $info = $repo->getLearningMaterial($learningMaterialsGroupId,$learningMaterialId);
        $filename = $info['name_of_content'];

        if($filename!="") {
            $repo->deleteFile($filename);
        }
        $repo->delete($learningMaterialsGroupId,$learningMaterialId);

        switch ($_SESSION['role']) {
            case "ROLE_ADMIN":
                {
                    return $this->redirectToRoute('learningMaterialList', [
                        'learningMaterialsGroupId' => $learningMaterialsGroupId,
                    ]);
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
}