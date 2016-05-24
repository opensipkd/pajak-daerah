<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {
	private $tbl = 'users';
	
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

  function set_last_login($id)
  {
      $data=array("last_login" => date('Y-m-d H:i:s'));
      return $this->update($id, $data);
  }
    
  function get_by_uid($uid)
  {
      $qry  = "select u.id, u.userid, u.nama, u.nip, u.passwd, u.last_login
              from $this->tbl u 
              where u.userid='$uid' and disabled<>1
              limit 1";
      $query = $this->db->query($qry);
      $row = $query->row();
      if (isset($row))
      {
          return $row;
      } else {
          return FALSE;
      }
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
	
	function get_by_group($group_id, $in_group=false) {	
        $sql = "select * from (
					select 1 in_group, u.*, ".$group_id." group_id
					from app.users u
					inner join app.user_groups ug on ug.user_id=u.id
					where group_id=".$group_id."
					union
					select 0 as in_group, u.*,".$group_id." group_id
					from users u
					where u.id not in (select user_id from user_groups where group_id=".$group_id.")
				) as gu
				".($in_group? " where in_group=1 ": "")."
				order by in_group desc, disabled desc, nama";
				
        $this->db->trans_start();
		$query = $this->db->query($sql);
        $this->db->trans_complete();
        
        if($this->db->trans_status() && $query->num_rows()>0)
            return $query->result();
        else
            return false;
	}
	
	function get($id)
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
	


    
    function check_group($uid)
    {
        $qry = "select g.*
        from app.groups g
                     inner join app.user_groups ug on g.id=ug.group_id
                 
        where ug.user_id='$uid'
            order by g.id limit 1 ";
        
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            return $rows->row();
        } else {
            return FALSE;
        }
    }
    
    function check_user_app()
    {
        $uid  = $this->session->userdata('userid');
        $mid  = $this->session->userdata('app_id');
        if($mid <> '')
            $mid = ' and m.app_id=' . $mid ;
        
        $qry  = "select distinct a.id app_id, a.app_path, g.id as group_id, g.kode as group_kode, g.nama as group_nama  
            from app.user_groups ug 
                inner join app.groups g on g.id=ug.group_id 
                inner join app.group_modules gm on g.id=gm.group_id
                inner join app.modules m on gm.module_id=m.id
                inner join app.apps a on m.app_id=a.id
            where ug.user_id={$uid} {$mid} and (gm.reads=1 or gm.writes=1 or gm.deletes=1 or gm.inserts=1)
                order by a.id";
                
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            //20140120 -- biar nanti bisa pilih module kalo usernya ada di lebih dari 1 module
            $ret = new stdClass();
            $ret = $rows->row();
            $ret->modcnt = $rows->num_rows();
            return $ret;
        } else {
            return FALSE;
        }
    }
    
    function get_module($app_id)
    {
        $qry = "select * 
                from app.apps a
                where a.id=$app_id
                limit 1";
        
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            return $rows->row();
        } else {
            return FALSE;
        }
    }
    
    function aktif_tahun($app_id)
    {
        $qry = "select * 
                from app.apps a
                inner join app.app_status s on a.id=s.app_id
                where s.step<>'closing' and a.id=$app_id
                order by a.id, s.tahun
                limit 1";
        
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            return $rows->row();
        } else {
            return FALSE;
        }
    }
    
    function inaktif_tahun($app_id)
    {
        $qry = "select max(tahun) tahun, step
                from app.apps a
                inner join app.app_status s on a.id=s.app_id
                where s.step='closing' and a.id=$app_id
                group by 2
                limit 1";
        
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            return $rows->row();
        } else {
            return FALSE;
        }
    }
    
    function get_appid($m)
    {
        $qry  = "select id 
                from app.apps a
                where a.app_path='$m'
                limit 1";
        $rows = $this->db->query($qry);
        if ($rows->num_rows() > 0) {
            return $rows->row();
        } else {
            return FALSE;
        }
    }
   
}

/* End of file _model.php */