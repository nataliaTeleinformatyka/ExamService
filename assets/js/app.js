/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');
//const logoPath = require('../images/pp.gif');

//var html = `<img src="${logoPath}">`;
// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');
/*$( document ).ready(function() {
    let question = document.querySelector('#questionContent');
//document.getElementById("questionContent");
    console.log(document.querySelector('#questionContent'));
    dump(question.innerHTML);
    question.innerHTML = 'Hello world!';


    /*var ques = $.getJSON('questions.json', function () {

        console.log('success');

    });*/
//});


var today = new Date();
var hour = today.getHours();

const cookies = document.cookie.split(";");

const questionId = document.getElementById("questionNumber");
const questionContent = document.getElementById("questionContent");
const nextButton = document.getElementById("buttonNext");

let numberQuestion=1;
let id=0;
setValues(0);

console.log(cookies);
document.getElementById("timer").innerHTML = hour;
nextButton.addEventListener('click', nextQuestion);
/*
for(let i=0;i<getCookie("amountOfAnswers"+id);i++) {
    createCheckboxAnswers(id, i);
}*/


function nextQuestion() {
    deleteCheckboxAnswers();
    deleteCheckboxAnswers();
    deleteCheckboxAnswers();
    id++;
    setValues(id);

}

function endExam() {
    console.log("koniec");
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
    console.log("ilosc pytan"+ getCookie("questionAmount"));
    console.log("numer id teraz: "+ id);

    if(id<getCookie("questionAmount")) {
        if (getCookie("questionContent" + id) == "") {
            console.log("brak takiego numeru pyt ");
           // id++;
            document.cookie = "questionAmount="+ (getCookie("questionAmount")-1);
            console.log("QQQQ" + getCookie("questionAmount"));
            id++;
            setValues(id);
        } else {
            console.log("question teraz jesy "+getCookie("questionContent"+id));
            questionId.innerHTML = "Pytanie numer: " + numberQuestion;
            questionContent.innerHTML = "Treść: " + getCookie("questionContent" + id);
            numberQuestion++;
            for(let i=0;i<getCookie("amountOfAnswers"+id);i++) {
                createCheckboxAnswers(id, i);
            }
            return id++;
        }
        if(id==getCookie("questionAmount")){
            nextButton.removeEventListener('click',nextQuestion);
            nextButton.innerHTML="End and save";
            nextButton.addEventListener('click',endExam);
            console.log('uuuuuuuuuuuuuuu');
        }
    }
}
function createCheckboxAnswers(id,i){
    let checkbox = document.createElement('input');
    let label = document.createElement('label');
    let answer = document.getElementById("answers");

    checkbox.type = "checkbox";
    checkbox.name = "answer";
    checkbox.id = i;
    checkbox.value=getCookie("answerContent"+id+i);
    label.innerHTML = getCookie("answerContent"+id+i);
    label.id = i;
    label.name = "label";
    answer.appendChild(checkbox);
    answer.appendChild(label);
    let br = document.createElement("br");
    br.id=i;
    answer.appendChild(br);
}
function deleteCheckboxAnswers() {
    for(let i=0;i<getCookie("amountOfAnswers"+id);i++) {
        document.getElementById("answers").removeChild(document.getElementById(i));
    }
}
