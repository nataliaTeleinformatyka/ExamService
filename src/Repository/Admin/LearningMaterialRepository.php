<?php

namespace App\Repository\Admin;

use App\Entity\Admin\LearningMaterial;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LearningMaterialRepository {

    protected $reference;
    private $connection;
    private $login;

    public function __construct() {
    $database = new DatabaseConnection();
    $this->reference = $database->getReference('LearningMaterialsGroup');

    $ftp_server = "ftp.files1.radiokomunikacja.edu.pl";
    $ftp_port =21;
    $ftp_time = 90;
    $ftp_user = "user@files01.radiokomunikacja.edu.pl";
    $ftp_password = "M5.wlx.KZH.4";
    $this->connection = ftp_connect($ftp_server,$ftp_port,$ftp_time) or die("Couldn't connect to $ftp_server");
    $this->login = ftp_login($this->connection,$ftp_user,$ftp_password);
}

    public function getLearningMaterial(int $materialsGroupId,int $materialId) {
        if ($this->reference->getSnapshot()->getChild($materialsGroupId)->hasChild("LearningMaterial")) {
            return $this->reference->getSnapshot()->getChild($materialsGroupId)
                ->getChild("LearningMaterial")->getChild($materialId)->getValue();
        } else
            return 0;
    }

    public function insert( int $learningMaterialsGroupId, array $data, UploadedFile $file, $filename) {
        if (empty($data))
            return false;

        $materialId = $this->nextLearningMaterialId($learningMaterialsGroupId);

        $this->uploadFile($file,$filename);

        $this->reference->getChild($learningMaterialsGroupId)
            ->getChild("LearningMaterial")->getChild($materialId)->set([
                'id' => $materialId,
                'learning_materials_group_id' => $learningMaterialsGroupId,
                'name' => $data[1],
                'name_of_content' => $filename,
                'is_required' => $data[3]
            ]);
        return true;
    }

    public function uploadFile(UploadedFile $file, $filename) {
        if(ftp_put($this->connection,$filename,$file,FTP_BINARY)) {
            echo "Successfully uploaded $file.";
        }
        else
            echo "Error uploading $file.";
        ftp_close($this->connection);
    }

    public function get_file(string $filename) {
        if(!ftp_get($this->connection,$filename,$filename,FTP_BINARY)) {
            echo("Error");
        exit;
        } else
            echo("Success");
    }

    public function deleteFile($filename) {
        if(ftp_delete($this->connection,$filename)) {
            echo "Successfully deleted $filename.";
        }
        else
            echo "Error deleting $filename.";
    }

    public function update(array $data, int $learningMaterialsGroupId,$materialId) {
        if (empty($data))
            return false;

        $this->reference->getChild($learningMaterialsGroupId)
            ->getChild("LearningMaterial")->getChild($materialId)->update([
                'name' => $data[1],
                'is_required' => $data[3]
            ]);
        return true;
    }

    public function delete(int $learningMaterialsGroupId, int $materialId) {
        if ($this->reference->getSnapshot()->getChild($learningMaterialsGroupId)
            ->hasChild("LearningMaterial")) {
            $this->reference->getChild($learningMaterialsGroupId)
                ->getChild("LearningMaterial")->getChild($materialId)->remove();
            return true;
        } else
            return false;
    }

    public function getQuantity(int $learningMaterialsGroupId) {
        return $this->reference->getSnapshot()->getChild($learningMaterialsGroupId)
            ->getChild("LearningMaterial")->numChildren();
    }

    public function getIdLearningMaterials(int $groupId) {

        $learningMaterialsReference = $this->reference->getChild($groupId)->getChild("LearningMaterial")->getSnapshot()->getReference();

        if($learningMaterialsReference->getSnapshot()->hasChildren()==NULL){
            return 0;
        } else
            return $learningMaterialsReference->getChildKeys();
    }

    public function find(int $materialId) {
        $information = $this->reference->getChild($_SESSION['group_id'])
            ->getChild("LearningMaterial")->getChild($materialId)->getSnapshot()->getValue();

        $learningMaterial = new LearningMaterial([]);

        $learningMaterial->setId($information['id']);
        $learningMaterial->setName($information['name']);
        $learningMaterial->setIsRequired($information['is_required']);

        return $learningMaterial;
    }

    public function nextLearningMaterialId($groupId) {
        $learningMaterialsId= $this->getIdLearningMaterials($groupId);
        if($learningMaterialsId!=0){
            $learningMaterialsAmount = count($learningMaterialsId);
        } else
            $learningMaterialsAmount=0;

        switch ($learningMaterialsAmount) {
            case 0:{
                $maxNumber = 0;
                break;
            }
            case 1:{
                $maxNumber=$learningMaterialsAmount[0]+1;
                break;
            }
            default:{
                $maxNumber=$learningMaterialsId[0];
                for($i=1;$i<$learningMaterialsAmount;$i++){
                    if($maxNumber<=$learningMaterialsId[$i]){
                        $maxNumber =$learningMaterialsId[$i];
                    }
                }
                $maxNumber=$maxNumber+1;
            }
        }
        return $maxNumber;
    }
}