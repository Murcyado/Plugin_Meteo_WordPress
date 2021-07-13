<?php
/*
 * Plugin Name: EISGE Meteo
 * Plugin URI:
 * Description: Un widget météo
 * Version: 1.0
 * Author: Murcyado
 * Author URI: 
*/

require_once ('onglet_meteo.php');

add_action('widgets_init','meteo_init');

function meteo_init() {
	register_widget("meteo_widget");
}

class meteo_widget extends WP_widget{ // Une classe = un widget

	// Constructeur du widgets
	function meteo_widget(){
		$options = array(
				"description" => "Un widget pour la météo"
		);
		$this->WP_widget("widget-meteo","Widget meteo",$options); //1er paramètre = id du widget (pas donné le meme a 2 widget), 2ème paramètre = nom du widget
		
	}

	// Mise en forme
	function widget($args,$instance){
		extract($args);
		echo $before_widget;
		echo $before_title.$instance["titre"].$after_title;
		
		?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script>
			var callBackGetSuccess = function(data){
				//console.log("donnees api", data)
				//alert("Temperature : " + data.temperature);
				var element = document.getElementById("<?php echo $this->id;?>_temp");
				element.innerHTML += "</br>La température est de " + data.temp + "°C à <?php echo $instance["city"]; ?>";
			}
			

			function appel_api(){
				//var urlAPI = "http://wordpress/wp-json/api/meteo?city=<?php echo $instance["city"]; ?>";
				var urlAPI = "<?php echo $row['option_value'];?> wp-json/api/meteo?city=<?php echo $instance["city"]; ?>";
				
				
				//console.log(urlAPI);
				$.get(urlAPI,callBackGetSuccess).done(function(){
					//alert("second success");
					
					})
				 .fail(function() {
				 	alert("Erreur");
				 });
			}
			
			appel_api();
		</script>

		
		<p id="<?php echo $this->id;?>_temp"><br>Bonjour ! Bienvenue sur ma page météo</p>

		<?php
		echo $after_widget;

	}

	// Récupération des paramètres
	function update($new,$old){
		return $new; //pour sauvegarder
	}

	// Paramètres dans l'administration
	function form($new){
		$default = array(
			"titre" => "Météo",
			"city" => "Montargis"
		);
		$instance = wp_parse_args($instance,$default); //va compléter avec les valeurs du 2nd tab le 1er
		?>
		<p>
				<label for="<?php echo $this->get_field_id("titre"); ?>">Titre : </label>
				<input value="<?php echo $instance["titre"]; ?>" name="<?php echo $this->get_field_name("titre"); ?>" id="<?php echo $this->get_field_id("titre"); ?>" type="text"/>
				<br><br>
				<label for="<?php echo $this->get_field_id("city"); ?>">Ville : </label>
				<input value="<?php echo $instance["city"]; ?>" name="<?php echo $this->get_field_name("city"); ?>" id="<?php echo $this->get_field_id("city"); ?>" type="text"/>
		</p>
		<?php
	}
}