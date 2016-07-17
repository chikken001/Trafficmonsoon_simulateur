<div class="contener">
	<h1>Mes packs</h1>
	<a href="/">Simulateur</a></br>
	<a href="/pack/supprimer">Supprimer mes packs</a>
	<form method="post">
		<textarea name="packs"></textarea>
		<input type="submit" name="valider" value="Valider">
	</form>
	<?php 
		echo "Solde sur le compte au $date_solde :" ;
		echo $solde_form ;
		echo '<p id="solde"></p>' ;
		echo $pack_form ;
	?>
</div>
<script>
	$(document).ready(function(){
		var start = '',
		start_solde = '',
		end   = new Date(),
		diff  = '',
		hours  = 0,
		add_montant = 0 ,
		id_form = 0 ,
		montant ,
		sub_montant = 0,
		solde_add_montant = 0 ,
		solde = parseFloat($("form[name='User']").find("input[name='User_solde']").val()) ;
	
		$(".Pack").each(function(index) 
		{
			id_form = $(this).find("input[type='hidden']").val() ;
			start = new Date(dateFRtoEN($(this).find("input[name='date_"+id_form+"']").val())) ;
			diff  = new Date(end - start) ;
			hours  = diff/1000/60/60 ;
			add_montant = 0.0416666666666667 * hours ;
			
			montant = parseFloat($(this).find("input[name='montant_"+id_form+"']").val()) ;
			//console.log(montant+' + '+add_montant) ;
			montant = montant + add_montant ;
			//console.log('= '+montant) ;
			if(montant > 55) montant = 55 ;
			
			$(this).append('<p>'+montant.toFixed(2)+'</p>') ;

			start_solde = new Date(dateFRtoEN($("form[name='User']").find("input[name='User_updated_at']").val())) ;
			diff = new Date(start_solde - start) ;
			
			if(diff > 0)
			{
				hours  = diff/1000/60/60 ;
				sub_montant = 0.0416666666666667 * hours ;
				solde_add_montant = add_montant - sub_montant ;
				solde = solde + solde_add_montant ;
			}
			else
			{
				solde = solde + montant ;
			}
		});
		$("#solde").text(solde.toFixed(2)) ;
	});
</script>