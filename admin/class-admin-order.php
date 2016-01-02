<?php if (!defined('ABSPATH')) exit;
if (!class_exists('WBM_Admin_Order')) {
    class WBM_Admin_Order{
        public function __construct() {
            add_action( 'add_meta_boxes', array(&$this,'add_order_meta_box'));
	        add_action( 'save_post',array(&$this,'wbm_save_order_user'));
	        add_filter('manage_edit-shop_order_columns', array(&$this,'add_new_shop_order_column'),15);
	        add_action('manage_shop_order_posts_custom_column', array(&$this,'manage_shop_order_column'), 10, 2);
	        add_filter('woocommerce_admin_order_actions',array(&$this,'wbm_manage_buttons'),99,2);
	        add_action('woocommerce_order_actions_start',array(&$this,'wbm_order_save_button'),15,1);
        }
		public function add_order_meta_box(){
			add_meta_box(
				'wbm-manager-assign',
				__( 'Booking Manager','wbm' ),
				array(&$this,'wbm_order_meta'),
				'shop_order',
				'side',
				'default'
			);
		}
	    function wbm_order_meta( $post ){
		    $assigned_user=get_post_meta($post->ID,'_wbm_order_user',true);
		    $check_user=$assigned_user==get_current_user_id()?true:false;
			if(is_super_admin()){
		?>
			<h4><?=__('Choose a User','wbm')?></h4>
			<select name="wbm_order_user">
				<option value="">---</option>
				<?php $all_users=get_users(array('role'=>'wbm_employee','orderby'=>'display_name'));
				if(!empty($all_users)){
					foreach($all_users as $user){
						$selected=$user->ID==$assigned_user?"selected":"";
						?>
						<option value="<?=$user->ID?>" <?=$selected;?>><?=$user->display_name;?></option>
					<?php }}?>
			</select>
		<?php }else{ if(empty($assigned_user) || $check_user){
				$checked=$assigned_user==get_current_user_id()?"checked":"";
				?>
				<p>
					<label for="wbm_order_user"><input id="wbm_order_user" type="checkbox" <?=$checked;?> name="wbm_order_user" value="<?=get_current_user_id();?>"> <?=__('Assign Yourself','wbm')?></label>
				</p>
				<?php }else{ $user_data=get_user_by('id',$assigned_user); ?>
				<p><?=$user_data->display_name;?></p>
			<?php }}
	    }
	    function wbm_save_order_user($post_id){
		    if(isset($_POST["wbm_order_user"]) && !empty($_POST["wbm_order_user"])){
			    update_post_meta($post_id,"_wbm_order_user",absint($_POST["wbm_order_user"]));
		    }else{
			    delete_post_meta($post_id,"_wbm_order_user");
		    }
	    }
	    function add_new_shop_order_column($columns){
		    $columns["wbm_assign_to"]=__('Assign To','wbm');
		    return $columns;
	    }
	    function manage_shop_order_column($column){
		    global $post, $woocommerce, $the_order;
		    switch ($column) {
			    case "wbm_assign_to" :
				    $wbm_assign_to = get_post_meta( $post->ID, '_wbm_order_user', true );
				    if ( !empty( $wbm_assign_to ) ) {
					    $user_data = get_user_by( 'id', $wbm_assign_to );
					    echo $user_data->display_name;
				    } else {
					    echo '---';
					}
				    break;

		    }
	    }
	    function wbm_manage_buttons($actions,$order){
		    if(!is_super_admin()){
			    $wbm_assign_to = get_post_meta( $order->id, '_wbm_order_user', true );
			    if(empty($wbm_assign_to) || $wbm_assign_to==get_current_user_id()){
				    return $actions;
			    }else{
				    $actions=array();
			    }
		    }
		    return $actions;
	    }
	    function wbm_order_save_button($product_id){
		    if(!is_super_admin()){
			    $wbm_assign_to = get_post_meta( $product_id, '_wbm_order_user', true );
			    if(empty($wbm_assign_to) || $wbm_assign_to==get_current_user_id()){}else{
				    exit;
			    }
		    }
	    }
    }
    new WBM_Admin_Order();
}
