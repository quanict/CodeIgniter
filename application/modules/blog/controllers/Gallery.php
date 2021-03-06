<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Gallery_Model');
        $this->load->module('layouts');
        $this->fields = $this->Gallery_Model->fields();
        set_temp_val('img_path',"gallery");
        add_site_structure('gallery',lang("Gallery") );
    }

    var $table_fields = array(
        'id'=>array("#",5,true,'text-center'),
        'image'=>array("Image"),
        'category_name'=>array("Category"),
        'actions'=>array(),
    );
    function items(){

        if( $this->uri->extension =='json' ){
            return $this->Gallery_Model->items_json();
        }

        $data = columns_fields($this->table_fields);

        $this->template->build('backend/datatables',$data);
    }

    var $formView = "blog/gallery-form";
    public function form($id=0){
        if ($this->input->post()) {
            $submit = input_post('submit');

            $formdata = array();

            foreach ($this->fields as $name => $field) {
                $this->fields[$name]['value'] = $formdata[$name] = $this->input->post($name);
            }

            if( $submit=='crop' && $formdata["id"] > 0 ){

                $newUri = str_replace('/edit/',"/$submit/",uri_string());
                redirect($newUri, 'refresh');
            }

            $add = $this->Gallery_Model->update($formdata);
            return submit_redirect($add);

        }

        if ($id > 0){
            $item = $this->Gallery_Model->get_item_by_id($id);
            foreach ($this->fields AS $field=>$val){
                if( isset($item->$field) ){
                    $this->fields[$field]['value']=$item->$field;
                }
            }
        }
        if( $id > 0 ){
            set_temp_val('formTitle',lang("Edit Gallery") );
        } else {
            set_temp_val('formTitle',lang("Add new Gallery") );
        }
        $data = array(
            'fields' => $this->fields
        );

        temp_view($this->formView,$data);
    }


    /*
     * todo
     * Category of Gallery
     */

    public function category($action=NULL,$id=0){
        add_site_structure('category',lang("Category Management") );
        $this->load->module('backend/category');
        $this->category->fields["type"]["value"] = "gallery";
        $this->category->fields["parent"]["type"] = "hidden";
        $this->category->fields["description"]["type"] = "hidden";
        if( strlen($action) > 0 ){
            if( method_exists($this->category, $action) ){
                return $this->category->$action($id);
            } elseif( in_array($action,['edit','new','add']) ){
                return $this->category->form($id);
            } else {
                show_404();
            }
        } else {
            return  $this->category->items();
        }
    }

    public function delete($id=0){
        $this->Gallery_Model->item_delete($id);
        $newUri = url_to_list();
        //return redirect($newUri, 'refresh');
    }

    public function crop($id=0){
        /*
         * http://deepliquid.com/content/Jcrop_API.html
         */
        $item = $this->Gallery_Model->get_item_by_id($id);
        $fields = [
            'x1'=>["title"=>"X top"],
            'y1'=>["title"=>"Y top"],
            'x2'=>["title"=>"X bottom"],
            'y2'=>["title"=>"Y bottom"],
            'w' =>["title"=>"Width"],
            'h' =>["title"=>"Height"],
        ];
        set_temp_val("fields",$fields);
        temp_view("gallery-img-edit",['img'=>"/images/gallery/".$item->image]);
    }


}