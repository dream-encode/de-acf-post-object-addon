<?php
/**
* Plugin Name: 		ACF Post Object Addon
* Plugin URI: 		https://github.com/dream-encode/de-acf-post-object-addon
* Description: 		A custom plugin to add ACF data to a post title for the "Post Object" field type in ACF.
* Version: 				1.1.4
* Author: 				David Baumwald
* Author URI: 		https://dream-encode.com/
* Text Domain: 		de-acfpoftao
**/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('DE_ACF_Post_Object_Addon_Plugin') ) :

class DE_ACF_Post_Object_Addon_Plugin {
	public $version = '1.1.4';
	private $acf_version;
	private $lang_domain = 'de-acfpoftao';
	private $plugin_name = 'ACF Post Object Add-On';
	private $plugin_key = 'de_acfpoftao';
	private $plugin_options_key = 'acf_post_object_field_type_add_on';
	private $plugin_icon = 'dashicons-randomize';
	private $plugin_menu_position = 99;
	private $settings_key = 'de_acfpoftao_settings';
	private $settings_pages = array(); 
	private $settings_page_options = array(); 
	private $sections = array();
	private $settings_fields = array();
	private $acf_field_groups_args = array( 
		'post_type' => 'portfolio', 
	);
	private $exclude_field_types = array(
		'radio',
		'checkbox',
		'image',
		'oembed',
		'file',
		'gallery',
		'true_false',
		'google_map',
		'message',
		'tab',
		'repeater',
		'flexible_content',
		'clone',
	);

