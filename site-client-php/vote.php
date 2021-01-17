<?php
  include_once 'header.php';
?>


<?php
    // On affiche cette page seulemrnt si l'utilisateur est connecté
    if (isset($_SESSION["useruid"])) {
        echo '<div id="Indication"></div>';
        echo '<div id="BoutonDeVote"></div>';
?>

<script type="text/javascript">
 
/*
recuperer un int qui va determiner 0 pas de vote en cours, 1 vote en cours, 2 vous avez déjà voté, 3 vote en récupération 

0 bouton de vote qui envoie à node.js l'utilisateur 
1 print vote en cours et aussi un bouton idem que 0
2 bouton grisé mais print vote en cours vous avez déjà voté 
3 bouton grisé print vote en récupération (peut être temps restant)
*/
var userid = <?php echo json_encode($_SESSION["userid"].$oid); ?>;
statutPageVote();


// Fonction qui demande le statut pour ensuite afficher la bonne page
function statutPageVote(){
    fetch('https://is-it-loud.herokuapp.com/statusVote', {
        method: "POST",
        body: '{"userid":\"'+userid+'\"}',
        headers: {
        'Accept': 'application/json',
        "Content-type": "application/json; charset=UTF-8"}
    })
    .then(response => response.json()) 
    .then(json => afficherLaPageHTML(json.reponse))
    .catch(err => console.log(err));
}

// Fonction qui envoie le vote de l'utilisateur
function envoyerVote(){
    fetch('https://is-it-loud.herokuapp.com/Vote', {
        method: "POST",
        body: '{"userid":\"'+userid+'\"}',
        headers: {
            'Accept': 'application/json',
            "Content-type": "application/json; charset=UTF-8"
        }
    });
    window.alert("Vote reçu !");
}

// Affiche le bon bouton et la bonne indication en fonction du pramètre passé 
function afficherLaPageHTML(reponse){
    if(reponse == "0"){
        document.getElementById("BoutonDeVote").innerHTML = "<a href=\"vote.php\" onClick='envoyerVote();' title=\"voteclickable\"><img class=\"voteButton\" src=\"img/voteDisponible.png\"/></a>";
        document.getElementById("Indication").innerHTML = "<div id=\"TextIndication\"><p id=\"ParagrapheIndication\">Voulez vous initier un vote ? Cliquez sur le bouton pour voter</p></div>";
    }
    else if(reponse == "1"){
        document.getElementById("BoutonDeVote").innerHTML = "<a href=\"vote.php\" onClick='envoyerVote();' title=\"voteclickable\"><img class=\"voteButton\" src=\"img/voteDisponible.png\"/></a>";
        document.getElementById("Indication").innerHTML = "<div id=\"TextIndication\"><p id=\"ParagrapheIndication\">Un vote est en cours ! Cliquez sur le bouton pour voter</p></div>";
    }
    else if(reponse == "2"){
        document.getElementById("BoutonDeVote").innerHTML = "<a title=\"voteclickable\"><img class=\"voteButton\" src=\"img/voteIndisponible.png\"/></a>";
        document.getElementById("Indication").innerHTML = "<div id=\"TextIndication\"><p id=\"ParagrapheIndication\">Vous avez déjà voté !</p></div>";
    }
    else if(reponse == "3"){
        document.getElementById("BoutonDeVote").innerHTML = "<a title=\"voteclickable\"><img class=\"voteButton\" src=\"img/voteIndisponible.png\"/></a>";
        document.getElementById("Indication").innerHTML = "<div id=\"TextIndication\"><p id=\"ParagrapheIndication\">Vote en cours de recharge... Réessayez dans quelques minutes...</p></div>";
    }
}

// Toute les 5sec la fonction pour actualiser le statut est appelé
setInterval(statutPageVote, 5000);
</script>
 
<?php


  }
  else {
?>
<a href="login.php" ><img class="MustBeLog" src="img/Mustbelog.png"/></a>
<script type="text/javascript">
 

 
</script>
<?php

  }

?>

<?php
  include_once 'footer.php';
?>
