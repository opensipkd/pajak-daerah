<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<script>

$.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback, bStandingRedraw )
{
    if ( typeof sNewSource != 'undefined' && sNewSource != null ) {
        oSettings.sAjaxSource = sNewSource;
    }

    /* Server-side processing should just call fnDraw */
    if ( oSettings.oFeatures.bServerSide ) {
        this.fnDraw();
        return;
    }

    this.oApi._fnProcessingDisplay( oSettings, true );
    var that = this;
    var iStart = oSettings._iDisplayStart;
    var aData = [];

    this.oApi._fnServerParams( oSettings, aData );

    oSettings.fnServerData.call( oSettings.oInstance, oSettings.sAjaxSource, aData, function(json) {
        /* Clear the old information from the table */
        that.oApi._fnClearTable( oSettings );

        /* Got the data - add it to the table */
        var aData =  (oSettings.sAjaxDataProp !== "") ?
            that.oApi._fnGetObjectDataFn( oSettings.sAjaxDataProp )( json ) : json;

        for ( var i=0 ; i<aData.length ; i++ )
        {
            that.oApi._fnAddData( oSettings, aData[i] );
        }

        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();

        if ( typeof bStandingRedraw != 'undefined' && bStandingRedraw === true )
        {
            oSettings._iDisplayStart = iStart;
            that.fnDraw( false );
        }
        else
        {
            that.fnDraw();
        }

        that.oApi._fnProcessingDisplay( oSettings, false );

        /* Callback user function - for event handlers etc */
        if ( typeof fnCallback == 'function' && fnCallback != null )
        {
            fnCallback( oSettings );
        }
    }, oSettings );
};

var dID;
var oTable;
var xRow;
var xRow2;
var asInitVals = new Array();

$(document).ready(function() {
    oTable = $('#table1').dataTable({
        "iDisplayLength": 8,
        "bJQueryUI" : true,
        "sScrollY": "325px",
        "bScrollCollapse": false,
        "bPaginate": false,
        "sPaginationType": "full_numbers",
        "bFilter": false,
        "bLengthChange": false,
        // "sDom": '<"top">rt<"bottom"ilp><"clear">',
        "sDom":'<"toolbar">fT<"clear">lrtip<"clear">',
        "aoColumnDefs": [
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 0 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 1 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 2 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 3 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 4 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 5 ] },
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 6 ] }
        ],
        "aoColumns": [
            { "sWidth": "15%" },
            { "sWidth": "10%" },
            { "sWidth": "10%" , "sClass": "right"},
            { "sWidth": "10%" , "sClass": "right"},
            { "sWidth": "10%" , "sClass": "right"},
            { "sWidth": "20%"},
            { "sWidth": "25%" }
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
        "sAjaxSource": "<?=active_module_url();?>blok/cari/"
        /*"fnFooterCallback": function( nFoot, aData, iStart, iEnd, aiDisplay ) {
            nFoot.getElementsByTagName('th')[2].innerHTML = aData[10];
            nFoot.getElementsByTagName('th')[3].innerHTML = 10;
            nFoot.getElementsByTagName('th')[4].innerHTML = 15;
        }*/
    });

    $("div.toolbar").append($('.asd'));

    $("thead input").keypress(function (event) {
        if (event.which == 13) {
            oTable.fnFilter(this.value, $("thead input").index(this));
        }
    });

    /*
     * Support functions to provide a little bit of 'user friendlyness' to the textboxes in
     * the footer
     */
    $("thead input").each(function (i) {
        asInitVals[i] = this.value;
    });

    $("thead input").focus(function () {
        if (this.className == "search_init") {
            this.className = "";
            this.value = "";
        }
    });


    $("thead input").blur(function (i) {
        if (this.value == "") {
            this.className = "search_init";
            this.value = asInitVals[$("thead input").index(this)];
        }
    });
});

$(document).ready(function () {
    var saved = null;
    var cetak = null;
    $("#btn_cari").click(function () {
        $("#btn_simpan,#btn_cetak, #btn_cetak2, #btn_cetak5,#btn_cetak3,#btn_cetak4").attr('disabled', 'disabled');

        var blok = $("#prefix").val() + $("#blok").val();
        var thn = $("#tahun").val();
        if (blok && thn) {
            saved = null;
            cetak = null;
            $("#btn_simpan").removeAttr('disabled');
            oTable.fnReloadAjax("<?=active_module_url();?>blok/cari/" + blok + '/' + thn);
        } else {
            alert('Harap mengisi Blok dan Tahun dengan benar!');
        }
    });

    $('#myform').submit(function () {
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            async: false,
            beforeSend: function () {},
            success: function (msg) {
                data = JSON.parse(msg);
                if (data['simpad']!='gagal') {
                    saved = data['saved'];
                    cetak = data['cetak'];
                    $('#data').val(JSON.stringify(saved));
                    alert('Data telah disimpan.');
                } else
                    alert('Data gagal disimpan.');
            }
        });
        return false;
    });

    $('#btn_simpan').click(function() {
        $('#myform').submit();
        $("#btn_cetak,#btn_cetak2,#btn_cetak5,#btn_cetak3,#btn_cetak4").removeAttr('disabled');
        $(this).attr('disabled', 'disabled');
    });

    $('#btn_cetak').click(function() {
          ajax_download("<?echo active_module_url('blok/cetak');?>", {'dtCetak': JSON.stringify({ "dtCetak" : cetak })});
    });

    $('#btn_cetak4').click(function() {
          ajax_download("<?echo active_module_url('blok/cetak_draft');?>", {'dtCetak': JSON.stringify({ "dtCetak" : cetak })});
    });
/*
    $('#btn_cetak5').click(function() {
        $("#rptform").attr("action", "<?echo active_module_url('blok/cetak_pdf');?>");
        $('#rptform').submit();
        $(this).attr('disabled', 'disabled');
    });

    $('#btn_cetak2').click(function() {
        $("#rptform").attr("action", "<?echo active_module_url('blok/cetak_pdf');?>");
        $('<input type="hidden" name="sttsno"/>').val("").appendTo('#rptform');
        $('#rptform').submit();
        $(this).attr('disabled', 'disabled');
    });

    $('#btn_cetak3').click(function() {
        $("#rptform").attr("action", "<?echo active_module_url('blok/cetak_pdf');?>");
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
            <li class="active"><a data-toggle="tab" href="#transaksi"><strong>Cetak STTS per Blok</strong></a></li>
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
                <span class="staticfont">Blok Objek Pajak</span>
                    <input class="span1" type="text" id="prefix" name="prefix" value="<?=$prefix;?>" readonly>
                    <input class="span2" type="text" id="blok" name="blok">

                <span class="staticfont">Tahun</span>
                <input class="span1" type="text" id="tahun" name="tahun" />

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
                </tr>
            </thead>
            <tbody>
            </tbody>
            <!--tfoot>
                <tr>
                    <th>TOTAL</th>
                    <th>&nbsp;</th>
                    <th>0</th>
                    <th>0</th>
                    <th>0</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot-->

        </table>
    </div>
</div>
<?  $this->load->view('_foot'); ?>