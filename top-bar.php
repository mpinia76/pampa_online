<div id="top_bar">
	<a href="#" onclick="$('#menu').toggle(); $('#menu .item').hide();"><div id="bt_menu"></div></a>
	<div id="texto">
	<img src="images/ico_users.png" align="absmiddle" /> Bienvenido 
	<a href="#" onClick="createWindow('w_usuario_edit','Editar usuario','usuarios.am.php?comun=1&dataid=<?php echo $_SESSION['userid']?>&extras=off','600','400');">
	<?php echo $_SESSION['usernombre']?></a>
	&nbsp; <a href="index.php?exit=on"><img border="0" src="images/bt_exit.png" align="absmiddle" /></a>
	</div>
</div>