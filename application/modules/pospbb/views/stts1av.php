<?
  $name = 'stts'.date('Ymdhis'); //The name of the csv file.
  // Build the headers to push out the file properly.
  //header('Pragma: public');     // required
  header('Expires: 0');         // no cache
  //header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  //header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($path)).' GMT');
  //header('Cache-Control: private',false);
  header('Content-Type: text/csv');  // Add the mime type from Code igniter.
  header('Content-Disposition: attachment; filename="'.$name.'.prn"');  // Add the file name
  //header('Content-Transfer-Encoding: binary');
  //header('Content-Length: '.filesize($output)); // provide file size
  //header('Connection: close');

?>
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',13).$nm_tp?> 
<?=str_repeat(' ',20).$thn_pajak_sppt?> 
<?=str_repeat(' ',13).substr($nm_wp_sppt,0,30)?> 
<?=str_repeat(' ',20).substr($nm_kecamatan,0,30)?> 
<?=str_repeat(' ',20).substr($nm_kelurahan,0,30)?> 
<?=str_repeat(' ',13)."$kd_propinsi.$kd_dati2.$kd_kecamatan.$kd_kelurahan.$kd_blok-$no_urut.$kd_jns_op"?> 
<?=str_repeat(' ',13).number_format($jml_sppt_yg_dibayar-$denda_sppt,0,',','.')?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',16).date('d/m/Y',strtotime($tgl_jatuh_tempo_sppt))?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',6) . 'TGL PEMBAYARAN    :   ' .str_pad(date('d/m/Y',strtotime($tgl_pembayaran_sppt)),16," ",STR_PAD_LEFT)?> 
<?=str_repeat(' ',6) . 'PEMBAYARAN        :Rp.' .str_pad(number_format($jml_sppt_yg_dibayar-$denda_sppt,0,',','.'), 16, " ", STR_PAD_LEFT)?> 
<?=str_repeat(' ',6) . 'DENDA ADMINISTRSI :Rp.' .str_pad(number_format($denda_sppt,0,',','.'), 16, " ", STR_PAD_LEFT)?> 
<?=str_repeat(' ',6) . 'TOTAL PEMBAYARAN  :Rp.' .str_pad(number_format($jml_sppt_yg_dibayar,0,',','.'), 16, " ", STR_PAD_LEFT)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?
  $sn=date('dmY',strtotime($tgl_pembayaran_sppt));
  $sn.=$kd_propinsi.$kd_dati2.$kd_kecamatan.$kd_kelurahan.$kd_blok.$no_urut.$kd_jns_op.$thn_pajak_sppt;
?>  
<?=str_repeat(' ',6) . 'SN : '. md5($sn)?> 
<?=str_repeat(' ',14) . str_pad(date('d/m/Y',strtotime($tgl_pembayaran_sppt)),12," ",STR_PAD_RIGHT).str_pad(number_format($luas_bumi_sppt,0,',','.'),10," ",STR_PAD_LEFT)?> 
<?=str_repeat(' ',26) . str_pad(number_format($luas_bng_sppt,0,',','.'),10," ",STR_PAD_LEFT)?> 
<?=str_repeat(' ',16) . str_pad(number_format($jml_sppt_yg_dibayar,0,',','.'),20," ",STR_PAD_RIGHT)?>
<?=str_repeat(' ',1)?> 
1
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',13).$nm_tp?> 
<?=str_repeat(' ',20).$thn_pajak_sppt?> 
<?=str_repeat(' ',13).substr($nm_wp_sppt,0,30)?> 
<?=str_repeat(' ',20).substr($nm_kecamatan,0,30)?> 
<?=str_repeat(' ',20).substr($nm_kelurahan,0,30)?> 
<?=str_repeat(' ',13)."$kd_propinsi.$kd_dati2.$kd_kecamatan.$kd_kelurahan.$kd_blok-$no_urut.$kd_jns_op"?> 
<?=str_repeat(' ',13).number_format($jml_sppt_yg_dibayar-$denda_sppt,0,',','.')?> 
<?=str_repeat(' ',13).date('d/m/Y',strtotime($tgl_pembayaran_sppt))?> 
<?=str_repeat(' ',16).number_format($jml_sppt_yg_dibayar,0,',','.')?> 
<?=str_repeat(' ',1)?> 
2
<?=str_repeat(' ',13).$nm_tp?> 
<?=str_repeat(' ',20).$thn_pajak_sppt?> 
<?=str_repeat(' ',13).substr($nm_wp_sppt,0,30)?> 
<?=str_repeat(' ',20).substr($nm_kecamatan,0,30)?> 
<?=str_repeat(' ',20).substr($nm_kelurahan,0,30)?> 
<?=str_repeat(' ',13)."$kd_propinsi.$kd_dati2.$kd_kecamatan.$kd_kelurahan.$kd_blok-$no_urut.$kd_jns_op"?> 
<?=str_repeat(' ',13).number_format($jml_sppt_yg_dibayar-$denda_sppt,0,',','.')?> 
<?=str_repeat(' ',13).date('d/m/Y',strtotime($tgl_pembayaran_sppt))?> 
<?=str_repeat(' ',16).number_format($jml_sppt_yg_dibayar,0,',','.')?> 
<?=str_repeat(' ',1)?> 
3
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',13).$nm_tp?> 
<?=str_repeat(' ',20).$thn_pajak_sppt?> 
<?=str_repeat(' ',13).substr($nm_wp_sppt,0,30)?> 
<?=str_repeat(' ',20).substr($nm_kecamatan,0,30)?> 
<?=str_repeat(' ',20).substr($nm_kelurahan,0,30)?> 
<?=str_repeat(' ',13)."$kd_propinsi.$kd_dati2.$kd_kecamatan.$kd_kelurahan.$kd_blok-$no_urut.$kd_jns_op"?> 
<?=str_repeat(' ',13).number_format($jml_sppt_yg_dibayar-$denda_sppt,0,',','.')?> 
<?=str_repeat(' ',13).date('d/m/Y',strtotime($tgl_pembayaran_sppt))?> 
<?=str_repeat(' ',16).number_format($jml_sppt_yg_dibayar,0,',','.')?> 
<?=str_repeat(' ',1)?> 
4
<?=str_repeat(' ',1)?> 
<?=str_repeat(' ',1)?> 