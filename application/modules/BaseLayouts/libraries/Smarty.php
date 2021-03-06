<?php if ( ! defined('BASEPATH') ) exit( 'No direct script access allowed' );

require_once( BASE_APP_PATH.'third_party/Smarty_3/SmartyBC.class.php' );

class CI_Smarty extends SmartyBC {

    var $CI=NULL;
	var $js = array();
	var $css = array();
    var $css_ie = array();
	var $css_all_ie = array();
    var $script = array('js'=>null,'ready'=>null,'header'=>'');
    var $tpl_vars = array();
    var $theme = 'default';

	public function __construct() {
		parent::__construct();

		$this->caching = false;
		$this->setCompileDir( APPPATH . 'compile' );
		$this->setCacheDir( BASEPATH . '../cache' );
		$this->loadPlugin('smarty_compiler_switch');
		if ( ! defined('IMGPATH') ) define('IMGPATH', BASEPATH.'../images/');

		if( env('TRIM_HTML'))
            $this->loadFilter('output', 'trimwhitespace');



    }

	public function useCached( $tpl, $data = null ) {
		if ( $this->isCached( $tpl, $data ) ) {
			$this->display( $tpl, $data );
			exit();
		}
	}

	var $useTemp = TRUE;
	var $layout = 'template';

	function view($resource_name, $params = array())   {
        if( is_array($params) && !empty($params) ) foreach ($params AS $k=>$d){
            $this->assign($k, $d);
        }
        if( !empty($this->script['ready']) ){
            $this->assign('js_ready', $this->script['ready']);
        }
        if( !empty($this->script['header']) ){
            $this->assign('js_header', $this->script['header']);
        }

        $ci = get_instance();

        list($path, $_view) = Modules::find($resource_name, $ci->router->class,"views/",true );
        if( $path === FALSE ){
            list($path, $_view) = Modules::find($resource_name, $ci->router->module,"views/",true );
        }

//        dd("debug view : path:$path | _view:$_view");
        //bug("smarty 55 ==== path:".$path." modulue:find ".$ci->router->class);
        //bug($ci->router);

        //die($resource_name);
        if( $path === FALSE ){
            list($path, $_view) = Modules::find($resource_name, SYSTEM_MODULE_PATH );
        }

		if( $path === FALSE ){
            list($path, $_view) = Modules::find($resource_name,APPPATH.'views/',null,true);
        }

		if( $path === FALSE) {

		    $resource_ext = pathinfo($resource_name, PATHINFO_EXTENSION);
		    foreach (array('.html','.htm') AS $ext){
		        if( $resource_ext ){
		            $file_name = str_replace($resource_ext, $ext, $resource_name);
		        } else {
		            $file_name = $resource_name.$ext;
		        }

		        list($path, $_view) = Modules::find($file_name, SYSTEM_MODULE_PATH, 'views/');

		        if ($path != FALSE)
		            break;

		        if ($path != FALSE)
		            break;
		    }

		}

		if ( $path === FALSE )
		{
		    $segments = explode('/', $resource_name);
		    $path = ltrim(implode('/', $segments).'/', '/');
		    bug("path:$path resouce_name:$resource_name template:$path$_view");
            //die('smarty 168 : check app');

		} else {
            $this->assign("tplPath", $path);
        }
        $this->addTemplateDir($path);
		return parent::fetch("$path$_view");
    }

    static function fetchView($resource_name,$data){
	    $dirDefault = config_item('theme_dir')."views/";
        list($view, $path) = Modules::is_file_in_dir($dirDefault, $resource_name,true );
        $smarty = get_instance()->smarty;
        if( $view ){
            return $smarty->fetch("$path$view",$data);
        }
    }

	function layout($file){
		$this->layout = $file;
	}
	var $theme_config = null;
	public function theme($item=null){


	}

	function checkResoure($file=NULL,$type='js'){
	    return $file;
// 		return ( ! preg_match('!^\w+://! i', $file)) ? ( str_replace('index.php/','',$this->CI->config->item('asset_url'))).$file : $file;
	}

	public function js($file){
		$this->js[] = $file;
	}
	public function add_js($file){
	    $this->js[] = $file;
	}
	public function css($file){
		$this->css[] = $file;
	}
	public function add_css($file){
	    $this->css[] = $file;
	}

	public function jsscript($str=null){
		$this->script['js'].= $str;
	}
	public function js_ready($str=null){
		$this->script['ready'].= $str;
	}

	public function js_header($str=null){
	    $this->script['header'].= $str;
	}

	function loadLibrariesPlugin($className=''){
		if( !$className ) return false;

		if( !class_exists($className) ){
			get_instance()->load->library($className);
		}

        $reflection = new ReflectionClass($className);
        $methods = $reflection->getMethods(ReflectionMethod::IS_STATIC);
		foreach ($methods AS $plugin){
            $registered_plugins = $this->registered_plugins;
            if (strpos($plugin->name, 'modifier_') === 0 ) {
                $func_name = substr($plugin->name, strlen("modifier_"));
                if( !array_key_exists('modifier',$registered_plugins) || !array_key_exists($func_name,$registered_plugins['modifier']) ){
                    $this->registerPlugin('modifier', $func_name, $className . '::' . $plugin->name);
                }

            } else if (!isset($registered_plugins['function']) || !array_key_exists($plugin->name, $registered_plugins['function']) ) {
                $this->registerPlugin('function', $plugin->name, $className . '::' . $plugin->name);
            }
		}

	}

}