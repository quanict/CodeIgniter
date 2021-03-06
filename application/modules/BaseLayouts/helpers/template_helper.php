<?php
if ( !function_exists('temp_view')) {
    function temp_view($view, $vars = array()){
        get_instance()->template->build($view,$vars);
    }

    function temp_subview($view, $vars = array()){
        return get_instance()->template->build_subview($view,$vars);
    }

}

/**
 * set_temp_val
 *
 * Lets you determine whether an array index is set and whether it has a value.
 * If the element is empty it returns NULL (or whatever you specify as the default value.)
 *
 * @param	string
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */
function set_temp_val($name="",$val=NULL){
    if( strlen($name) > 0 ){
    	get_instance()->smarty->assign($name,$val);
    }
}

/**
 * @param string $name name of variable
 * @param null $val value of variable
 */
function add_temp_val($name="",$val=NULL,$key=null){
    if( strlen($name) > 0 ){
        $tempVal = get_instance()->smarty->getTemplateVars($name);
        if( is_null($tempVal) ){
            $tempVal = [$val];
        } else {
            if( is_string($tempVal) ){
                $tempVal = [$tempVal];
            }

            if( is_string($key)){
                $tempVal[$key] = $val;
            } else {
                $tempVal[] = $val;
            }

        }
        get_instance()->smarty->assign($name,$tempVal);
    }
}

function add_site_structure($name,$title=null,$uri=null){
    $varName = "site_structure";
    if( is_null($uri) ){
        $site_structure = get_instance()->smarty->getTemplateVars($varName);

        if( !empty($site_structure) ){
            $last_item = array_pop($site_structure);
            $uri = $last_item["uri"].DS.$name;
        } else {
            $uri = $name;
        }
    }
    add_temp_val($varName,['name'=>$name,'title'=>$title,'uri'=>$uri]);
}

function get_temp_val($name="",$default=""){
    $value = null;
    if( strlen($name) > 0 ){
        $value =  get_instance()->smarty->getTemplateVars($name);
    }
    if( !$value ){
        $value = $default;
    }
    return $value;
}

function smarty_view($view,$params=[]){
    if( strlen($view) > 0 ){
        echo get_instance()->smarty->view($view,$params);
    }
}
//
//function temp_view($view,$params=[],$title=null,$page_icon=null){
//    if( strlen($view) > 0 ){
//        $template = get_instance()->template;
//        if( !empty($title) ){
//            $template->title($title);
//        }
//        if( !empty($page_icon) ){
//            $template->set('page_icon',$page_icon);
//        }
//        $template->build($view,$params);
//    }
//}

function set_layout($layoutName){
    $ci = get_instance();
    if( isset($ci->template) ){
        $ci->template->set_layout($layoutName);
    }
}