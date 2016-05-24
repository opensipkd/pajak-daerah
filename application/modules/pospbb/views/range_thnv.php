<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style>
table.dataTable tfoot th {
    padding: 3px 10px !important;
}
</style>

<script>
var dID;
var oTable;
var xRow;
var xRow2;
var asInitVals = new Array();

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

$(document).ready(function() {
    $('#table1 a.delete').live('click', function (e) {
        e.preventDefault();
        var nRow = $(this).parents('tr')[0];
        // alert(nRow.toSource());
        oTable.fnDeleteRow( nRow );
    });

    oTable = $('#table1').dataTable({
        "iDisplayLength": 100,
        "bJQueryUI" : true,
        // "sScrollY": "325px",
        "bScrollCollapse": true,
        "bPaginate": false,
        "bAutoWidth" : false,
        "sPaginationType": "full_numbers",
        "bFilter": false,
        "bLengthChange": false,
        // "sDom": '<"top">rt<"bottom"ilp><"clear">',
        "sDom":'<"toolbar">fT<"clear">lrtip<"clear">',
        // "sDom":'<"toolbar">fT<"clear">lrtip<"clear">',
        "aoColumnDefs": [
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 0 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 1 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 2 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 3 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 4 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 5 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 6 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 7 ] }
        ],
        "aoColumns": [
            { "sWidth": "15%" },
            { "sWidth": "10%" },
            { "sWidth": "10%" , "sClass": "right" },
            { "sWidth": "10%" , "sClass": "right" },
            { "sWidth": "10%" , "sClass": "right" },
            { "sWidth": "20%"},
            { "sWidth": "25%" },
            { "sWidth": "25%" },
        ],
        "oTableTools": {
            "sSwfPath": "<?=base_url()?>assets/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
        },
        "oLanguage": {
            "sProcessing":   "<img border='0' src='<?=base_url('assets/img/ajax-loader-big-circle-ball.gif')?>' />",
        },
        "bSort": true,
        "bInfo": true,
        "bServerSide": false,
        "bProcessing": true,
        "sAjaxSource": "<?=active_module_url();?>range_thn/cari/",
        /*
        "fnFooterCallback": function( nFoot, aData, iStart, iEnd, aiDisplay ) {
            nFoot.getElementsByTagName('th')[2].innerHTML = aData[10];
            nFoot.getElementsByTagName('th')[3].innerHTML = 10;
            nFoot.getElementsByTagName('th')[4].innerHTML = 15;
        }
        */
        "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
            var iTotalPokok  = 0;
            var iTotalDenda  = 0;
            var iTotalJumlah = 0;
            if (aaData.length > 0) {
                for (var i = 0; i < aaData.length; i++) {
                    // alert(aaData[i][2]);
                    iTotalPokok  += parseInt(aaData[i][2].replace(/[^0-9]/gi, ''));
                    iTotalDenda  += parseInt(aaData[i][3].replace(/[^0-9]/gi, ''));
                    iTotalJumlah += parseInt(aaData[i][4].replace(/[^0-9]/gi, ''));
                }
            }
            /*
             * render the total row in table footer
             */
            var nCells = nRow.getElementsByTagName('th');
            nCells[2].innerHTML = numberWithCommas(iTotalPokok);
            nCells[3].innerHTML = numberWithCommas(iTotalDenda);
            nCells[4].innerHTML = numberWithCommas(iTotalJumlah);

        }
    });

    $("div.toolbar").append($('.asd'));

    /*
    $("thead input").keypress(function(event) {
      if ( event.which == 13 ) {
          oTable.fnFilter( this.value, $("thead input").index(this) );
      }});
     */

    /*
     * Support functions to provide a little bit of 'user friendlyness' to the textboxes in
     * the footer
     */

    /*
    $("thead input").each( function (i) {
        asInitVals[i] = this.value;
    } );

    $("thead input").focus( function () {
        if ( this.className == "search_init" )
        {
            this.className = "";
            this.value = "";
        }
    } );


    $("thead input").blur( function (i) {
        if ( this.value == "" )
        {
            this.className = "search_init";
            this.value = asInitVals[$("thead input").index(this)];
        }
    });
     */
});

