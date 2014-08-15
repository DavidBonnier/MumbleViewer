<?php
/**
	Copyright (C) août 2014  David Bonnier
	Inspirer du mumble Viewer de Dominik Radner en license GPL
	Compatible : 	- version 3.4 de ICE
					- version 1.2.4 de Mumble jusqu'à 1.2.8
	Problème principale, fonction getTree ne retourne pas les channels dans l'ordre.
	Obliger de faire un trie dans l'ordre des positions puis l'ordre alphabétique
*/

if(empty($_GET['police']))
	$_GET['police'] = 'Verdana';
else
	$_GET['police'] = htmlentities($_GET['police'], ENT_QUOTES);

if(empty($_GET['taille']))
	$_GET['taille'] = '12';
else
	$_GET['taille'] = htmlentities($_GET['taille'], ENT_QUOTES);

if(empty($_GET['couleur']))
	$_GET['couleur'] = 'Black';
else
	$_GET['couleur'] = htmlentities($_GET['couleur'], ENT_QUOTES);

?>

<html>
<head>
    <meta charset="UTF-8">
    <title>Mumble Viewer FlyerDavid</title>
    <style type="text/css"> 
		.div_channel {
		margin: 0px;
		padding: 0px;
		position:relative; top:0px;
		border-width: 0.1em;
		border-style: hidden;
		border-color: blue;
		font-family: <?php echo $_GET['police']; ?>;
		font-size: <?php echo $_GET['taille'];'px'; ?>;
		color:<?php echo $_GET['couleur']; ?>;
		}
		.div_player {
		margin: 0px;
		padding: 0px;
		position:relative; top:0px;
		border-width: 0.1em;
		border-style: hidden;
		border-color: red;
		font-family: <?php echo $_GET['police']; ?>;
		font-size: <?php echo $_GET['taille'].'px'; ?>;
		color:<?php echo $_GET['couleur']; ?>;
		}

		a:link {
		color:<?php echo $_GET['couleur']; ?>;
		text-decoration:none;
		}
		a:visited {
		color:<?php echo $_GET['couleur']; ?>;
		text-decoration:none;
		}
		a:active {
		color:<?php echo $_GET['couleur']; ?>; 
		text-decoration:none;
		}
		a:hover {
		color:<?php echo $_GET['couleur']; ?>; 
		text-decoration:underline
		}
	</style>
	<script type="text/javascript">
		IE4 = (document.all) ? 1 : 0;
		NS6 = (document.getElementById) ? 1 : 0;

		function set_Layer(layername)
		{

			theImage = layername;

			if (document.images[theImage].src.match('mid2'))
			{
				document.images[theImage].src = './images/list_tree_mid3.gif';
			} 
			else if (document.images[theImage].src.match('mid3'))
			{
				document.images[theImage].src = './images/list_tree_mid2.gif';
			} 
			else if (document.images[theImage].src.match('end2'))
			{
				document.images[theImage].src = './images/list_tree_end3.gif';
			} 
			else if (document.images[theImage].src.match('end3'))
			{
				document.images[theImage].src = './images/list_tree_end2.gif';
			} 

			theLayer = layername;
			if(IE4) {
				theStatus = document.all(theLayer).style.display;
				if (theStatus == 'none') {
					document.all(theLayer).style.display = "inline"; }
				else {
					document.all(theLayer).style.display = "none";} }
			if(NS6) 
			{
				theStatus = document.getElementById(theLayer).style.display;
				if (theStatus == 'none') {
					document.getElementById(theLayer).style.display = "inline"; }
				else {
					document.getElementById(theLayer).style.display = "none"; 
				} 
			}
		}
	</script>
