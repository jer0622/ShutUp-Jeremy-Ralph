    </div>
  </body>
</html>

<script src="js/script.js"></script>
<?php
    if (isset($_SESSION["useruid"])) {
        echo "<script src=\"js/microphone.js\"></script>";
    }
?>