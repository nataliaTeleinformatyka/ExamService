<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 13:56
 */

namespace App\Repository\Admin;

use Google\Cloud\Storage\StorageClient;
use App\Entity\Admin\Question;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


class QuestionRepository
{
    protected $db;
    protected $database;
    protected $dbname = 'Exam';
    private $entityManager = 'Question';
    protected $reference;


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
        $this->database = $factory->createDatabase();
        $this->reference = $this->database->getReference($this->dbname);
    }

    public function getQuestion(int $examId, int $questionId)
    {
        $examReference = $this->database->getReference("Exam");
        try {
           if ($examReference->getSnapshot()->getChild($examId)->hasChild("Question")) {
                return $examReference->getSnapshot()->getChild($examId)->getChild("Question")->getChild($questionId)->getValue();
            } else {
                return 0;
            }
        } catch (ApiException $e) {

        }
    }
    public function getFile($filename){
    return $this->bucket->object($filename);

    }


    public function insert( int $idExam, array $data, string $filename)
    {
        if (empty($data) /*|| isset($data)*/) {
            return false;
        }
        $questionId = $this->getQuantity($idExam);
        $examReference = $this->database->getReference("Exam");

        $examReference->getChild($idExam)
            ->getChild("Question")->getChild($questionId)->set([
                'id' => $questionId,
                'exam_id' => $idExam,
                'content' => $data[0],
                'max_answers' => $data[1],
                'is_multichoice' => $data[2],
                'name_of_file' => $filename
            ]);
        return true;
    }

    public function uploadFile(UploadedFile $file)
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

    public function updateFile(UploadedFile $file, string $filenameFromDatabase)
    {
        $this->deleteFile($filenameFromDatabase);

        $fileName = $this->uploadFile($file);
        return $fileName;
    }

    public function deleteFile(string $filename){
        $fileObject = $this->getFile($filename);
        $fileObject->delete();
    }


    public function delete(int $idExam, int $questionId)
    {
        $examReference = $this->database->getReference("Exam");
        try {
            if ($examReference->getSnapshot()->getChild($idExam)->hasChild("Question")) {
                $examReference->getChild($idExam)->getChild("Question")->getChild($questionId)->remove();
                return true;
            } else {
                return false;
            }
        } catch (ApiException $e) {
        }
    }

    public function getQuantity(int $idExam)
    {
        try {
            $examReference = $this->database->getReference("Exam");

            return $examReference->getSnapshot()->getChild($idExam)->getChild("Question")->numChildren();
        } catch (ApiException $e) {
        }
    }

    public function update(array $data, int $id,int $questionId,string $filename) {
        if (empty($data)) {
            return false;
        }

        $this->reference ->getChild($id)
                ->getChild("Question")->getChild($questionId)->update([
                'content' => $data[0],
                'max_answers' => $data[1],
                'is_multichoice' => $data[2],
                'name_of_file' => $filename
            ]);
        return true;
    }

    public function find(int $questionId){
        $information = $this->reference->getSnapshot()->getChild($_SESSION['exam_id'])
            ->getChild("Question")->getChild($questionId)->getValue();
        $question = new Question([]);
        $question->setContent($information['content']);
        $question->setMaxAnswers($information['max_answers']);
        $question->setIsMultichoice($information['is_multichoice']);
        $question->setNameOfFile($information['name_of_file']);

        return $question;
    }
}