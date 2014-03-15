	<?php
	/**
	 * Elgg plugin to allow using the web cam to capture profile icons
	 * 
	 *	captureicon.php - View for the Capture icon swf
	 */

	global $CONFIG;
	?>
	<div id="flashContent">
	<object type="application/x-shockwave-flash" data="<?php echo($CONFIG->wwwroot); ?>mod/webcam/swf/take_picture.swf" width="600" height="350" id="take_picture" style="float: none; vertical-align:middle">
				<param name="movie" value="take_picture.swf" />
				<param name="quality" value="high" />
				<param name="bgcolor" value="#ffffff" />
				<param name="play" value="true" />
				<param name="loop" value="true" />
				<param name="wmode" value="transparent" />
				<param name="scale" value="noscale" />
				<param name="menu" value="true" />
				<param name="devicefont" value="false" />
				<param name="salign" value="" />
				<param name="allowScriptAccess" value="sameDomain" />
				<a href="http://www.adobe.com/go/getflash">
				<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
				</a>
			</object>
	</p>
