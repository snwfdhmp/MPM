<?
include_once $GLOBALS['config']['paths']['libs'].'db.funcs.php';
include_once $GLOBALS['config']['paths']['libs'].'std.funcs.php';

function matieres_line_view($promo, $focus = 1) {
	$matieres = getMatieres($promo);

	echo '<div class="col-md-1">';
	echo '<ul class="nav nav-pills nav-stacked">';

	$top = "";
	$bottom = "";

	if($matieres == NULL) {
		echo "</div>";
		echo "<div class='col-md-10'> <h2>Il n'y a pas encore de matières pour votre promo ... <a href='?u=addMat'>Soyez le premier à en créer une.</a></h2>
	</div>";
	return false;
}

foreach ($matieres as $matiere) {
	$firstChap = getFirstChap($matiere['id_matiere']);
	if($matiere['id_matiere'] == $focus)
		$top .= '<a class="btn btn-primary nav-btn" href="?u='.
	$GLOBALS['active_view'].'&m='.$matiere['id_matiere'].'&c='.$firstChap['id_chapitre'].'#explore-view">'.$matiere['diminutif'].'</a><br/>';
	else
		$bottom .= '<a class="label label-default nav-btn" href="?u='.
	$GLOBALS['active_view'].'&m='.$matiere['id_matiere'].'&c='.$firstChap['id_chapitre'].'#explore-view">'.$matiere['diminutif'].'</a><br/>';
}
echo $top.$bottom;
echo '<a class="label label-default" href="?u=addMat&promo='.$promo.'"><i class="fa fa-plus-square" aria-hidden="true"></i></a></li>';
echo'</ul></div>';
return true;
}

function matieres_select_view($promo, $focus = 1) {
	$matieres = getMatieres($promo);

	if($matieres == NULL) {
		echo "<option value='' selected disabled>Il n'y a pas encore de matière pour votre promo ... </option><a href='?u=addMat'>Soyez le premier à créer une matière</a>";
		return false;
	}

	foreach ($matieres as $matiere) {
		echo '<option value="'.$matiere['id_matiere'].'"';
		if(isset($focus) && $matiere['id_matiere'] == $focus)
			echo ' selected';
		echo '>'.$matiere['diminutif'].'</option>';
	}
	return true;
}

function chapitres_list_view($matiere, $focus) {
	$chapitres = getChapitres($matiere);

	echo '<ul class="nav nav-tabs">';

	if($chapitres == NULL) {
		echo "<h2>Il n'y a pas encore de chapitres pour cette matière ... <a href='?u=addChap&m=".$matiere."'>Soyez le premier à créer un chapitre</a></h2>";
		return false;
	}

	foreach ($chapitres as $chapitre) {
		echo '<li role="presentation"';
		if($chapitre['id_chapitre'] == $focus)
			echo 'class="active"';
		echo '><a class="chapitre-menu" href="?u='.$GLOBALS['active_view'].'&m='.$chapitre['id_matiere'].'&c='.$chapitre['id_chapitre'].'">'.$chapitre['nom'].'</a></li>';
	}
	echo "<li role='presentation'><a href='?u=addChap&m=".$matiere."'>+</a></li>";

	echo '</ul><br/>';
	return true;
}

function chapitres_select_view($matiere, $focus = 1) {
	$chapitres = getChapitres($matiere);


	if($chapitres == NULL) {
		echo "<option value='' selected disabled>Il n'y a pas encore de chapitres dans cette matière... </option><p><a href='?u=addCourse'>Soyez le premier à créer un chapitre</a></p>";
		return false;
	}

	foreach ($chapitres as $chapitre) {
		echo '<option value="'.$chapitre['id_chapitre'].'"';
		if(isset($focus) && ($chapitre['id_chapitre'] == $focus))
			echo ' selected';
		echo '>'.$chapitre['nom'].'</option>';
	}
	return true;
}

function promo_select_view($focus) {
	$promos = getPromos();

	if($promos == NULL) {
		echo "<option value='' selected disabled>Il n'y a pas encore de promo dans votre école... </option><p><a href='?u=addCourse'>Soyez le premier à créer une promo</a></p>";
		return false;
	}

	foreach ($promos as $promo) {
		echo '<option value="'.$promo['id_promo'].'"';
		if($promo['id_promo'] == $focus)
			echo ' selected';
		echo '>'.$promo['nom'].'</option>';
	}
}

