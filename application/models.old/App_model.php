<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_model extends CI_Model {
	private $tbl = 'apps';
	
	function __construct() {
		parent::__construct();
	}

	function insert($data) {
    $this->db->trans_start();
		$this->db->insert($this->tbl, $data);
    $this->db->trans_complete();
        
    if($this->db->trans_status())
        return $this->db->insert_id();
    else
        return false;
	}
	
	function update($id, $data) {
    $this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update($this->tbl,$data);
    $this->db->trans_complete();
        
    if($this->db->trans_status())
        return true;
    else
        return false;
	}
	
	function delete($id) {
    $this->db->trans_start();
    $this->db->where('id', $id);
    $this->db->delete($this->tbl);
    $this->db->trans_complete();
        
    if($this->db->trans_status())
        return true;
    else
        return false;
	}
    
	function get_all() {
    $sql = "select a.*
    from $this->tbl a";
    
		$query = $this->db->query($sql);
    $rows = $query->result();
    if (isset($rows))
    {
        return $rows;
    } else {
        return FALSE;
    }
  }
  
	function get_active_only() {
    $sql = "select a.*
    from $this->tbl a
    where disabled=0";
    
		$query = $this->db->query($sql);
    $rows = $query->result();
    if (isset($rows))
    {
        return $rows;
    } else {
        return FALSE;
    }
  }  
  
	function get_by_id($id)
	{
    $this->db->trans_start();
		$this->db->where('id',$id);
		$query = $this->db->get($this->tbl);
    $this->db->trans_complete();

    if($this->db->trans_status() && $query->num_rows()>0)
        return $query->row();
    else
        return false;
	}
	  
}

/* End of file app_model.php */