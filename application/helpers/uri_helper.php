<?php

function uri_extension(){

}

function url_to_edit($uri=null,$editId=0) {
    $editId = intval($editId);
    if( !$uri ){
        $uri = uri_string();
    }
    $ci = get_instance();
    $uri_length = $ci->uri->total_segments();
    $is_add = $ci->uri->rsegment($uri_length-1);

    if( $editId > 0 && ( $is_add =='add' || $ci->uri->rsegment($uri_length) =='add' )){
        $uri = str_replace(["/add"],'/edit',$uri);
        $uri .= "/$editId";
    }


    return $uri;
}

function url_to_list(){
    $ci = get_instance();
    $uri_length = $ci->uri->total_segments();
    $action = $ci->uri->rsegment($uri_length-1);
    $uri = uri_string();

    if( in_array($action,['delete','edit','add']) ){
        $uri = "";
        for ($i=1;$i < $uri_length-1 ;$i++){
            $uri .= $ci->uri->rsegment($i).DS;
        }
    }
    return $uri;
}