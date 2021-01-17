<?php
  include_once 'header.php';
?>


<?php

  if (!isset($_SESSION["useruid"]))
    header("location: https://shut-up-jr.herokuapp.com/index.php");
?>


<div id="Compte">
    <h2>Profil</h2>

    <table>
        <tbody>
            <tr>
                <td>Pseudonyme</td>
                <td id="ProfilPseudo"></td>
            </tr>
            <tr></tr>
            <tr>
                <td>Nom</td>
                <td id="ProfilNom"></td>
            </tr>
            <tr></tr>
            <tr>
                <td>E-mail</td>
                <td id="ProfilMail"></td>
            </tr>
        </tbody>
    </table>

                

</div>

<canvas id="canvas" touch-action="none" height="0" width="0"></canvas>


<script>

// On récupère les informations pour la page de profil
var pseudo = <?php echo json_encode($_SESSION["useruid"].$oid); ?>;
var name = <?php echo json_encode($_SESSION["name"].$oid); ?>;
var email = <?php echo json_encode($_SESSION["email"].$oid); ?>;

// On affiche les informations
document.getElementById("ProfilPseudo").innerHTML = pseudo;
document.getElementById("ProfilNom").innerHTML = name;
document.getElementById("ProfilMail").innerHTML = email;
</script>


</section>
<?php
  include_once 'footer.php';
?>