function documents_table_view_($chapitre) {

	$documents = getDocuments($chapitre);
	$chapitre = getChapitre($chapitre);

	if($documents == NULL) {
		echo "<h2>Il n'y a pas encore de documents pour cette matière ... <a href='?u=addCourse&m=".$chapitre['id_matiere']."&c=".$chapitre['id_chapitre']."'>Soyez le premier à poster un document</a>";
		return false;
	}
	$i=0;
	$inners = Array();
	$top ="";
	$bottom ="";
	foreach ($documents as $document) {
		$auteur = getUser($document['id_auteur']);
		if(!isset($inners[doctypeToStr($document['doc_type'])])) $inners[doctypeToStr($document['doc_type'])] = "";

		if(! hasSeen($document['id_doc'], $_SESSION['id_user'])) {
			$notSeen = "<span class='label label-success'>nouveau</span>";
		} else {
			$notSeen = "";
		}
		$nbrComs = countComments($document['id_doc']);
		$vues = "<span class='label label-default'>".$document['vues']." <i class='fa fa-eye' aria-hidden='true'></i></span>";
		if($nbrComs > 0)
			$coms = "<span class='label label-primary'>".$nbrComs." <i class='fa fa-commenting' aria-hidden='true'></i></span>";
		else
			$coms = "";
		$inners[doctypeToStr($document['doc_type'])] .= 
		'<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading text-center">
				<a href="?u=view&id='.$document['id_doc'].'">
					'.$document['nom'].'
				</a>'
				.$vues
				.' '
				.$coms
				.' '
				.$notSeen
				.'</div>
				<div class="panel-body>"'
					.'<p>Uploadé par '
					.'<a href="?u=profile&id='
					.$auteur['id_user']
					.'">'
					.$auteur['prenom']
					.' '
					.$auteur['nom']
					.'</a></p>

				</div>'
				.'<div class="panel-footer">'
				.time2str($document['date_creation'])
				.'</div></div></div>';
			}
			foreach($GLOBALS['config']['database']['doctypes'] as $doctype) {
				if(! isset($inners[$doctype])) {
					$bottom .= "<div class='badge badge-lg badge-default doc-table-view-type'><a href='?u=addCourse&m=".$chapitre['id_matiere']."&c=".$chapitre['id_chapitre']."&t=".$doctype."'>$doctype +</a></div><br/>";
				}
				else {
					$top .= "<div class='badge badge-lg doc-table-view-type doc-table-view-type-nonvoid'>$doctype</div>";
			/*
			<a class='doc-table-view-type-add' href='?u=addCourse&m=".$chapitre['id_matiere']."&c=".$chapitre['id_chapitre']."&t=".$doctype."'>
			<span class='label label-danger doc-table-view-type-add'>+</span>
			</a>
			*/
			$top .= "<div class='row doc-list'>"
			.
			$inners[$doctype]."<a class='doc-table-view-type-add' href='?u=addCourse&m=".$chapitre['id_matiere']."&c=".$chapitre['id_chapitre']."&t=".$doctype."'>"
			.
			"</div>";
		}
	}
	echo $top;
	echo $bottom;
	return true;
}

function documents_table_view($chapitre) {
	$documents = getDocuments($chapitre);
	$chapitre = getChapitre($chapitre);

	if($documents == NULL) {
		echo "<h2>Il n'y a pas encore de documents pour cette matière ... <a href='?u=addCourse&m=".$chapitre['id_matiere']."&c=".$chapitre['id_chapitre']."'>Soyez le premier à poster un document</a>";
		return false;
	}
	$i=0;
	$inners = Array();
	$top ="";
	$bottom ="";
	foreach ($documents as $document) {
		$auteur = getUser($document['id_auteur']);
		if(!isset($inners[doctypeToStr($document['doc_type'])])) $inners[doctypeToStr($document['doc_type'])] = "";

		if(! hasSeen($document['id_doc'], $_SESSION['id_user'])) {
			$notSeen = "<span class='badge'>nouveau !</span>";
		} else {
			$notSeen = "";
		}
		$nbrComs = countComments($document['id_doc']);

		//labels
		$vues = " | ".$document['vues']." <i class='fa fa-eye' aria-hidden='true'></i>";
		$coms = " | ".$nbrComs." <i class='fa fa-commenting' aria-hidden='true'></i>";
		$note = getLikes($GLOBALS['config']['database']['type_ref']['document'], $document['id_doc'])." <i class='fa fa-thumbs-o-up' aria-hidden='true'></i>";

		$inners[doctypeToStr($document['doc_type'])] .= '<tr class="doc-table-view-row"><td><a class="doc-table-name" href="?u=view&id='.$document['id_doc'].'"><span class="label label-danger doc-table-name">'.$document['nom'].'</span></a> '.$notSeen.'<td><span class="label label-primary">'.$note.' '.$vues.' '.$coms.' </span></td><td><a href="?u=profile&id='.$auteur['id_user'].'">'.$auteur['prenom'].' '.$auteur['nom'].'</a> '.getNoteDisplay($auteur['id_user']).'</td><td>'.time2str($document['date_creation']).'</td></tr>';
	}
	echo "<table class='table table-hover'>
	<tr class='doc-table-view-row'><th>Nom</th><th></th><th>Auteur</th><th>Date d'ajout</th></tr>";
	foreach($GLOBALS['config']['database']['doctypes'] as $doctype) {
		if(! isset($inners[$doctype])) {
			$bottom .= "<tr><td><div class='badge badge-lg badge-default doc-table-view-type'><a href='?u=addCourse&m=".$chapitre['id_matiere']."&c=".$chapitre['id_chapitre']."&t=".$doctype."'>$doctype +</a></div></td><td></td><td></td><td></td></tr>";
		}
		else {
			$top .= "<tr><td><div class='badge badge-lg doc-table-view-type doc-table-view-type-nonvoid'>$doctype</div>";
			/*
			<a class='doc-table-view-type-add' href='?u=addCourse&m=".$chapitre['id_matiere']."&c=".$chapitre['id_chapitre']."&t=".$doctype."'>
			<span class='label label-danger doc-table-view-type-add'>+</span>
			</a>
			*/
			$top .= "</td><td></td><td></td><td></td></tr>"
			.
			$inners[$doctype]."<tr class='doc-view-section-end'><td><a class='doc-table-view-type-add' href='?u=addCourse&m=".$chapitre['id_matiere']."&c=".$chapitre['id_chapitre']."&t=".$doctype."'>Publier un document
		</a></tr>";
	}
}
echo $top;
echo $bottom;
echo '</table>';
return true;
}

