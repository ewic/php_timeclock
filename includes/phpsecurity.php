<?

function safe( &$string ) 
{
        return strip_tags("'" . mysql_real_escape_string( $string ) . "'") ;
}

function safe_db( &$string ) 
{
        return  mysql_real_escape_string( $string );
}


function unsafe( &$string ) 
{
	$string = str_replace("\\","",$string);
	$string = str_replace("'","",$string);
	return $string;
}

function safe_db_array( &$my_array ) 
{
        foreach( $my_array as $key => &$value )
        {
                $value = safe_db( $value ) ;
        }
	return $my_array;
}


function safe_array( &$my_array ) 
{
        foreach( $my_array as $key => &$value )
        {
                $value = safe( $value ) ;
        }
	return $my_array;
}

function unsafe_array( &$my_array )
{
	foreach( $my_array as $key => &$value )
	{
		$value = unsafe( $value );
	}
	return $my_array;
}

function strip_slashes_from_array( &$my_array )
{
	foreach( $my_array as $key => &$value )
	{
		$value = stripslashes( $value ) ;
	}
	return $my_array;
}

function get_array_contents(&$my_array)
{
	$output = "";
        if( is_array( $my_array ) )
	{
		foreach( $my_array as $key => $value )
        	{
			if( is_array ( $value ) )
			{ 
				$output .= get_array_contents($my_array); 
			}
			else 
			{ 
				$output .= "Key " . $key . " is " . $value . ".<br>" ; 
			}
        	}
	}
	else
	{ $output = "not an array<br>"; }
	return $output;
}

function print_array( $my_array ) {
	echo get_array_contents( $my_array );
}

?>
