<?php
 if($_POST['pmbc_hidden'] === "Y") {
//Form data sent
        $dbhost = $_POST['pmbc_dbhost'];
        update_option('pmbc_dbhost', $dbhost);
         
        $dbname = $_POST['pmbc_dbname'];
        update_option('pmbc_dbname', $dbname);
         
        $dbuser = $_POST['pmbc_dbuser'];
        update_option('pmbc_dbuser', $dbuser);
         
        $dbpwd = $_POST['pmbc_dbpwd'];
        update_option('pmbc_dbpwd', $dbpwd);

        $opac = $_POST['pmbc_opac'];
        update_option('pmbc_opac',$opac);

        $notice = $_POST['pmbc_notice'];
        update_option('pmbc_notice',$notice);

        $car_use = $_POST['pmbc_car_use'];
        update_option('pmbc_car_use',$car_use);
?>
<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
<?php } else {
        //Normal page display
        $dbhost = get_option('pmbc_dbhost');
        $dbname = get_option('pmbc_dbname');
        $dbuser = get_option('pmbc_dbuser');
        $dbpwd = get_option('pmbc_dbpwd');
        $opac = get_option('pmbc_opac');
        $notice = get_option('pmbc_notice');
        $car_use = get_option('pmbc_car_use');
    }
?>
<div class="wrap">
    <?php    echo "<h2>" . __( 'PMB connector Options', 'pmbc_trdom' ) . "</h2>"; ?>
     
    <form name="pmbc_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="pmbc_hidden" value="Y">
        <hr />
        <?php    echo "<h4>" . __( 'PMBc - local Database Settings', 'pmbc_trdom' ) . "</h4>"; ?>
        <p><?php _e("Database host: " ); ?><input type="text" name="pmbc_dbhost" value="<?php echo $dbhost; ?>" size="20"><?php _e(" ex: localhost" ); ?></p>
        <p><?php _e("Database name: " ); ?><input type="text" name="pmbc_dbname" value="<?php echo $dbname; ?>" size="20"><?php _e(" ex: bibli" ); ?></p>
        <p><?php _e("Database user: " ); ?><input type="text" name="pmbc_dbuser" value="<?php echo $dbuser; ?>" size="20"><?php _e(" ex: bibli" ); ?></p>
        <p><?php _e("Database password: " ); ?><input type="text" name="pmbc_dbpwd" value="<?php echo $dbpwd; ?>" size="20"><?php _e(" ex: bibli" ); ?></p>
        <hr />
        <?php    echo "<h4>" . __( 'PMBc - OPAC', 'pmbc_trdom' ) . "</h4>"; ?>
        <p><?php _e("opac_css adress: " ); ?><input type="text" name="pmbc_opac" value="<?php echo $opac; ?>" size="20"><?php _e(" ex: http://monsite.fr/pmb/opac_css/" ); ?></p> 
        <p><?php _e("WP notice page: " ); ?><input type="text" name="pmbc_notice" value="<?php echo $notice; ?>" size="20"><?php _e(" ex: http://monsite.fr/wp/notice" ); ?></p> 
        <hr />
        <?php    echo "<h4>" . __( 'PMBc - Carousel', 'pmbc_trdom' ) . "</h4>"; ?>
        <p><?php _e("using Carousel function : " ); ?><input type="text" name="pmbc_car_use" value="<?php echo $car_use; ?>" size="20"><?php _e(" Yes or No" ); ?></p> 
        <p class="submit">
        <input type="submit" name="Submit" value="<?php _e('Update Options', 'pmbc_trdom' ) ?>" />
        </p>
    </form>
</div>
