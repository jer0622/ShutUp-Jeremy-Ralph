
// Importation des modules
var path = require('path');
var net = require('net');
var http = require('http');
var url = require('url');
console.log("camerche bien");
//--- MQTT module
const mqtt = require('mqtt')
// Topics MQTT
const TOPIC_LEVEL = 'sensors/level'

// Client MQTT
const mqtt_url = 'http://broker.hivemq.com'
var client_mqtt = mqtt.connect(mqtt_url);


// Tableau des données reçu
var tabLevel = [];

// Si un vote est en cours et si il est disponible
var voteEnCours = false;
var voteDispo = true;

// Les identifiant des utilisateur ayant voté
var aVote = [];
var VoteTime = 0;

// Compteur
var compteur = 0;

const express = require('express');
// et pour permettre de parcourir les body des requetes
const bodyParser = require('body-parser');

const app = express();
var cors = require('cors');

app.use(cors()) // Use this after the variable declaration
app.use(bodyParser.urlencoded({ extended: true }))
app.use(bodyParser.json())
app.use(express.static(path.join(__dirname, '/')));app.use(function(request, response, next) { //Pour eviter les problemes de CORS/REST
    response.header("Access-Control-Allow-Origin", "*");
    response.header("Access-Control-Allow-Headers", "*");
    response.header("Access-Control-Allow-Methods", "POST, GET, OPTIONS, PUT, DELETE");
    next();
});

// Requete receptionant le level du niveau sonor d'un utilisateur
app.post('/level', function(req, res) {
    tabLevel.push(req.body.levelMic);
    res.send("1");
});

// Requete renvoyant le statut du vote
app.post("/statusVote", function(req, res) {
    if (!voteEnCours && voteDispo) {
        res.send("{\"reponse\":\"" + 0 + "\"}");
    }
    else if (voteEnCours && !aVote.includes(req.body.userid)) {
        res.send("{\"reponse\":\"" + 1 + "\"}");
    }
    else if (voteEnCours && aVote.includes(req.body.userid)) {
        res.send("{\"reponse\":\"" + 2 + "\"}");
    }
    else{
        res.send("{\"reponse\":\"" + 3 + "\"}");
    }
});

// Requete récupérant le vote d'un utilisateur (et place son id dans un tableau)
app.post("/Vote", function(req, res) {
    aVote.push(req.body.userid);
    VoteTime = 0;
    voteEnCours = true;
    res.send("1");
});

// Le port de l'écoute
var PORT = process.env.PORT || 3000;

// Démarre l'écoute
app.listen(PORT, () => {
    console.log('Server listening on port 3000');
});

// Fonction qui gère le compteur (appeler toute les socondes)
function timeout(){
    // Quand le vote n'est pas disponible on attend que le compteur soit a plus de 300 pour le remetre actif.  
    if(!voteDispo){
        // Quand compteur est inférieur à 300 on l'incrémente de 1
        if(compteur < 300){
            compteur++;
        }
        // On rénitialise le compteur et le vote aussi.
        else{
            voteDispo = true;
            compteur = 0; 
        }
    }

    // Quand un vote est en cours
    if(voteEnCours){
        // Le délai du vote n'est pas dépasser, alors on l'incrémente de 1
        if(VoteTime < 300){
            VoteTime++;
        }
        // Le délai est dépasser alors on rénitialise le vote et le compteur de vote
        else{
            VoteTime = 0;
            voteEnCours = false;
            aVote = [];
            voteDispo = true;
        }
    }
}

// Fonction qui calcule la moyenne des level du niveau sonor (valeur contenue dans tabLevel)
function moyenne(tableau){
    var sum = 0;
    var longueur = 0;
    tableau.forEach(element => {
        sum += element;
        longueur+=1;
    });
    if(longueur == 0){
        return 0;
    }
    var average = Math.floor(sum/longueur);
    return average;
}

// Fonction qui publie dans le broker
function publish(){
    nbVotant = 0;
    aVote.forEach(element => {
        nbVotant++;
    });
    // Si le nombre de votant est supérieur à 2, alors on publie de faire clignoter les LED (alerte maximale)
    if(nbVotant>=2){
        client_mqtt.publish(TOPIC_LEVEL, "6");
        voteEnCours=false;
        aVote = [];
        voteDispo = false;
    }
    // L'alerte maximale a a été atteinte, alors on publie pendant 1min de faire clignoter les LED
    else if(!voteDispo && compteur < 60){
        client_mqtt.publish(TOPIC_LEVEL, "6");
    }
    else{
        // Publie le level de la moyenne du niveau sonor
        client_mqtt.publish(TOPIC_LEVEL, String(moyenne(tabLevel)));
    }
    tabLevel = [];
}

// Toute les 5sec on publie dans le broker
setInterval(publish, 5000);

// Toute les secondes on appelle la fonction
setInterval(timeout, 1000);
