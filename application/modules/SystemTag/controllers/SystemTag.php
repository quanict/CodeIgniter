<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class SystemTag
 * @property SystemTagModel $SystemTagModel
 * @property CI_Output $output
 * @property array $fields
 */
class SystemTag extends MX_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('SystemTag/SystemTagModel');
        $this->model = $this->SystemTagModel;
        $this->fields =  $this->SystemTagModel->fields();
    }

    var $table_fields = [
        'id'=>      ["#",5,true,'text-center'],
        'word'=>    ["Keyword"],
        'actions'=> ['',5,false]
    ];

    public function dataTable(){
        if( $this->uri->extension =='json' ){
            return $this->SystemTagModel->items_json();
        }
        $data = columns_fields($this->table_fields);
        temp_view('datatables',$data);
    }

    /**
     * @param int $id
     * @return bool|void
     */
    var $uriForm = "keyword/%s/%d";
    public function form($id=0){
        if( $this->input->post() ){
            $this->formSubmit();
        }
        $this->formFill($id);
        temp_view("Tag/form",['fields'=>$this->fields]);
    }

    private function formSubmit(){
        $fields = $this->fields;

        if( isset($_POST['cancel']) ){
            redirect('keyword');
            return false;
        }

        $formData = [];
        foreach ($fields as $name => $field) {
            if( array_key_exists($name,$fields) ){
                if( !is_array($fields[$name]) ){
                    $fields[$name] = [];
                }
                $fields[$name]['value'] = $formData[$name] = input_post($name);
            }
        }
        $add = $this->SystemTagModel->update($formData);
        if( $add ){
            $uriEdit  = sprintf($this->uriForm,'edit',$add);
            redirect($uriEdit, 'refresh');
            return false;
        }
    }

    public function typeHead(){
        $data = ["aaaa","bbb"];
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}