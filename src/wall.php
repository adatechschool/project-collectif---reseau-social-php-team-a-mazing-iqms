<?php 
    session_start(); 
    include './navrefactoring.php'
?>

                 
        
        <div id="wrapper">
            <?php
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             * La première étape est donc de trouver quel est l'id de l'utilisateur
             * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
             * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
             * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
             */
            $userId =intval($_GET['user_id']);

          
            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             */
            $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
            ?>

            <aside>
                <?php
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */                
                $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId'";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
            
                
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
                // echo "<pre>" . print_r($user, 5) . "</pre>";
    
                ?>
               
                <img src="nft.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias']?>
                        (n° <?php echo $userId ?>)
                    </p>
                </section>
            </aside>
            <main>
            <form action="wall.php?user_id=<?php echo $userId ?> " method="post">
                        
                        <dl>
                            <dt><label for='auteur'>Auteur</label></dt>
                            <dd><p name='auteur'>
                                    <?php
                                    echo $user['alias']
                                    ?>
                                </p></dd>
                            <dt><label for='message'>Message</label></dt>
                            <dd><textarea name='message'></textarea></dd>
                            <dt><label for='tag'>Add a tag</label></dt>
                            <dd><input type='text' name='tag' value='add a tag'></dd>
                        </dl>
                        <input type='submit'>
                    </form> 
                <?php

                $enCoursDeTraitement = isset($_POST['message']);

                if ($enCoursDeTraitement){
        
                $authorId = $userId ;
                $postContent = $_POST['message'];
                
                $authorId = intval($mysqli->real_escape_string($authorId));
                $postContent = $mysqli->real_escape_string($postContent);
               
                 $lInstructionSql = "INSERT INTO posts "
                 . "(id, user_id, content, created, parent_id) "
                 . "VALUES (NULL, "
                 . $authorId . ", "
                 . "'" . $postContent . "', "
                 . "NOW(), "
                 /* . "'', " */
                 . "NULL);"
                 ;

                 $getPostId = "
                 SELECT $postContent, posts.id as post_id FROM posts; 
                 SELECT tags.id as tag_id FROM tags";

                 $insertPostId = "INSERT INTO posts_tags "
                 . "(id, post_id, tag_id) "
                 . "VALUES (NULL, "
                 . "post_id, "
                 . "tag_id);";
            
              
                 
                // echo $lInstructionSql;
                $ok = $mysqli->query($lInstructionSql);
                if ( ! $ok)
                {
                    echo "Impossible d'ajouter le message: " . $mysqli->error;
                } else
                {
                    echo "Message posté";
                }
                
             
            }

         

            $tagProcessing = isset($_POST['tag']);
            if ($tagProcessing){
                //$tagId=$_POST['tag'];
                $tagContent = $_POST['tag'];
                //$tagId = intval($mysqli->real_escape_string($tagId));
                $tagContent = $mysqli->real_escape_string($tagContent);
               /*  echo $tagContent; */
                $tagInsert =   "INSERT INTO tags "
                . "(id, label) "
                . "VALUES (NULL, "
                . "'" . $tagContent . "');"
            ;
          
           /*  $tagInsert = "INSERT INTO tags "
                 . "(id, label) "
                 . "VALUES (NULL, "
                 . $tagContent . "); "
                 ;
            */
              

             $tagOK = $mysqli->query($tagInsert);
             if (! $tagOK){
                 echo "Impossible d'ajouter le tag: " . $mysqli->error;
             } else {
                 echo "Tag posté";
             }

            }
                
                 //requête pour récupérer id_post 

            //   $postId =intval($_GET['tag_id']);
              $getTable = "
              SELECT posts.id as post_id FROM posts; 
              SELECT tags.id as tag_id FROM tags;";


            //   $tagPostProcessing = isset($getTable);
            
               $insertPostId = "INSERT INTO posts_tags "
               . "(id,post_id,tag_id) "
               . "VALUES(NULL, "
               . "post_id, "
               . "tag_id);";
            
            
            //   $idPost = $mysqli->query($getTable);
            //   $message = $idPost->fetch_assoc();
      
              $insertPostTag = $mysqli->query($insertPostId);
              if ( ! $insertPostTag)
              {
                  echo("Échec de la requete : " . $mysqli->error);
              }
      

                //  * Etape 3: récupérer tous les messages de l'utilisatrice
                 
                $laQuestionEnSql = "
                    SELECT posts.content, posts.created, users.alias as author_name, users.id as author_id, 
                    COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist, GROUP_CONCAT(DISTINCT tags.id) AS taglistid 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE posts.user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }
        
                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 */
                while ($post = $lesInformations->fetch_assoc())
                {

                    // echo "<pre>" . print_r($post, 1) . "</pre>";
                    ?>   
                     
                 
                    <article>
                        <h3>
                        <time><?php echo $post['created']?></time>
                        </h3>
                        <address><a href="wall.php?user_id=<?php echo $post['author_id']?>"><?php echo $post['author_name'] ?></a></address>
                        <div>
                            <!-- <p>Ceci est un paragraphe</p>
                            <p>Ceci est un autre paragraphe</p> -->
                            <p><?php echo $post['content'] ?></p>
                        </div>                                            
                        <footer>
                            <small><?php echo $post["like_number"] ?></small>
                            <a href="tags.php?tag_id=<?php echo $post['taglistid']?>">#<?php echo $post['taglist'] ?></a>
                        </footer>
                    </article>
                <?php } ?>


            </main>
        </div>
    </body>
</html>
