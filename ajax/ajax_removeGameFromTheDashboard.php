<?php
	include "../constant.php";

	$fileNameGamesBanned = '../'.FILENAME_GAMES_BANNED;

	$response = [];
	$response['code'] = 200;
	$response['message'] = 'The game has been banned from your dashboard.';

	if(empty($_POST['gamename']))
	{
		$response['code'] = 400;
		$response['message'] = 'Please put the gamename variable as POST';
		print json_encode($response);
		exit();
	}
	$gameToBan = $_POST['gamename'];

	$gamesRemoved = [];
	if(file_exists($fileNameGamesBanned))
	{
		$gamesRemoved = json_decode(file_get_contents($fileNameGamesBanned), true);
		$gamesRemoved[] = $gameToBan;
		file_put_contents($fileNameGamesBanned, json_encode($gamesRemoved));
	}
	else
	{
		$gamesRemoved[] = $gameToBan;
		file_put_contents($fileNameGamesBanned, json_encode($gamesRemoved));	
	}

	print json_encode($response);
?>