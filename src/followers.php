<?php session_start(); 
$session = $_SESSION['connected_id'];
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mes abonnés </title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <img src="logo.jpg" alt="Logo de notre réseau social"/> 
            <nav id="menu">
                <a href="news.php">Actualités</a>
                <a href="wall.php?user_id=<?php echo $userId ?>">Mur</a>
                <a href="feed.php?user_id=<?php echo $userId ?>">Flux</a>
                <a href="tags.php?tag_id=<?php echo $tagId ?>">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href="#">Profil</a>
                <ul>
                    <li><a href="settings.php?user_id=<?php echo $session ?>">Paramètres</a></li>
                    <li><a href="followers.php?user_id=<?php echo $session ?>">Mes suiveurs</a></li>
                    <li><a href="subscriptions.php?user_id=<?php echo $session ?>">Mes abonnements</a></li>
                </ul>

            </nav>
        </header>
        <div id="wrapper">          
            <aside>
                <img src = "nft.jpg" alt = "Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez la liste des personnes qui
                        suivent les messages de l'utilisatrice
                        n° <?php echo intval($_GET['user_id']) ?></p>

                </section>
            </aside>
            <main class='contacts'>
                <?php
                // Etape 1: récupérer l'id de l'utilisateur
                $userId = intval($_GET['user_id']);
                // Etape 2: se connecter à la base de donnée
                $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
                // Etape 3: récupérer le nom de l'utilisateur
                $laQuestionEnSql = "
                    SELECT users.*
                    FROM followers
                    LEFT JOIN users ON users.id=followers.following_user_id
                    WHERE followers.followed_user_id='$userId'
                    GROUP BY users.id
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                while ($user = $lesInformations->fetch_assoc())
                {
                // Etape 4: à vous de jouer
                //@todo: faire la boucle while de parcours des abonnés et mettre les bonnes valeurs ci dessous 
                ?>
                <article>
                    <img src="user.jpg" alt="blason"/>
                    <p><a href="wall.php?user_id=<?php echo $user['id']?>"><?php echo $user['alias'] ?></a></p>
                    <p>id: <?php echo $user['id'] ?></p>                    
                </article>
                <?php } ?>
            </main>
        </div>
    </body>
</html>