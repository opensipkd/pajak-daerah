<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ipbb extends CI_Controller {
    private $module = 'ipbb';

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $nopthn = $_GET['nopthn'];
        if(!$nopthn) return NULL;

        $qwe = preg_replace("/[^0-9]/","",$nopthn);
        $nop = substr($qwe,0,18);
        $thn = substr($qwe,18,4);

        $nop_cnt = strlen($nop);
        $thn_cnt = strlen($thn);

        if(!$nop || !$thn || $thn_cnt!=4 || $nop_cnt!=18) return NULL;

        $kdprop=''; $kddati=''; $kdkec=''; $kdkel=''; $kdblok=''; $nourut=''; $jns='';
        $nop_num = preg_replace("/[^0-9]/","",$nop);
        $nop_dot = preg_replace("/([0-9]{2})([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{1})/", "$1.$2.$3.$4.$5.$6.$7", $nop_num);

        $kode = explode(".", $nop_dot);
        list($kdprop, $kddati, $kdkec, $kdkel, $kdblok, $nourut, $jns) = $kode;

        /*
        # 2015-06-17 CC. Aa
        # ganti FROMnya dari: dat_objek_pajak LEFT JOIN sppt ke: sppt LEFT JOIN dat_objek_pajak
        # status_pembayaran_sppt <> 2 (batal)
        */

        $sql = "SELECT
            s.kd_propinsi||'.'||s.kd_dati2||'-'||s.kd_kecamatan||'.'||s.kd_kelurahan ||'-'|| s.kd_blok ||'.'||s.no_urut||'.'|| s.kd_jns_op as nop,
            coalesce(dop.jalan_op,'')||', '||coalesce(dop.blok_kav_no_op,'') as alamat_op,
            coalesce(dop.rt_op,'')||' / '||coalesce(dop.rw_op,'') as rt_rw_op,
            coalesce(kec.nm_kecamatan,'') nm_kecamatan, coalesce(kel.nm_kelurahan,'') nm_kelurahan,
            dop.total_luas_bumi, dop.total_luas_bng,
            s.thn_pajak_sppt, coalesce(s.pbb_yg_harus_dibayar_sppt, 0) as tagihan,
            sum(coalesce(ps.jml_sppt_yg_dibayar,0))-sum(coalesce(ps.denda_sppt,0)) as bayar, 
            coalesce(to_char(max(ps.tgl_pembayaran_sppt),'DD-MM-YYYY'),'') as tgl_bayar

            FROM sppt s
            LEFT JOIN dat_objek_pajak dop
                ON dop.kd_propinsi = s.kd_propinsi AND dop.kd_dati2 = s.kd_dati2
                AND dop.kd_kecamatan = s.kd_kecamatan
                AND dop.kd_kelurahan = s.kd_kelurahan
                AND dop.kd_blok = s.kd_blok
                AND dop.no_urut = s.no_urut
                AND dop.kd_jns_op = s.kd_jns_op
            LEFT JOIN pembayaran_sppt ps
                ON ps.kd_propinsi = s.kd_propinsi AND ps.kd_dati2 = s.kd_dati2
                AND ps.kd_kecamatan = s.kd_kecamatan AND ps.kd_kelurahan = s.kd_kelurahan
                AND ps.kd_blok = s.kd_blok AND ps.no_urut = s.no_urut
                AND ps.kd_jns_op = s.kd_jns_op AND ps.thn_pajak_sppt = s.thn_pajak_sppt
            LEFT JOIN ref_kecamatan kec
                ON kec.kd_propinsi = s.kd_propinsi
                AND kec.kd_dati2 = s.kd_dati2
                AND kec.kd_kecamatan = s.kd_kecamatan
            LEFT JOIN ref_kelurahan kel
                ON kel.kd_propinsi = s.kd_propinsi
                AND kel.kd_dati2 = s.kd_dati2
                AND kel.kd_kecamatan = s.kd_kecamatan
                AND kel.kd_kelurahan = s.kd_kelurahan

            WHERE s.kd_propinsi = '" . $kdprop. "'
                AND s.kd_dati2 = '" . $kddati . "'
                AND s.kd_kecamatan = '" . $kdkec . "'
                AND s.kd_kelurahan = '" . $kdkel . "'
                AND s.kd_blok = '" . $kdblok . "'
                AND s.no_urut = '" . $nourut . "'
                AND s.kd_jns_op = '" . $jns . "'
                AND trim(s.thn_pajak_sppt) = '" . trim($thn) . "'
                AND s.status_pembayaran_sppt::int <> 2

            GROUP BY 1,2,3,4,5,6,7,8,9";

        $query = $this->db->query($sql);
        if($query->num_rows() > 0) {
            echo json_encode($query->row_array());
        } else {
            return NULL;
        }
    }
}

