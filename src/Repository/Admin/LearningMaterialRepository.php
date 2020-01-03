<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 07.12.2019
 * Time: 22:27
 */

namespace App\Repository\Admin;


use App\Entity\Admin\LearningMaterial;
use Google\Cloud\Storage\StorageClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Constraints\File;

class LearningMaterialRepository {
  protected $db;
    protected $database;
    protected $dbname = 'LearningMaterialGroup';
    private $entityManager = 'LearningMaterial';
    protected $reference;
    private $bucket,$storage;

    public function __construct()
{
    $serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');

    $factory = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

    $this->storage = new StorageClient([
        'keyFilePath' => 'C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json',
        'projectId' => 'examservicedatabase']);
    $this->bucket = $this->storage->bucket('examservicedatabase.appspot.com');
 //   print_r($this->bucket);
    /*$storageClient = $storage->getStorageClient();
    $defaultBucket = $storage->getBucket();*/
  //  $anotherBucket = $storage->getBucket('learning_material');

    $this->database = $factory->createDatabase();
    $this->reference = $this->database->getReference($this->dbname);
}

    public function getLearningMaterial(int $materialsGroupId,int $materialId)
{
    $learningMaterialsGroupReference = $this->database->getReference("LearningMaterialsGroup");

    try {
        if ($learningMaterialsGroupReference->getSnapshot()->getChild($materialsGroupId)->hasChild("LearningMaterial")) {
            return $learningMaterialsGroupReference->getSnapshot()->getChild($materialsGroupId)
                ->getChild("LearningMaterial")->getChild($materialId)->getValue();
        } else {
            return 0;
        }
    } catch (ApiException $e) {

    }
}

    public function insert( int $learningMaterialsGroupId, array $data, UploadedFile $file)
    {
        if (empty($data) /*|| isset($data)*/) {
            return false;
        }
        $materialId = $this->getQuantity($learningMaterialsGroupId);
        $learningMaterialsGroupReference = $this->database->getReference("LearningMaterialsGroup");
        $filename = $this->upload_file($file);
        $learningMaterialsGroupReference->getChild($learningMaterialsGroupId)
            ->getChild("LearningMaterial")->getChild($materialId)->set([
                'id' => $materialId,
                'learning_materials_group_id' => $learningMaterialsGroupId,
                'name' => $data[1],
                'name_of_content' => $filename,
                'is_required' => $data[3]
            ]);
        return true;
    }

    public function upload_file(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = $originalFilename.'-'.uniqid().'.'.$file->guessExtension();
        $this->bucket->upload(
            $file,
            [
                'name' => $fileName
            ]
        );
        return $fileName;
    }

    public function get_file(string $filename){
//todo: get files from database
    //    mkdir (".../Downloads/Exam", 0777);
   //     touch($filename);
 //  $rootPath="../Downloads".'/'.$filename;
   // $localFile = new File($rootPath,"imageName.txt");

       //return $this->bucket->object($filename)->info();//->downloadToFile();
        return $this->bucket->object($filename)->info();
    }


    public function delete(int $learningMaterialsGroupId, int $materialId, string $filename)
    {
        $learningMaterialsGroupReference = $this->database->getReference("LearningMaterialsGroup");

        try {
            if ($learningMaterialsGroupReference->getSnapshot()->getChild($learningMaterialsGroupId)
                ->hasChild("LearningMaterial")) {
                $learningMaterialsGroupReference->getChild($learningMaterialsGroupId)
                    ->getChild("LearningMaterial")->getChild($materialId)->remove();
               // $this->bucket->delete($filename);
                return true;
            } else {
                return false;
            }
        } catch (ApiException $e) {
        }
    }

    public function getQuantity(int $learningMaterialsGroupId)
    {
        try {
            $examReference = $this->database->getReference("LearningMaterialsGroup");

            return $examReference->getSnapshot()->getChild($learningMaterialsGroupId)
                ->getChild("LearningMaterial")->numChildren();
        } catch (ApiException $e) {
        }
    }
    public function find(int $materialId){
        $information = $this->reference->getSnapshot()->getChild($_SESSION['group_id'])
            ->getChild("LearningMaterial")->getChild($materialId)->getValue();
        $learningMaterial = new LearningMaterial([]);
        $learningMaterial->setName($information['name']);
        $learningMaterial->setLearningMaterialsGroupId($information['learning_materials_group_id']);
        $learningMaterial->setNameOfContent($information['name_of_content']);
        $learningMaterial->setIsRequired($information['is_required']);

        return $learningMaterial;
    }
}