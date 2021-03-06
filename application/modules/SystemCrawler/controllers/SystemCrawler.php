<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class SystemCrawler
 * @property CrawlerMask_Model $CrawlerMask_Model
 */
class SystemCrawler extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('simple_html_dom');
        $this->load->model('crawler/CrawlerMask_Model');
    }

    function get_content($url)
    {
        $content = array(
            NULL,
            NULL
        );
        //bug("get content url:$url",'a');
        $parse = parse_url($url);
        $parse['host'] = str_replace("www.",null,$parse['host']);
        $crawlerMask = $this->CrawlerMask_Model->get_site($parse['host']);
        if( $crawlerMask ){
            $articleObject = $this->findArticleObject($url,$crawlerMask);
            $content = [
                $articleObject->title,
                $articleObject->content,
                $articleObject->thumbnail
            ];
        } else {
            switch ($parse['host']) {
                case 'blog.itviec.com':
                    $this->load->library('blog_itviec', array(
                        'url' => $url
                    ));
                    $content = array(
                        $this->blog_itviec->title,
                        $this->blog_itviec->content
                    );
                    break;
                case 'hanoiscrum.net':
                    $this->load->library('hanoiscrum_net', array(
                        'url' => $url
                    ), 'site_dome');
                    $content = array(
                        $this->site_dome->title,
                        $this->site_dome->content
                    );
                    break;
                case 'techtalk.vn':
                    $this->load->library('techtalk_vn', array(
                        'url' => $url
                    ), 'site_dome');
                    $content = array(
                        $this->site_dome->title,
                        $this->site_dome->content
                    );
                    break;
                case 'genk.vn':
                    $this->load->library('genk_vn', array(
                        'url' => $url
                    ), 'site_dome');
                    $content = array(
                        $this->site_dome->title,
                        $this->site_dome->content
                    );
                    break;
                case 'vilancer.com':
                    $this->load->library('vilancer_com', array(
                        'url' => $url
                    ), 'site_dome');
                    $content = array(
                        $this->site_dome->title,
                        $this->site_dome->content
                    );
                    break;
                case 'pcworld.com.vn':
                case 'www.pcworld.com.vn':
                    $this->load->library('pcworld_com_vn', array(
                        'url' => $url
                    ), 'site_dome');
                    $content = array(
                        $this->site_dome->title,
                        $this->site_dome->content
                    );
                    break;
                case 'toidicode.com':
                    $this->load->library('article/toidicode_com', array(
                        'url' => $url
                    ), 'site_dome');
                    $content = array(
                        $this->site_dome->title,
                        $this->site_dome->content
                    );
                    break;

                case "o7planning.org":
                    $this->load->library('article/o7planning_org', array(
                        'url' => $url
                    ), 'site_dome');
                    $content = array(
                        $this->site_dome->title,
                        $this->site_dome->content
                    );
                    break;
                case "laptrinhandroid.vn":
                    $this->load->library('article/laptrinhandroid_vn', array(
                        'url' => $url
                    ), 'site_dome');
                    $content = array(
                        $this->site_dome->title,
                        $this->site_dome->content
                    );
                    break;
                case '':
                    break;
            }
            $content[] = null;
        }
        return $content;
    }

    //http://simplehtmldom.sourceforge.net/manual.htm
    private function findArticleObject($url,$crawlerMask){
        $content = get_site_html_curl($url);
        $html = new simple_html_dom();
        if( env('DEBUG_CRAWLER','boolean')){
            dd($url,false);
            dd($content);
        }
        $html->load($content);
        $title = $thumbnail = $content = null;
        try{
            foreach ($html->find('iframe,script') as $node)
            {
                $node->outertext = '';
            }


            $replace="textarea";
            foreach($html->find($replace) as $key=>$element){
                $html->find($replace,$key)->outertext="<div>".$element->innertext."</div>";
            }

            $titleObject = $html->find($crawlerMask->title_element,0);

            if( $titleObject  ){
                $title = trim( strip_tags($titleObject->innertext) );
            }

            $contents = $html->find($crawlerMask->content_element);
            if( count($contents) > 0 ){
                foreach ($contents AS $b ){
                    $content .= trim($b->innertext);
                }
            }

            if( strlen($crawlerMask->thumbnail_element) > 3 ){
                $thumbnailSource = $html->find($crawlerMask->thumbnail_element,0);
                if( $thumbnailSource ){
                    $thumbnail = trim($thumbnailSource->find("img",0)->src);
                }

            }

        } catch (Exception $e){
            bug($e);die;
        }

        return (object)['title'=>$title,'thumbnail'=>$thumbnail,'content'=>$content];
    }
}