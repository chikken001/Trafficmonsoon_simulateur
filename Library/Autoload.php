<?php
function autoload($class)
{
	$file = __DIR__.'/../'.str_replace('\\', '/', $class).'.php' ;
	
	if(file_exists($file))
	{
		require $file ;
	}
}

spl_autoload_register('autoload');


/*function autoload($class)
{
	$file = __DIR__.'/../'.str_replace('\\', '/', $class).'.php' ;

	if(file_exists($file))
	{
		require $file ;
	}
	else
	{
		$class = multiexplode(array("/", '\\'), $class);

		if($class[0] == 'Applications')
		{
			if(isset($class[4]) && file_exists(__DIR__.'/Entities/'.$class[4].'.php'))
			{
				$chemin = __DIR__.'/Entities/'.$class[4] ;
				require __DIR__.'/Entities/'.$class[4].'.php' ;
			}
		}
	}
}

function multiexplode ($delimiters,$string) {

	$ready = str_replace($delimiters, $delimiters[0], $string);
	$launch = explode($delimiters[0], $ready);
	return  $launch;
}

spl_autoload_register('autoload');

*/