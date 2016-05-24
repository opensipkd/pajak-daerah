<? $this->load->view('_head'); ?>
<? $this->load->view('_navbar'); ?>
<style type="text/css">@import "<?=base_url()?>assets/css/pbbm.css";</style>
<script>
    $(document).ready(function() {
        var oTable = $('#datatable').dataTable( {
            "iDisplayLength": 100,
            "sScrollY": "260px",
            "bJQueryUI" : true,
            "bAutoWidth": true,
            "bScrollCollapse": false,
            "bLengthChange": false,
            "bPaginate": true,
            "bFilter": true,
            "sPaginationType" : "full_numbers",
            "bSort": false,
            "bInfo": true,
            "bServerSide": true,
            "bProcessing": true,
            "sAjaxSource": "<?=$data_source?>",
            "sDom":'<"toolbar">fTl<"clear">rtip',
            // "sDom": '<"H"lfr>t<"F"ip>T',

            "aoColumns" : [
                { sWidth: '12%', sClass: "center" },
                { sWidth: '25%', sClass: "" },
                null,
                { sWidth: '10%', sClass: "right" },
                { sWidth: '8%', sClass: "right" },
            ],

            "aoColumnDefs": [
                { "bSearchable": false, "aTargets": [ 0 ], "bSortable": true, "aTargets": [ 0 ] },
                { "bSearchable": false, "aTargets": [ 1 ], "bSortable": true, "aTargets": [ 1 ] }
            ],

            "oTableTools": {
                "sSwfPath": "<?=base_url()?>assets/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            },

            "oLanguage": {
                "sProcessing":   "<img border='0' src='<?=base_url('assets/img/ajax-loader-big-circle-ball.gif')?>' />",
                "sLengthMenu":   "Tampilkan _MENU_",
                // "sZeroRecords":  "Tidak ada data",
                "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "sInfoPostFix":  "",
                "sSearch":       "Cari : ",
                "sUrl":          "",
            },
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                $.getJSON( sSource, aoData, function (json) {
                    //Here you can do whatever you want with the additional data
                    // console.dir(json);
                    $('#pokok').html(json['pokok']);
                    $('#denda').html(json['denda']);
                    $('#total').html(json['total']);

                    //Call the standard callback to redraw the table
                    fnCallback(json);
                });
            },
        });

        var tb_array = [
            '<div class="btn-group pull-left">',
            '	<button id="btn_cetak1" class="btn btn-success" type="button">Cetak Muka</button>',
            '	<button id="btn_cetak2" class="btn btn-success" type="button">Cetak Isi</button>',
            '	<button id="btn_cetak3" class="btn btn-success" type="button">Cetak Rekap</button>',
            '</div>',
        ];
        var tb = tb_array.join(' ');	
        $("div.toolbar").html(tb);

        $('#btn_cetak1').click(function() {
            var rpt = 'dhkp_front';
            var rptparams = {
                rpt: rpt,
                blok1 : $("#blok1").val(),
                blok2 : $("#blok2").val(),
                tglawal : $("#tglawal").val(),
                tglakhir : $("#tglakhir").val(),
                tahun_sppt1 : $("#tahun_sppt1").val(),
                tahun_sppt2 : $("#tahun_sppt2").val(),
                kec_kd : $("#kec_kd").val(),
                kec : $("#kec_kd option:selected").text(),
                kel_kd : $("#kel_kd").val(),
                kel : $("#kel_kd option:selected").text(),
                buku : $("#buku").val(),
                tp_kd : $("#tp_kd").val(),
                tp : $("#tp_kd option:selected").text(),
            }
            var rptdata = decodeURIComponent($.param(rptparams));
            var winparams = 'location=1,status=1,scrollbars=1,resizable=no,width='+screen.width+',height='+screen.height+',menubar=no,toolbar=no,fullscreen=no';
            window.open('<?=active_module_url($this->uri->segment(2));?>cetak/pdf/?'+rptdata, 'Laporan', winparams);
        });

        $('#btn_cetak2').click(function() {
            var rpt = 'dhkp';
            var rptparams = {
                rpt: rpt,
                blok1 : $("#blok1").val(),
                blok2 : $("#blok2").val(),
                tglawal : $("#tglawal").val(),
                tglakhir : $("#tglakhir").val(),
                tahun_sppt1 : $("#tahun_sppt1").val(),
                tahun_sppt2 : $("#tahun_sppt2").val(),
                kec_kd : $("#kec_kd").val(),
                kec : $("#kec_kd option:selected").text(),
                kel_kd : $("#kel_kd").val(),
                kel : $("#kel_kd option:selected").text(),
                buku : $("#buku").val(),
                tp_kd : $("#tp_kd").val(),
                tp : $("#tp_kd option:selected").text(),
            }
            var rptdata = decodeURIComponent($.param(rptparams));
            var winparams = 'location=1,status=1,scrollbars=1,resizable=no,width='+screen.width+',height='+screen.height+',menubar=no,toolbar=no,fullscreen=no';
            window.open('<?=active_module_url($this->uri->segment(2));?>cetak/pdf/?'+rptdata, 'Laporan', winparams);
        });

        $('#btn_cetak3').click(function() {
            var rpt = 'dhkp_end';
            var rptparams = {
                rpt: rpt,
                blok1 : $("#blok1").val(),
                blok2 : $("#blok2").val(),
                tglawal : $("#tglawal").val(),
                tglakhir : $("#tglakhir").val(),
                tahun_sppt1 : $("#tahun_sppt1").val(),
                tahun_sppt2 : $("#tahun_sppt2").val(),
                kec_kd : $("#kec_kd").val(),
                kec : $("#kec_kd option:selected").text(),
                kel_kd : $("#kel_kd").val(),
                kel : $("#kel_kd option:selected").text(),
                buku : $("#buku").val(),
                tp_kd : $("#tp_kd").val(),
                tp : $("#tp_kd option:selected").text(),
            }
            var rptdata = decodeURIComponent($.param(rptparams));
            var winparams = 'location=1,status=1,scrollbars=1,resizable=no,width='+screen.width+',height='+screen.height+',menubar=no,toolbar=no,fullscreen=no';
            window.open('<?=active_module_url($this->uri->segment(2));?>cetak/pdf/?'+rptdata, 'Laporan', winparams);
        });

        $( "#tglawal, #tglakhir" ).datepicker({
            dateFormat:'dd-mm-yy',
            changeMonth:true,
            changeYear:true
        });

        $("#btngo").click(function(){
            var blok1 = $("#blok1").val();
            var blok2 = $("#blok2").val();
            var tglawal = $("#tglawal").val();
            var tglakhir = $("#tglakhir").val();
            var tahun_sppt1 = $("#tahun_sppt1").val();
            var tahun_sppt2 = $("#tahun_sppt2").val();
            var kec_kd = $("#kec_kd").val();
            var kel_kd = $("#kel_kd").val();
            var buku = $("#buku").val();
            var tp = $("#tp_kd").val();
            window.location = "<?=active_module_url();?>dhkp?tahun_sppt1="+ tahun_sppt1 +"&kec_kd=" + kec_kd +"&kel_kd=" + kel_kd +"&blok1=" + blok1 +"&blok2=" + blok2 +"&buku=" + buku+ "&tp_kd=" + tp;
        });

        $("#kec_kd, #kel_kd, #buku, #tahun_sppt1, #tahun_sppt2,#tp_kd").change(function(){
            var blok1 = $("#blok1").val();
            var blok2 = $("#blok2").val();
            var tglawal = $("#tglawal").val();
            var tglakhir = $("#tglakhir").val();
            var tahun_sppt1 = $("#tahun_sppt1").val();
            var tahun_sppt2 = $("#tahun_sppt2").val();
            var kec_kd = $("#kec_kd").val();
            var kel_kd = $("#kel_kd").val();
            var buku = $("#buku").val();
            var tp = $("#tp_kd").val();
            window.location = "<?=active_module_url();?>dhkp?tahun_sppt1="+ tahun_sppt1 +"&kec_kd=" + kec_kd +"&kel_kd=" + kel_kd +"&blok1=" + blok1 +"&blok2=" + blok2 +"&buku=" + buku+ "&tp_kd=" + tp;
        });
    });
