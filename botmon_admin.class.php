<?php


class BotmonAdmin {



	public static function build_options_page()
	{
		$noErrors = true;

		?>

		<form method="post" action="options.php"
		      enctype="multipart/form-data">  <?php settings_fields('botmon_options'); ?>  <?php do_settings_sections('botmon.php'); ?>

			<p class="submit"><input <?=$noErrors?'':'disabled="disabled"'?> name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>"/></p>

		</form>


		Force data file update: <a href="<?=BOTMON__PLUGIN_URL?>update.php" target="_botmon_dat_file_update"><?=BOTMON__PLUGIN_URL?>update.php</a>
	<?php
	}


}

