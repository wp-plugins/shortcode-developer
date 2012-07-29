<?php

/*
Plugin Name: Shortcode Developer
Plugin URI: http://www.elliotcondon.com/
Description: Quickly create and edit shortcodes to use within your website. This plugin gives you full PHP control whilst taking care of the hard work in the background. Creating shortcodes has never been this easy!
Version: 1.0.0
Author: Elliot Condon
Author URI: http://www.elliotcondon.com/
License: GPL
Copyright: Elliot Condon
*/

$shortcode_developer = new shortcode_developer();

class shortcode_developer
{ 
	var $dir,
		$path,
		$version,
		$params,
		$data;
	
	
	/*
	*  Constructor
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 23/06/12
	*/
	
	function __construct()
	{

		// vars
		$this->dir = plugins_url('',__FILE__);
		$this->path = plugin_dir_path(__FILE__);
		$this->version = '1.0.0';
		
		$this->params = array_merge( array(
			'id' => false,
			'action' => false,
			'message' => false
		), $_GET );
		
		$this->data = array(
			'url' => admin_url('options-general.php?page=shortcode-developer')
		);
		
		
		// set text domain
		load_plugin_textdomain('scd', false, basename(dirname(__FILE__)).'/lang' );
		
		
		// actions
		add_action('admin_menu', array($this,'admin_menu'));
		add_action('init', array($this,'init'));
		
		return true;
	}
	
	
	/*
	*  Init
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 28/07/12
	*/
	
	function init()
	{
		// admin?
		if( is_admin() )
		{
			return false;
		}
		
		// login?
		if ( in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) )
		{
			return false;
		}
		
		
		$shortcodes = $this->get_shortcodes();
		