function noteToColorText($note) {
	if($note < 10)
		$color = "primary";
	else if ($note < 50)
		$color = "primary";
	else if ($note < 100)
		$color = "default";
	else if ($note < 150)
		$color = "default";
	else
		$color = "default";

	return $color;
}

function getUserDisplay($id_user) {
	$user = getUser($id_user);

	return $user['prenom']." ".$user['nom']." ".getNoteDisplay($user['id_user']);
}

function getNoteDisplay($id_user) {
	$note = getGlobalNote($id_user);
	$color = noteToColor($note);
	return "<span class='label label-".$color."'>".noteToRang($note)."</span>";
}

function noteToRang($note) {
	if($note < 10) $rang = "casual";
	else if ($note < 30) $rang = "regular";
	else if ($note < 50) $rang = "active";
	else if ($note < 75) $rang = "smart";
	else if ($note < 100) $rang = "major";
	else if ($note < 135) $rang = "genius";
	else if ($note < 180) $rang = "super genius";
	else if ($note < 200) $rang = "power genius";
	else if ($note < 250) $rang = "hardcore genius";
	else if ($note < 350) $rang = "master genius";
	else if ($note == 333) $rang = "einstein";
	else $rang = "god";

	return $rang;
}

function noteToColor($note) {
	if($note < 10) $color = "info";
	else if ($note < 30) $color = "info";
	else if ($note < 50) $color = "warning";
	else if ($note < 75) $color = "warning";
	else if ($note < 100) $color = "default";
	else if ($note < 135) $color = "default";
	else if ($note < 180) $color = "success";
	else if ($note < 200) $color = "success";
	else if ($note < 250) $color = "warning";
	else if ($note < 350) $color = "warning";
	else $color = "danger";

	return $color;
}

function comments_doc_view($id_doc, $logged = false) {
	$comments = getComments($id_doc);

	if($comments==NULL) {
		echo "<p class='well'>Il n'y a pas encore de commentaires sur ce post. Soyez le premier à commenter.</p>";
		return false;
	}

	$inners = Array();

	foreach ($comments as $comment) {
		$auteur = getUser($comment['id_auteur']);
		$likes = getLikes($GLOBALS['config']['database']['type_ref']['comment'], $comment['id_com']);
		echo '
		<div class="comment">
			<div class="panel panel-'.$comment['type'].'">
				<div class="panel-heading">
					<div class="row">
						<span class="col-md-10 comment-prenom"><a href="?u=profile&id='.$auteur['id_user'].'">'.$auteur['prenom'].' '.$auteur['nom']."</a> ".getNoteDisplay($auteur['id_user'])."</span><span class='col-md-1 col-md-offset-1'>".
						(($auteur['id_user'] == $_SESSION['id_user']) || (adminOnly()) ? "<span class='text-right' onclick='delCom(".$comment['id_com'].");getComments();'>&#10008;</span></span>":"")
						.'
					</div>
				</div>
				<div class="panel-body">
					'.$comment['contenu'].'
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-md-9 text-muted">
							'.time2str($comment['date_creation']).'
						</div>
						<div class="like-view">
							<div class="btn-toolbar" role="toolbar">
								<div class="btn-group-xs" role="group">
									<button onclick="putLike('.$GLOBALS['config']['database']['type_ref']['comment'].','.$comment['id_com'].')" class="btn btn-default like-btn"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i></button>
									<button class="btn btn-default"> <span class="like-com" id="badge-like-'.$GLOBALS['config']['database']['type_ref']['comment'].'-'.$comment['id_com'].'"> '."$likes".' </span> </button><button class="btn btn-default dislike-btn" onclick="putDislike('.$GLOBALS['config']['database']['type_ref']['comment'].','.$comment['id_com'].')"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>';
	}
	return true;
}

