<? 
$auth->requiresAuth();
$GLOBALS['active_view']="addCourse";

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>

	<link rel="stylesheet" href="ressources/css/navbar.css">
	<link rel="stylesheet" href="ressources/css/addCourse.css">
	<link rel="stylesheet" href="ressources/css/menu.css">
	<link rel="stylesheet" href="ressources/css/style.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://use.fontawesome.com/f51a5e5d23.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<script>
		
	</script>
</head>
<body>
	<a href="?u=accueil">
		<button class="btn btn-danger" style="float: right; margin-right: 150px;">Retour</button>
	</a>
	<div class="container">
		<h1>Ajouter un chapitre</h1>
		<h2>LearnHub vous permet d'ajouter des chapitres à vos matières afin de pouvoir publier du contenu.<br/>
		Si vous le souhaitez vous pouvez également <a href="?u=addMat">ajouter une matière</a>.</h2><br/>
		<?
		if(isset($_GET["x"])) {
			echo '<div class="alert alert-danger">';
			switch($_GET["x"]) {
				case "addfail" :
				echo 'Le chapitre n\'a pas pu être ajoutée. Veuillez réessayer.';
				break;
				case "fill":
				echo "Veuillez remplir tous les champs du formulaire.";
				break;
				case "exist":
				echo "Ce chapitre existe déjà.";
				break;
				default:
				echo "Erreur lors de l'ajout du chapitre. Contactez un administrateur.";
				break;
			}
			echo "</div>";
		}
		?>
		<? include_once('layouts/addChapForm.php') ?>
	</div>
</div>
</body>