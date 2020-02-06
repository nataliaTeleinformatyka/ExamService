require('../css/app.css');

const questionId = document.getElementById("questionNumber");
const questionContent = document.getElementById("questionContent");
const nextButton = document.getElementById("buttonNext");


window.onload = function() {
    attachSorting();
    let userName = document.getElementById("userNameMenu");
    userName.innerHTML = getCookie("userName");
    checkSelect();
}

function checkSelect(){
    let userRoles = document.getElementById("user_roles");
    let groupOfStudents = document.getElementById('user_group_of_students');
    for(let i=0;i<userRoles.length;i++){
        if(userRoles[i].selected) {
            if(userRoles[i].value != "student") {
                groupOfStudents.disabled = true;
            } else {
                groupOfStudents.disabled = false;
            }
        }
    }
    window.requestAnimationFrame(checkSelect);
}

let numberQuestion=1;
let id=0;
let questionAmount = getCookie("questionAmount") - 1;
setValues(0);

let timer = document.getElementById("timer");
nextButton.addEventListener('click', nextQuestion);


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
        let s = remainingTime / 1000;   // secunds
        let min = s / 60;               // minutes
        let h = min / 60;               // hours
        let sLeft = Math.floor(s % 60);    // remaining seconds
        let minLeft = Math.floor(min % 60); // remaining minutes
        let hLeft = Math.floor(h);          // remaining hours
        if (minLeft < 10)
            minLeft = "0" + minLeft;
        if (sLeft < 10)
            sLeft = "0" + sLeft;
        document.getElementById("timer").innerHTML = hLeft + " : " + minLeft + " : " + sLeft;
        window.requestAnimationFrame(time);

    } else {
        console.log("THE END");
        window.location.href = "result";
        return "Czas minal";
    }
}



function contains(classArray,value){
    for (var i=0; i<classArray.length;i++)
        if (classArray[i]===value)
            return true;
    return false;
}

function integerSort(a,b){ return parseInt(a)>parseInt(b); }
function valueSort(a,b){ return a>b; }

function attachSorting() {
    var handlers=[['SSort', valueSort],['ISort',integerSort]];
    for(var i=0, ths=document.getElementsByTagName('th'); th=ths[i]; i++){
        for (var h=0; h<handlers.length;h++) {
            if(contains(th.className.split(" "), handlers[h][0])){
                th.columnIndex=i;
                th.order=-1;
                th.sortHandler = handlers[h][1];
                th.onclick=function(){sort(this);}
                var divNode = document.createElement('div');
                var textNode = document.createTextNode('');
                divNode.appendChild(textNode);
                th.appendChild(divNode);
                th.sortTextNode = textNode;
            }
        }
    }
}
function setOrder(th) {
    th.order *= -1;
    th.sortTextNode.nodeValue=th.order<0?'\u25B2':'\u25BC';
}
function resetOrder(th){
    th.sortTextNode.nodeValue='';
    th.order=-1;
}

function sort(header){
    setOrder(header);
    var table = header.parentNode.parentNode;
    for (var i=0, th, ths=table.getElementsByTagName('th'); th=ths[i]; i++)
        if (th.order && th!=header)
            resetOrder(th);
    var rows=table.getElementsByTagName('tr');
    for(var i=1, tempRows=[], tr; tr=rows[i]; i++){tempRows[i-1]=tr}
    tempRows.sort(function(a,b){
        return header.order*
            (header.sortHandler(
                a.getElementsByTagName('td')[header.columnIndex].innerHTML,
                b.getElementsByTagName('td')[header.columnIndex].innerHTML)?1:-1)});
    for(var i=0; i<tempRows.length; i++){
        table.appendChild(tempRows[i]);
    }
}