function search_view($search) {
	$results = searchDocuments($search);

	if($results == NULL) {
		echo "<li><a href='#'>Pas de résultat</a></li>";
		die();
	}

	foreach ($results as $result) {
		echo "<li><a href='?u=view&id=".$result['id_doc']."'>".$result['nom']."</a></li>";
	}

	return true;
}

function online_users_view() {
	$online = getOnlineUsers();

	if($online == NULL) {
		echo "<li><a href='#'>Personne en ligne actuellement</a></li>";
		die();
	}

	foreach ($online as $on) {
		$id_user = $on['id_user'];
		$user = getUser($id_user);
		echo "<li><a href='?u=profile&id=".$id_user."'><span class='green-dot'></span>".$user['prenom']." ".$user['nom']."</a></li>";
	}

	return true;
}
function online_users_sidebar() {
	$online = getOnlineUsers();

	if($online == NULL) {
		echo "<li><a href='#'>Personne en ligne actuellement</a></li>";
		die();
	}

	foreach ($online as $on) {
		$id_user = $on['id_user'];
		$user = getUser($id_user);
		echo '<div class="user-online-box"><li><a href="?u=profile&id='.$id_user.'"><span class="green-dot"></span>'.$user["prenom"].' '.$user["nom"].'</a><span class="user-online-links"> <a href="?u=profile&id='.$id_user.'"><i class="fa fa-address-card" aria-hidden="true"></i></a> <a><i class="fa fa-comments" aria-hidden="true"></i></a></span></li></div>';
	}

	return true;
}

function select_promo_admin_change($promo) {
	$promos = getPromos();

	foreach ($promos as $promo) {
		echo "<a href='?u=explore&r=setpromo&promo=".$promo['id_promo']."' class='btn btn-sm btn-primary'>".$promo['nom']."</a> ";
	}
}

function tokens_list_view($id_user) {
	$tokens = getUserTokens($id_user);

	if($tokens == NULL) {
		echo "<h2>Vous n'avez pas de tokens en attente</h2><h3>Vous souhaitez peut-être inviter vos amis ?</h3> <a href='index.php?u=addAccounts' class='btn btn-lg btn-primary'>Inviter mes amis</a><br/><br/><a href='index.php?u=explore' class='btn btn-default btn-xs'><i class='fa fa-caret-left' aria-hidden='true'></i> Retourner sur LearnHub</a>  ";
		return false;
	}

	echo "<table class='table table-hover'>";
	echo "<tr><th>Nom</th><th>Email</th><th>Promo</th><th>Etat</th><th>Lien d'activation</th></tr>";

	foreach ($tokens as $token) {
		$user = getUser($token['id_user']);
		if($user != NULL) {
			echo "<tr><td>".getUserDisplay($user['id_user'])."</td><td>".$user['email']."</td><td>".getPromoName($user['promo'])."</td><td>";
			switch($token['used']) {
				case 0:
				echo "<span class='text-danger'>Non utilisé</span>";
				break;
				case 1:
				echo "<span class='text-success'>Utilisé</span>";
				break;
			}
			echo "</td><td><div class='row'>";
			if($token['used'] != 1) {
				echo "<div class='col-md-6'><input class='form-control' type='text' value='"."http://".$GLOBALS['config']['domain']."/"."?u=initAccount&token=".$token['value']."'></div><div class='col-md-3'><a href='?u=viewTokens&r=emailToken&id=".$token['id']."'><button class='btn btn-primary btn-sm'>Renvoyer le lien</button></a></div><div class='col-md-3'><a href='?u=viewTokens&r=delToken&id=".$token['id']."'><button class='btn btn-danger btn-sm'>&#10008;</button></a></div></div>";
			} else {
				echo "<div class='col-md-8'><input class='form-control' type='text' value='"."http://".$GLOBALS['config']['domain']."/"."?u=initAccount&token=".$token['value']."'></div><div class='col-md-4'><a href='?u=viewTokens&r=delToken&id=".$token['id']."'><button class='btn btn-danger btn-sm'>&#10008;</button></a></div></div>";
			}

			echo "</td></tr>";
		}
	}

	return true;
}