</head>
<body>
	<?php

	//URL pour accéder au sereur Mumble
	$url='mumble://mumble.zenserv.fr:';
	$serverid = $_GET['serverid'];

	function printMainChannel($url, $tree) 
	{
		$channeldepth = 0;
		$menustatus = array("1","1");

		echo "<a href=\"".$url."\">".$tree[0]->name."</a><br>\n";

		if (count($tree['children']) + count($tree['users']) > 0)
		{
			echo "<div class=div_channel id=div_channel_".$tree[0]->id.">\n";			
			foreach ($tree['users'] as $players)
				printplayers($players, end($tree['users'])->userid, $channeldepth+1, $menustatus);

			foreach ($tree['children'] as $key => $children)
				printchannel($children, end($tree['children'])[0]->id, $channeldepth+1, $menustatus, $url);
			echo "</div>\n";
		}
	}

	function printchannel($channelobject, $lastid, $channeldepth, $menustatus, $url) 
	{
		$menustatus[$channeldepth] = 1;
		if ($channelobject[0]->id == $lastid)
			$menustatus[$channeldepth] = 0;
		
		$count = 1;
		while($count < $channeldepth)
	    {
			if ($menustatus[$count] == 0)
				echo "<img border=0 src=./images/list_tree_space.gif>";
			else
				echo "<img border=0 src=./images/list_tree_line.gif>";
		    $count++;
	    }

		if (count($channelobject['children']) + count($channelobject['users']) > 0)
		{
			if ($channelobject[0]->id != $lastid)
				echo "<a href=\"javascript:set_Layer('div_channel_".$channelobject[0]->id."')\"><img border=0 name=div_channel_".$channelobject[0]->id." src=./images/list_tree_mid2.gif></a>";
			else
				echo "<a href=\"javascript:set_Layer('div_channel_".$channelobject[0]->id."')\"><img border=0 name=div_channel_".$channelobject[0]->id." src=./images/list_tree_end2.gif></a>";
		}
		else 
		{
			if ($channelobject[0]->id != $lastid)
				echo "<img border=0 src=./images/list_tree_mid.gif>";
			else
				echo "<img border=0 src=./images/list_tree_end.gif>";
		}

		echo "<img border=0 src=./images/list_channel.gif>";
		echo "<a href=\"".$url."".str_replace(" ","%20",$channelobject[0]->name)."\">".$channelobject[0]->name."</a><br>\n";

		if (count($channelobject['children']) + count($channelobject['users']) > 0)
		{
			echo "<div class=div_channel id=div_channel_".$channelobject[0]->id.">\n";
			foreach ($channelobject['users'] as $players)
				printplayers($players, end($channelobject['users'])->userid, $channeldepth+1, $menustatus);

			foreach ($channelobject['children'] as $key => $children)
				printchannel($children, end($channelobject['children'])[0]->id, $channeldepth+1, $menustatus, $url);
			echo "</div>\n";
		}
		return $menustatus;
	}

	function printplayers($playerobject, $lastid, $channeldepth, $menustatus) 
	{
		echo "<div class=div_player id=div_player>\n";

		$menustatus[$channeldepth] = 1;		
		$count = 1;

		while($count < $channeldepth)
	    {
			if ($menustatus[$count] == 0)
				echo "<img border=0 src=./images/list_tree_space.gif>";
			else
				echo "<img border=0 src=./images/list_tree_line.gif>";
		    $count++;
	    }

		if ($playerobject->userid == $lastid)
			echo "<img border=0 src=./images/list_tree_end.gif>";
		else 
			echo "<img border=0 src=./images/list_tree_mid.gif>";
		echo "<img border=0 src=./images/list_player.gif>";
		echo $playerobject->name;

		if ($playerobject->userid != -1)
			echo "<img border=0 src=./images/player_auth.gif>";
		
		if ($playerobject->mute)
			echo "<img border=0 src=./images/player_unknown.gif>";	
		
		if ($playerobject->deaf)
			echo "<img border=0 src=./images/player_unknown2.gif>";	
		
		if ($playerobject->suppress)
			echo "<img border=0 src=./images/player_suppressed.gif>";	
		
		if ($playerobject->selfMute)
			echo "<img border=0 src=./images/player_selfmute.gif>";	
		
		if ($playerobject->selfDeaf)
			echo "<img border=0 src=./images/player_selfdeaf.gif>";	
		echo "<br></div>\n"; 
		return $menustatus;
	}

	if(!empty($serverid))
	{
		require_once 'class/VirtualServeurICE.php';
		$serv = VirtualServeurICE::instance((int) $serverid);
		if($serv)
		{
			if($serv->ouvert())
			{
				$url = $url . $serv->getConf('port') . '/';
				$tree = $serv->getTree();
				printMainChannel($url, $tree);				
			}
			else
				echo "Serveur n°".$serverid." non lancer";
		}
		else
			echo "Pas de serveur : n°".$serverid;
		$serv = null;
	}
	else
	{
		echo "Pas de serveur choisit";
	}

	?>
</body>
</html>