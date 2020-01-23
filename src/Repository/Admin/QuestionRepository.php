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
    private $connection;
    private $login;

    public function __construct()
    {
        $serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');

        $factory = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

        $this->database = $factory->createDatabase();
        $this->reference = $this->database->getReference($this->dbname);


        $ftp_server = "ftp.files1.radiokomunikacja.edu.pl";
        $ftp_port = 21;
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

    public function getQuestion(int $examId, int $questionId) {
        $examReference = $this->database->getReference("Exam");

        if ($examReference->getSnapshot()->getChild($examId)->hasChild("Question")) {
            return $examReference->getSnapshot()->getChild($examId)->getChild("Question")->getChild($questionId)->getValue();
        } else {
            return 0;
        }
    }

    public function insert( int $examId, array $data, string $filename) {
        if (empty($data)) {
            return false;
        }

        $questionRepository = new QuestionRepository();
        $examReference = $this->database->getReference("Exam");

        $maxNumber = $questionRepository->getNextId($examId);

        $examReference->getChild($examId)
            ->getChild("Question")->getChild($maxNumber)->set([
                'id' => $maxNumber,
                'exam_id' => $examId,
                'content' => $data[0],
                'max_answers' => $data[1],
                'name_of_file' => $filename
            ]);
        return true;
    }

    public function uploadFile(UploadedFile $file, $filename) {
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

    public function getFile(string $filename){
        if(!ftp_get($this->connection,$filename,$filename,FTP_BINARY)) {
            echo("Błąd przy próbie pobrania pliku $filename...");
            exit;
        } else {
            echo("ALL IS GOOD");
        }
    }

    public function deleteFile($filename) {
        if(ftp_delete($this->connection,$filename)) {
            echo "Successfully deleted $filename.";
        }
        else
        {
            echo "Error deleting $filename.";
        }
    }


    public function delete(int $examId, int $questionId)
    {
        $examReference = $this->database->getReference("Exam");
        try {
            if ($examReference->getSnapshot()->getChild($examId)->hasChild("Question")) {
                $examReference->getChild($examId)->getChild("Question")->getChild($questionId)->remove();
                return true;
            } else {
                return false;
            }
        } catch (ApiException $e) {
        }
    }

    public function getQuantity(int $examId)
    {
        try {
            $examReference = $this->database->getReference("Exam");

            return $examReference->getSnapshot()->getChild($examId)->getChild("Question")->numChildren();
        } catch (ApiException $e) {
        }
    }

    public function getIdQuestions(int $examId)
    {
        $examReference = $this->database->getReference("Exam");
        $questionReference= $examReference->getChild($examId)->getChild("Question")->getSnapshot()->getReference();
        if($questionReference->getSnapshot()->hasChildren()==NULL){
            return 0;
        } else {
            return $questionReference->getChildKeys();
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
        $question->setNameOfFile($information['name_of_file']);

        return $question;
    }
    public function getNextId($examId){
        $questionId= $this->getIdQuestions($examId);
        if($questionId!=0){
            $questionsAmount = count($questionId);
        } else {
            $questionsAmount=0;
        }
        switch ($questionsAmount) {
            case 0:{
                $maxNumber = 0;
                break;
            }
            case 1:{
                $maxNumber=$questionsAmount[0]+1;
                break;
            }
            default:{
                $maxNumber=$questionId[0];
                for($i=1;$i<$questionsAmount;$i++){
                    if($maxNumber<=$questionId[$i]){
                        $maxNumber =$questionId[$i];
                    }
                }
                $maxNumber=$maxNumber+1;
            }
        }
        return $maxNumber;
    }
}