		if( $shortcodes )
		{
			foreach( $shortcodes as $shortcode)
			{
				$this->register_shortcode( $shortcode );
			}
		}
		
	}
	
	
	/*
	*  register_shortcode
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 28/07/12
	*/
	
	function register_shortcode( $shortcode )
	{
		// vars
		$function = "";
		
		
		// extract atts
		if( $shortcode['atts'] )
		{
			$function .= 'extract( shortcode_atts( array( ';
			
			foreach( $shortcode['atts'] as $att )
			{
				$function .= '"' . $att['name'] . '" => "' . $att['default'] . '", ';
			}
			
			$function .= '), $atts ) ); ';
		}
		
		
		// add the php body
		$function .= $shortcode['php'];
		
		
		// add the return
		$function .= ' return $html;';
		

		// create as a static function
		$function_name = create_function( '$atts, $content', $function );
		
		
		// register the function as a shortcode
		add_shortcode( $shortcode['name'], $function_name );
	}
	
	
	/*
	*  admin_menu
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 25/07/12
	*/
	
	function admin_menu()
	{
		$page = add_options_page( __("Shortcode Developer",'scd'), __("Shortcode Developer",'scd'), 'manage_options', 'shortcode-developer', array($this, 'html') );
		
		// actions
		add_action('load-' . $page, array($this,'load'));
		add_action('admin_print_scripts-' . $page, array($this, 'admin_print_scripts'));
		add_action('admin_head-' . $page, array($this,'admin_head'));
	}
	
	
	/*
	*  load
	*
	*  @description: run only on $page, before admin_head. Looks like a good place to save data
	*  @since 1.0.0
	*  @created: 28/07/12
	*/
	
	function load()
	{
		if( !empty($_POST) )
		{
			// strip slashes
			$_POST = array_map( 'stripslashes_deep', $_POST );
			
			
			// find message (1 = "Created", 2 = "Updated")
			$message = ($_POST['id'] == "") ? "1" : "2";
			
			
			// save shortcode
			$id = $this->save_shortcode( $_POST );
			
			
			// redirec with message
			wp_redirect( $this->data['url'] . '&action=edit&id=' . $id . '&message=' . $message );
			exit;
		}
		
		if( $this->params['action'] == 'delete' )
		{
			// delete shortcode
			$id = $this->params['id'];
			
			
			// validate id
			if( ! $id )
			{
				wp_die( __("Error: No shortcode ID found.",'scd') );
			}
			
			
			// delete shortcode
			$this->delete_shortcode( $id );
			
			
			// redirec with message
			wp_redirect( $this->data['url'] . '&message=3' );
			exit;
		}
	}
	
	
	/*
	*  delete_shortcode
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 28/07/12
	*/
	
	function delete_shortcode( $id )
	{
		// remove from shortcode_ids
		$shortcode_ids = get_option( 'shortcode_ids', array() );
		if( $shortcode_ids )
		{
			foreach( $shortcode_ids as $k => $v )
			{
				if( $v == $id )
				{
					unset( $shortcode_ids[$k] );
				}
			}	
		}
		update_option( 'shortcode_ids', $shortcode_ids );
		
		
		// option_name
		$option_name = 'shortcode_' . $id . '_';
		
		
		// remove data
		delete_option( $option_name . 'id' );
		delete_option( $option_name . 'name' );
		delete_option( $option_name . 'atts' );
		delete_option( $option_name . 'php' );
		
	}
	
	
	/*
	*  save_shortcode
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 28/07/12
	*/
	
	function save_shortcode( $shortcode )
	{
		
		// id
		if( ! $shortcode['id'] )
		{
			$shortcode['id'] = uniqid();
			
			// add this id to the shortcode_ids option
			$shortcode_ids = get_option( 'shortcode_ids', array() );
			$shortcode_ids[] = $shortcode['id'];
			update_option( 'shortcode_ids', $shortcode_ids );
		}
		

		// option_name
		$option_name = 'shortcode_' . $shortcode['id'] . '_';
		
		
		// save data
		update_option( $option_name . 'id', $shortcode['id'] );
		update_option( $option_name . 'name', $shortcode['name'] );
		update_option( $option_name . 'atts', $shortcode['atts'] );
		update_option( $option_name . 'php', $shortcode['php'] );
		
		
		// return ID
		return $shortcode['id'];
	}
	
	
	/*
	*  get_shortcode
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 28/07/12
	*/
	
	function get_shortcode( $id )
	{
		// option_name
		$option_name = 'shortcode_' . $id . '_';
		
		
		// build shortcode array
		$shortcode = array(
			'id' => get_option( $option_name . 'id' ),
			'name' => get_option( $option_name . 'name' ),
			'atts' => get_option( $option_name . 'atts' ),
			'php' => get_option( $option_name . 'php' )
		);
		
		
		// return
		return $shortcode;
	}
	
	
	/*
	*  get_shortcodes
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 28/07/12
	*/
	
	function get_shortcodes()
	{
		// option_name
		$ids = get_option( 'shortcode_ids', array() );
		$shortcodes = array();
		
		
		if( $ids )
		{
			foreach( $ids as $id )
			{
				$shortcodes[ $id ] = $this->get_shortcode( $id );
			}
		}		
		
		// return
		return $shortcodes;
	}
	
	
	/*
	*  admin_print_scripts
	*
	*  @description:
	*  @since 1.0.0
	*  @created: 25/07/12
	*/
	
	function admin_print_scripts() {

  		wp_enqueue_script( 'jquery' );

	}
	
	
	/*
	*  admin_head
	*
	*  @description:
	*  @since 1.0.0
	*  @created: 25/07/12
	*/
	
	
	function admin_head()
	{
		// CodeMirror
		echo '<link rel="stylesheet" type="text/css" href="' . $this->dir . '/codemirror/codemirror.css" />';
		echo '<link rel="stylesheet" type="text/css" href="' . $this->dir . '/codemirror/theme/shortcode.css" />';
		echo '<script type="text/javascript" src="' . $this->dir . '/codemirror/codemirror.js" ></script>';
		echo '<script type="text/javascript" src="' . $this->dir . '/codemirror/mode/xml.js" ></script>';
		//echo '<script type="text/javascript" src="' . $this->dir . '/codemirror/mode/javascript.js" ></script>';
		//echo '<script type="text/javascript" src="' . $this->dir . '/codemirror/mode/css.js" ></script>';
		echo '<script type="text/javascript" src="' . $this->dir . '/codemirror/mode/clike.js" ></script>';
		echo '<script type="text/javascript" src="' . $this->dir . '/codemirror/mode/php.js" ></script>';
		
		// Style
		echo '<link rel="stylesheet" type="text/css" href="' . $this->dir . '/css/style.css?ver=' . $this->version . '" />';
		
		
		// Javascript
		echo '<script type="text/javascript" src="' . $this->dir . '/js/functions.js?ver=' . $this->version . '" ></script>';
		echo '<script type="text/javascript">
			shortcode.text.confirm_delete = "' . __("Are you sure?",'scd') . '";
			shortcode.text.form_php_var = "${name}: ' . __("Defaults to",'scd') . ' \"{default}\"";
		</script>';
	}
	
	
	/*
	*  html
	*
	*  @description: renders the options page
	*  @since 1.0.0
	*  @created: 25/07/12
	*/
	
	
	function html()
	{
		// include body
		if( $this->params['action'] == 'edit' )
		{
			include( 'views/edit-single.php' );
		}
		else
		{
			include( 'views/edit-list.php' );
		}
		
	}
	
	
	/*
	*  Display Message
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 28/07/12
	*/
	
	function display_message()
	{
		$html = "";
		
		if( $this->params['message'] == "1" )
		{
			$html = '<div class="updated"><p>' . __("Shortcode Created",'scd') . '</p></div>';
		}
		elseif( $this->params['message'] == "2" )
		{
			$html = '<div class="updated"><p>' . __("Shortcode Updated",'scd') . '</p></div>';
		}
		if( $this->params['message'] == "3" )
		{
			$html = '<div class="error"><p>' . __("Shortcode Deleted",'scd') . '</p></div>';
		}
		
		echo $html;
	}
	
}

?>