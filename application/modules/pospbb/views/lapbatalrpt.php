<html>
<head>
</head>
<body>
<?
//  Result := false;
  $nomor = 0;
  $nom   = 0;
  $hal   = 1;
  $jml1  = 0;
  $jml2  = 0;
  $jml3  = 0;

  
?>
<pre>
&nbsp;<br>
<?
	echo str_repeat('&nbsp;',2)."DAFTAR PEMBATALAN TRANSAKSI TANGGAL $tgl S.D. $tgl2<br>";
	echo "<br>";
    
	echo "===================================================================<br>";
	echo "NO.      TANGGAL     NOP                                      NILAI<br>";
	echo "===================================================================<br>";
	if ($rows)
	{
		foreach ($rows as $row)
		{
			$nop = KD_PROPINSI.'.'.KD_DATI2.'.'.
                $row['kd_kecamatan'].'.'.
				$row['kd_kelurahan'].'.'.
				$row['kd_blok'].'-'.
				$row['no_urut'].'.'.
				$row['kd_jns_op'];
			$nomor +=  1;
			$nom   +=  1;
			$jml1 += $row['jml_batal'];
            
			echo str_replace(" ","&nbsp;",str_pad(number_format($nomor,0,',','.'),7," ",STR_PAD_LEFT)).
                 str_replace(" ","&nbsp;",str_pad(date('d-m-Y', strtotime($row['tgl_batal'])),12," ",STR_PAD_LEFT)).
                 "&nbsp;&nbsp;{$nop}&nbsp;&nbsp;".
			     str_replace(" ","&nbsp;",str_pad(number_format($row['jml_batal'],0,',','.'),20,' ',STR_PAD_LEFT))."<br>";
			if ($nom % 50 == 0 )
			{
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				$hal+=1;
				echo str_repeat('&nbsp;',28)."DAFTAR PENERIMAAN HARIAN TANGGAL $tgl <br>";
				echo "<br>";
                echo "===================================================================<br>";
                echo "NO.      TANGGAL     NOP                                      NILAI<br>";
                echo "===================================================================<br>";
			}
		}
	}else
	{
		echo "TIDAK ADA DATA UNTUK PARAMETER YANG DIPILIH<br>";
	}
    echo  "===================================================================<br>";
    echo  str_repeat('&nbsp;',9)."JUMLAH :".str_repeat('&nbsp;',38).
                  str_replace(" ","&nbsp;",str_pad(number_format($jml1,0,',','.'),12,' ',STR_PAD_LEFT))."<br>";
    echo  "===================================================================<br>";

?>
</pre>
</body>
<html>  

