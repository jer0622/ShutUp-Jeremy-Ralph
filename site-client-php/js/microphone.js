/* Comme on n'a pas de composant pour faire office de micro, nous utilisons ce code
 disponible en libre service pour utiliser le micro de notre ordinateur */

 //On a éditez légrement le code pour qu'il soit adapté a nos benoins.

// Code by : https://codepen.io/travisholliday/pen/gyaJk


// URL du NodeJS
var node_url = 'https://is-it-loud.herokuapp.com/'

// Tableau contenant les niveaux (level) sonors produit par l'utilisateur
var tabValue =[];

//------------------------- Debut du code de travisholliday :--------------------------//
let valeurMic = 0;

navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;

if (navigator.getUserMedia) {
    navigator.getUserMedia({
        audio: true
        },
        function(stream) {
        audioContext = new AudioContext();
        analyser = audioContext.createAnalyser();
        microphone = audioContext.createMediaStreamSource(stream);
        javascriptNode = audioContext.createScriptProcessor(2048, 1, 1);

        analyser.smoothingTimeConstant = 0.8;
        analyser.fftSize = 1024;

        microphone.connect(analyser);
        analyser.connect(javascriptNode);
        javascriptNode.connect(audioContext.destination);

        var canvasContext = document.getElementById('canvas');
        canvasContext = canvasContext.getContext('2d');

        javascriptNode.onaudioprocess = function() {
            var array = new Uint8Array(analyser.frequencyBinCount);
            analyser.getByteFrequencyData(array);
            var values = 0;

            var length = array.length;
            for (var i = 0; i < length; i++) {
                values += (array[i]);
            }
    /****************** debut modification  ***********************/
            let average = values / length;

            canvasContext.clearRect(0,0, 100, 100);

            canvasContext.strokeStyle = '#0d98de';
            canvasContext.beginPath();
            canvasContext.ellipse(50, 50, average/2, average/2, 0,0, 2 * Math.PI, false);
            canvasContext.stroke();

            canvasContext.fillStyle = '#0d98de';
            canvasContext.lineWidth = 3;
            canvasContext.beginPath();
            canvasContext.ellipse(50, 50, average/4, average/4, 0,0, 2 * Math.PI, false);
            canvasContext.fill();
            
            valeurMic = Math.round(average);
            canvasContext.fillText(valeurMic, 80, 95);
    /****************** fin modification  ***********************/        
        }
    },
    function(err) {
        console.log("The following error occured: " + err.name)
    });
} else {
    console.log("getUserMedia not supported");
}

//------------------------- Fin du code de travisholliday :--------------------------//

// Fonction retournant un level pour les LED en fonction du niveau sonor de l'utilisateur
function setlevel(){
    if(valeurMic<20){
        return '0';
    }
    if(valeurMic<40){
        return '1';
    }
    if(valeurMic<60){
        return '2';
    }
    if(valeurMic<80){
        return '3';
    }
    if(valeurMic<90){
        return '4';
    }
    if(valeurMic<100){
        return '5';
    }
    if(valeurMic>=100){
        return '6';
    }
}

// Fonction qui envoie le level du niveau sonor au NodeJS
function sendValue(node_url,value) {
    fetch(node_url+"level",{
        headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
        },
        //mode: 'no-cors',
        //credentials: 'include',
        method:'POST',
        body: '{"levelMic":'+value+'}',
    });
}

// Fonction permettant d'actualiser la barre du niveau sonor sur la page de l'utilisateur
function setSlots(){
    console.log("val mic : ",valeurMic);
    console.log(setlevel());
    tabValue.push(setlevel());
    var level = setlevel();
    console.log("refreshing the slots");
    
    if(level==0){
        document.getElementById("slot").innerHTML = "<img class=\"barreLevel\" src=\"img/LEVEL0.png\"/>";
    }
    if(level==1){
        document.getElementById("slot").innerHTML = "<img class=\"barreLevel\" src=\"img/LEVEL1.png\"/>";
    }
    if(level==2){
        document.getElementById("slot").innerHTML = "<img class=\"barreLevel\" src=\"img/LEVEL2.png\"/>";
    }
    if(level==3){
        document.getElementById("slot").innerHTML = "<img class=\"barreLevel\" src=\"img/LEVEL3.png\"/>";
    }
    if(level==4){
        document.getElementById("slot").innerHTML = "<img class=\"barreLevel\" src=\"img/LEVEL4.png\"/>";
    }
    if(level==5){
        document.getElementById("slot").innerHTML = "<img class=\"barreLevel\" src=\"img/LEVEL5.png\"/>";
    }
    if(level==6){
        document.getElementById("slot").innerHTML = "<img class=\"barreLevel\" src=\"img/SHUTUP!.png\"/>";
    }
}

// Fonction qui prend le maximum du niveau sonor de l'utilisateur pour ensuite pouvoir l'envoyer au NodeJS
function maxtable(){
    var maximum = Math.max.apply(null, tabValue);
    console.log(tabValue);
    tabValue = [];
    console.log("sending value to node.js : " + maximum);
    return maximum;
}

// Fonction qui fait appel a sendValue() pour envoyer les infos au NodeJS
function envoieInfo(){
    sendValue(node_url, maxtable());
}

// Toutes les 0.5sec la barre est actualisé
setInterval(setSlots, 500);

// Toutes les 3sec on envoie les infos au NodeJS
setInterval(envoieInfo, 3000);