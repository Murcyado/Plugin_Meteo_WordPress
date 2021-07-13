<?php
/*
 * Plugin Name: EISGE core_API
 * Plugin URI: 
 * Description: Un Plugin qui appelle une API
 * Version: 1.0
 * Author: Murcyado
 * Author URI: 
*/

require_once ('onglet_meteo.php');

add_action( "rest_api_init", function () {
    register_rest_route( "api/", "/meteo", array(
        "methods" => "GET",
        "callback" => "call_api"
    ) );
} ); //http://wordpress/wp-json/api/meteo

function call_api() {
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
	
    $query = "SELECT url FROM ".$GLOBALS["table_prefix"]."eisge_meteo WHERE ID=(select max(ID) FROM ".$GLOBALS["table_prefix"]."eisge_meteo )";
    $req = $bdd->query($query) or die(print_r($bdd->errorInfo())); //permet de séléctionner l'ID max de la table meteo
    $donnees=$req->fetch();

    //Dans la base de données on a remplacer Montargis,fr par [city]
    $url=$donnees['url'];
    $url=str_replace("[city]",$_GET['city'],$url);

    $data = CallAPI("GET", $url);
    $temperature = json_decode($data); 
    return json_decode('{"temp":'.$temperature->{"main"}->{"temp"}.'}');


    $req->closeCursor();
}

function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        case "GET":
            $ch = curl_init($url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                                                                    
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}