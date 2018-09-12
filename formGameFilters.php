<div class="container">
	<div class="col-xs-12">
		<form action="" method="GET">
			<div class="form-group">
				<label for="maxprice">Max Price</label>
				<input id="maxprice" class="form-control" value="<?=$_GET['maxprice'] ?>" name="maxprice" type="number" />
			</div>
			<div class="form-group">
				<input id="hidemygames" class="form-check-input" name="hidemygames" type="checkbox" <?=isset($_GET['hidemygames'])?'checked':''?> />
				<label class="form-check-label" for="hidemygames">Hide my games</label>
			</div>
			<div class="form-group">
				<input id="tradingcard" class="form-check-input" name="tradingcard" type="checkbox" <?=isset($_GET['tradingcard'])?'checked':''?> />
				<label class="form-check-label" for="tradingcard">Game with trading cards (Could be very long)</label>
			</div>
			<div class="form-group text-center">
				<input class="btn btn-info" type="submit" value="Search" />
			</div>
		</form>
	</div>
</div>

<div class="thumbnail filterGamesDynamically">
	<h3>Dynamic filters after games are loaded</h3>
	<div class="form-check">
		<input class="form-check-input" type="checkbox" value="" id="hideGamesWithoutCards">
		<label class="form-check-label" for="defaultCheck1">
		Hide games without cards
		</label>
	</div>

	<script type="text/javascript">
		$(document).ready(function(){
			$('#hideGamesWithoutCards').change(function(){
				$('.gameYouDontOwn').each(function(){
					if($(this).find('.haveTradingCard').length === 0)
					{
						$(this).hide();
					}
				});
			});
		});
	</script>
</div>

<?php
/*formGameFilters*/



?>