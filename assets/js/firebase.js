
require('../css/app.css');


var firebaseConfig = {
    apiKey: "AIzaSyCo8mZ4-inkU6tCAfqKjZqdyOg-66tKDik",
    authDomain: "examservicedatabase.firebaseapp.com",
    databaseURL: "https://examservicedatabase.firebaseio.com",
    projectId: "examservicedatabase",
    storageBucket: "examservicedatabase.appspot.com",
    messagingSenderId: "497128504673",
    appId: "1:497128504673:web:fe4b1317dd061fff525920",
    measurementId: "G-KHZBDXT9HV"
};
firebase.initializeApp(firebaseConfig);


var storage = firebase.storage();
var storageRef = storage.ref();
