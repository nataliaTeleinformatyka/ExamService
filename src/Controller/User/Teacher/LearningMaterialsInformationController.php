<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 09.12.2019
 * Time: 12:16
 */

namespace App\Controller\User\Teacher;


use App\Entity\Admin\LearningMaterial;
use App\Repository\Admin\LearningMaterialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LearningMaterialsInformationController extends AbstractController
{
    /**
     * @Route("teacherLearningMaterialsInfo/{groupId}", name="teacherLearningMaterialsInfo")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function learningMaterialsListCreate(Request $request)
    {

        $materialGroupId = $request->attributes->get('groupId');
        $learningMaterialsInformation = new LearningMaterialRepository();
        $id = $learningMaterialsInformation->getQuantity($materialGroupId);
        if ($id > 0) {
            for ($i = 0; $i < $id; $i++) {
                $learningMaterials = $learningMaterialsInformation->getLearningMaterial($materialGroupId,$i);
                if($learningMaterials['is_required'] == true) {
                    $is_required = "true";
                } else {
                    $is_required="false";
                }
                    $learningMaterialsArray[$i] = array(
                        'id' => $i,
                        'learning_materials_group_id' => $learningMaterials['learning_materials_group_id'],
                        'name' => $learningMaterials['name'],
                        'name_of_content' => $learningMaterials['name_of_content'],
                        'is_required' => $is_required
                    );
                }
        } else {
            $learningMaterialsArray = array(
                'id' => 0,
                'learning_materials_group_id' => 0,
                'name' => 0,
                'name_of_content' => 0,
                'is_required' => 0

            );
        }
        return $this->render('teacherLearningMaterialsInfo.html.twig', array(
            'learning_materials_data' => $learningMaterialsArray
        ));

    }
}