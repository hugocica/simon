<?php
if ( ! class_exists('NHP_Options') ){
	
	// windows-proof constants: replace backward by forward slashes - thanks to: https://github.com/peterbouwmeester
	$fslashed_dir = trailingslashit(str_replace('\\','/',dirname(__FILE__)));
	$fslashed_abs = trailingslashit(str_replace('\\','/',ABSPATH));
	
	if(!defined('NHP_OPTIONS_DIR')){
		define('NHP_OPTIONS_DIR', $fslashed_dir);
	}

	
	if(!defined('NHP_OPTIONS_URL')){
		define('NHP_OPTIONS_URL', site_url(str_replace( $fslashed_abs, '', $fslashed_dir )));
	}
	
	if(!defined('THEME_NAME')){
		define('THEME_NAME', 'NAMO');
	}
	
	if(!defined('THEME_VERSION')){
		define('THEME_VERSION', '1.5.1');
	}
	
class NHP_Options{
	
	protected $framework_url = 'http://leemason.github.com/NHP-Theme-Options-Framework/';
	protected $framework_version = '1.0.6';
		
	public $dir = NHP_OPTIONS_DIR;
	public $url = NHP_OPTIONS_URL;
	public $page = '';
	public $args = array();
	public $sections = array();
	public $extra_tabs = array();
	public $errors = array();
	public $warnings = array();
	public $options = array();
	public $theme_name = THEME_NAME;
	public $theme_version = THEME_VERSION;
	
	

	/**
	 * Class Constructor. Defines the args for the theme options class
	 *
	 * @since NHP_Options 1.0
	 *
	 * @param $array $args Arguments. Class constructor arguments.
	*/
	function __construct($sections = array(), $args = array(), $extra_tabs = array()){
		
		$defaults = array();
		
		$defaults['opt_name'] = '';//must be defined by theme/plugin
		
		//$defaults['google_api_key'] = 'AIzaSyCkK4tQEoWgvuXMq5hN9jf2iG0knuZobzU';//must be defined for use with google webfonts field type
		
		$defaults['menu_icon'] = NHP_OPTIONS_URL.'/be-themes_image/icon/be-themes-small.png';
		$defaults['menu_title'] = __('Options', 'be-themes');
		$defaults['page_icon'] = 'icon-themes';
		$defaults['page_title'] = __('Options', 'be-themes');
		$defaults['page_slug'] = '_options';
		$defaults['page_cap'] = 'manage_options';
		$defaults['page_type'] = 'menu';
		$defaults['page_parent'] = '';
		$defaults['page_position'] = 100;
		$defaults['allow_sub_menu'] = false;
		
		$defaults['show_import_export'] = true;
		$defaults['dev_mode'] = false;
		$defaults['stylesheet_override'] = false;
		
		$defaults['footer_credit'] = '<span id="footer-thankyou">'.__('Namo Options', 'be-themes').'</span>';
		
		$defaults['help_tabs'] = array();
		$defaults['help_sidebar'] = __('', 'be-themes');
		
		//get args
		$this->args = wp_parse_args($args, $defaults);
		$this->args = apply_filters('nhp-opts-args-'.$this->args['opt_name'], $this->args);
		
		//get sections
		$this->sections = apply_filters('nhp-opts-sections-'.$this->args['opt_name'], $sections);
		
		//get extra tabs
		$this->extra_tabs = apply_filters('nhp-opts-extra-tabs-'.$this->args['opt_name'], $extra_tabs);
		
		//set option with defaults
		add_action('init', array(&$this, '_set_default_options'));

		add_action('wp_ajax_be_themes_data_save', array(&$this, '_be_themes_save_ajax'));
		
		//options page
		add_action('admin_menu', array(&$this, '_options_page'));
		
		//register setting
		add_action('admin_init', array(&$this, '_register_setting'));
		
		//add the js for the error handling before the form
	//	add_action('nhp-opts-page-before-form-'.$this->args['opt_name'], array(&$this, '_errors_js'), 1);
		
		//add the js for the warning handling before the form
		add_action('nhp-opts-page-before-form-'.$this->args['opt_name'], array(&$this, '_warnings_js'), 2);
		
		//hook into the wp feeds for downloading the exported settings
		add_action('do_feed_nhpopts-'.$this->args['opt_name'], array(&$this, '_download_options'), 1, 1);
		
		//get the options for use later on
		$this->options = get_option($this->args['opt_name']);
		
	}//function
	
	
	/**
	 * ->get(); This is used to return and option value from the options array
	 *
	 * @since NHP_Options 1.0.1
	 *
	 * @param $array $args Arguments. Class constructor arguments.
	*/
	function get($opt_name, $default = null){
		return (!empty($this->options[$opt_name])) ? $this->options[$opt_name] : $default;
	}//function
	
	/**
	 * ->set(); This is used to set an arbitrary option in the options array
	 *
	 * @since NHP_Options 1.0.1
	 * 
	 * @param string $opt_name the name of the option being added
	 * @param mixed $value the value of the option being added
	 */
	function set($opt_name = '', $value = '') {
		if($opt_name != ''){
			$this->options[$opt_name] = $value;
			update_option($this->args['opt_name'], $this->options);
		}//if
	}
	
	/**
	 * ->show(); This is used to echo and option value from the options array
	 *
	 * @since NHP_Options 1.0.1
	 *
	 * @param $array $args Arguments. Class constructor arguments.
	*/
	function show($opt_name, $default = ''){
		$option = $this->get($opt_name);
		if(!is_array($option) && $option != ''){
			echo $option;
		}elseif($default != ''){
			echo $default;
		}
	}//function
	
	
	
	/**
	 * Get default options into an array suitable for the settings API
	 *
	 * @since NHP_Options 1.0
	 *
	*/
	function _default_values(){
		
		$defaults = array();
		
		foreach($this->sections as $k => $section){
			
			if(isset($section['fields'])){
		
				foreach($section['fields'] as $fieldk => $field){
					
					if(!isset($field['std'])){$field['std'] = '';}
						
						$defaults[$field['id']] = $field['std'];
					
				}//foreach
			
			}//if
			
		}//foreach
		
		//fix for notice on first page load
		$defaults['last_tab'] = 0;

		return $defaults;
		
	}
	
	
	
	/**
	 * Set default options on admin_init if option doesnt exist (theme activation hook caused problems, so admin_init it is)
	 *
	 * @since NHP_Options 1.0
	 *
	*/
	function _set_default_options(){
		if(!get_option($this->args['opt_name'])){
			add_option($this->args['opt_name'], $this->_default_values());
		}
		$this->options = get_option($this->args['opt_name']);
	}//function
	
	
	/**
	 * Class Theme Options Page Function, creates main options page.
	 *
	 * @since NHP_Options 1.0
	*/
	function _options_page(){
		if($this->args['page_type'] == 'submenu'){
			if(!isset($this->args['page_parent']) || empty($this->args['page_parent'])){
				$this->args['page_parent'] = 'themes.php';
			}
		/*	$this->page = add_submenu_page(
							$this->args['page_parent'],
							$this->args['page_title'], 
							$this->args['menu_title'], 
							$this->args['page_cap'], 
							$this->args['page_slug'], 
							array(&$this, '_options_page_html')
						); */
		}else{
			$this->page = add_theme_page (
							$this->args['page_title'], 
							$this->args['menu_title'], 
							$this->args['page_cap'], 
							$this->args['page_slug'], 
							array(&$this, '_options_page_html')
						);
			
		}//else 

		add_action('admin_enqueue_scripts', array(&$this, '_enqueue'));
		add_action('load-'.$this->page, array(&$this, '_load_page'));
	}//function	
	
	

	/**
	 * enqueue styles/js for theme page
	 *
	 * @since NHP_Options 1.0
	*/
	function _enqueue($hook){
		if( 'appearance_page_namo_theme_options' != $hook ) {
        	return;
        }
		
		wp_register_style (
			'nhp-opts-css', 
			$this->url.'css/options.css',
			array('farbtastic'),
			time(),
			'all'
		);
			
		wp_register_style(
			'nhp-opts-jquery-ui-css',
			apply_filters('nhp-opts-ui-theme', $this->url.'css/jquery-ui-aristo/aristo.css'),
			'',
			time(),
			'all'
		);
		
		// wp_register_style(
		// 	'bootstrap-icons',
		// 	apply_filters('bootstrap-icons', $this->url.'css/bootstrap/css/bootstrap.min.css'),
		// 	'',
		// 	time(),
		// 	'all'
		// );
	//	wp_enqueue_style( 'bootstrap-icons' );
			
			
		if(false === $this->args['stylesheet_override']){
			wp_enqueue_style('nhp-opts-css');
		}
		
		
		wp_enqueue_script( 'pnotify-js', $this->url.'js/jquery.pnotify.min.js');

		wp_enqueue_script( 'nhp-opts-js', $this->url.'js/options.js', array('jquery','pnotify-js'));

		wp_localize_script('nhp-opts-js', 'nhp_opts', array('reset_confirm' => __('Are you sure? Resetting will loose all custom values.', 'be-themes'), 'opt_name' => $this->args['opt_name']));
		
		do_action('nhp-opts-enqueue-'.$this->args['opt_name']);
		
		
		foreach($this->sections as $k => $section){
			
			if(isset($section['fields'])){
				
				foreach($section['fields'] as $fieldk => $field){
					
					if(isset($field['type'])){
					
						$field_class = 'NHP_Options_'.$field['type'];
						
						if(!class_exists($field_class)){
							require_once($this->dir.'fields/'.$field['type'].'/field_'.$field['type'].'.php');
						}//if
				
						if(class_exists($field_class) && method_exists($field_class, 'enqueue')){
							$enqueue = new $field_class('','',$this);
							$enqueue->enqueue();
						}//if
						
					}//if type
					
				}//foreach
			
			}//if fields
			
		}//foreach
			
		
	}//function
	
	/**
	 * Download the options file, or display it
	 *
	 * @since NHP_Options 1.0.1
	*/
	function _download_options(){
		//-'.$this->args['opt_name']
		if(!isset($_GET['secret']) || $_GET['secret'] != md5(AUTH_KEY.SECURE_AUTH_KEY)){wp_die('Invalid Secret for options use');exit;}
		if(!isset($_GET['feed'])){wp_die('No Feed Defined');exit;}
		$backup_options = get_option(str_replace('nhpopts-','',$_GET['feed']));
		$backup_options['nhp-opts-backup'] = '1';
		$content = '###'.serialize($backup_options).'###';
		
		
		if(isset($_GET['action']) && $_GET['action'] == 'download_options'){
			header('Content-Description: File Transfer');
			header('Content-type: application/txt');
			header('Content-Disposition: attachment; filename="'.str_replace('nhpopts-','',$_GET['feed']).'_options_'.date('d-m-Y').'.txt"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			echo $content;
			exit;
		}else{
			echo $content;
			exit;
		}
	}
	
	
	
	
	/**
	 * show page help
	 *
	 * @since NHP_Options 1.0
	*/
	function _load_page(){
		
		//do admin head action for this page
		add_action('admin_head', array(&$this, 'admin_head'));
		
		//do admin footer text hook
		add_filter('admin_footer_text', array(&$this, 'admin_footer_text'));
		
		$screen = get_current_screen();
		
		if(is_array($this->args['help_tabs'])){
			foreach($this->args['help_tabs'] as $tab){
				$screen->add_help_tab($tab);
			}//foreach
		}//if
		
		if($this->args['help_sidebar'] != ''){
			$screen->set_help_sidebar($this->args['help_sidebar']);
		}//if
		
		do_action('nhp-opts-load-page-'.$this->args['opt_name'], $screen);
		
	}//function
	
	
	/**
	 * do action nhp-opts-admin-head for theme options page
	 *
	 * @since NHP_Options 1.0
	*/
	function admin_head(){
		
		do_action('nhp-opts-admin-head-'.$this->args['opt_name'], $this);
		
	}//function
	
	
	function admin_footer_text($footer_text){
		return $this->args['footer_credit'];
	}//function
	
	
	
	
	/**
	 * Register Option for use
	 *
	 * @since NHP_Options 1.0
	*/
	function _register_setting(){
		
		register_setting($this->args['opt_name'].'_group', $this->args['opt_name'], array(&$this,'_validate_options'));
		
		foreach($this->sections as $k => $section){
			
			add_settings_section($k.'_section', $section['title'], array(&$this, '_section_desc'), $k.'_section_group');
			
			if(isset($section['fields'])){
			
				foreach($section['fields'] as $fieldk => $field){
					
					if(isset($field['title'])){
					
						$th = (isset($field['sub_desc']))?$field['title'].'<span class="description">'.$field['sub_desc'].'</span>':$field['title'];
					}else{
						$th = '';
					}
					$title = (isset($field['tooltip'])) ? '<a href="'.$field['tooltip'].'" class="tooltip"></a>'.$th : $th;											
					add_settings_field($fieldk.'_field', $title, array(&$this,'_field_input'), $k.'_section_group', $k.'_section', $field); // checkbox
					
				}//foreach
			
			}//if(isset($section['fields'])){
			
		}//foreach
		
		do_action('nhp-opts-register-settings-'.$this->args['opt_name']);
		
	}//function
	
	/**
	 * Ajax Options Saving.
	 *
	 * @since BE Themes V 2.0
	*/
	function _be_themes_save_ajax() {
		
		check_ajax_referer('be-themes-data', 'security');
		unset($_POST['security'], $_POST['action']);
		
		if($_POST['trigger'] == 'import'  ){
			$imported_options = stripslashes_deep($_POST[$this->args['opt_name']]['import_code']);
			$imported_options = unserialize(trim($imported_options,'###'));
			if(update_option($this->args['opt_name'], $imported_options))	
				die('1');
			else
				die('0');	
		}
		elseif($_POST['trigger'] == 'save'  && update_option($this->args['opt_name'], $_POST[$this->args['opt_name']])){	
			die('1');
		}
		elseif($_POST['trigger'] == 'reset' && update_option($this->args['opt_name'], $this->_default_values() )){
			die('2');
		}
		else{
			die('0');
		}
	}

	
	/**
	 * Validate the Options options before insertion
	 *
	 * @since NHP_Options 1.0
	*/
	function _validate_options($plugin_options){
		
		set_transient('nhp-opts-saved', '1', 1000 );
		
		if(!empty($plugin_options['import'])){
			
			if($plugin_options['import_code'] != ''){
				$import = $plugin_options['import_code'];
			}elseif($plugin_options['import_link'] != ''){
				$import = wp_remote_retrieve_body( wp_remote_get($plugin_options['import_link']) );
			}
			
			$imported_options = unserialize(trim($import,'###'));
			if(is_array($imported_options) && isset($imported_options['nhp-opts-backup']) && $imported_options['nhp-opts-backup'] == '1'){
				$imported_options['imported'] = 1;
				return $imported_options;
			}
			
			
		}
		
		
		if(!empty($plugin_options['defaults'])){
			$plugin_options = $this->_default_values();
			return $plugin_options;
		}//if set defaults

		
		//validate fields (if needed)
		$plugin_options = $this->_validate_values($plugin_options, $this->options);
		
		if($this->errors){
			set_transient('nhp-opts-errors-'.$this->args['opt_name'], $this->errors, 1000 );		
		}//if errors
		
		if($this->warnings){
			set_transient('nhp-opts-warnings-'.$this->args['opt_name'], $this->warnings, 1000 );		
		}//if errors
		
		do_action('nhp-opts-options-validate-'.$this->args['opt_name'], $plugin_options, $this->options);
		
		
		unset($plugin_options['defaults']);
		unset($plugin_options['import']);
		unset($plugin_options['import_code']);
		unset($plugin_options['import_link']);
		
		return $plugin_options;	
	
	}//function
	
	
	
	
	/**
	 * Validate values from options form (used in settings api validate function)
	 * calls the custom validation class for the field so authors can override with custom classes
	 *
	 * @since NHP_Options 1.0
	*/
	function _validate_values($plugin_options, $options){
		foreach($this->sections as $k => $section){
			
			if(isset($section['fields'])){
			
				foreach($section['fields'] as $fieldk => $field){
					$field['section_id'] = $k;
					
					if(isset($field['type']) && $field['type'] == 'multi_text'){continue;}//we cant validate this yet
					
					if(!isset($plugin_options[$field['id']]) || $plugin_options[$field['id']] == ''){
						continue;
					}
					
					//force validate of custom filed types
					
					if(isset($field['type']) && !isset($field['validate'])){
						if($field['type'] == 'color' || $field['type'] == 'color_gradient'){
							$field['validate'] = 'color';
						}elseif($field['type'] == 'date'){
							$field['validate'] = 'date';
						}
					}//if
	
					if(isset($field['validate'])){
						$validate = 'NHP_Validation_'.$field['validate'];
						
						if(!class_exists($validate)){
							require_once($this->dir.'validation/'.$field['validate'].'/validation_'.$field['validate'].'.php');
						}//if
						
						if(class_exists($validate)){
							$validation = new $validate($field, $plugin_options[$field['id']], $options[$field['id']]);
							$plugin_options[$field['id']] = $validation->value;
							if(isset($validation->error)){
								$this->errors[] = $validation->error;
							}
							if(isset($validation->warning)){
								$this->warnings[] = $validation->warning;
							}
							continue;
						}//if
					}//if
					
					
					if(isset($field['validate_callback']) && function_exists($field['validate_callback'])){
						
						$callbackvalues = call_user_func($field['validate_callback'], $field, $plugin_options[$field['id']], $options[$field['id']]);
						$plugin_options[$field['id']] = $callbackvalues['value'];
						if(isset($callbackvalues['error'])){
							$this->errors[] = $callbackvalues['error'];
						}//if
						if(isset($callbackvalues['warning'])){
							$this->warnings[] = $callbackvalues['warning'];
						}//if
						
					}//if
					
					
				}//foreach
			
			}//if(isset($section['fields'])){
			
		}//foreach
		return $plugin_options;
	}//function
	
	
	
	
	
	
	
	
	/**
	 * HTML OUTPUT.
	 *
	 * @since NHP_Options 1.0
	*/
	function _options_page_html(){
		
		echo '<div class="wrap">';
			echo '<div id="'.$this->args['page_icon'].'" class="icon32"><br/></div>';
			
			do_action('nhp-opts-page-before-form-'.$this->args['opt_name']);
			echo '<div id="be-themes-dialog" title="" class="pattern-wrapper"><img src="" /></div>';	
			echo '<form method="post" action="options.php" enctype="multipart/form-data" id="nhp-opts-form-wrapper">';
				
				$this->options['last_tab'] = (isset($this->options['last_tab'])) ?  (isset($_GET['tab']) && !get_transient('nhp-opts-saved'))?$_GET['tab']:$this->options['last_tab'] :  0;
				
				
				echo '<input type="hidden" id="last_tab" name="'.$this->args['opt_name'].'[last_tab]" value="'.$this->options['last_tab'].'" />';
				echo '<input type="hidden" class="field-url" value="'.NHP_OPTIONS_URL.'">';
				echo '<input type="hidden" name="action" value="be_themes_data_save" />';
    			echo '<input type="hidden" name="security" value="'.wp_create_nonce('be-themes-data').'" />';
    			echo '<input type="hidden" name="option_page" value="'.$this->args['opt_name'].'_group">';

				echo '<div id="nhp-opts-header" class="clearfix">';
				echo '<div class="left"><h1 class="opt-header-title">'.$this->theme_name.' <span>'.$this->theme_version.'</span></h1></div>';
				$my_theme = wp_get_theme();
				echo '</div>';
				
					if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-saved') == '1'){
						if(isset($this->options['imported']) && $this->options['imported'] == 1){
							echo '<div id="nhp-opts-imported">'.apply_filters('nhp-opts-imported-text-'.$this->args['opt_name'], __('<strong>Settings Imported!</strong>', 'be-themes')).'</div>';
						}else{
							echo '<div id="nhp-opts-save">'.apply_filters('nhp-opts-saved-text-'.$this->args['opt_name'], __('<strong>Settings Saved!</strong>', 'be-themes')).'</div>';
						}
						delete_transient('nhp-opts-saved');
					}
					
					echo '<div id="nhp-opts-save">'.apply_filters('nhp-opts-saved-text-'.$this->args['opt_name'], __('<strong>Settings Saved!</strong>', 'be-themes')).'</div>';					
					echo '<div id="nhp-opts-field-reset">'.apply_filters('nhp-opts-saved-text-'.$this->args['opt_name'], __('<strong>Settings Reset!</strong>', 'be-themes')).'</div>';					
					echo '<div id="nhp-opts-save-warn">'.apply_filters('nhp-opts-changed-text-'.$this->args['opt_name'], __('<strong>Settings have changed!, you should save them!</strong>', 'be-themes')).'</div>';
					echo '<div id="nhp-opts-field-errors">'.__('<strong><span></span> error(s) were found!</strong>', 'be-themes').'</div>';
					
					echo '<div id="nhp-opts-field-warnings">'.__('<strong><span></span> warning(s) were found!</strong>', 'be-themes').'</div>';

					echo '<div id="nhp-opts-field-not-save">'.__('<strong><span></span> No Changes Made!</strong>', 'be-themes').'</div>';
				
				echo '<div class="clear"></div><!--clearfix-->';
				
				
				
				echo '<div id="nhp-opts-main">';
					echo '<div id="nhp-opts-sidebar">';
					echo '<ul id="nhp-opts-group-menu">';
						foreach($this->sections as $k => $section){
							$icon = (!isset($section['icon']))?'<img src="'.$this->url.'img/glyphicons/glyphicons_019_cogwheel.png" /> ':'<img src="'.$section['icon'].'" /> ';
							echo '<li id="'.$k.'_section_group_li" class="nhp-opts-group-tab-link-li">';
								echo '<a href="javascript:void(0);" id="'.$k.'_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="'.$k.'">'.$icon.'<span>'.$section['title'].'</span></a>';
							echo '</li>';
						}
						
						do_action('nhp-opts-after-section-menu-items-'.$this->args['opt_name'], $this);
						
						if(true === $this->args['show_import_export']){
							echo '<li id="import_export_default_section_group_li" class="nhp-opts-group-tab-link-li">';
									echo '<a href="javascript:void(0);" id="import_export_default_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="import_export_default"><img src="'.$this->url.'img/glyphicons/glyphicons_082_roundabout.png" /> <span>'.__('Import / Export', 'be-themes').'</span></a>';
							echo '</li>';
						}//if
						
						
						
						
						
						foreach($this->extra_tabs as $k => $tab){
							$icon = (!isset($tab['icon']))?'<img src="'.$this->url.'img/glyphicons/glyphicons_019_cogwheel.png" /> ':'<img src="'.$tab['icon'].'" /> ';
							echo '<li id="'.$k.'_section_group_li" class="nhp-opts-group-tab-link-li">';
								echo '<a href="javascript:void(0);" id="'.$k.'_section_group_li_a" class="nhp-opts-group-tab-link-a custom-tab" data-rel="'.$k.'">'.$icon.'<span>'.$tab['title'].'</span></a>';
							echo '</li>';
						}

						
						if(true === $this->args['dev_mode']){
							echo '<li id="dev_mode_default_section_group_li" class="nhp-opts-group-tab-link-li">';
									echo '<a href="javascript:void(0);" id="dev_mode_default_section_group_li_a" class="nhp-opts-group-tab-link-a custom-tab" data-rel="dev_mode_default"><img src="'.$this->url.'img/glyphicons/glyphicons_195_circle_info.png" /> <span>'.__('Dev Mode Info', 'be-themes').'</span></a>';
							echo '</li>';
						}//if
						
					echo '</ul>';
				echo '</div>';
				
					foreach($this->sections as $k => $section){
						echo '<div id="'.$k.'_section_group'.'" class="nhp-opts-group-tab">';
							$this->custom_do_settings_sections($k.'_section_group');
						echo '</div>';
					}					
					
					
					if(true === $this->args['show_import_export']){
						echo '<div id="import_export_default_section_group'.'" class="nhp-opts-group-tab">';
							echo '<h3>'.__('Import / Export Options', 'be-themes').'</h3>';
							
							echo '<h4>'.__('Import Options', 'be-themes').'</h4>';
							
							echo '<p><a href="javascript:void(0);" id="nhp-opts-import-code-button" class="button-secondary">Import from file</a> <a href="javascript:void(0);" id="nhp-opts-import-link-button" class="button-secondary">Import from URL</a></p>';
							
							echo '<div id="nhp-opts-import-code-wrapper">';
							
								echo '<div class="nhp-opts-section-desc">';
				
									echo '<p class="description" id="import-code-description">'.apply_filters('nhp-opts-import-file-description',__('Input your backup file below and hit Import to restore your sites options from a backup.', 'be-themes')).'</p>';
								
								echo '</div>';
								
								echo '<textarea id="import-code-value" name="'.$this->args['opt_name'].'[import_code]" class="large-text" rows="8"></textarea>';
							
							echo '</div>';
							
							
							echo '<div id="nhp-opts-import-link-wrapper">';
							
								echo '<div class="nhp-opts-section-desc">';
									
									echo '<p class="description" id="import-link-description">'.apply_filters('nhp-opts-import-link-description',__('Input the URL to another sites options set and hit Import to load the options from that site.', 'be-themes')).'</p>';
								
								echo '</div>';

								echo '<input type="text" id="import-link-value" name="'.$this->args['opt_name'].'[import_link]" class="large-text" value="" />';
							
							echo '</div>';
							
							
							
							echo '<p id="nhp-opts-import-action"><input type="submit" id="nhp-opts-import" name="'.$this->args['opt_name'].'[import]" class="button-primary" value="'.__('Import', 'be-themes').'"> <span>'.apply_filters('nhp-opts-import-warning', __('WARNING! This will overwrite any existing options, please proceed with caution!', 'be-themes')).'</span></p>';
							echo '<div id="import_divide"></div>';
							
							echo '<h4>'.__('Export Options', 'be-themes').'</h4>';
							echo '<div class="nhp-opts-section-desc">';
								echo '<p class="description">'.apply_filters('nhp-opts-backup-description', __('Here you can copy/download your themes current option settings. Keep this safe as you can use it as a backup should anything go wrong. Or you can use it to restore your settings on this site (or any other site). You also have the handy option to copy the link to yours sites settings. Which you can then use to duplicate on another site', 'be-themes')).'</p>';
							echo '</div>';
							
								echo '<p><a href="javascript:void(0);" id="nhp-opts-export-code-copy" class="button-secondary">Copy</a> <a href="'.add_query_arg(array('feed' => 'nhpopts-'.$this->args['opt_name'], 'action' => 'download_options', 'secret' => md5(AUTH_KEY.SECURE_AUTH_KEY)), site_url()).'" id="nhp-opts-export-code-dl" class="button-primary">Download</a> <a href="javascript:void(0);" id="nhp-opts-export-link" class="button-secondary">Copy Link</a></p>';
								$backup_options = $this->options;
								$backup_options['nhp-opts-backup'] = '1';
								$encoded_options = '###'.serialize($backup_options).'###';
								echo '<textarea class="large-text" id="nhp-opts-export-code" rows="8">';print_r($encoded_options);echo '</textarea>';
								echo '<input type="text" class="large-text" id="nhp-opts-export-link-value" value="'.add_query_arg(array('feed' => 'nhpopts-'.$this->args['opt_name'], 'secret' => md5(AUTH_KEY.SECURE_AUTH_KEY)), site_url()).'" />';
							
						echo '</div>';
					}
					
					
					
					foreach($this->extra_tabs as $k => $tab){
						echo '<div id="'.$k.'_section_group'.'" class="nhp-opts-group-tab">';
						echo '<h3>'.$tab['title'].'</h3>';
						echo $tab['content'];
						echo '</div>';
					}

					
					
					if(true === $this->args['dev_mode']){
						echo '<div id="dev_mode_default_section_group'.'" class="nhp-opts-group-tab">';
							echo '<h3>'.__('Dev Mode Info', 'be-themes').'</h3>';
							echo '<div class="nhp-opts-section-desc">';
							echo '<textarea class="large-text" rows="24">'.print_r($this, true).'</textarea>';
							echo '</div>';
						echo '</div>';
					}
					
					
					do_action('nhp-opts-after-section-items-'.$this->args['opt_name'], $this);
				
					echo '<div class="clear"></div><!--clearfix-->';
				echo '</div>';
				echo '<div class="clear"></div><!--clearfix-->';
				
				echo '<div id="nhp-opts-footer">';
				
					if(isset($this->args['share_icons'])){
						echo '<div id="nhp-opts-share">';
						foreach($this->args['share_icons'] as $link){
							echo '<a href="'.$link['link'].'" title="'.$link['title'].'" target="_blank"><img src="'.$link['img'].'"/></a>';
						}
						echo '</div>';
					}
					
					submit_button('', 'primary', '', false,array('id'=>'be_themes_opt_submit'));
					submit_button(__('Reset to Defaults', 'be-themes'), 'secondary', $this->args['opt_name'].'[defaults]', false,array('id'=>'be_themes_opt_reset'));
					echo '<div class="clear"></div><!--clearfix-->';
				echo '</div>';
			
			echo '</form>';
			
			do_action('nhp-opts-page-after-form-'.$this->args['opt_name']);
			
			echo '<div class="clear"></div><!--clearfix-->';	
		echo '</div><!--wrap-->';

	}//function
	
	
	
	/**
	 * JS to display the errors on the page
	 *
	 * @since NHP_Options 1.0
	*/	
	function _errors_js(){
		
		if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-errors-'.$this->args['opt_name'])){
				$errors = get_transient('nhp-opts-errors-'.$this->args['opt_name']);
				$section_errors = array();
				foreach($errors as $error){
					$section_errors[$error['section_id']] = (isset($section_errors[$error['section_id']]))?$section_errors[$error['section_id']]:0;
					$section_errors[$error['section_id']]++;
				}
				
				
				echo '<script type="text/javascript">';
					echo 'jQuery(document).ready(function(){';
						echo 'jQuery("#nhp-opts-field-errors span").html("'.count($errors).'");';
						echo 'jQuery("#nhp-opts-field-errors").show();';
						
						foreach($section_errors as $sectionkey => $section_error){
							echo 'jQuery("#'.$sectionkey.'_section_group_li_a").append("<span class=\"nhp-opts-menu-error\">'.$section_error.'</span>");';
						}
						
						foreach($errors as $error){
							echo 'jQuery("#'.$error['id'].'").addClass("nhp-opts-field-error");';
							echo 'jQuery("#'.$error['id'].'").closest("td").append("<span class=\"nhp-opts-th-error\">'.$error['msg'].'</span>");';
						}
					echo '});';
				echo '</script>';
				delete_transient('nhp-opts-errors-'.$this->args['opt_name']);
			}
		
	}//function
	
	
	
	/**
	 * JS to display the warnings on the page
	 *
	 * @since NHP_Options 1.0.3
	*/	
	function _warnings_js(){
		
		if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-warnings-'.$this->args['opt_name'])){
				$warnings = get_transient('nhp-opts-warnings-'.$this->args['opt_name']);
				$section_warnings = array();
				foreach($warnings as $warning){
					$section_warnings[$warning['section_id']] = (isset($section_warnings[$warning['section_id']]))?$section_warnings[$warning['section_id']]:0;
					$section_warnings[$warning['section_id']]++;
				}
				
				
				echo '<script type="text/javascript">';
					echo 'jQuery(document).ready(function(){';
						echo 'jQuery("#nhp-opts-field-warnings span").html("'.count($warnings).'");';
						echo 'jQuery("#nhp-opts-field-warnings").show();';
						
						foreach($section_warnings as $sectionkey => $section_warning){
							echo 'jQuery("#'.$sectionkey.'_section_group_li_a").append("<span class=\"nhp-opts-menu-warning\">'.$section_warning.'</span>");';
						}
						
						foreach($warnings as $warning){
							echo 'jQuery("#'.$warning['id'].'").addClass("nhp-opts-field-warning");';
							echo 'jQuery("#'.$warning['id'].'").closest("td").append("<span class=\"nhp-opts-th-warning\">'.$warning['msg'].'</span>");';
						}
					echo '});';
				echo '</script>';
				delete_transient('nhp-opts-warnings-'.$this->args['opt_name']);
			}
		
	}//function
	
	

	
	
	/**
	 * Section HTML OUTPUT.
	 *
	 * @since NHP_Options 1.0
	*/	
	function _section_desc($section){
		
		$id = rtrim($section['id'], '_section');
		
		if(isset($this->sections[$id]['desc']) && !empty($this->sections[$id]['desc'])) {
			echo '<div class="nhp-opts-section-desc">'.$this->sections[$id]['desc'].'</div>';
		}
		
	}//function
	
	
	
	
	/**
	 * Field HTML OUTPUT.
	 *
	 * Gets option from options array, then calls the speicfic field type class - allows extending by other devs
	 *
	 * @since NHP_Options 1.0
	*/
	function _field_input($field){
		
		
		if(isset($field['callback']) && function_exists($field['callback'])){
			$value = (isset($this->options[$field['id']]))?$this->options[$field['id']]:'';
			do_action('nhp-opts-before-field-'.$this->args['opt_name'], $field, $value);
			call_user_func($field['callback'], $field, $value);
			do_action('nhp-opts-after-field-'.$this->args['opt_name'], $field, $value);
			return;
		}
		
		if(isset($field['type'])){
			
			$field_class = 'NHP_Options_'.$field['type'];
			
			if(class_exists($field_class)){
				require_once($this->dir.'fields/'.$field['type'].'/field_'.$field['type'].'.php');
			}//if
			
			if(class_exists($field_class)){
				$value = (isset($this->options[$field['id']]))?$this->options[$field['id']]:'';
				do_action('nhp-opts-before-field-'.$this->args['opt_name'], $field, $value);
				$render = '';
				$render = new $field_class($field, $value, $this);
				$render->render();
				do_action('nhp-opts-after-field-'.$this->args['opt_name'], $field, $value);
			}//if
			
		}//if $field['type']
		
	}//function

	/**
	 * HTML OUTPUT LAYOUT.
	 *
	 * @since BE Themes 2.0
	*/	
	function custom_do_settings_sections($page) {
        global $wp_settings_sections, $wp_settings_fields;

        if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
            return;

        foreach( (array) $wp_settings_sections[$page] as $section ) {
        	echo "<h3>{$section['title']}</h3>\n";
            call_user_func($section['callback'], $section);
            if ( !isset($wp_settings_fields) ||
                 !isset($wp_settings_fields[$page]) ||
                 !isset($wp_settings_fields[$page][$section['id']]) )
                    continue;
            echo '<div class="settings-form-wrapper">';
            $this->custom_do_settings_fields($page, $section['id']);
            echo '</div>';
        }
    }
    function custom_do_settings_fields($page, $section) {
        global $wp_settings_fields;

        if ( !isset($wp_settings_fields) ||
             !isset($wp_settings_fields[$page]) ||
             !isset($wp_settings_fields[$page][$section]) )
            return;

        foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
            echo '<div class="settings-form-row clearfix">';
			echo '<div class="left">';
            if ( !empty($field['args']['label_for']) )
                echo '<label for="' . $field['args']['label_for'] . '">' .
                    $field['title'] . '</label><br />';
            else
                echo '<h4>' . $field['title'] . '</h4>';
			echo '</div>';
			echo '<div class="left right-section">';
            call_user_func($field['callback'], $field['args']);
			echo '</div>';
            echo '</div>';
        }
    }

	
	
}//class
}//if
?>