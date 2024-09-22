<?php
$url = explode( '?', esc_url_raw( add_query_arg( array() ) ) );
$urltrimmedtab = $url[0];

$urlsettings = esc_url( add_query_arg( 'page', 'wp_fb-settings',$urltrimmedtab ) );
$urlwelcome = esc_url( add_query_arg( 'page', 'wp_google-welcome',$urltrimmedtab ) );
$urlgooglesettings = esc_url( add_query_arg( 'page', 'wp_google-googlesettings',$urltrimmedtab ) );
$urlgoogleapi = esc_url( add_query_arg( 'page', 'wp_google-googleplacesapi',$urltrimmedtab ) );
$urlgooglegooglecrawl = esc_url( add_query_arg( 'page', 'wp_google-googlecrawl',$urltrimmedtab ) );
$urlreviewlist = esc_url( add_query_arg( 'page', 'wp_google-reviews',$urltrimmedtab ) );
$urltemplateposts = esc_url( add_query_arg( 'page', 'wp_google-templates_posts',$urltrimmedtab ) );
$urlgetpro = esc_url( add_query_arg( 'page', 'wp_google-get_pro',$urltrimmedtab ) );
?>	
<div class="w3-bar w3-border w3-white">
  <a href="<?php echo $urlwelcome; ?>" class="w3-bar-item w3-button <?php if($_GET['page']=='wp_google-welcome'){echo 'w3-green';} ?>"><i class="fa fa-home"></i> <?php _e('Welcome', 'wp-google-reviews'); ?></a>
  <a href="<?php echo $urlgooglesettings; ?>" class="w3-bar-item w3-button <?php if($_GET['page']=='wp_google-googlesettings'){echo 'w3-green';} ?>"><i class="fa fa-search"></i> <?php _e('Get Google Reviews', 'wp-google-reviews'); ?></a>
  <a href="<?php echo $urlreviewlist; ?>" class="w3-bar-item w3-button <?php if($_GET['page']=='wp_google-reviews'){echo 'w3-green';} ?>"><i class="fa fa-list"></i> <?php _e('Review List', 'wp-google-reviews'); ?></a>
  <a href="<?php echo $urltemplateposts; ?>" class="w3-bar-item w3-button <?php if($_GET['page']=='wp_google-templates_posts'){echo 'w3-green';} ?>"><i class="fa fa-commenting-o"></i> <?php _e('Templates', 'wp-google-reviews'); ?></a>
  <a href="https://wpreviewslider.com/" target="_blank" class="goprohbtn w3-bar-item w3-button"><i class="fa fa-external-link-square" aria-hidden="true"></i> <?php _e('Get Pro Version', 'wp-google-reviews'); ?></a>
</div>