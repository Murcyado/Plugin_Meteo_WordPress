<?php

require_once ('wp-config.php');

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
    $table="CREATE TABLE ".$GLOBALS["table_prefix"]."eisge_meteo(
    ID INT AUTO_INCREMENT PRIMARY KEY,
    url TEXT NOT NULL)";
    
    $bdd->exec($table);
    error_log("La table à été crée !!");
	
    $var = $bdd->prepare("SELECT option_name, option_value FROM bitnami_wordpress.wp_options WHERE option_name='siteurl'");
        $var->execute(array(
        'url' => 'http://api.openweathermap.org/data/2.5/weather?q=[city]&appid=c21a75b667d6f7abb81f118dcf8d4611&units=metric'
        ));
        echo 'table créer';
}
catch(Exception $e)
{
    die ('Erreur : '.$e->getMessage()); 
}
?>