<?php if (!defined('BASEPATH')) exit('No direct script coess allowed');

class BaseCategoryMapModel extends MX_Model
{

    var $table = 'category_map';
    function __construct()
    {
        parent::__construct();
    }

    public function update($target_id, $target_table, $categories = [],$alias=null)
    {
        $where = ['target_id' => $target_id, 'target_table' => $target_table];
        if( strlen($alias)>0 ){
            $where['alias'] = $alias;
        }
        $query = $this->db->where($where)->get($this->table);

        if ($query->num_rows() < 1 && !empty($categories)) {
            foreach ($categories AS $cate) {
                if( strlen($cate) > 0 && is_numeric($cate) ){
                    $where['category_id'] = $cate;
                    $this->db->insert("category_map", $where);
                }
            }
        } else {
            foreach ($query->result() AS $row) {
                if (in_array($row->category_id, $categories) != true) {
                    $this->db->where('id', $row->id)->delete($this->table);
                } else if (($key = array_search($row->category_id, $categories)) !== false) {
                    unset($categories[$key]);
                }
            }
            if (!empty($categories)) {
                foreach ($categories AS $cate) {
                    if( strlen($cate) > 0 && is_numeric($cate) ){
                        $where['category_id'] = $cate;
                        $this->db->insert($this->table, $where);
                    }
                }
            }
        }
    }

    public function getCategories($target_id,$target_table='article',$alias=''){
        $data = [];
        $where = ['target_id' => $target_id, 'target_table' => $target_table];
        if( strlen($alias)>0 ){
            $where['alias'] = $alias;
        }
        $query = $this->db->where($where)->get($this->table);
        if( $query->num_rows() > 0 ) foreach ($query->result() AS $cate){
            $data[] = $cate->category_id;
        }
        return $data;
    }

    public function GetRowMapping($target_id,$target_table='article',$alias=''){
        $where = ['target_id' => $target_id, 'target_table' => $target_table];
        if( strlen($alias)>0 ){
            $where['alias'] = $alias;
        }
        $query = $this->db->where($where)->limit(1)->get($this->table);
        if( $query->num_rows() > 0 ) {
            $row = $query->row();
            $map_data =$this->row_get($row->category_id,"category");

            if( $map_data ){
                $row->mapping_value = $map_data->name;
                $row->mapping_id = $map_data->id;
            } else {
                return [];
            }

            return $row;
        }
        return [];
    }

    public function getChildrens($id,$target_table='category'){
        $data = [];
        $where = ['category_id' => $id, 'target_table' => $target_table];
        $query = $this->db->where($where)->get($this->table);
        if( $query->num_rows() > 0 ) foreach ($query->result() AS $cate){
            $data[] = $cate->target_id;
        }
        return $data;
    }

    public function LinkIDConcat($link_id_to='id',$target_alias='',$target_table="category",$tb_alias='m'){
        $sql = "SELECT GROUP_CONCAT($tb_alias.category_id) FROM category_map AS $tb_alias WHERE $tb_alias.target_id = $link_id_to AND m.target_table = '$target_table' ";
        if ( strlen($target_alias) > 0){
            $sql .= " AND m.alias = '$target_alias' ";
        }
        return $sql;
    }

    public function LinkNameConcat($link_id_to='id',$target_alias='',$target_table="category",$tb_alias='cate'){
        $sql = "SELECT GROUP_CONCAT($tb_alias.name) FROM category AS $tb_alias ";
        $sql.= "LEFT JOIN category_map AS m ON m.category_id = $tb_alias.id WHERE m.target_id = $link_id_to AND m.target_table = '$target_table' ";

        if ( strlen($target_alias) > 0){
            $sql .= " AND m.alias = '$target_alias' ";
        }
        return $sql;
    }

    public function LinkCount($link_id_from='id',$target_alias='',$target_table="category",$tb_alias='m'){
        $sql = "SELECT COUNT($tb_alias.category_id) FROM category_map AS $tb_alias WHERE $tb_alias.category_id = $link_id_from AND m.target_table = '$target_table' ";
        if ( strlen($target_alias) > 0){
            $sql .= " AND m.alias = '$target_alias' ";
        }
        return $sql;
    }

    protected $tree_from_top = [];
    public function GetTreeFromTop($target_id,$target_table='article',$stop_at=null){
        $type_skip = ['class_cladus','cladus'];

        $sync = "SELECT GROUP_CONCAT(sync.value) FROM synonym AS sync WHERE sync.link_table = 'category' AND sync.link_id=m.category_id";
        $query = $this->db->from($this->table." AS m")
                ->where(['m.target_id' => $target_id, 'm.target_table' => $target_table])
                ->join("$target_table AS c","c.id=m.category_id")
                ->select("c.id, c.name, c.type, m.alias AS link_alias, c.alias")
                ->select("($sync) AS synonyms",false)
                ->where_not_in('m.alias',$type_skip)
                ->get();


        if( $query->num_rows() > 0 ) {
            $row = $query->row();

            $this->tree_from_top[] = $row;
            $stopped = (is_string($stop_at) && $row->link_alias == $stop_at) || (is_numeric($stop_at) && $row->id == $stop_at);
            if(  count($this->tree_from_top) > 20 ){
                $stopped = true;
            }
            if (  $stopped != true ){
                $this->GetTreeFromTop($row->id,$target_table,$stop_at);
            }
        } else {
//            dd($this->db->last_query(),false,0);
        }
        return $this->tree_from_top;
    }


}