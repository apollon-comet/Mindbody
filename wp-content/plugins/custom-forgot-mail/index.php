<?php
/*
Plugin Name: Custom Forgot Password Mail
Plugin URI: http://www.coralwebdesigns.com/
Version: 1.0
Author: Coral Web Design
Description: A plugin to create custom forgot mail
*/


add_action('admin_menu','write_forgot_mail');


function write_forgot_mail()
{

    add_menu_page('custom_forgot_Mail','Custom Forgot Password Mail','manage_options','Custom_Forgot_Mail','overwrite',plugins_url('/images/Forgot_password.png',__FILE__),82);

}


function overwrite()
{


    if($_POST)
    {
        if(get_option('forgot_mail_cwd')!==false)
        {
            update_option('forgot_mail_cwd',stripslashes($_POST['message']));
        }
        else
        {
            add_option('forgot_mail_cwd',stripslashes($_POST['message']));
        }

    }

     if(!get_option('forgot_mail_cwd'))
     {
         $text = '<p>Someone requested that the password be reset for the following account: <a href="'.get_bloginfo("url").'">'.get_bloginfo("url").'</a></p>
         Username: %username%<br/>
        <p>If this was a mistake, just ignore this email and nothing will happen.</p>
         To reset your password, visit the following address:<br/>
          <a href="%reseturl%">%reseturl%</a>';

         update_option('forgot_mail_cwd',$text);

     }

    echo '<form action="" method="post">';
    echo '<h2>Forgot password custom email:</h2><br><br>
    <textarea rows=10 cols=40 name="message" id="message" >'.get_option('forgot_mail_cwd').'</textarea><br><br>';
    echo '<b>(Note:)&nbsp;</b>Use placeholders <b>%username%</b> for username and <b>%reseturl%</b> for reset url<br><br>';
    echo '<input type="submit" name="setmesssage" value="Ok" class="button-primary">';
    echo '</form>';


}



function my_retrieve_password_subject_filter($old_subject)
{

    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    $subject = sprintf( __('[%s] Password Reset'), $blogname );

    return $subject;
}

function my_retrieve_password_message_filter($old_message, $key)
{

    if ( strpos( $_POST['user_login'], '@' ) )
    {
        $user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );

    }
    else
    {
        $login = trim($_POST['user_login']);
        $user_data = get_user_by('login', $login);
    }

    $user_login = $user_data->user_login;


    $custom = get_option('forgot_mail_cwd');
    $reset_url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');

    $message .= str_replace("%reseturl%",$reset_url,(str_replace("%username%",$user_login,$custom))); //. "\r\n";


    return $message;
}

// To get these filters up and running:
add_filter ( 'retrieve_password_title', 'my_retrieve_password_subject_filter', 10, 1 );
add_filter ( 'retrieve_password_message', 'my_retrieve_password_message_filter', 10, 2 );


    add_filter( 'wp_mail_content_type', 'set_content_type' );
    function set_content_type( $content_type )
    {
        return 'text/html';
    }

