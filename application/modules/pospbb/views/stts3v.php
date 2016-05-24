<?
  $name = 'stts'.date('Ymdhis'); //The name of the csv file.
  // Build the headers to push out the file properly.
  //header('Pragma: public');     // required
  header('Expires: 0');         // no cache
  //header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  //header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($path)).' GMT');
  //header('Cache-Control: private',false);
  //header('Content-Type: text/csv');  // Add the mime type from Code igniter.
  header('Content-Disposition: attachment; filename="'.$name.'.prn"');  // Add the file name
  //header('Content-Transfer-Encoding: binary');
  //header('Content-Length: '.filesize($output)); // provide file size
  //header('Connection: close');
  $sn=date('dmY',strtotime($tgl_pembayaran_sppt));
  $sn.=$kd_propinsi.$kd_dati2.$kd_kecamatan.$kd_kelurahan.$kd_blok.$no_urut.$kd_jns_op.$thn_pajak_sppt;
  $nohuruf1 = terbilang($jml_sppt_yg_dibayar); 
  $nohuruf2 = "";
  while (strlen($nohuruf1)>75){
    $n = strrpos($nohuruf1,' ');
    $nohuruf2 = substr($nohuruf1,$n).$nohuruf2;
    $nohuruf1 = substr($nohuruf1,0,$n);
  }
  $nohuruf1 = '#'.$nohuruf1.'#';
  $nohuruf2 = '#'.trim($nohuruf2).' rupiah #';
?>
  <?=str_pad('SURAT TANDA TERIMA SETORAN (STTS)',77," ",STR_PAD_BOTH)?> 
  <?=str_pad('BUKTI PEMBAYARAN LUNAS PAJAK PBB-P2',77," ",STR_PAD_BOTH)?> 
  KOTA/KABUPATEN    : <?=$nm_dati2?> 
  TEMPAT PEMBAYARAN : <?=$nm_tp?> 
  TANGGAL TRANSAKSI : <?=str_pad(date('d/m/Y',strtotime($tgl_pembayaran_sppt)),15," ",STR_PAD_RIGHT)?>SN:<?=MD5($sn)?> 
  NOP               : <?=str_pad("$kd_propinsi.$kd_dati2.$kd_kecamatan.$kd_kelurahan.$kd_blok-$no_urut.$kd_jns_op",30," ",STR_PAD_RIGHT)?>THN PAJAK :<?=$thn_pajak_sppt?> 
  NAMA WAJIB PAJAK  : <?=substr($nm_wp_sppt,0,30)?> 
  ALAMAT WAJIB PAJAK: <?=substr($jln_wp_sppt,0,45).' '.substr($blok_kav_no_wp_sppt,10)?> 
  LETAK OBJEK PAJAK                              URAIAN PEMBAYARAN
  KELURAHAN : <?=str_pad(substr($nm_kelurahan,0,30),35," ",STR_PAD_RIGHT)?>POKOK : <?=str_pad(number_format($jml_sppt_yg_dibayar-$denda_sppt,0,',','.'),15," ",STR_PAD_LEFT)?> 
  KECAMATAN : <?=str_pad(substr($nm_kecamatan,0,30),35," ",STR_PAD_RIGHT)?>DENDA : <?=str_pad(number_format($denda_sppt,0,',','.'),15," ",STR_PAD_LEFT)?> 
  LUAS TANAH: <?=str_pad(number_format($luas_bumi_sppt,0,',','.'),8," ",STR_PAD_LEFT)?> M2                        BAYAR : <?=str_pad(number_format($jml_sppt_yg_dibayar,0,',','.'),15," ",STR_PAD_LEFT)?> 
  LUAS BNG  : <?=str_pad(number_format($luas_bng_sppt, 0,',','.'),8," ",STR_PAD_LEFT)?> M2  
  TGL JATUH TEMPO : <?=str_pad(date('d/m/Y',strtotime($tgl_jatuh_tempo_sppt)),29," ", STR_PAD_RIGHT)?> 
  TERBILANG :                                    PETUGAS BANK
  <?=substr($nohuruf1,0,77)?> 
  <?=substr($nohuruf2,0,77)?> 
  ------------------------------------------------------------------------------
  SELURUH PEMERINTAH KABUPATEN/KOTA PROPINSI <?=$nm_propinsi?> 
  MENYATAKAN RESI INI SEBAGAI BUKTI PEMBAYARAN PAJAK DAERAH YANG SAH.
  PEMBAYARAN PAJAK DAERAH DAPAT DILAKUKAN DI JARINGAN KANTOR BANK TERDEKAT
  ==============================================================================