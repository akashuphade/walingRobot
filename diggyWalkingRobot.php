<?php

//Get arguments array
$intArgCount = count($argv);

//Check if all arguments are passed or not and if not then give error message
if($intArgCount!=5)
{
	echo "Please pass valid number of arguments";
	exit;
}

/* Array for directions */
$arrDirection = array('north','south','east','west');

//Get the passed arguments
$x 					= $argv[1]; 					//current X position
$y 					= $argv[2];						//current Y position
$robotDirection 	= $argv[3]; 					//current direction the robot is facing (North, East, South, West)
$strWalk 			= $argv[4];						//Walk string

/*validate arguments*/
$errorMessage = "";

//Check for valid X value
if(!is_numeric($x))
	$errorMessage.="value passed for X position of robot is invalid<br>";

//Check for valid Y value
if(!is_numeric($y))
	$errorMessage.="value passed for Y position of robot is invalid<br>";

//Check for valid Direction
if(!in_array(strtolower($robotDirection), $arrDirection))
	$errorMessage.="value passed for Current direction is invalid<br>";

//Replace all the characters which should not be in walk string 
$result = preg_replace("/[^rRlLwW0-9]+/", "", $strWalk);

//Check if walk string contains other characters than alphanumeric or after replacing 
//non allowed characters in string if both strings are not same means string is invalid
if(!ctype_alnum($strWalk) || $result!=$strWalk)
	$errorMessage.="walk string is invalid";

if(!empty($errorMessage))
{	
	echo $errorMessage;
	exit;
}


/* Following part of code is written for the scenario like 
	RW12LW22 -> where if units are more than 1 digit so we need 
	to extract that
*/
$previousChar = '';
$strLen = strlen($strWalk);
for($i=0; $i<$strLen; $i++)
{
	if(!empty($previousChar) && is_numeric($previousChar) && is_numeric($strWalk[$i]))
	{
		$arrWalk[count($arrWalk)-1] = "$previousChar$strWalk[$i]";
		$previousChar = "$previousChar$strWalk[$i]";
	}	
	else 
	{	
		$arrWalk[]= $strWalk[$i];
		$previousChar = $strWalk[$i];
	}
}

$previousAction = "";

/* Once we check all the validations then perform the further processing 
	to get the final direction of robot and X, Y positions */
foreach($arrWalk as $action)
{
	if(!is_numeric($action))
		$action = strtoupper($action);
	
	//Check for valid sequence of actions in walk String
	if((!empty($previousAction) && $previousAction == 'W' && !is_numeric($action)) || (!is_numeric($previousAction) && $previousAction!='W' && is_numeric($action)))
	{
		echo "Invalid walk string";
		exit;
	}	
	
	if($action == 'L' || $action == 'R')
	{
		$robotDirection = getDirection($robotDirection, $action);
	}
	elseif(is_numeric($action))
	{
		$arrXY = getXY($robotDirection, $x, $y, $action);
		$x = $arrXY['x'];
		$y = $arrXY['y'];
	}
	
	$previousAction = $action;
}

echo "X = ".$x." Y = ".$y." Direction = ".$robotDirection;

/*This will check the current direction and based on the input will give the next direction */
function getDirection($currentDirection, $rotation)
{
	switch(strtoupper($rotation))
	{
		case "R":
			switch(strtolower($currentDirection))
			{
				case "north":
					$direction = "East";
					break;
				case "east":
					$direction = "South";
					break;
				case "south":
					$direction = "West";
					break;
				case "west":
					$direction = "North";
					break;
			}
			break;
		case "L":
			switch(strtolower($currentDirection))
			{
				case "north":
					$direction = "West";
					break;
				case "west":
					$direction = "South";
					break;
				case "south":
					$direction = "east";
					break;
				case "east":
					$direction = "North";
					break;
			}
			break;
		
	}
	
	return $direction;
}

/*This function will get the direction and units to walk
and will return the X and Y Position in array */
function getXY($direction, $x, $y, $units)
{
	$arrXY = array();
	$x = $x;
	$y = $y;
	
	switch(strtolower($direction))
	{
		case "north":
			$y = $y + $units;
			break;
		case "west":
			$x = $x - $units;
			break;
		case "south":
			$y = $y - $units;
			break;
		case "east":
			$x = $x + $units;
			break;	
	}
	
	$arrXY['x'] = $x;
	$arrXY['y'] = $y;
	return $arrXY;
}

?>