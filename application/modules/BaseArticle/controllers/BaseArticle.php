<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class SystemArticle
 * @property BaseArticleModel $BaseArticleModel
 * @property SystemCrawler $SystemCrawler
 * @property BaseArticleModel $model
 * @property SystemTagModel $SystemTagModel
 * @property CI_Upload $upload
 */
class BaseArticle extends MX_Controller
{
    var $lazyLoad = false;
    function __construct()
    {
        parent::__construct();
        $this->load->module('layouts');
        if (!function_exists('columns_fields')) {
            $this->load->helper("BaseArticle/dataTables");
        }
        $this->load->module('SystemCrawler');
        // $this->model->fields = $this->BaseArticleModel->fields();
    }

    var $table_fields = [
        'id' => array("#", 5, true, 'text-center'),
        'title' => array("Title", 30),
        'category_name' => array("Category", 10,false,'hidden-sm hidden-xs'),
        'source' => ['Source', 5, false, 'text-center'],
        'tag_names' => ['Keywords', 10, false,'hidden-xs'],
        'news_actions' => array('', 5, false,'hidden-xs'),
    ];

    function items()
    {
        if ($this->uri->extension == 'json') {
            $category_id = null;
            if (isset($this->model->fields['category']['value']) && $this->model->fields['category']['value'] > 0) {
                $category_id = $this->model->fields['category']['value'];
            }
            $filter = input_get('filter');
            return $this->model->items_json($category_id,false,$filter);
        }
        $data = columns_fields($this->table_fields);
        $data['filter'] = ['tags'=>[1,43]];
        temp_view('BaseArticle/articles', $data);
    }

    var $formView = "BaseArticle/form";
    var $uriEdit = "article/edit/%d";
    var $uriList = "article";
    /**
     * @param int $id
     */
    public function form($id = 0)
    {
        header('X-XSS-Protection:0');
        if ($this->input->post()) {
            $this->formSubmit();
        } else {
            $item = $this->model->get_item_by_id($id);
            if ($id > 0) {
                if( array_key_exists('value',$this->model->fields['source']) && strlen($this->model->fields['source']['value']) > 0 ){
                    $this->model->fields['source']['type'] = 'source_link';
                }
                $this->model->fields['alias']['type'] = env('ARTICLE_SITE') ? 'news_link': 'editable';
            }
            foreach ($this->model->fields AS $field => $val) {
                if (isset($item->$field)) {
                    $this->model->fields[$field]['value'] = $item->$field;
                }
            }
        }
        if ($id > 0) {
            set_temp_val('formTitle', lang("Edit"));
        } else {
            set_temp_val('formTitle', lang("Add new"));
        }
        $this->LazyLoadImage();
        $data = array(
            'fields' => $this->model->fields
        );
        add_js('crawler_form_actions.js');
        temp_view($this->formView, $data);
    }

    private function formSubmit(){
        $crawlerSource = $this->input->post("crawler_source");

        $formData = [];
        foreach ($this->model->fields as $name => $field) {
            $this->model->fields[$name]['value'] = $formData[$name] = $this->input->post($name);
        }

        $config['upload_path'] = APPPATH . "/uploads/article/";
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('imgthumbUpload')) {
            $upload_data = $this->upload->data();
            $formData["imgthumb"] = $upload_data['file_name'];
        } else {
            //$formdata["imgthumb"] = NULL;
            //bug($this->upload->display_errors());die;
        }

        if (!$crawlerSource) {
            $add = $this->BaseArticleModel->update($formData);
            if ($add) {
                set_success(lang('Success.'));
                $uri_edit = get_temp_val("uri_edit");
                if( strlen($uri_edit) > 0 ){
                    $this->uriEdit = $uri_edit;
                }
                $newUri = sprintf($this->uriEdit,$add);

                if (input_post('back')) {
                    $newUri = sprintf($this->uriList);
                }
                redirect($newUri, 'refresh');
            } else {
                redirect(uri_string(), 'refresh');
            }
            return true;
        } else {
            $check = $this->model->where('source',$crawlerSource);
            if( ($row = $check->get_row()) != null ){
                $uriEdit = sprintf($this->uriEdit,$row->id);
                set_error('Dupplicate Article ' .  anchor($uriEdit, $row->title));
                redirect($uriEdit);
            } else {
                list($c_title, $c_content, $c_thumb) = Modules::run('SystemCrawler/get_content', $crawlerSource);

                if (!is_null($c_title)) {
                    $this->model->fields["title"]['value'] = $c_title;
                    $this->model->fields["content"]['value'] = $c_content;
                    $this->model->fields["imgthumb"]['value'] = $c_thumb;
                }
            }
        }
    }

    public function crawler()
    {
        $this->load->module('SystemCrawler');

        if (strlen($source = $this->input->get('s')) > 0) {
            list($c_title, $c_content) = $this->SystemCrawler->get_content($source);

            if (is_string($c_title)) {
                $this->model->fields['title']['value'] = $c_title;
                $this->model->fields['alias']['value'] = url_title($c_title, '-', true);
            }
            if (is_string($c_content)) {
                $this->model->fields['content']['value'] = $c_content;
            }

            $this->model->fields['source']['value'] = $source;
        }

        if ($this->input->post()) {
            $formData = [];
            foreach ($this->model->fields as $name => $field) {
                $formData[$name] = $this->input->post($name);
            }
            if (!empty($formData) AND ($add = $this->BaseArticleModel->update($formData))) {
                set_error(lang('Success.'));
            }
        }

        $data = array(
            'fields' => $this->model->fields
        );
        $this->template
            ->title(lang('welcome_to'))
            ->build('backend/form', $data);
    }

    public function delete($id = 0)
    {
        $this->BaseArticleModel->item_delete($id);
        $newUri = url_to_list();

        return redirect($newUri, 'refresh');
    }

    private function LazyLoadImage(){
        if( $this->lazyLoad && !empty($this->model->fields) && array_key_exists('content',$this->model->fields) ){
            if( array_key_exists('value',$this->model->fields['content']) != true ){
                return;
            }
            $html = $this->model->fields['content']['value'];
            $htmlDom = str_get_html($html);
            if( !is_object($htmlDom) )
                return;
            // Find all images
            foreach($htmlDom->find('img') as $img){
                //$img->{'data-src'} = $img->src;
                //$img->src = $this->config->item('theme_url').DS."images/no-image.svg";
                if( !isset($img->class) || strpos('img-fluid',$img->class) < 0 ){
                    $img->class = "img-fluid img-thumbnail text-center";
                }

                $img->style = null;
            }
            $this->model->fields['content']['value'] = $htmlDom->save();
            //dd($this->model->fields['content']['value']);
        }
    }
}