$(document).ready(function () {
    var saved = null;
    var cetak = null;
    $("#btn_cari").click(function () {
        $("#btn_simpan,#btn_cetak, #btn_cetak2, #btn_cetak5, #btn_cetak3, #btn_cetak4").attr('disabled', 'disabled');

        var blok = $("#prefix").val() + $("#blok").val();
        // var blok2 = $("#blok2").val();
        // var thn = $("#tahun").val();
        if (blok) {
            saved = null;
            cetak = null;
            $("#btn_simpan").removeAttr('disabled');
            oTable.fnReloadAjax("<?=active_module_url();?>range_thn/cari/" + blok);
        } else {
            alert('Harap mengisi NOP dengan benar!');
        }
    });

    // $('#myform').submit(function () {
    // });

    $('#btn_simpan').click(function() {
        $.ajax({
            type: 'POST',
            url: "<?=active_module_url('range_thn/simpan')?>",
            data: $('#myform').serialize(),
            data: "data=" + encodeURIComponent(JSON.stringify(oTable.fnGetData())),
            async: false,
            beforeSend: function () {},
            success: function (msg) {
                data = JSON.parse(msg);
                if (data['simpan']!='gagal') {
                    saved = data['saved'];
                    cetak = data['cetak'];
                    $('#data').val(JSON.stringify(saved));
                    $("#btn_cetak,#btn_cetak6,#btn_cetak2,#btn_cetak5,#btn_cetak3,#btn_cetak4").removeAttr('disabled');
                    alert('Data telah disimpan.');
                } else
                    alert('Data gagal disimpan.');
            }
        });
        $(this).attr('disabled', 'disabled');
    });

    $('#btn_cetak').click(function() {
        ajax_download("<?echo active_module_url('range_thn/cetak');?>", {'dtCetak': JSON.stringify({ "dtCetak" : cetak })});
    });

    $('#btn_cetak4').click(function() {
          ajax_download("<?echo active_module_url('range_thn/cetak_draft');?>", {'dtCetak': JSON.stringify({ "dtCetak" : cetak })});
    });
/*
    $('#btn_cetak5').click(function() {
        $("#rptform").attr("action", "<?echo active_module_url('range_thn/cetak_bank');?>");
        $('#rptform').submit();
        $(this).attr('disabled', 'disabled');
    });

    $('#btn_cetak2').click(function() {
        $("#rptform").attr("action", "<?echo active_module_url('range_thn/cetak_pdf');?>");
        $('<input type="hidden" name="sttsno"/>').val("").appendTo('#rptform');
        $('#rptform').submit();
        $(this).attr('disabled', 'disabled');
    });

    $('#btn_cetak3').click(function() {
        $("#rptform").attr("action", "<?echo active_module_url('range_thn/cetak_pdf');?>");
        $('<input type="hidden" name="sttsno"/>').val("2").appendTo('#rptform');
        $('#rptform').submit();
        $(this).attr('disabled', 'disabled');
    });
*/
});

$(document).keypress(function(event){
    if (event.which == '13') {
        event.preventDefault();
    }
});
/*http://stackoverflow.com/questions/4545311/download-a-file-by-jquery-ajax*/
function ajax_download(url, data) {
    var $iframe,
        iframe_doc,
        iframe_html;
    if (($iframe = $('#download_iframe')).length === 0) {
        $iframe = $("<iframe id='download_iframe'" +
                    " style='display: none' src='about:blank'></iframe>"
                   ).appendTo("body");
    }

    iframe_doc = $iframe[0].contentWindow || $iframe[0].contentDocument;
    if (iframe_doc.document) {
        iframe_doc = iframe_doc.document;
    }

    iframe_html = "<html><head></head><body><form method='POST' action='" +
                  url +"'>"

    Object.keys(data).forEach(function(key){
        iframe_html += "<input type='hidden' name='"+key+"' value='"+data[key]+"'>";

    });

        iframe_html +="</form></body></html>";

    iframe_doc.open();
    iframe_doc.write(iframe_html);
    $(iframe_doc).find('form').submit();
}
</script>

<!-- form untuk report pdf //  , 'target'=>'_blank')  -->
<?php echo form_open("", array('id'=>'rptform', 'target'=>'_blank', 'style'=>'display:none')); ?>
<input type="hidden" id="data" name="data">
<?echo form_close();?>

<div class="content">
    <div class="container-fluid">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#transaksi"><strong>Cetak STTS per Range Tahun</strong></a></li>
        </ul>

        <?php
        if(validation_errors()){
            echo '<blockquote><strong>Harap melengkapi data berikut :</strong>';
            echo validation_errors('<small>','</small>');
            echo '</blockquote>';
        }
        ?>

        <?=msg_block();?>

        <div class="asd pull-left">
            <button type="button" class="btn btn-primary" id="btn_simpan" name="btn_simpan" disabled>Bayar</button>
            <button type="button" class="btn btn-success" id="btn_cetak"  name="btn_cetak"  >Cetak (Draft)</button>
            <!--button type="button" class="btn btn-success" id="btn_cetak2" name="btn_cetak2" >Cetak 1 (PDF)</button>
            <button type="button" class="btn btn-success" id="btn_cetak3" name="btn_cetak3" >Cetak 2 (PDF)</button-->
            <button type="button" class="btn btn-success" id="btn_cetak4" name="btn_cetak4" >Cetak Bank (Draft)</button>
            <!--button type="button" class="btn btn-success" id="btn_cetak5" name="btn_cetak5" >Cetak Bank</button-->
        </div>

        <div class="asdx">
            <?php echo form_open($faction, array('id'=>'myform', 'style'=>'margin: 0px;'));?>
                <span class="staticfont">NOP </span>
                <input class="span1" type="text" id="prefix" name="prefix" value="<?=$prefix;?>" readonly>
                <input class="span2" type="text" id="blok" name="blok">
                <!--input class="span2" type="text" id="blok" name="blok">s.d.
                <input class="span1" type="text" id="blok2" name="blok2">

                <span class="staticfont">Tahun</span>
                <input class="span1" type="text" id="tahun" name="tahun" /-->

                <span class="staticfont">&nbsp;</span>
                <button type="button" class="btn btn-info" id="btn_cari" name="btn_cari">Cari</button>
            </form>
        </div>
        <hr />

        <table class="table" id="table1">
            <thead>
                <tr>
                    <th>NOP</th>
                    <th>Tahun</th>
                    <th>Pokok</th>
                    <th>Denda</th>
                    <th>Jumlah</th>
                    <th>Nama WP</th>
                    <th>Alamat WP</th>
                    <th>Batal</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th style="text-align: right">Total:</th>
                    <th>&nbsp;</th>
                    <th style="text-align: right;">&nbsp;</th>
                    <th style="text-align: right;">&nbsp;</th>
                    <th style="text-align: right;">&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?  $this->load->view('_foot'); ?>