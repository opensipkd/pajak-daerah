<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class rpt_model extends CI_Model
{
    private $tbl = 'sppt';

    function __construct()
    {
        parent::__construct();
    }

    function get_lap_harian($tgl)
    {
        $order = "";
        $urut  = "";
        if (isset($_POST['urut']))
            $urut = (int) $_POST['urut'];
        if ($urut == 1)
            $order = " order by  b.nm_wp_sppt";
        elseif ($urut == 2)
            $order = " order by  a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan,
                     a.kd_blok, a.no_urut, a.kd_jns_op";

        else if ($urut == 3)
            $order = " order by  a.thn_pajak_sppt";
        else
            $order = " order by  a.jml_sppt_yg_dibayar";


        $where = '';
        if ($_POST['buku'] != 5) {
            $b_awal  = buku_bawah($_POST['buku']);
            $b_akhir = buku_atas($_POST['buku']);
            $where .= " and a.jml_sppt_yg_dibayar-a.denda_sppt between $b_awal and $b_akhir ";
        }
        $kel = substr($_POST['kel'], 0, 7);
        if ($kel != '000.000') {
            $where .= " and a.kd_kecamatan='" . substr($kel, 0, 3) . "'
                     and a.kd_kelurahan='" . substr($kel, -3) . "'";
        }

        $uid = $_POST['user'];
        if ($uid != '') {
            $where .= " and a.user_id=" . $uid;
        }


        $fields = explode(',', POS_FIELD);
        //$fs= $this->session->userdata('pos_field') ;
        foreach ($fields as $f) {
            $where .= " and a.$f='" . $this->session->userdata($f) . "' ";
        }


        $sql   = "select a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan,
                    a.kd_blok, a.no_urut, a.kd_jns_op, a.thn_pajak_sppt, a.pembayaran_sppt_ke,
                    a.denda_sppt, a.jml_sppt_yg_dibayar, a.tgl_pembayaran_sppt,
                    b.nm_wp_sppt, a.jml_sppt_yg_dibayar-a.denda_sppt pbb_yg_harus_dibayar_sppt
                from pembayaran_sppt a, sppt b
                where a.kd_propinsi = b.kd_propinsi
                    and a.kd_dati2 = b.kd_dati2
                    and a.kd_kecamatan = b.kd_kecamatan
                    and a.kd_kelurahan = b.kd_kelurahan
                    and a.kd_blok = b.kd_blok
                    and a.no_urut = b.no_urut
                    and a.kd_jns_op = b.kd_jns_op
                    and a.thn_pajak_sppt = b.thn_pajak_sppt
                    and to_char(a.tgl_pembayaran_sppt,'YYYY-MM-DD') = '$tgl'
                    $where
                    $order
                     ";
        $query = $this->db->query($sql);

        if ($query->num_rows() !== 0) {
            return $query->result_array();
        } else
            return FALSE;
    }

    function get_lap_harian2($tgl) //csv
    {
        $order = "";
        $urut  = "";
        if (isset($_POST['urut']))
            $urut = (int) $_POST['urut'];
        if ($urut == 1)
            $order = " order by  b.nm_wp_sppt";
        elseif ($urut == 2)
            $order = " order by  a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan,
                     a.kd_blok, a.no_urut, a.kd_jns_op";

        else if ($urut == 3)
            $order = " order by  a.thn_pajak_sppt";
        else
            $order = " order by  a.jml_sppt_yg_dibayar";


        $where = '';
        if ($_POST['buku'] != 5) {
            $b_awal  = buku_bawah($_POST['buku']);
            $b_akhir = buku_atas($_POST['buku']);
            $where .= " and a.jml_sppt_yg_dibayar-a.denda_sppt between $b_awal and $b_akhir ";
        }
        $kel = substr($_POST['kel'], 0, 7);
        if ($kel != '000.000') {
            $where .= " and a.kd_kecamatan='" . substr($kel, 0, 3) . "'
                     and a.kd_kelurahan='" . substr($kel, -3) . "'";
        }
        $uid = $_POST['user'];
        if ($uid != '') {
            $where .= " and a.user_id=" . $uid;
        }

        $fields = explode(',', POS_FIELD);
        foreach ($fields as $f) {
            $where .= " and a.$f='" . $this->session->userdata($f) . "' ";
        }

        $sql = "select
            a.kd_propinsi||'.'||a.kd_dati2||'.'||a.kd_kecamatan||'.'||a.kd_kelurahan||'.'||a.kd_blok||'.'||a.no_urut||'.'||a.kd_jns_op nop, a.thn_pajak_sppt thn_sppt,
            b.nm_wp_sppt nm_wp,
            a.jml_sppt_yg_dibayar-a.denda_sppt pbb_yg_harus_dibayar_sppt,
            a.denda_sppt,
            a.jml_sppt_yg_dibayar,
            a.pembayaran_sppt_ke,
            a.tgl_pembayaran_sppt

            from pembayaran_sppt a, sppt b
            where a.kd_propinsi = b.kd_propinsi
            and a.kd_dati2 = b.kd_dati2
            and a.kd_kecamatan = b.kd_kecamatan
            and a.kd_kelurahan = b.kd_kelurahan
            and a.kd_blok = b.kd_blok
            and a.no_urut = b.no_urut
            and a.kd_jns_op = b.kd_jns_op
            and a.thn_pajak_sppt = b.thn_pajak_sppt
            and to_char(a.tgl_pembayaran_sppt,'YYYY-MM-DD') = '$tgl'
            $where $order  ";
        $query = $this->db->query($sql);

        if ($query->num_rows() !== 0) {
            return $query->result_array();
        } else
            return FALSE;
    }


    function get_lap_pembatalan($tgl, $tgl2)
    {
        $where = '';
        $uid = $_POST['user'];
        if ($uid != '') {
            if($this->session->userdata('groupkd')=='posspv')
                $where .= " and a.user_id_batal=" . $uid;
            else
                $where .= " and a.user_id=" . $uid;
        }

        $sql   = "select a.kd_propinsi, a.kd_dati2, a.kd_kecamatan, a.kd_kelurahan,
                    a.kd_blok, a.no_urut, a.kd_jns_op, a.thn_pajak_sppt, a.pembayaran_sppt_ke,
                    a.denda_sppt, a.jml_sppt_yg_dibayar, a.tgl_pembayaran_sppt,
                    b.nm_wp_sppt, a.jml_sppt_yg_dibayar-a.denda_sppt pbb_yg_harus_dibayar_sppt,
                    a.jml_batal, a.tgl_batal
                from pembayaran_sppt a, sppt b
                where a.kd_propinsi = b.kd_propinsi
                    and a.kd_dati2 = b.kd_dati2
                    and a.kd_kecamatan = b.kd_kecamatan
                    and a.kd_kelurahan = b.kd_kelurahan
                    and a.kd_blok = b.kd_blok
                    and a.no_urut = b.no_urut
                    and a.kd_jns_op = b.kd_jns_op
                    and a.thn_pajak_sppt = b.thn_pajak_sppt
                    and date(a.tgl_batal) between '{$tgl}' and '{$tgl2}'
                    and a.jml_sppt_yg_dibayar=0 and coalesce(a.jml_batal,0)>0
                    {$where}
                    order by a.tgl_batal, a.thn_pajak_sppt, a.kd_propinsi, a.kd_dati2,
                    a.kd_kecamatan, a.kd_kelurahan, a.kd_blok, a.no_urut, a.kd_jns_op
                ";
        $query = $this->db->query($sql);

        if ($query->num_rows() !== 0) {
            return $query->result_array();
        } else
            return FALSE;
    }

    function get_lap_pembatalan2($tgl, $tgl2)
    {
        $where = '';
        $uid = $_POST['user'];
        if ($uid != '') {
            if($this->session->userdata('groupkd')=='posspv')
                $where .= " and a.user_id_batal=" . $uid;
            else
                $where .= " and a.user_id=" . $uid;
        }

        $sql   = "select a.tgl_batal, a.kd_propinsi||'.'||a.kd_dati2||'.'||a.kd_kecamatan||'.'||a.kd_kelurahan||'.'||
                    a.kd_blok||'.'||a.no_urut||'.'||a.kd_jns_op as nop, a.jml_batal
                from pembayaran_sppt a, sppt b
                where a.kd_propinsi = b.kd_propinsi
                    and a.kd_dati2 = b.kd_dati2
                    and a.kd_kecamatan = b.kd_kecamatan
                    and a.kd_kelurahan = b.kd_kelurahan
                    and a.kd_blok = b.kd_blok
                    and a.no_urut = b.no_urut
                    and a.kd_jns_op = b.kd_jns_op
                    and a.thn_pajak_sppt = b.thn_pajak_sppt
                    and date(a.tgl_batal) between '{$tgl}' and '{$tgl2}'
                    and a.jml_sppt_yg_dibayar=0 and coalesce(a.jml_batal,0)>0
                    {$where}
                    order by a.tgl_batal, a.thn_pajak_sppt, a.kd_propinsi, a.kd_dati2,
                    a.kd_kecamatan, a.kd_kelurahan, a.kd_blok, a.no_urut, a.kd_jns_op
                ";
        $query = $this->db->query($sql);

        if ($query->num_rows() !== 0) {
            return $query->result_array();
        } else
            return FALSE;
    }
}

/* End of file _model.php */
