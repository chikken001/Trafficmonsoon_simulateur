<div class="contener">
	<h1>Mes packs</h1>
	<a href="/">Simulateur</a></br>
	<a href="/pack/supprimer">Supprimer mes packs</a>
	<form method="post">
		<textarea name="packs"></textarea>
		<input type="submit" name="valider" value="Valider">
	</form>
	<?php 
		echo $solde_form ;
		echo $pack_form ;
	?>
</div>
<script>
	var start = '',
	end   = new Date(),
	diff  = '',
	hours  = 0,
	add_montant = 0 ,
	id_form = 0 ,
	montant ;

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
		
		$(this).find('.montant').text('montant : '+montant.toFixed(2)) ;
	});
</script>