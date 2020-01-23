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
    private $connection;
    private $login;

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

    $ftp_server = "ftp.files1.radiokomunikacja.edu.pl";
    $ftp_port =21;
    $ftp_time = 90;
    $ftp_user = "user@files01.radiokomunikacja.edu.pl";
    $ftp_password = "M5.wlx.KZH.4";
    $this->connection = ftp_connect($ftp_server,$ftp_port,$ftp_time) or die("Couldn't connect to $ftp_server");
        $this->login = ftp_login($this->connection,$ftp_user,$ftp_password);

    if ((!$this->connection) || (!$this->login)) {
        echo "Połączenie FTP się nie powiodło!";
        echo "Próbowano połączyć się do $ftp_server jako użytkownik"
            . $ftp_user;
        die;
    } else {
        echo "Połączony z $ftp_server jako użytkownik $ftp_user<br>";
    }

}

    public function getLearningMaterial(int $materialsGroupId,int $materialId) {
        $learningMaterialsGroupReference = $this->database->getReference("LearningMaterialsGroup");
        if ($learningMaterialsGroupReference->getSnapshot()->getChild($materialsGroupId)->hasChild("LearningMaterial")) {
            return $learningMaterialsGroupReference->getSnapshot()->getChild($materialsGroupId)
                ->getChild("LearningMaterial")->getChild($materialId)->getValue();
        } else {
            return 0;
        }
    }

    public function insert( int $learningMaterialsGroupId, array $data, UploadedFile $file, $filename) {
        if (empty($data)) {
            return false;
        }
        $materialId = $this->nextLearningMaterialId($learningMaterialsGroupId);
        $learningMaterialsGroupReference = $this->database->getReference("LearningMaterialsGroup");
        $this->upload_file($file,$filename);
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

    public function upload_file(UploadedFile $file, $filename)
    {
        print_r(" file remote ".$filename. " trutru ");
        if(ftp_put($this->connection,$filename,$file,FTP_BINARY))
        {
            echo "Successfully uploaded $file.";
        }
        else
        {
            echo "Error uploading $file.";
        }
        ftp_close($this->connection);

    }

    public function get_file(string $filename){
        if(!ftp_get($this->connection,$filename,$filename,FTP_BINARY)) {
            echo("Błąd przy próbie pobrania pliku $filename...");
        exit;
        } else {
            echo("ALL IS GOOD");
        }
    }

    public function deleteFile($filename){
        if(ftp_delete($this->connection,$filename)) {
            echo "Successfully deleted $filename.";
        }
        else
        {
            echo "Error deleting $filename.";
        }
    }

    public function get_all_files(){
        print_r(ftp_rawlist($this->connection,"/"));

    }


    public function delete(int $learningMaterialsGroupId, int $materialId)
    {
        $learningMaterialsGroupReference = $this->database->getReference("LearningMaterialsGroup");

        try {
            if ($learningMaterialsGroupReference->getSnapshot()->getChild($learningMaterialsGroupId)
                ->hasChild("LearningMaterial")) {
                $learningMaterialsGroupReference->getChild($learningMaterialsGroupId)
                    ->getChild("LearningMaterial")->getChild($materialId)->remove();

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
            $groupReference = $this->database->getReference("LearningMaterialsGroup");

            return $groupReference->getSnapshot()->getChild($learningMaterialsGroupId)
                ->getChild("LearningMaterial")->numChildren();
        } catch (ApiException $e) {
        }
    }

    public function getIdLearningMaterials(int $groupId)
    {
        $groupReference = $this->database->getReference("LearningMaterialsGroup");
        $learningMaterialsReference= $groupReference->getChild($groupId)->getChild("LearningMaterial")->getSnapshot()->getReference();
        if($learningMaterialsReference->getSnapshot()->hasChildren()==NULL){
            return 0;
        } else {
            return $learningMaterialsReference->getChildKeys();
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

    public function nextLearningMaterialId($groupId){
        $learningMaterialsId= $this->getIdLearningMaterials($groupId);
        if($learningMaterialsId!=0){
            $learningMaterialsAmount = count($learningMaterialsId);
        } else {
            $learningMaterialsAmount=0;
        }
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