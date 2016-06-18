<!DOCTYPE html>
<html>
	<head>
    
		<meta charset="utf-8">
		
		<title><?php if (!isset($title)) { echo'Trafficmonsoon Simulateur' ;} else { echo $title; } ?></title>
        
        <link rel="stylesheet" href="Web/css/reset.css"/>
		<link rel="stylesheet" href="Web/css/style.css"/>
		
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script> window.jQuery || document.write('<script src="/Web/js/jquery.js"><\/script>')</script>
        <script src="Web/js/script.js"></script>
	</head>
 
	<body>
        
        <?php echo $content; ?>
		
        <?php if ($user->hasFlash()) echo '<div class="flash-notice"><h3 style="text-align:center;color:red;text-shadow: 0 0 0.2em #FFF, 0 0 0.2em #FFF, 0 0 0.2em #FFF; font-size:150%;">'.$user->getFlash().'<h3></div>'; ?>	
		
	</body>
</html>