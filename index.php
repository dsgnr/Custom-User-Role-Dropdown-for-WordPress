// Add two new role.
// Full list of capabilities can be found at http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table
 add_role('writer', 'Writer', array( 
	'delete_posts' => true,
	'delete_published_posts' => true,
	'edit_posts' => true,
	'edit_published_posts' => true,
	'publish_posts' => true,
	'read' => true,
	'upload_files' => true,
	'edit_users' => true
));

  add_role('uk', 'uk', array( 
	'delete_posts' => true,
	'delete_published_posts' => true,
	'edit_posts' => true,
	'edit_published_posts' => true,
	'publish_posts' => true,
	'read' => true,
	'upload_files' => true,
	'edit_users' => true
));

 add_role('designer', 'Designer', array(
	'edit_files' => true,
	'edit_plugins' => true,
	'edit_theme_options' => true,
	'edit_themes' => true,
	'install_plugins' => true,
	'install_themes' => true,
	'switch_themes' => true,
	'update_plugins' => true,
	'update_themes' => true,
	'read' => true,
	'edit_users' => true
));

add_action('register_form','role_registration_form');
function role_registration_form(){
	$wp_roles = new WP_Roles();
	$wp_roles->use_db = true;
	$role_names = $wp_roles->get_names();
  
	foreach( $role_names as $role_name ) {
		// Ensure that the options exclude default Wordpress roles
		if ( ($role_name !== 'Administrator') and ($role_name !== 'Editor') and ($role_name !== 'Author') and ($role_name !== 'Contributor' ) and ($role_name !== 'Subscriber') ) {
			//  Role value below needs to be in lowercase only
			$role_option .= "<option value='".strtolower($role_name)."'>";
			$role_option .= $role_name;
			$role_option .= "</option>";
		}
	}
	$html = '
	<style type="text/css">
			#role {
			background:#FBFBFB none repeat scroll 0 0;
			border:1px solid #E5E5E5;
			font-size:24px;
			margin-bottom:16px;
			margin-right:6px;
			margin-top:2px;
			padding:3px;
			width:97%;
		}
	</style>
	
	<div width="100%">
		<p>
			<label style="display: block; margin-bottom: 5px;">' . __('Role', 'Role') . '
				<select id="role" name="role" class="input">
				' . $role_option . '
				</select>
			</label>
		</p>
	</div>
	';
	echo $html;
}

add_action('user_register', 'register_role');
function register_role($user_id, $password="", $meta=array()) {

   $userdata = array();
   $userdata['ID'] = $user_id;
   $userdata['role'] = $_POST['role'];

   // allow if a role is selected
   if ( $userdata['role'] ){
      wp_update_user($userdata);
   }
}


add_action( 'show_user_profile', 'role_selection_field' );
add_action( 'edit_user_profile', 'role_selection_field' );
function role_selection_field( $user ) {
	$wp_roles = new WP_Roles();
	$wp_roles->use_db = true;
	$role_names = $wp_roles->get_names();

	foreach( $role_names as $role_name ) {
		if ( ($role_name !== 'Administrator') and ($role_name !== 'Editor') and ($role_name !== 'Author') and ($role_name !== 'Contributor' ) and ($role_name !== 'Subscriber') ) {
			if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
				foreach ( $user->roles as $role ) {			
					if ( strtolower($role_name) == $role ) {
						$role_option .= "<option value='".strtolower($role_name)."' selected='selected'>";
						$currentrole = strtolower($role_name);
					} else {
						$role_option .= "<option value='".strtolower($role_name)."'>";
					}
					
					$role_option .= $role_name;
					$role_option .= "</option>";
				}
			}
		}
	}
?>
<h3><?php _e("Extra profile information", "blank"); ?></h3>
<style type="text/css">
#role { width: 15em; }
</style>
<table class="form-table">
	<tr>
		<th><label for="role"><?php _e("Role"); ?></label></th>
		<td>
			<select id="role" name="role" class="input">
			<?php 
			echo $role_option;
			?>
			</select>
			<span class="description"><?php _e("Select your role if you feel like going to the other side"); ?></span>
		</td>
	</tr>
</table>

<?php }
 
add_action( 'personal_options_update', 'save_role_selection_field' );
add_action( 'edit_user_profile_update', 'save_role_selection_field' );
function save_role_selection_field( $user_id ) {
	//if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }

	update_user_meta( $user_id, 'role', $_POST['role'] );
	
	$user = new WP_User( $user_id );

	// Remove role
	$current_user_role = get_current_user_role();
	
	$user->remove_role( $current_user_role );

	// Add role
	$user->add_role( $_POST['role'] );
}

function get_current_user_role () {
    global $current_user;
    get_currentuserinfo();
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
    return $user_role;
};
