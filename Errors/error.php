<div class="contener">
    <div class="block_1">
    
    	<div class="block_titre"><h3 class="titre_block">Le site</h3></div>

        <div class="block">
            
            <ul class="liste_block">
                <li><a href="/">Accueil</a></li>
                <?php
                if ($user->isAuthenticated() && $user->isPrestataire()) { 
                	echo'
                    <li><a href="/nouvelle-operation">Nouvelle opération</a></li>
                    <li><a href="/nouveau-technicien">Nouveau technicien</a></li>
                    <li><a href="/operation">Mes opérations</a></li>
                    <li><a href="/technicien">Techniciens</a></li>';
                } 
				elseif($user->isAuthenticated() && $user->isIntermittent()) { 
					
				}
				elseif(!$user->isAuthenticated())
				{
					echo'<li><a href="/inscription">Inscription</a></li>';
				}
				?>
            </ul>
            
        </div>
    </div>
    
    <div class="block_3">
        <div class="block_titre"><h3 class="titre_block">Block 3</h3></div>
        <div class="block">
        </div>
    </div>
    
    <div class="block_2">
        <div class="block_titre"><h3 class="titre_block">Erreur</h3></div>
        <div class="block">
            <p style="color:#FFF;Text-align:center;">Une erreur est survenue</p>
        </div>
    </div>
</div>