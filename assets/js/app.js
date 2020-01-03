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

let id=0;
setValues(0);


console.log(cookies);
document.getElementById("timer").innerHTML = hour;
nextButton.addEventListener('click', nextQuestion);

for(let i=0;i<getCookie("amountOfAnswers"+id);i++){

   /* buttonAnswer = document.createElement("button");
    buttonAnswer.innerHTML = getCookie("answerContent"+id+i);
    var body = document.getElementsByTagName("body")[0];
    body. appendChild(button);*/
   
}


function nextQuestion() {
    id++;
    setValues(id);
}

function endExam() {
    console.log("koniec");
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
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
    questionId.innerHTML="Pytanie numer: " + getCookie("questionId"+id);
    questionContent.innerHTML="Treść: " + getCookie("questionContent"+id);
    return id++;
}