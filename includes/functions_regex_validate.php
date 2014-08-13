<?
function validate_email($email)
{
	$email_regex = "/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/";
	if(preg_match($email_regex,$email))
	{ return true; } else { return false; }
}

function validate_mac($macaddr)
{
	$mac_regex = "/^[0-9a-fA-F]{2}([-|:]{0,1}[a-fA-F0-9]{2}){5}$/";
	if (preg_match($mac_regex,$macaddr))
	{ return true; } else { return false; }
}

function validate_phone($phone)
{
	$phone_regex = "/^\+?[0-9]?[0-9]?[-. ]?\(?[0-9]{3}\)?[-. ]?[0-9]{3}[-. ]?[0-9]{4}$/";
	if (preg_match($phone_regex,$phone))
	{ return true; } else { return false; }
}

function validate_screenname($screenname)
{
	// strip out formatting whitespace
	$screenname = preg_replace("[ ]","",$screenname);
	$screenname_regex = "/^[a-zA-Z][a-zA-Z0-9]{2,15}$/";
	if (preg_match($screenname_regex,$screenname))
	{ return true; } else { return false; }
}

function validate_street_address($address)
{
	$address_regex = "/^[\-\.\,\#\w\s]+$/";
	if (preg_match($address_regex,$address))
	{ return true; } else { return false; }
}

function clean_up_phone($phone)
{
	$phone = preg_replace("/[^0-9]/", "", $phone);
	$clean_regex="/([0-9]{3})([0-9]{3})([0-9]{4})/";
	$final_format="$1-$2-$3";
	return preg_replace($clean_regex, $final_format, $phone);
}

function clean_up_mac($mac)
{
	return $mac;
}

function jackcheck_validate_phone($phone)
{
	$phone_regex = "/^\+?[0-9]?[0-9]?[-. ]?\(?[0-9]{3}\)?[-. ]?[0-9]{3}[-. ]?[0-9]{4}$/";
	if (preg_match($phone_regex,$phone))
		return true; 
	else 
		return false;
}//finished

function jackcheck_validate_rt($rt)
{
	$rt_regex = "/^[0-9]{5,6}$/" ;
	if(preg_match($rt_regex,$rt))
		return true; 
	else 
		return false;
}//finished

function jackcheck_validate_name($first,$last)
{ 
	$name_regex = "/\A([a-zA-Z\-]+)\Z/" ;
	if(preg_match($name_regex,$first) && preg_match($name_regex,$last))
		return true ;
	else 
		return false ;
}//finished

function jackcheck_validate_rcc_username($username)
{
	$username_regex = "/\A[a-zA-Z]{3,6}[0-9]{2}\Z/" ;
	if(preg_match($username_regex,$username))
		return true; 
	else 
		return false;
}

function jackcheck_validate_customer_username($username)
{
	$username_regex = "/\A[a-zA-Z]{3,6}[0-9]{2}\Z/" ;
	if(preg_match($username_regex,$username))
		return true;
	 else 
		return false;
}//finished

function jackcheck_validate_jackid($desc)
{
	$jackid_regex = "/^([a-zA-Z\-()\s0-9]+)$/" ;
	if(preg_match($jackid_regex,$desc))
		return true ;
	else 
		return false ;
}//finished

function jackcheck_validate_room_number($dorm,$room)
{

	$room_regex = "/\A([a-zA-Z\s0-9]+)\Z/" ;
	if(preg_match($room_regex,$room))
		return true ;
	else 
		return false ;
/*
	echo "checking" ;
	switch($dorm)
	{
		case "ANTHONY HOUSE": return true ;
			break ;
		case "BARTOL HOUSE": return true ;
			break ;
		case "BLAKELEY HALL": return true ; 
			break ;
		case "BUSH HALL":  return true ;
			break ;
		case "CAPEN HOUSE": return true ;
			break ;
		case "CARMICHAEL HALL": return true ;
			break ;
		case "CARPENTER HOUSE": return true ;
			break ;
		case "CHANDLER HOUSE": return true ;
			break ;
		case "CURTIS ST., 90": return true ;
			break ;
		case "CURTIS ST,  92": return true ;
			break ;
		case "CURTIS ST,  94": return true ;
			break ;
		case "CURTIS ST, 176": return true ;
			break ;
		case "DAVIES HOUSE": return true ;
			break ;
		case "DEARBORN RD, 12": return true ;
			break ;
		case "FAIRMONT HOUSE": return true ;
			break ;
		case "GORDON HALL": return true ;
			break ;
		case "HALL HOUSE": return true ;
			break ;
		case "HASKELL HALL": return true ;
			break ;
		case "HILL HALL": return true ;
			break ;
		case "HILLSIDE APARTMENTS": return true ;
			break ;
		case "HODGDON HALL": return true ;
			break ;
		case "HOUSTON HALL": return true ;
			break ;
		case "LATIN WAY": return true ;
			break ;
		case "LEWIS HALL": return true ;
			break ;
		case "MCCOLLESTER HOUSE": return true ;
			break ;
		case "METCALF HALL": return true ;
			break ;
		case "MILLER HALL": return true ;
			break ;
		case "MILNE HOUSE": return true ;
			break ;
		case "RICHARDSON HOUSE": return true ;
			break ;
		case "SAWYER AVE., 45": return true ;
			break ;
		case "SCHMALZ HOUSE": return true ;
			break ;
		case "SOUTH HALL": return true ;
			break ;
		case "START HOUSE": return true ; 
			break ;
		case "STRATTON HALL": return true ;
			break ;
		case "SUNSET RD,  9": return true ;
			break ;
		case "SUNSET RD, 11": return true ;
			break ;
		case "TALBOT AVENUE, 101": return true ;
			break ;
		case "TILTON HALL": return true ;
			break ;
		case "TOUSEY HOUSE": return true ;
			break ;
		case "WEST HALL": return true ;
			break ;
		case "WILSON HOUSE": return true ;
			break ;
		case "WINTHROP ST, 10": return true ;
			break ;
		case "WREN HALL": return true ;
			break ;
		case "WYETH HOUSE": return true ;
			break ;
	}
*/
}//finished?///

function fieldticket_validate_username($username)
{
	$username_regex = "/\A[a-zA-Z\d]{3,8}\Z/" ;
	if(preg_match($username_regex,$username))
		return true; 
	else 
		return false;
}

function validate_date($date)
{
	$date_regex = "/\A[0-9]{4}[-\/][0-9]{1,2}[-\/][0-9]{1,2}\Z/";
	if(preg_match($date_regex,$date))
	{ return true; } else { return false; }
}


?>
