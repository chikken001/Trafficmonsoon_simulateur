<div class="contener">
	<h1>Mes packs</h1>
	<a href="/">Simulateur</a><br/>
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
	$(document).ready(function()
	{
		var start = '',
		start_solde = '',
		end   = new Date(),
		diff  = '',
		hours  = 0,
		add_montant = 0 ,
		id_form = 0 ,
		montant ,
		sub_montant = 0,
		time = 0,
		min = 0,
		add = 0,
		// 0.0416666666666667
		// 0.0438582
		// 0,04401293
		gain_hourly = 0.0416666666666667,
		solde_add_montant = 0 ,
		reste = 0 ,
		solde = parseFloat($("form[name='User']").find("input[name='User_solde']").val()),
		total = 0 ;

		time = end.getTime() ;
		min = end.getMinutes() ;
		add = (60 - min) * 60000 ;
		end.setTime(time + add) ;
		
		$(".Pack").each(function(index) 
		{
			id_form = $(this).find("input[type='hidden']").val() ;
			start = new Date(dateFRtoEN($(this).find("input[name='date_"+id_form+"']").val())) ;
			
			time = start.getTime() ;
			min = start.getMinutes() ;
			add = (60 - min) * 60000 ;
			start.setTime(time + add) ;
			
			diff  = new Date(end - start) ;
			
			if(diff > 0)
			{
				hours  = diff/1000/60/60 ;
				add_montant = gain_hourly * hours ;
			}
			else
			{
				add_montant = 0 ;
			}
			
			montant = parseFloat($(this).find("input[name='montant_"+id_form+"']").val()) ;
			montant = montant + add_montant ;
			if(montant > 55) montant = 55 ;
			console.log(montant);
			total = total + montant ;
			reste = reste + (55-montant) ;
			
			$(this).append('<p>'+montant.toFixed(2)+'</p>') ;

			start_solde = new Date(dateFRtoEN($("form[name='User']").find("input[name='User_updated_at']").val())) ;
			diff = new Date(start_solde - start) ;
			
			if(diff > 0)
			{
				hours  = diff/1000/60/60 ;
				sub_montant = gain_hourly * hours ;
				solde_add_montant = add_montant - sub_montant ;
				solde = solde + solde_add_montant ;
			}
			else
			{
				diff = new Date(start - start_solde) ;
				hours  = diff/1000/60/60 ;
				sub_montant = gain_hourly * hours ;
				solde_add_montant = add_montant + sub_montant ;
				solde = solde + solde_add_montant ;
			}
		});
		console.log('total : '+total) ;
		console.log('reste : '+reste) ;
		$("#solde").text(solde.toFixed(2)) ;
	});
</script>