	function __construct() { 
		// Init
		add_action( 'init', array( $this, 'load_settings' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		
		// Actions/filters
		add_filter( 'custom_menu_order', array( $this, 'acf_submenu_order' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_pages_to_menus' ) );
		
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_action_links' ) );
	
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue'), 10, 1 );
		
		add_action( 'wp_ajax_de_acfpoftao_update', array( $this, 'ajax_update' ) );
		
		add_action( 'admin_init', array( $this, 'add_acf_filter' ) );
		
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );


		register_activation_hook( __FILE__, array( $this, 'activate' ) );
	}
	
	function load_plugin_textdomain() {
	 	$plugin_dir = basename(dirname(__FILE__));
	 
	 	load_plugin_textdomain( $this->lang_domain, false, $plugin_dir );
	}

	/**
	 * Activation hook
	 * Install the profiler loader as a mu-plugin
	 */
	public static function activate() {
		global $wp_version;

		if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'advanced-custom-fields/acf.php' ) &&  !is_plugin_active( 'advanced-custom-fields-pro/acf.php' )  ) {
        	add_action( 'admin_notices', 'acf_plugin_required_notice' );

        	deactivate_plugins( plugin_basename( __FILE__ ) ); 

        	if ( isset( $_GET['activate'] ) ) {
            	unset( $_GET['activate'] );
        	}
    	} else {
    		update_option( $plugin_key.'_version', $this->version );
    	}
	}

	function acf_plugin_required_notice() {
?>

	<div class="error"><p>Sorry, but ACF Post Object Add-on requires either Advanced Custom Fields or Advanced Custom Fields PRO to be installed and active.</p></div>

<?php
	}

	public static function uninstall() {
		delete_option( $plugin_key.'_version' );
	}
	
	function add_acf_filter() {		
		if ( is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
			add_filter( 'acf/fields/post_object/result', array( $this, 'update_acf_post_object_field_choices'), 10, 4 );
		} else {
			add_action( 'admin_notices', array( $this, 'display_acf_notice' ) );
		}
	}
	
	function load_settings() {
		$this->acf_version = acf_get_setting( 'version' );
		$this->settings = (array) get_option( $this->settings_key );
	}
	
	function load_settings_page_options() {
		$this->settings_page_options[] = array(
			'name' => $this->plugin_name,
			'menus' => array(
				array(
					'is_main'		=> true,
					'type' 			=> 'sub',
					'parent' 		=> 'edit.php?post_type=acf-field-group',
					'title' 		=> $this->plugin_name,
					'capability' 	=> 'manage_options',
					'slug' 			=> $this->plugin_key,
					'icon' 			=> $this->plugin_icon,
					'position' 		=> $this->plugin_menu_position,
				),
			),
		); 
	}
	
	function load_settings_fields() {
		// Sections & Fields
		$this->settings_fields = array(
			'enable' => array(
				'key' => 'enable',
				'label' => __( 'Enable', $this->lang_domain ),
				'type' => 'checkbox',
				'required' => false,
				'default' => 'checked',
				'args' => array(
					'value' => '1',
					'validator' => '',
					'toggle' => 'append_field',
					'toggle_value' => '1',
				),
			),
			'append_field' => array(
				'key' => 'append_field',
				'label' => __('Field To Append', $this->lang_domain ),
				'type' => 'select',
				'default' => array(),
				'required' => 'conditional',
				'args' => array(
					'multiple' => false,
					'options' => self::get_acf_append_field_options(),
					'validator' => '',
					'required_conditions' => array(
						'operand' => 'and',
						'conditions' => array(
							array(
								'key' => 'enable',
								'value' => 1
							),
						),
					),
					'hint' => __( 'This field will be displayed after the post title.', $this->lang_domain ),
					'toggled_by' => 'enable',
					'class' => array(
						'select2',
					),
				),
			),
			'append_field_format' => array(
				'key' => 'append_field_format',
				'label' => __( 'Append Field Formatting', $this->lang_domain ),
				'type' => 'select',
				'default' => '',
				'required' => 'conditional',
				'args' => array(
					'multiple' => false,
					'options' => self::get_acf_append_field_format_options(),
					'validator' => '',
					'required_conditions' => array(
						'operand' => 'and',
						'conditions' => array(
							array(
								'key' => 'enable',
								'value' => 1
							),
						),
					),
					'hint' => __( 'This is how the append field will be formatted.', $this->lang_domain ),
					'toggled_by' => 'enable',
					'class' => array(
						'select2',
					),
				),
			),
		); 
	}
	
	function load_setting_sections() { 
		// Page settings
		self::load_settings_fields();
		
		// Sections & Fields
		$this->sections['Settings'] = array(
			'name' => __( 'Settings', $this->lang_domain ),
			'description' => __( 'Settings for ACF Post Object Field Type plugin.  This plugin is specfically for appending post data to each option for the "post object" field type.  When enabled, the selected fields will be suffixed to each available option in "post object" selects.', $this->lang_domain ),
			'id' => 'settings',
			'fields' => $this->settings_fields,
		); 
	}

	function add_settings_pages_to_menus() { 
		// Page settings
		self::load_settings_page_options();
		
		foreach ($this->settings_page_options as $page) {
			if ( is_array($page['menus']) ) {
				foreach ($page['menus'] as $index => $page_menu) {
					if ( $page_menu['type'] == 'sub' && $page_menu['parent'] !== false ) {
						// parent_slug: The slug name for the parent menu, or the file name of a standard WordPress admin file
						// page_title: Text that will go into the HTML page title for the page when the submenu is active.
						// menu_title: The text to be displayed in the title tags of the page when the menu is selected.
						// capability: The capability required for this menu to be displayed to the user.
						// menu_slug: For existing WordPress menus, the PHP file that handles the display of the menu page content.
						// function: The function that displays the page content for the menu page.
						$this->settings_pages[] = add_submenu_page( 
							$page_menu['parent'],
							__($page['name'], $this->lang_domain),
							__($page_menu['title'], $this->lang_domain),
							$page_menu['capability'], 
							$page_menu['slug'],
							array( $this, 'options_page' ),
							$page_menu['icon']
						);
					} else {
						// page_title: The text to be displayed in the title tags of the page when the menu is selected.
						// menu_title: The on-screen name text for the menu.
						// capability : The capability required for this menu to be displayed to the user. 
						// menu_slug: The slug name to refer to this menu by (should be unique for this menu). 
						// function: The function that displays the page content for the menu page.
						// icon_url: The url to the icon to be used for this menu. (optional).
						// position: The position in the menu order this menu should appear. By default, is bottom. (optional)
						$this->settings_pages[] = add_menu_page( 
							__($page['name'], $this->lang_domain),
							__($page_menu['title'], $this->lang_domain),
							$page_menu['capability'], 
							$page_menu['slug'],
							array( $this, 'options_page' ),
							$page_menu['icon'],
							$page_menu['position']
						);
					}
				}
			}
		}
	}
	
	function settings_init() {
		// Sections & Fields
		self::load_setting_sections();
		
		$sections = apply_filters( 'de/acf-post-object-addon/alter_settings_sections', $this->sections) ;
		
		register_setting( $this->settings_key, $this->settings_key );
	
		foreach ($sections as $section) {
			// Section
			// id: String for use in the 'id' attribute of tags.
			// title: Title of the section.
			// callback: Function that fills the section with the desired content. 
			// page: The menu page on which to display this section. 
			$section_id = $section['id'];
			$section_callback = $section['name'].'_section_callback';
			
			add_settings_section(
				$section_id,
				__($section['name'], $this->lang_domain),
				array( $this, $section_callback ),
				$this->settings_key
			);
	
			if ($section['fields']) {
				foreach ($section['fields'] as $field_key => $field_data) {
					// id: String for use in the 'id' attribute of tags.
					// title: Title of the field.
					// callback: Function that fills the field with the desired inputs as part of the larger form. Passed $args array. 
					// page: The menu page on which to display this field. 
					// section: The section of the settings page in which to show the box (default or section added above)
					// args: Additional arguments that are passed to the $callback function
					$field_id = $field_key;
					
					// Default field args
					$field_data['label_for'] = $field_id;
					
					add_settings_field( 
						$field_id,
						__($field_data['label'], $this->lang_domain),
						array( $this, 'settings_field_render' ),
						$this->settings_key,
						$section_id,
						$field_data
					);
				}
			}
		}
	}
	
	function plugin_action_links( $links ) {
		 $links[] = '<a href="'. esc_url( menu_page_url( $this->plugin_key, false ) ) .'">' . __( 'Settings', $this->lang_domain ) . '</a>';
		 
		 return $links;
	}
	
	function add_action_plugin( $actions, $plugin_file ) {
		static $plugin;
	
		if (!isset($plugin)) {
			$plugin = plugin_basename(__FILE__);
		}
		
		if ($plugin == $plugin_file) {
			$settings = array(
				'settings' => '<a href="'. esc_url( menu_page_url( $this->plugin_key, false ) ) .'">' . __( 'Settings', $this->lang_domain ) . '</a>'
			);
		
			$actions = array_merge($settings, $actions);
			$actions = array_merge($site_link, $actions);		
		}
			
		return $actions;
	}
	
	function admin_enqueue($hook) {
    	$screen = get_current_screen();
		
		if ( in_array($screen->id, $this->settings_pages) == false ) return;

		wp_enqueue_style( 'de-acfpoftao-select2-style', plugins_url('de-acf-post-object-addon/assets/dist/vendor/select2/dist/css/select2.min.css'));	
		wp_enqueue_style( 'de-acfpoftao-checkbox-style', plugins_url('de-acf-post-object-addon/assets/dist/vendor/iCheck/skins/flat/grey.css'));	
		wp_enqueue_style( 'de-acfpoftao-settings-style', plugins_url('de-acf-post-object-addon/assets/dist/css/style.css'));	
		
		wp_register_script( 'de-acfpoftao-select2-js', plugins_url('de-acf-post-object-addon/assets/dist/vendor/select2/dist/js/select2.min.js'), array('jquery'), NULL );		
		wp_register_script( 'de-acfpoftao-checkbox-js', plugins_url('de-acf-post-object-addon/assets/dist/vendor/iCheck/icheck.min.js'), array('jquery'), NULL );
		wp_register_script( 'de-acfpoftao-settings-js', plugins_url('de-acf-post-object-addon/assets/dist/js/settings.js'), array('jquery', 'de-acfpoftao-select2-js'), NULL );		
				
		wp_enqueue_script( 'de-acfpoftao-select2-js' );			
		wp_enqueue_script( 'de-acfpoftao-checkbox-js' );
		wp_enqueue_script( 'de-acfpoftao-settings-js' );
	}
	
	function settings_field_render($field_data) {	
		$html = '';
		$classes = array();					
		
		if (is_array($field_data) && !empty($field_data)) {
			$field_key = $field_data['key'];
			$field_name = $this->settings_key.'['.$field_key.']';
			$field_id = $this->plugin_key.'-'.$field_data['key'];
			$field_value = isset($this->settings[$field_key]) ? $this->settings[$field_key] : '';
			$field_params = $field_data['args'];
			
			$field_attrs = '';
			
			if (is_array($field_params) && !empty($field_params)) {						
				if (isset($field_params['attrs']) && is_array($field_params['attrs']) && !empty($field_params['attrs'])) {
					foreach ($field_params['attrs'] as $attr_key => $attr_val) {
						$field_attrs .= ' '.$attr_key.'="'.esc_attr($attr_val).'"'; 
					}
				}
				
				if (isset($field_params['toggle']) && !empty($field_params['toggle'])) {
					$field_attrs .= ' data-toggle="'.$this->plugin_key.'-'.$field_params['toggle'].'" data-toggle-value="'.$field_params['toggle_value'].'"'; 
				}
					
				if (isset($field_params['toggled_by']) && !empty($field_params['toggled_by'])) {
					$field_attrs .= ' data-toggled-by="'.$this->plugin_key.'-'.$field_params['toggled_by'].'"'; 
					
					$toggler_value = $this->settings[$field_params['toggled_by']];
					
					if ($toggler_value != $this->sections['Settings']['fields'][$field_params['toggled_by']]['args']['toggle_value']) {
						$classes[] = ' conditional-hidden';
					}
				}
				
				if (isset($field_params['class']) && !empty($field_params['class'])) {
					foreach ($field_params['class'] as $class) {
						$classes[] = $class;
					}
				}
				
				if (!empty($classes)) {
					$field_attrs .= ' class="'.implode(" ", $classes).'"';
				}
			}			
			
			switch ($field_data['type']) {
				case 'text':		
				case 'hidden':		
				case 'password':		
				case 'number':	
				case 'email':			
				case 'tel':		
				case 'url':
				case 'search':		
				case 'range':	
				case 'date':		
				case 'datetime':	
				case 'datetime-local':	
				case 'month':	
				case 'week':
				case 'time':									
					$html .= '<input type="'.$field_data['type'].'" name="'.esc_attr($field_name).'" id="'.esc_attr($field_id).'" value="'.esc_attr($field_value).'"'.$field_attrs.'>';
					break;
					
				case 'checkbox':
					$checked = $field_value == $field_params['value'];
							
					$input = '<input type="checkbox" name="'.esc_attr($field_name).'" id="'.esc_attr($field_id).'" value="'.esc_attr($field_params['value']).'"'.($checked  ? ' checked' : '').$field_attrs.' />';
					
					if (isset($field_params['wrap_label']) && false !== $field_params['wrap_label']) {
						$input = '<label for="'.esc_attr($field_name.'_'.$value).'">'.$input.$label.'</label> ';
					}
					
					$html .= $input;
					 
					break;

				case 'checkbox_multi':
					if (is_array($field_params) && isset($field_params['options']) && !empty($field_params['options'])) {
						foreach ($field_params['options'] as $value => $label) {
							$checked = false;
							
							$checked = is_array($field_value) && in_array($value, $field_value);
							
							$input = '<input type="checkbox" '.checked($checked, true, false).' name="'.esc_attr($field_name).'[]" value="'.esc_attr($k).'" id="'.esc_attr($field_id.'_'.$value).'"'.$field_attrs.' />';
							
							if ($field_params['wrap_label']) {
								$input = '<label for="'.esc_attr($field_name.'_'.$value).'">'.$input.$label.'</label> ';
							}
							
							$html .= $input;
						}
					}
					break;

				case 'radio':
					if (is_array($field_params) && isset($field_params['options']) && !empty($field_params['options'])) {
						foreach ($field_params['options'] as $value => $label) {
							$checked = false;
							
							$checked = $value == $field_value;
							
							$input = '<input type="radio" '.checked($checked, true, false).' name="'.esc_attr($field_name).'" value="'.esc_attr($value).'" id="'.esc_attr($field_id.'_'.$value).'"'.$field_attrs.' />';
							
							if ($field_params['wrap_label']) {
								$input = '<label for="'.esc_attr($field_name.'_'.$value).'">'.$input.$label.'</label> ';
							}
						
							$html .= $input;
						}
					}
					break;
					
				case 'select':
					$is_multiple = false;
					$select_name = esc_attr($field_name);
					
					if (is_array($field_params) && isset($field_params['multiple']) && $field_params['multiple'] !== false) {
						$is_multiple = true;
						
						$select_name .= '[]';
					}
					
					$html .= '<select name="'.$select_name.'" id="'.esc_attr($field_id).'"'.$field_attrs;
					
					if ($is_multiple) {
						$html .= ' multiple="multiple"';
					}
					
					$html .= '>'; 
					 
					if (is_array($field_params) && isset($field_params['options']) && !empty($field_params['options'])) {
						$html .= self::select_choices($field_params['options'], $field_value);
					}
						
					$html .= '</select>';
					break;

				case 'textarea':
						$html .= '<textarea id="'.esc_attr($field_id).'" rows="'.$field_params['rows'].'" cols="'.$field_params['rows'].'" name="'.esc_attr($field_name).'"'.$field_attrs.'>'.$field_value.'</textarea>';
						break;

				case 'image':
					$image_thumb = '';
					
					if ($field_value) {
						$image_thumb = wp_get_attachment_thumb_url($field_value);
					}
					
					$html .= '<img id="'.esc_attr($field_name).'_preview" class="image_preview" src="'.$image_thumb.'" /><br/>';
					$html .= '<input id="'.esc_attr($field_name).'_button" type="button" data-uploader_title="'.__('Upload an image', $this->lang_domain).'" data-uploader_button_text="'.__('Use image', $this->lang_domain).'" class="image_upload_button button" value="'.__('Upload new image', $this->lang_domain).'" />';
					$html .= '<input id="'.esc_attr($field_name).'_delete" type="button" class="image_delete_button button" value="'.__('Remove image', $this->lang_domain) . '" />';
					$html .= '<input id="'.esc_attr($field_name).'" class="image_data_field" type="hidden" name="'.esc_attr($field_name).'" value="'.$field_value.'"/>';
					break;
			}
			 
			if (is_array($field_params) && !empty($field_params)) {
				if (isset($field_params['hint']) && !empty($field_params['hint'])) {
					$html .= '<p class="description" id="'.$this->plugin_key.'-'.esc_attr($field_name).'-description">'.$field_params['hint'].'</p>'; 
				}
			}
		}
     
    echo $html;	
	}
	
	function select_choices($choices, $values) {	
		$choices_html  = '';
			
		if( empty($choices) ) return $choices_html;	
		
		foreach($choices as $id => $value ) {			
			if( is_array($value) ){
				$choices_html .= '<optgroup label="'.esc_attr($id).'">';				

				$choices_html .= self::select_choices( $value, $values );		
				
				$choices_html .= '</optgroup>';	

				continue;				
			}		

			$option_value = html_entity_decode($id);
			
			if ( is_array($values) ) {			
				$is_selected = array_search($option_value, $values);
			} else {
				$is_selected = $option_value == $values;
			}	
			
			$choices_html .= '<option value="'.esc_attr($id).'"';				
			
			if( $is_selected !== false ) {				
				$choices_html .= ' data-i="'.(int)$is_selected.'" selected';					
			}			
			
			$choices_html .= '>'.$value.'</option>';			
		}	
		
		return $choices_html;	
	}
	
	function get_acf_append_field_options() {	
		$options_array = array();
		
		$options_array['Post']['id'] = __('Post ID', $this->lang_domain);
		
		$field_groups_args = apply_filters( 'de/acf_post_object_addon/alter_acf_field_group_args', $this->acf_field_groups_args );			
		
		$groups = apply_filters( 'de/acf_post_object_addon/alter_acf_field_groups', acf_get_field_groups( $field_groups_args ) );		
		
		if( $groups ) {			
			foreach( $groups as $index => $group ){	
				$group_id = $group['ID'];	
				$group_title = $group['title'];	

				$fields = array();
		
				if ( version_compare( '5.7.11', $this->acf_version ) ) {
					$fields = apply_filters( 'de/acf_post_object_addon/alter_acf_field_group_fields', acf_get_fields( $group_id ) );
				} else {
					$fields = apply_filters( 'de/acf_post_object_addon/alter_acf_field_group_fields', acf_get_fields_by_id( $group_id ) );
				}
		
				if( $fields && is_array($fields) && !empty($fields) ) {			
					foreach( $fields as $index => $field_data ){	
						$field_type = $field_data['type'];	
						$field_name = $field_data['name'];	
						$field_label = $field_data['label'];	
							
						if (is_array($this->exclude_field_types) && !in_array($field_type, $this->exclude_field_types)) {
							$options_array[$group_title][esc_attr($field_name)] = __($field_label, $this->lang_domain);
						}
					}
				}
			}
		}
		
		return $options_array;
	}
	
	function get_acf_append_field_format_options() {
		$post_title_text = __( 'Post Title', $this->lang_domain );
		$append_field_value_text = __( 'Append Field Value', $this->lang_domain );
		
		$format_options = array(
			'wrap::par' 				=> $post_title_text.' &lpar;'.$append_field_value_text.'&rpar;',
			'separator::dash' 	=> $post_title_text.' &dash; '.$append_field_value_text,
			'separator::colon' 	=> $post_title_text.'&colon; '.$append_field_value_text,
			'separator::gt' 		=> $post_title_text.' &gt; '.$append_field_value_text,
			'separator::raquo' 	=> $post_title_text.' &raquo; '.$append_field_value_text,
			'separator::gg' 		=> $post_title_text.' &gg; '.$append_field_value_text,
			'separator::verbar' => $post_title_text.' &verbar; '.$append_field_value_text,
			'wrap::brack' 			=> $post_title_text.' &lbrack;'.$append_field_value_text.'&rbrack;',
			'wrap::brace' 			=> $post_title_text.' &lbrace;'.$append_field_value_text.'&rbrace;',
		);
		
		return $format_options;
	}
	
	function settings_section_callback($args) {
		echo '<p>'.$this->sections[$args['title']]['description'].'</p>';
	}
	
	function options_page() {
?>

    <div class="wrap"> 
      <h2><?php echo __( $this->plugin_name, $this->lang_domain ); ?></h2>

      <?php settings_errors(); ?>
    	<div id="saved"></div>

      <form method="post" action="options.php" id="<?php echo $this->plugin_key; ?>-form">
				<?php settings_fields( $this->settings_key ); ?>
        <?php do_settings_sections( $this->settings_key ); ?>     
        
        <p class="submit">      
        	<?php submit_button( __( 'Save Changes', $this->lang_domain ), 'primary', $this->plugin_key.'-submit', false, array( 'id' => $this->plugin_key.'-submit' ) ); ?>&nbsp;&nbsp;<img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" id="<?php echo $this->plugin_key; ?>-ajax-working" class="hidden">
        </p>
      </form> 
    </div>
    
<?php
} 
	
	function validate_inputs( $input ) {   
    $output = array();
		$message = NULL;
    $type = NULL;
		
		return $input;
 
		foreach ( $this->settings_fields as $field_key => $field_data ) {
			/*if ( isset($field_data['required']) && !empty($field_data['required']) ) { 
				if ( $field_data['required'] === true ) {
					if ( !isset($input[$field_key]) || empty($input[$field_key]) ) {
						$type = 'error';
						$message = __( $field_data.' is required', $this->lang_domain );
							
						add_settings_error(
							$this->settings_key,
							esc_attr( 'settings_updated' ),
							$message,
							$type
						);
					}
				} else if ( $field_data['required'] == 'conditional' ) {
					$conditionals_test = self::validate_condtional_requirements( $input, $field_data );
					
					if ( $conditionals_test['fail'] === true ) {
						foreach ( $conditionals_test['failures'] as $failure ) {
							$type = 'error';
							$message = __( $failure['reason'], $this->lang_domain );
							
							add_settings_error(
								$this->settings_key,
								esc_attr( 'settings_updated' ),
								$message,
								$type
							);
						}
					}						
				}
			}*/
			
			switch ( $field_data['type'] ) {						
				case 'checkbox':
					$output[$key] = (isset($input[$key]) && !empty($input[$key])) ? 1 : 0;
					break; 
					
				case 'radio':						  
				case 'input':
				case 'select':
				case 'textarea':
					if ( isset($input[$field_key]) ) {
						$output[$key] = sanitize_text_field(strip_tags(stripslashes($input[$key])));
					} 
					break;						
			}				
    }
     
    return apply_filters('de/acf_post_object_addon/validate_inputs', $output, $input); 
	} 
	
	function validate_condtional_requirements( $input, $field_data ) {
		$fail = false;
		$conditional_results = array();	
		
		$field_value = $input[$field_key];	
		
		$conditional_operator = isset($field_data['args']['required_conditions']['operand']) ? $field_data['args']['required_conditions']['operand'] : 'AND';
		$conditionals = $field_data['args']['required_conditions']['conditions'];
		
		if ( !is_array($conditionals) ) {
			$conditionals = array( 
				'key' => $conditionals['key'],
				'value' => $conditionals['value'],				
			);
		}
		
		if ( $conditional_operator == 'AND' ) {	
			$conditional_errors = array();
				
			foreach ( $conditionals as $condition ) {
				if ( !isset($input[$condition['key']]) || $input[$condition['key']] != $condition['value'] ) {
					
					$message = $field_data['label'].' is required if '.$this->settings_fields[$condition['key']]['label'].' is ';
					
					if ( $this->settings_fields[$condition['key']]['type'] == 'checkbox' ) {
						$message .= $condition['value'] == 1 ? 'enabled' : 'disabled';
					} else {
						$message .= $condition['value'];
					}
					
					$conditional_errors[] = $condition['key'];
					$conditional_results[] = __( $message, $this->lang_domain );
				}
			}
		} else {
			$conditional_errors = array();
				
			foreach ( $conditionals as $condition ) {
				if ( !isset($input[$condition['key']]) || $input[$condition['key']] != $condition['value'] ) {
					$conditional_error = true;
					$fail = true;
					
					$message = $field_data['label'].' '.__( 'is required if', $this->lang_domain ).' '.$this->settings_fields[$condition['key']]['label'].' '.__( 'is', $this->lang_domain ).' ';
					
					if ( $this->settings_fields[$condition['key']]['type'] == 'checkbox' ) {
						$message .= $condition['value'] == 1 ? 'enabled' : 'disabled';
					} else {
						$message .= $condition['value'];
					}
					
					$conditional_errors[] = $condition['key'];
					$conditional_results[] = __( $message, $this->lang_domain );
				}
			}	
			
			if ( count($conditional_results) == count($conditionals) ) {
				$fail = true;
			}
		}
		
		return array( 'fail' => $fail, 'results' => $conditional_results );
	}
	
	function ajax_update() { 
		check_ajax_referer( '_wpnonce', '_wpnonce' );
		
    $data = $_POST;
		
    unset($data['option_page'], $data['action'], $data['_wpnonce'], $data['_wp_http_referer']);
 
    if (update_option($this->settings_key, $data)) {
			die('1');
		} else {
			die('0');
		}        
	}
	
	function acf_submenu_order( $menu_ord ) {
		global $submenu;
	
		// Enable the next line to see all menu orders
		$link = "edit.php?post_type=acf-field-group";
		$menu_item = $submenu[$link][4];
		unset($submenu[$link][4]);
	
		$arr = array();
		$i = 0;	
		
		foreach ($submenu[$link] as $index => $item) {
			$arr[$i] = $submenu[$link][$index];
			
			$i += 5;
		}
		
		// Add back our menu item
		$arr[$i] = $menu_item;	
		
		$submenu[$link] = $arr;
	
		return $menu_ord;
	}
	
	function update_acf_post_object_field_choices($title, $post, $field, $post_id) {
		$enabled = $this->settings['enable'];
		$append_field = $this->settings['append_field'];
		$format = apply_filters( 'de/acfpoftao/append_field_data_format', $this->settings['append_field_format'] );
		
		if ($enabled && !empty($append_field)) {
			$append_field_value = get_field($append_field, $post->ID);
			
			if (!empty($append_field_value)) {
				switch (true) {
					case preg_match("/^separator::(.*)/", $format, $matches):
						$title .= ' &' . $matches[1] . '; ' . $append_field_value;
						break;
					case preg_match("/^wrap::(.*)/", $format, $matches):
						$title .= ' &l' . $matches[1] . ';' . $append_field_value . '&r' . $matches[1] . ';';
						break;
					default:
						$title .= ' (' . $append_field_value .  ')';
						break;					
				}
			}
		}
		
		$title = apply_filters( 'de/acfpoftao/format_post_title', $title, $post, $field, $post_id );
	
		return $title;	
	}
}

// Initialize the plugin
add_action( 'plugins_loaded', create_function( '', '$settings_de_acf_post_object_addon_plugin = new DE_ACF_Post_Object_Addon_Plugin;' ) );

endif;