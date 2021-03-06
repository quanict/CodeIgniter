<?php
class SmartadminInput_select extends CI_Smarty {

    static function input_select($params = null)
    {
        if( !isset($params['value']) ){
            $params['value'] = [];
        } else if ( is_numeric($params['value']) || is_string($params['value']) ){
            $params['value'] = [$params['value']];
        }
        $params['label'] = NULL;
        $params['state'] = "state-success";
        return parent::fetchView("inputs/select",$params);
    }

    static function input_multi_select($params){
        return self::input_select($params);
    }

    static function input_select_category($params){
        $level = isset($params['level']) ? $params['level'] : 2;

        $ci = get_instance();
        $ci->load->model('BaseCategory/BaseCategoryModel');
        if( !isset($params['category-type']) ){
            $params['category-type'] = 'category';
        }
        $without_ids = [];
        if( array_key_exists('without_ids',$params) ){
            $without_ids = $params['without_ids'];
        }
        $params['options'] = $ci->BaseCategoryModel->load_options($params['category-type'],null,$without_ids,$level);
        if( array_key_exists('multiple',$params) != true || $params['multiple'] != true ){
            $text = array_key_exists('label',$params) ? $params['label'] : "Select Category";
            $params['options'] = array_merge([0=>lang("-- $text -- ")],$params['options']);
        }
        return self::input_select2($params);
    }

    static function input_select2($params = null){
        if(!isset($params['value'])){
            $params['value'] = [];
        } else if ( is_numeric($params['value']) || is_string($params['value']) ){
            $params['value'] = [$params['value']];
        }
        $params['label'] = NULL;
        $params['state'] = "state-success";
        if( !array_key_exists('optgroup',$params) ){
            $params['optgroup'] = true;
        }
        if( array_key_exists('class',$params) != true ){
            $params['class'] = '';
        }
        if( array_key_exists('multiple',$params) && $params['multiple'] == true ){
            $params['name'] .= '[]';
            $params['class'] .= ' select-multi-level';
        }
//        dd($params,false,0);
        return parent::fetchView("inputs/select2",$params);

    }

    static function input_select_category_tag($params=[]){
        $ci = get_instance();
        $ci->load->model('SystemTag/SystemTagModel');
        if( !isset($params['category-type']) ){
            $params['category-type'] = 'category';
        }
        $without_ids = [];
        if( array_key_exists('without_ids',$params) ){
            $without_ids = $params['without_ids'];
        }
        $params['options'] = $ci->SystemTagModel->load_options(null,$without_ids,$level=2);
        $params['optgroup'] = false;
        $params['multiple'] = true;
        return self::input_select2($params);
    }

    static function input_tags($params = [])
    {
        $ci = get_instance();
        $ci->load->model('SystemTag/SystemTagModel');
        if( !array_key_exists('tag-type',$params) ){
            $params['tag-type'] = FALSE;
        }
        $params['options'] = $ci->SystemTagModel->load_options($status=1,$idsUsing=[],$level=2);
        return self::input_select2($params);
    }

    static function input_multiple_image($params = null){
        if( !isset($params['value']) ){
            $params['value'] = [];
        } else if ( is_numeric($params['value']) || is_string($params['value']) ){
            $params['value'] = [$params['value']];
        }

        $params['label'] = NULL;
        $params['state'] = "state-success";
        return parent::fetchView("inputs/multiple_image",$params);
    }

    static function input_synonym($params){
        return parent::fetchView("inputs/synonyms",$params);

    }

}