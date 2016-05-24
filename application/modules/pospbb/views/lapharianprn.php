<?
//  Result := false;
$name = 'L1';
//.date('Ymdhis'); //The name of the csv file.
    // Build the headers to push out the file properly.
  //   //header('Pragma: public');     // required
  header('Expires: 0');         // no cache
  //       //header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  //         //header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($path)).' GMT');
  //           //header('Cache-Control: private',false);
  header('Content-Type: text/csv');  // Add the mime type from Code igniter.
  header('Content-Disposition: attachment; filename="'.$name.'.prn"');  // Add the file name
  //                 //header('Content-Transfer-Encoding: binary');
  //                   //header('Content-Length: '.filesize($output)); // provide file size
  //                     //header('Connection: close');
  //
  $nomor = 0;
  $nom   = 0;
  $hal   = 1;
  $jml1  = 0;
  $jml2  = 0;
  $jml3  = 0;

  
?>
<?
	echo str_repeat(' ',28)."DAFTAR PENERIMAAN HARIAN TANGGAL $tgl \r\n";
	echo "\r\n";
	echo "Kelurahan:$kelnm, Buku:$bukunm\r\n";
	echo "Tempat Pembayaran : ".str_replace(" "," ",str_pad($banknm,30,' ',STR_PAD_RIGHT)).
	     str_replace(" "," ",str_pad("Halaman : ".number_format($hal,0,',','.'),48,' ',STR_PAD_LEFT))."\r\n";
	echo "==================================================================================================\r\n";
	echo "NO.    NOP                 NAMA WP                        THN.    KET. PBB      DENDA        TOTAL  \r\n";
	echo "==================================================================================================\r\n";
	if ($rows)
	{
		foreach ($rows as $row)
		{
			$nop = $row['kd_kecamatan'].'.'.
				$row['kd_kelurahan'].'.'.
				$row['kd_blok'].'-'.
				$row['no_urut'].'.'.
				$row['kd_jns_op'];
			$nomor +=  1;
			$nom   +=  1;
			$jml1 += $row['pbb_yg_harus_dibayar_sppt'];
			$jml2 += $row['denda_sppt'];
			$jml3 += $row['jml_sppt_yg_dibayar'];
			echo str_replace(" "," ",str_pad(number_format($nomor,0,',','.'),6," ",STR_PAD_LEFT))." $nop ".
					  str_replace(" "," ",str_pad($row['nm_wp_sppt'],31,' ',STR_PAD_RIGHT)) .
					  $row['thn_pajak_sppt'].
					  str_replace(" "," ",str_pad(number_format($row['pbb_yg_harus_dibayar_sppt'],0,',','.'),12,' ',STR_PAD_LEFT)) .
					  str_replace(" "," ",str_pad(number_format($row['denda_sppt'],0,',','.'),11,' ',STR_PAD_LEFT)) .
					  str_replace(" "," ",str_pad(number_format($row['jml_sppt_yg_dibayar'],0,',','.'),13,' ',STR_PAD_LEFT))."\r\n";
			if ($nom % 53 == 0 )
			{
				echo "\r\n";
				echo "\r\n";
				echo "\r\n";
				echo "\r\n";
				echo "\r\n";
				echo "\r\n";
				$hal+=1;
				echo str_repeat(' ',28)."DAFTAR PENERIMAAN HARIAN TANGGAL $tgl \r\n";
				echo "\r\n";
				echo "  Kelurahan:$kelnm, Buku:$bukunm\r\n";
				echo "  Tempat Pembayaran : ".str_pad($banknm,30," ",STR_PAD_LEFT).str_repeat(' ',30)."Halaman : ".number_format($hal,0,',','.')."\r\n";
				echo "==================================================================================================\r\n";
				echo "NO.  NOP                 NAMA WP                        THN.    KET. PBB      DENDA        TOTAL  \r\n";
				echo "==================================================================================================\r\n";
			}
		}
	}else
	{
		echo "TIDAK ADA DATA UNTUK PARAMETER YANG DIPILIH\r\n";
	}
    echo  "==================================================================================================\r\n";
    echo  str_repeat(' ',7)."JUMLAH :".str_repeat(' ',46).
                  str_replace(" "," ",str_pad(number_format($jml1,0,',','.'),12,' ',STR_PAD_LEFT)) .
                  str_replace(" "," ",str_pad(number_format($jml2,0,',','.'),11,' ',STR_PAD_LEFT)) .
                  str_replace(" "," ",str_pad(number_format($jml3,0,',','.'),13,' ',STR_PAD_LEFT))."\r\n";
    echo  "==================================================================================================\r\n";

?>