</script>
<div class="content">
    <div class="container-fluid">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a><strong><?=$title;?></strong></a></li>
        </ul>
        <div class="form-horizontal">
            <div class="control-group">
                <label class="control-label">Thn. SPPT</label>
                <div class="controls">
                    <select id="tahun_sppt1" name="tahun_sppt1" style="width:80px;">
                    <?php
                        $maxtahun=date('Y');
                        $mintahun=mintahun_sppt();
                        $thncnt = $maxtahun - $mintahun;
                        for ($i=$maxtahun; $i>=$maxtahun-$thncnt; $i--)
                        {
                          $selected='';
                          if ($i==$tahun_sppt1) $selected=" selected";
                          echo "<option value=\"$i\" $selected>$i</option>\n";
                        }
                        ?>
                    </select>
                    Buku
                    <select id="buku" name="buku" style="width:125px;">
                    <?for ($i=1; $i<=5; $i++){
                        for ($j=$i;$j<=5;$j++){
                            $r="";
                            for ($k=$i;$k<=$j;$k++) $r.="$k,";
                            $r=substr($r,0,strlen($r)-1);
                            if ($buku=="$i$j") $selected="selected";
                            else $selected="";
                            echo "<option value=\"$i$j\" $selected>Buku $r</option>\n";
                        }
                        }
                        ?>
                    </select>
                    TP
                    <select id="tp_kd" name="tp_kd">
                    <?php
                        echo "<option value=\"\">Semua TP</option>\n";
                        foreach ($tp as $row)  {
                            $selected='';
                            if ($row->kode==$tp_kd) $selected=" selected";
                            echo "<option value=\"".$row->kode ."\" $selected>".$row->nm_tp."</option>\n";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Kecamatan</label>
                <div class="controls">
                    <select id="kec_kd" name="kec_kd" <?=($user_kec_kd!='000'?" disabled" :"")?>>
                    <?php
                        echo "<option value=\"000\">Semua</option>\n";

                        foreach ($kecamatan as $kec)
                        {
                         $selected='';
                         if ($kec->kd_kecamatan==$kec_kd) $selected=" selected";
                         echo "<option value=\"".$kec->kd_kecamatan ."\" $selected>".$kec->nm_kecamatan."</option>\n";
                        }
                        ?>
                    </select>
                    Kelurahan
                    <select id="kel_kd" name="kel_kd">
                    <?php
                        if ($user_kel_kd=='000')
                            echo "<option value=\"000\">Semua</option>\n";
                        foreach ($kelurahan as $kel)
                        {
                          $selected='';
                          if ($kel->kd_kelurahan==$kel_kd) $selected=" selected";
                          echo "<option value=\"".$kel->kd_kelurahan."\" $selected>".$kel->nm_kelurahan."</option>\n";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Range Blok</label>
                <div class="controls">
                    <input type="text" style="width:40px;" id="blok1" value="<?=$blok1;?>">&nbsp;
                    s/d
                    <input type="text" style="width:40px;" id="blok2" value="<?=$blok2;?>">&nbsp;
                    <button class="btn" id="btngo" name="btngo">Go</button>
                </div>
            </div>
        </div>
        <hr>
        <table class="display" id="datatable">
            <thead>
                <tr>
                    <th>NOP</th>
                    <th>Wajib Pajak</th>
                    <th>Alamat</th>
                    <th>Terhutang</th>
                    <th>Tgl. Bayar</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">TOTAL</td>
                    <td><span id="total">&nbsp;</span></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<? $this->load->view('_foot'); ?>