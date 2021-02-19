		<h1>Bienvenue sur le jeu de la roulette</h1>
		<h2>Inscription</h2>
		<p>Choisissez un login et un mot de passe</p>
		<form method="post" action="index.php">
			<input type="text" name="username" placeholder="Identifiant">
			<input type="password" name="password" placeholder="Mot de Passe">
			<input type="password" name="password2" placeholder="Mot de Passe">
			<input type="submit" name="creer">
			<input type="reset" name="effacer">
		</form>
		<p><?php if ($message1!='vide') echo "$message1" ?></p>						<!--informe le joueur sur la création de son compte-->
