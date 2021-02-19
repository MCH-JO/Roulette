	<h1>Bienvenue sur le jeu de la roulette</h1>
		<h2>Connexion</h2>
		<form method="post" action="index.php">
			<input type="text" name="username" placeholder="Identifiant">
			<input type="password" name="password" placeholder="Mot de Passe">
			<input type="submit" name="connecter">
			<input type="reset" name="effacer">
		</form>
		<p><?php if ($message !='vide') echo "$message" ?></p>						<!--informe le joueur sur la connection-->
