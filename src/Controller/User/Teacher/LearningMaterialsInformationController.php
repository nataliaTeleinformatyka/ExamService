<?php

namespace App\Controller\User\Teacher;

use App\Repository\Admin\LearningMaterialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LearningMaterialsInformationController extends AbstractController
{
    /**
     * @Route("teacherLearningMaterialsInfo/{groupId}", name="teacherLearningMaterialsInfo")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function learningMaterialsListCreate(Request $request)
    {
        $_SESSION['group_id']="";
        $materialGroupId = $request->attributes->get('groupId');
        $_SESSION['group_id']=$materialGroupId;
        $learningMaterialsInformation = new LearningMaterialRepository();
        $existLearningMaterial=false;

        $learningMaterialsId = $learningMaterialsInformation->getIdLearningMaterials($materialGroupId);
        if($learningMaterialsId!=0){
            $learningMaterialsCount = count($learningMaterialsId);
        } else {
            $learningMaterialsCount=0;
        }
        if ($learningMaterialsCount > 0) {
            $existLearningMaterial=true;
            for ($i = 0; $i < $learningMaterialsCount; $i++) {
                $learningMaterials = $learningMaterialsInformation->getLearningMaterial($materialGroupId, $learningMaterialsId[$i]);
                if($learningMaterials['is_required'] == true) {
                    $is_required = "Tak";
                } else {
                    $is_required="Nie";
                }
                    $learningMaterialsArray[$i] = array(
                        'id' => $learningMaterials['id'],
                        'name' => $learningMaterials['name'],
                        'name_of_content' => $learningMaterials['name_of_content'],
                        'is_required' => $is_required
                    );
                }
        } else {
            $learningMaterialsArray = array(
                'id' => '',
                'name' => '',
                'name_of_content' => '',
                'is_required' => ''

            );
        }
        if( isset( $_SESSION['information'] ) && count( $_SESSION['information'] ) > 0  ) {
            $infoDelete = $_SESSION['information'];
        } else {
            $infoDelete = "";
        }
        $_SESSION['information'] = array();
        return $this->render('teacherLearningMaterialsInfo.html.twig', array(
            'learning_materials_data' => $learningMaterialsArray,
            'learning_materials_group_id' => $materialGroupId,
            'info_delete' => $infoDelete,
            'information' => $existLearningMaterial,
            'exam_id' => $_SESSION['exam_id']

        ));

    }
}