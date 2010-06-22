<?php
/*
Plugin Name: JR Sitemap
Plugin URI: http://www.jakeruston.co.uk/2010/05/wordpress-plugin-jr-sitemap/
Description: Allows you to generate a simple sitemap file for Google to use - automatically!
Version: 1.0.6
Author: Jake Ruston
Author URI: http://www.jakeruston.co.uk
*/

/*  Copyright 2010 Jake Ruston - the.escapist22@gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Hook for adding admin menus
add_action('admin_menu', 'jr_Sitemap_add_pages');

// action function for above hook
function jr_Sitemap_add_pages() {
    add_options_page('JR Sitemap', 'JR Sitemap', 'administrator', 'jr_Sitemap', 'jr_Sitemap_options_page');
}

if (!function_exists("_iscurlinstalled")) {
function _iscurlinstalled() {
if (in_array ('curl', get_loaded_extensions())) {
return true;
} else {
return false;
}
}
}

if (!function_exists("jr_show_notices")) {
function jr_show_notices() {
echo "<div id='warning' class='updated fade'><b>Ouch! You currently do not have cURL enabled on your server. This will affect the operations of your plugins.</b></div>";
}
}

if (!_iscurlinstalled()) {
add_action("admin_notices", "jr_show_notices");

} else {
if (!defined("ch"))
{
function setupch()
{
$ch = curl_init();
$c = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
return($ch);
}
define("ch", setupch());
}

if (!function_exists("curl_get_contents")) {
function curl_get_contents($url)
{
$c = curl_setopt(ch, CURLOPT_URL, $url);
return(curl_exec(ch));
}
}
}

if (!function_exists("jr_Sitemap_refresh")) {
function jr_Sitemap_refresh() {
update_option("jr_submitted_Sitemap", "0");
}
}

register_activation_hook(__FILE__,'Sitemap_choice');

function Sitemap_choice () {
if (get_option("jr_Sitemap_links_choice")=="") {

if (_iscurlinstalled()) {
$pname="jr_Sitemap";
$url=get_bloginfo('url');
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname);
update_option("jr_submitted_Sitemap", "1");
wp_schedule_single_event(time()+172800, 'jr_Sitemap_refresh'); 
} else {
$content = "Powered by <a href='http://arcade.xeromi.com'>Free Online Games</a> and <a href='http://directory.xeromi.com'>General Web Directory</a>.";
}

if ($content!="") {
$content=utf8_encode($content);
update_option("jr_Sitemap_links_choice", $content);
}
}

if (get_option("jr_Sitemap_link_personal")=="") {
$content = curl_get_contents("http://www.jakeruston.co.uk/p_pluginslink4.php");

update_option("jr_Sitemap_link_personal", $content);
}
}

// jr_effects_options_page() displays the page content for the Test Options submenu
function jr_Sitemap_options_page() {

    // variables for the field and option names
    $opt_name_1 = 'mt_Sitemap_click';	
    $opt_name_5 = 'mt_Sitemap_plugin_support';
	$opt_name_6 = 'mt_Sitemap_rss';
	$opt_name_7 = 'mt_Sitemap_highlight';
    $hidden_field_name = 'mt_Sitemap_submit_hidden';
	$data_field_name_1 = 'mt_Sitemap_click';
    $data_field_name_5 = 'mt_Sitemap_plugin_support';
	$data_field_name_6 = 'mt_Sitemap_rss';
	$data_field_name_7 = 'mt_Sitemap_highlight';

    // Read in existing option value from database
	$opt_val_1 = get_option($opt_name_1);
    $opt_val_5 = get_option($opt_name_5);
	$opt_val_6 = get_option($opt_name_6);
	$opt_val_7 = get_option($opt_name_7);
	
if (!$_POST['hidbuild']!="1") {
generate_sitemap();

?>
<div class="updated"><p><strong><?php _e('Sitemap Rebuilt!', 'mt_trans_domain' ); ?></strong></p></div>
<?php
}
    
if (!$_POST['feedback']=='') {
$my_email1="the.escapist22@gmail.com";
$plugin_name="JR Sitemap";
$blog_url_feedback=get_bloginfo('url');
$user_email=$_POST['email'];
$subject=$_POST['subject'];
$name=$_POST['name'];
$response=$_POST['response'];
$category=$_POST['category'];
if ($response=="Yes") {
$response="REQUIRED: ";
}
$feedback_feedback=$_POST['feedback'];
$feedback_feedback=stripslashes($feedback_feedback);
if ($user_email=="") {
$headers1 = "From: feedback@jakeruston.co.uk";
} else {
$headers1 = "From: $user_email";
}
$emailsubject1=$response.$plugin_name." - ".$category." - ".$subject;
$emailmessage1="Blog: $blog_url_feedback\n\nUser Name: $name\n\nUser E-Mail: $user_email\n\nMessage: $feedback_feedback";
mail($my_email1,$emailsubject1,$emailmessage1,$headers1);
?>

<div class="updated"><p><strong><?php _e('Feedback Sent!', 'mt_trans_domain' ); ?></strong></p></div>

<?php
}

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
		$opt_val_1 = $_POST[$data_field_name_1];
        $opt_val_5 = $_POST[$data_field_name_5];
		$opt_val_6 = $_POST[$data_field_name_6];
		$opt_val_7 = $_POST[$data_field_name_7];

        // Save the posted value in the database
		update_option( $opt_name_1, $opt_val_1 );
        update_option( $opt_name_5, $opt_val_5 );
		update_option( $opt_name_6, $opt_val_6 );
		update_option( $opt_name_7, $opt_val_7 );

        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php

    }

    // Now display the options editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'JR Sitemap Plugin Options', 'mt_trans_domain' ) . "</h2>";
$blog_url_feedback=get_bloginfo('url');
	$donated=curl_get_contents("http://www.jakeruston.co.uk/p-donation/index.php?url=".$blog_url_feedback);
	if ($donated=="1") {
	?>
		<div class="updated"><p><strong><?php _e('Thank you for donating!', 'mt_trans_domain' ); ?></strong></p></div>
	<?php
	} else {
	?>
	<div class="updated"><p><strong><?php _e('Please consider donating to help support the development of my plugins!', 'mt_trans_domain' ); ?></strong><br /><br /><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="ULRRFEPGZ6PSJ">
<input type="image" src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form></p></div>
<?php
}

    // options form
    
    $change3 = get_option("mt_Sitemap_plugin_support");
	$change4 = get_option("mt_Sitemap_click");
	$change5 = get_option("mt_Sitemap_rss");
	$change6 = get_option("mt_Sitemap_highlight");

if ($change3=="Yes" || $change3=="") {
$change3="checked";
$change31="";
} else {
$change3="";
$change31="checked";
}

if ($change4=="Yes" || $change4=="") {
$change4="checked";
$change41="";
} else {
$change4="";
$change41="checked";
}

if ($change5=="Yes") {
$change5="checked";
$change51="";
} else {
$change5="";
$change51="checked";
}

if ($change6=="Yes" || $change6=="") {
$change6="checked";
$change61="";
} else {
$change6="";
$change61="checked";
}
    ?>
	<iframe src="http://www.jakeruston.co.uk/plugins/index.php" width="100%" height="20%">iframe support is required to see this.</iframe>
<form name="rebuild" method="post" action="">
<input type="hidden" name="hidbuild" value="1" /><input type="submit" value="<?php _e('Manually Rebuild Sitemap', 'mt_trans_domain' ) ?>" />
</form>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Show Plugin Support?", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_5; ?>" value="Yes" <?php echo $change3; ?>>Yes
<input type="radio" name="<?php echo $data_field_name_5; ?>" value="No" <?php echo $change31; ?> id="Please do not disable plugin support - This is the only thing I get from creating this free plugin!" onClick="alert(id)">No
</p>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p><hr />

</form>
<script type="text/javascript">
function validate_required(field,alerttxt)
{
with (field)
  {
  if (value==null||value=="")
    {
    alert(alerttxt);return false;
    }
  else
    {
    return true;
    }
  }
}

function validateEmail(ctrl){

var strMail = ctrl.value
        var regMail =  /^\w+([-.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;

        if (regMail.test(strMail))
        {
            return true;
        }
        else
        {

            return false;

        }
					
	}

function validate_form(thisform)
{
with (thisform)
  {
  if (validate_required(subject,"Subject must be filled out!")==false)
  {email.focus();return false;}
  if (validate_required(email,"E-Mail must be filled out!")==false)
  {email.focus();return false;}
  if (validate_required(feedback,"Feedback must be filled out!")==false)
  {email.focus();return false;}
  if (validateEmail(email)==false)
  {
  alert("E-Mail Address not valid!");
  email.focus();
  return false;
  }
 }
}
</script>
<h3>Submit Feedback about my Plugin!</h3>
<p><b>Note: Only send feedback in english, I cannot understand other languages!</b><br /><b>Please do not send spam messages!</b></p>
<form name="form2" method="post" action="" onsubmit="return validate_form(this)">
<p><?php _e("Your Name:", 'mt_trans_domain' ); ?> 
<input type="text" name="name" /></p>
<p><?php _e("E-Mail Address (Required):", 'mt_trans_domain' ); ?> 
<input type="text" name="email" /></p>
<p><?php _e("Message Category:", 'mt_trans_domain'); ?>
<select name="category">
<option value="General">General</option>
<option value="Feedback">Feedback</option>
<option value="Bug Report">Bug Report</option>
<option value="Feature Request">Feature Request</option>
<option value="Other">Other</option>
</select>
<p><?php _e("Message Subject (Required):", 'mt_trans_domain' ); ?>
<input type="text" name="subject" /></p>
<input type="checkbox" name="response" value="Yes" /> I want e-mailing back about this feedback</p>
<p><?php _e("Message Comment (Required):", 'mt_trans_domain' ); ?> 
<textarea name="feedback"></textarea>
</p>
<p class="submit">
<input type="submit" name="Send" value="<?php _e('Send', 'mt_trans_domain' ); ?>" />
</p><hr /></form>
</div>
<?php } ?>
<?php
if (get_option("jr_Sitemap_links_choice")=="") {
Sitemap_choice();
}

function generate_sitemap() {
$file=ABSPATH."sitemap.xml";
	
$output='<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
			    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
		
$output .= "<url>
<loc>".get_option('siteurl')."</loc>
<changefreq>daily</changefreq>
<priority>1.0</priority>
</url>";

  $pages = get_pages(); 
  foreach ($pages as $pagg) {
  	$output .= "<url>
<loc>".get_page_link($pagg->ID)."</loc>
<changefreq>weekly</changefreq>
<priority>0.7</priority>
</url>";
  }
		
 global $post;
 $myposts = get_posts('numberposts=-1');
 foreach($myposts as $post) :
   setup_postdata($post);
			
$output .= "<url>
<loc>".get_permalink($post)."</loc>
<lastmod>".$post->post_modified."</lastmod>
<changefreq>monthly</changefreq>
<priority>0.4</priority>
</url>";
endforeach;

$output .= "</urlset>";	
@file_put_contents($file, $output);
}


function sitemap_rob() {
	echo "Sitemap: ".get_option('siteurl')."/sitemap.xml\n\n";
}

add_action('do_robotstxt', 'sitemap_rob');

function Sitemap_footer_plugin_support() {
if (get_option("jr_Sitemap_plugin_support")=="" || get_option("jr_Sitemap_plugin_support")=="Yes") {
$linkper=utf8_decode(get_option('jr_Sitemap_link_personal'));

if (get_option("jr_Sitemap_link_newcheck")=="") {
$pieces=explode("</a>", get_option('jr_Sitemap_links_choice'));
$pieces[0]=str_replace(" ", "%20", $pieces[0]);
$pieces[0]=curl_get_contents("http://www.jakeruston.co.uk/newcheck.php?q=".$pieces[0]."");
$new=implode("</a>", $pieces);
update_option("jr_Sitemap_links_choice", $new);
update_option("jr_Sitemap_link_newcheck", "444");
}

if (get_option("jr_submitted_Sitemap")=="0") {
$pname="jr_Sitemap";
$url=get_bloginfo('url');
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname);
update_option("jr_submitted_Sitemap", "1");
update_option("jr_Sitemap_links_choice", $content);

wp_schedule_single_event(time()+172800, 'jr_Sitemap_refresh'); 
} else if (get_option("jr_submitted_Sitemap")=="") {
$pname="jr_Sitemap";
$url=get_bloginfo('url');
$current=get_option("jr_Sitemap_links_choice");
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname."&override=".$current);
update_option("jr_submitted_Sitemap", "1");
update_option("jr_Sitemap_links_choice", $content);

wp_schedule_single_event(time()+172800, 'jr_Sitemap_refresh'); 
}

  $pshow = "<p style='font-size:x-small'>Sitemap Plugin created by ".$linkper." - ".stripslashes(get_option('jr_Sitemap_links_choice'))."</p>";
  echo $pshow;
}
}

add_action("wp_footer", "Sitemap_footer_plugin_support");
add_action("deleted_post", "generate_sitemap");
add_action("save_post", "generate_sitemap");

?>
