<!DOCTYPE html>
<html>
	<head>
		<title>Find Tremor Games you don't own !</title>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

		<!-- Bootstrap Dialog -->
		<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>
	</head>
	<body>
		<style type="text/css">
			.gameYouDontOwn
			{
				margin:5px;
			}
		</style>
	<?php
		set_time_limit(0);
		use Sunra\PhpSimple\HtmlDomParser;
		require "vendor/autoload.php";
		include "constant.php";

		function checkIfGameExists($name, $yourGames)
		{
			foreach($yourGames as $oneGame)
			{
				if($name == $oneGame['name'])
				{
					return true;
				}
			}
			return false;
		}

		function displayGame($gameName, $price, $gamesQuantity, $gameOwned, $hideOwnedGame, $tremorLink, $haveTradingCard)
		{
			$class = '';
			if($gameOwned)
			{
				if($hideOwnedGame)
				{
					return;
				}
				$class = 'alert-success';
			}
			print '<div class="col-md-4 col-xs-12">';
				print '<div class="gameYouDontOwn '.$class.' thumbnail">';
					print '<div class="text-right">';
						print '<button gameToBan="'.$gameName.'" class="btn btn-danger neverShowMeThisAgain">';
							print 'Never show me this game again !';
						print '</button>';
					print '</div>';
					print '<div class="titleBlock">';
						print $gameName;
					print '</div>';
					print '<div class="priceBlock">';
						print $price;
					print '</div>';
					print '<div class="gameQuantityBlock">';
						print $gamesQuantity;
					print '</div>';
					if($haveTradingCard)
					{
						print '<div class="alert-success">';
							print 'Have Trading Cards !';
						print '</div>';
					}
					print '<div class="gotoTremor text-right">';
						print '<a target="_blank" href='.$tremorLink.' class="btn btn-info">';
							print 'see On Tremor ! ';
						print '</a>';
					print '</div>';
				print '</div>';
			print '</div>';
		}


		$urlSteamApiNoGameInfo   = "http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=".STEAM_API_KEY."&steamid=".STEAM_ID."&format=json";
		$urlSteamApiWithGameInfo = "http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=".STEAM_API_KEY."&steamid=".STEAM_ID."&format=json&include_appinfo=1";	

		$myGames = [];

		if(file_exists(FILENAME_GAMES_JSON))
		{
			//We Check if there is new games
			$gamesInformationsCached = json_decode(file_get_contents(FILENAME_GAMES_JSON), true);
			$myGames = $gamesInformationsCached;

			$jsonSteamNoGameInfo = json_decode(file_get_contents($urlSteamApiNoGameInfo), true);
			$gamesQuantity = $jsonSteamNoGameInfo['response']['game_count'];
			$oldGamesQuantity = $gamesInformationsCached['response']['game_count'];

			if($oldGamesQuantity != $gamesQuantity)
			{
				//We need to get Games informations
				$newGamesInformations = json_decode(file_get_contents($urlSteamApiWithGameInfo), true);
				file_put_contents(FILENAME_GAMES_JSON, json_encode($newGamesInformations));
				$myGames = $newGamesInformations;
			}
		}
		else
		{
			//We need to get Games informations
			$newGamesInformations = json_decode(file_get_contents($urlSteamApiWithGameInfo), true);
			file_put_contents(FILENAME_GAMES_JSON, json_encode($newGamesInformations));
			$myGames = $newGamesInformations;
		}

		$myGames = $myGames['response']['games'];

		$gamesBanned = [];
		if(file_exists(FILENAME_GAMES_BANNED))
		{
			$gamesBanned = json_decode(file_get_contents(FILENAME_GAMES_BANNED), true);
		}


		if(empty($_GET['maxprice']))
		{
			print 'Please put in GET variable your maxprice';
			die;
		}

		$hideOwnedGame = false;
		if(!empty($_GET['hidemygames']))
		{
			$hideOwnedGame = true;
		}
		$checkForTradingCard = false;
		if(!empty($_GET['tradingcard']))
		{
			$checkForTradingCard = true;
		}

		$pageNumber = 1;
		$maxPrice = $_GET['maxprice'];
		$price = 0;

		while (((int)$price) < ((int)$maxPrice)) 
		{
			$urlTremor = "http://www.tremorgames.com/index.php?action=shop&searchterm=steam+game&search_category=0&hideoutofstock=0&sort=price_asc&page=".$pageNumber;
			$dom = HtmlDomParser::file_get_html( $urlTremor, false, null, 0 );

			foreach($dom->find('div.shop_item_box') as $boxTremor)
			{
				$tremorLink = $boxTremor->find('.popover_tooltip')[0]->getAttribute('href');

				$counter = 1;

				$gameName = $boxTremor->find('.shop_item_box_name')[0]->find('a')[0]->innertext();

				$gameName = str_replace(' Steam Game', '', $gameName);
				$gameName = trim($gameName);

				if(in_array($gameName, $gamesBanned))
				{
					continue;
				}

				//Check DLC in the name ...
				if(!SHOW_DLC)
				{
					if(strpos($gameName, 'DLC') !== false)
					{
						continue;
					}
				}


				foreach($boxTremor->find('.shop_item_box_type') as $itemBox)
				{

					if($counter == 2)
					{
						//price
						$price = str_replace(' Tremor Coins','', $itemBox->innertext());
					}
					if($counter == 3)
					{
						//availability
						$itemQuantity = str_replace('In Stock : ','', $itemBox->innertext());
					}
					$counter++;
				}

				$haveTradingCard = false;
				if($checkForTradingCard)
				{
					$domTradingCard = HtmlDomParser::file_get_html( $tremorLink, false, null, 0 );
					
					if(!empty($domTradingCard->find('.well')))
					{
						foreach($domTradingCard->find('.well')[0]->find('ul')[1]->find('li') as $categorie)
						{
							if($categorie->find('a')[0]->innertext() == 'Steam Trading Cards')
							{
								$haveTradingCard = true;
								break;
							}
						}
					}
				}


				if($itemQuantity > 0)
				{
					$gameOwned = checkIfGameExists($gameName, $myGames);
					displayGame($gameName, $price, $itemQuantity, $gameOwned, $hideOwnedGame, $tremorLink, $haveTradingCard);
				}
			}
			$pageNumber++;
		}


	?>
	<script>
		$(document).ready(function(){
			$('.neverShowMeThisAgain').click(function(){
				var gameToBan = $(this).attr('gameToBan');
				var elementToRemove = $(this).parent().parent();

		        BootstrapDialog.confirm('Are you sure ?', function(result){
		            if(result) 
		            {
		                //Ok we'll remove the game name...
	                	$.ajax({
							method: "POST",
							url: "ajax/ajax_removeGameFromTheDashboard.php",
							data: { gamename: gameToBan }
						})
						.done(function( msg ) {
						    var response = JSON.parse(msg);
						    if(response.code == 200)
						    {
					    		elementToRemove.remove();
						    }
						    BootstrapDialog.alert(response.message);
						});
		            }
		            else 
		            {
		            	//Do nothing
		            }
		        });
			});
		});
	</script>
	</body>
</html>