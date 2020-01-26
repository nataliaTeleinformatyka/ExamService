
require('../css/app.css');


const cookies = document.cookie.split(";");

const questionId = document.getElementById("questionNumber");
const questionContent = document.getElementById("questionContent");
const nextButton = document.getElementById("buttonNext");


window.onload = function() {
    attachSorting();
    let userName = document.getElementById("userNameMenu");
    userName.innerHTML = getCookie("userName");
}
let numberQuestion=1;
let id=0;
let questionAmount = getCookie("questionAmount") - 1;
setValues(0);
let accessTime = getCookie("accessTime");
let accessHours= Math.floor(accessTime/60);
let accessMinute = accessTime - accessHours*60;

let timer = document.getElementById("timer");
nextButton.addEventListener('click', nextQuestion);


function Contains(classArray,value){
    for (var i=0; i<classArray.length;i++)
        if (classArray[i]===value) return true;
    return false;
}

function IntegerSort(a,b){return parseInt(a)>parseInt(b);}
function ValueSort(a,b){return a>b;}

function attachSorting(){
    console.log("ATTACH SORT");
    var handlers=[["SSort", ValueSort],["ISort",IntegerSort]];
    for(var i=0, ths=document.getElementsByTagName('th'); th=ths[i]; i++){
        for (var h=0; h<handlers.length;h++) {
            if(Contains(th.className.split(" "), handlers[h][0])){
                th.columnIndex=i;
                th.order = -1;
                th.sortHandler = handlers[h][1];
                th.onclick=function(){sort(this);}
            }
        }
    }
}

window.requestAnimationFrame(time);
/*window.onload = function() {
   // let timer = document.getElementById("timer").innerHTML = time();
    setInterval(time(), 1000);
// todo: jezeli czas minal -> przekierowanie do result, brak mozliwosc cofniecia do egzaminu i dalszego rozwiazywania
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
    nextButton.href="result";
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
            nextButton.innerHTML="Zakończ i zapisz";
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
        checkbox.value = getCookie("answerId" + id + i);
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

function checkForm(id) {
    let userAnswerAmount=0;
    if (document.getElementById("answers").length != null) {
        for (let i = 0; i < document.getElementById("answers").length; i++) {
            if (document.getElementById("answers")[i].checked) {
                document.cookie = "userAnswer"+id+i+"="+document.getElementById("answers")[i].value;
                userAnswerAmount++;
            }
        }
        document.cookie = "userAnswerAmount"+id+"="+userAnswerAmount;
    }
}

function time() {
    let accessTime = getCookie("accessTime");
    let accessHours = Math.floor(accessTime / 60);
    let accessMinute = accessTime - accessHours * 60;
    let startTime = new Date();
    let actualDay = startTime.getDate();
    let actualMonth = startTime.getMonth();
    let actualYear = startTime.getFullYear();


    let endDate = new Date(actualYear, actualMonth, actualDay, accessHours, accessMinute, 0, 0);
    let remainingTime = endDate.getTime() - startTime.getTime();
    if (remainingTime > 0) {
        let s = remainingTime / 1000;   // sekundy
        let min = s / 60;               // minuty
        let h = min / 60;               // godziny
        let sLeft = Math.floor(s % 60);    // pozostało sekund
        let minLeft = Math.floor(min % 60); // pozostało minut
        let hLeft = Math.floor(h);          // pozostało godzin
        if (minLeft < 10)
            minLeft = "0" + minLeft;
        if (sLeft < 10)
            sLeft = "0" + sLeft;
        document.getElementById("timer").innerHTML = hLeft + " : " + minLeft + " : " + sLeft;
        window.requestAnimationFrame(time);

    } //else
       // return "Czas minal";
        //timer.innerHTML = "czas minal";
}

function sortTableByName(){
    const name = document.getElementById("name");
    const tr = document.getElementsByTagName("tr");
}

function sort(header) {
    header.order *= -1;
    var table = header.parentNode.parentNode;
    for (var i = 0, th, ths = table.getElementsByTagName('th'); th = ths[i]; i++)
        if (th != header) th.order = -1;
    var rows = table.getElementsByTagName('tr');
    for (var i = 1, tempRows = [], tr; tr = rows[i]; i++) {
        tempRows[i - 1] = tr
    }
    tempRows.sort(function (a, b) {
        return header.order *
            (header.sortHandler(
                a.getElementsByTagName('td')[header.columnIndex].innerHTML,
                b.getElementsByTagName('td')[header.columnIndex].innerHTML) ? 1 : -1)
    });
    for (var i = 0; i < tempRows.length; i++) {
        table.appendChild(tempRows[i]);
    }
    console.log("POSOTROWANIE");
}
