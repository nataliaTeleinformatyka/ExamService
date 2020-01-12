
require('../css/app.css');

var today = new Date();
var hour = today.getHours();

/*

let role = document.getElementById("user_roles");
console.log("UUU");
if(role.value === "student") {
    console.log("YUPI");
    //document.getElementById("group_of_students").style.display = "visible";

} else {
    console.log("BUU");
    //document.getElementById("group_of_students").style.display = "none";

}*/
const cookies = document.cookie.split(";");

const questionId = document.getElementById("questionNumber");
const questionContent = document.getElementById("questionContent");
const nextButton = document.getElementById("buttonNext");

let numberQuestion=1;
let id=0;
let questionAmount = getCookie("questionAmount") - 1;
setValues(0);
let accessTime = getCookie("accessTime");
let accessHours= Math.floor(accessTime/60);
let accessMinute = accessTime - accessHours*60;

console.log(accessTime);

console.log("godzin "+accessHours+" minut "+ accessMinute);

console.log(cookies);
let timer = document.getElementById("timer").innerHTML = hour;

nextButton.addEventListener('click', nextQuestion);
/*
for(let i=0;i<getCookie("amountOfAnswers"+id);i++) {
    createCheckboxAnswers(id, i);
}*/


function nextQuestion() {
    checkForm(id);
    deleteCheckboxAnswers();
    deleteCheckboxAnswers();
    deleteCheckboxAnswers();
    id++;
    setValues(id);
}

function endExam() {
    checkForm(id);
  /*  $.ajax({
        url: 'http://127.0.0.1:8000/result',
        type: "POST",
        dataType:'text',
        data: {'date': "testDZIALANIA"},
        success: function(data){
            console.log("successfully");
        }
    });*/
  nextButton.href="result";

  //  let url = Routing.generate('result');
    //location.href = url;
    //location.href = "http://127.0.0.1:8000/result";
}

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
function setValues(id){

    if(id<=questionAmount) {
        if(id===questionAmount){
            nextButton.removeEventListener('click',nextQuestion);
            nextButton.innerHTML="End and save";
            nextButton.addEventListener('click',endExam);
        }
        questionId.innerHTML = "Pytanie numer: " + numberQuestion;
        questionContent.innerHTML = "Treść: " + getCookie("questionContent" + id);
        numberQuestion++;
        for(let i=0;i<getCookie("amountOfAnswers"+id);i++) {
             createCheckboxAnswers(id, i);
        }
        return id++;
    } else {
        nextButton.removeEventListener('click',nextQuestion);
        nextButton.innerHTML="End and save";
        nextButton.addEventListener('click',endExam);
    }
}
function createCheckboxAnswers(id,i){
        let checkbox = document.createElement('input');
        let label = document.createElement('label');
        let answer = document.getElementById("answers");

        checkbox.type = "checkbox";
        checkbox.name = "answer";
        checkbox.id = i;
        checkbox.value = getCookie("answerContent" + id + i);
        label.innerHTML = getCookie("answerContent" + id + i);
        label.id = i;
        label.name = "label";
        answer.appendChild(checkbox);
        answer.appendChild(label);
        let br = document.createElement("br");
        br.id = i;
        answer.appendChild(br);
}
function deleteCheckboxAnswers() {
    for(let i=0;i<getCookie("amountOfAnswers"+id);i++) {
        document.getElementById("answers").removeChild(document.getElementById(i));
    }
}
//id - id pytania
// i - id odpowiedzi
function checkForm(id) {
    console.log("dlugosc "+ document.getElementById("answers").length);
    let userAnswerAmount=0;
    //let id;
    if (document.getElementById("answers").length != null) {
        for (let i = 0; i < document.getElementById("answers").length; i++) {
            if (document.getElementById("answers")[i].checked) {
                console.log("wartosc " + document.getElementById("answers")[i].value);
                //id=document.getElementById("answers")[i].id;

                document.cookie = "userAnswer"+id+i+"="+document.getElementById("answers")[i].value;
                userAnswerAmount++;
            }
        }
        document.cookie = "userAnswerAmount"+id+"="+userAnswerAmount;
    }
}