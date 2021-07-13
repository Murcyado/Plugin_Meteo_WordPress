<?php

add_action( "rest_api_init", function () {
    register_rest_route( "install/", "/table", array(
        "methods" => "POST",
        "callback" => "install_table"
    ) );
} ); //http://wordpress/wp-json/install/table

function install_table(){
    require_once('create_table.php');
}

add_action('admin_menu', 'add_menu_api');
function add_menu_api()
{
    add_menu_page('Menu API', 'Menu API', 'administrator', 'api-plugin', 'onglet_api_init');
}

function onglet_api_init(){
    require_once ('../wp-config.php');
	
    try
    {
        $bdd = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';'
                     .'charset='.DB_CHARSET,
                      DB_USER, DB_PASSWORD); // Connexion BDD
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(Exception $e)
    {
        die ('Erreur : '.$e->getMessage()); // die interrompt la lecture du script
    }

    try{
        $req = $bdd->query("SELECT url FROM wp_eisge_meteo ORDER BY ID DESC LIMIT 0,1");
    }
    catch(Exception $e)
    {
        $req=0;
    }
	
	try{
		$siteurl = $bdd->query("SELECT option_name, option_value FROM bitnami_wordpress.wp_options WHERE option_name='siteurl'");
		$row = $siteurl->fetch();
	}
	catch(Exception $e)
	{
		$siteurl=0;
	}
	
	if($siteurl){
		?>
            <h2>Fonctionnement de l'URL</h2>
            <br><br>
			<?php echo $donneesurl['siteurl']; echo $donneesurl; ?>
               
				
        <?php
		echo $row['option_name'];
		echo $row['option_value'];

	}
	
    if($req){

        ?>
            <div class="wrap nosubsub">
                <h2>Menu API</h2>
                <br><br>
            <form method="post">
                <div>
                    <input name="api" type="text" size="75"/> <!--value=<?php echo $donnees['url']; ?> -->
                    <input type="submit" value="Valider">
                    <p>Attention !! Ne fonctionne pas avec toutes les API !</p>
                </div>
            </form>
        <?php //http://api.openweathermap.org/data/2.5/weather?q=Montargis,fr&appid=c21a75b667d6f7abb81f118dcf8d4611&units=metric on a remplacé Montargis,fr par [city]

        $donnees = $req->fetch();

        if($_POST["api"]){
            $url = $_POST["api"];
            echo 'API actuellement utilisée : '.$url;//.'  hey'.'<br>';
            $var = $bdd->prepare("INSERT INTO".$GLOBALS["table_prefix"]."eisge_meteo(url) VALUES (:url)");
            $var->execute(array( 
            'url' => $url
            ));
        }
        else{
            echo 'API actuellement utilisée : '.$donnees['url'];
        }

        $req->closeCursor();
    }
    else{
        ?>
        <h2>Menu API</h2>
        <br><br>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>
            function installTable(){
                $.get('http://wordpress/wp-json/install/table')
                location.reload();
            }
        </script>

        <input type="button" value="Créer les éléments dans la base de données" onclick="installTable()"/>
        <?php
    }
	

}


/*
    1 : formulaire : X fonctionne pas : https://openclassrooms.com/fr/courses/918836-concevez-votre-site-web-avec-php-et-mysql/913099-transmettez-des-donnees-avec-les-formulaires

    2 : Ecrire/Lire fichier : X j'arrive pas a modif le CHMOD : https://openclassrooms.com/fr/courses/918836-concevez-votre-site-web-avec-php-et-mysql/913492-lisez-et-ecrivez-dans-un-fichier

    3 : Base de données (phpmyadmin) :  : https://openclassrooms.com/fr/courses/918836-concevez-votre-site-web-avec-php-et-mysql/913655-quest-ce-quune-base-de-donnees (voir les 3 chapitres qui suivent celui ci)
*/