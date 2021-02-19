		<h1>Jeu de la roulette</h1>
	<!--<p><?= $nb ?></p>															affiche le nb à trouver (pour le debug)-->
		<p>Choisisez une mise, puis un nombre ou la parité:</p>
		<p>Votre porte-monnaie:  <?= $porte_monnaie . '€'?></p>						<!--affiche le porte-monnaie du joueur-->
		<form method="post" action="index.php">
			Mise<input type="number" name="mise" value="10" step="5" min="10">
			Choix (entre 1 et 36)<input type="number" name="choix" value="0" min="0" max="36">
			<input type="hidden" name="NB" value="<?= $nb ?>">
			<label>Pair<input type="radio" name="choix_p" value="pair"></label>
			<label>Impair<input type="radio" name="choix_p" value="impair"></label>
			<input type="hidden" name="PARITE" value="<?= $parite ?>">
			<input type="submit" name="envoyer">
		</form>
		<p class="message2"><?php if ($message2!='vide') echo "$message2" ?><p>		<!--affiche victoire ou pas (perdu, gagné!!, ...)-->
		<p><?= 'Vos gains: ' . $gain . '€' ?></p>									<!--affiche les gains-->
		<div>
			<form method="post" action="index.php">
				<input type="submit" name="nouvelle_partie" value="Nouvelle Partie">
			</form>
		</div>
