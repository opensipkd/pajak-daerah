<? $this->load->view('_head'); ?>
<? $this->load->view(active_module().'/_navbar'); ?>

<style>
.progress {
    position:relative;
    width:400px;
    border: 1px solid #ddd;
    padding: 1px;
    border-radius: 3px;
}

.bar { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
.percent { position:absolute; display:inline-block; top:3px; left:48%; }
</style>

<script src="<?=base_url()?>assets/pad/js/bootstrap.file-input.js"></script>

<script>
var dID;
var oTable;
var xRow;
var xRow2;

$(document).ready(function() {
    var saved = null;
    var cetak = null;
    var bar = $('.bar');
    var percent = $('.percent');
    var status = $('#status');

    oTable = $('#table1').dataTable({
        "iDisplayLength": 8,
        "aaSorting": [[ 0, "desc" ]],
        "bJQueryUI" : true,
        "bProcessing": true,
        "sScrollY": "325px",
        "bScrollCollapse": false,
        "bPaginate": false,
        "sPaginationType": "full_numbers",
        "bFilter": false,
        "bLengthChange": false,
        "sDom":'<"toolbar">fT<"clear">lrtip<"clear">',
        "aoColumnDefs": [
            { "bSortable": false, "bSearchable": false, "bVisible": true, "aTargets": [ 0,1,2,3,4,5,6 ] },
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
    });

    $("div.toolbar").append($('.asd'));


    // ----
    $('#myform').ajaxForm({
        beforeSubmit: function() {
            if(!$('#userfile').val()) {
                alert('Silahkan pilih file yang akan diupload.');
                return false;
            }
        },
        beforeSend: function() {
            $("#btn_simpan,#btn_cetak,#btn_cetak6, #btn_cetak2, #btn_cetak5,#btn_cetak3,#btn_cetak4").attr('disabled', 'disabled');

            status.empty();
            var percentVal = '0%';
            bar.width(percentVal)
            percent.html(percentVal);

            oTable.fnClearTable();
        },
        uploadProgress: function(event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            bar.width(percentVal)
            percent.html(percentVal);
            console.log(percentVal, position, total);
        },
        success: function(response) {
            var percentVal = '100%';
            bar.width(percentVal)
            percent.html(percentVal);

            oTable.fnReloadAjax('<? echo base_url('assets/dokumen/dtsrc.xxx'); ?>', function() {
                alert(response);
            });
            $("#btn_simpan").removeAttr('disabled');
        },
        complete: function(xhr) {
            // alert(xhr.responseText);
        },
        error: function(xhr) {
            alert(xhr.responseText);
        },
    });

    $('#btn_simpan').click(function() {
        $.ajax({
            type: 'POST',
            url: "<?=active_module_url('upload_nop/simpan')?>",
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
          ajax_download("<?echo active_module_url('upload_nop/cetak');?>", {'dtCetak': JSON.stringify({ "dtCetak" : cetak })});
    });

    $('#btn_cetak4').click(function() {
          ajax_download("<?echo active_module_url('upload_nop/cetak_draft');?>", {'dtCetak': JSON.stringify({ "dtCetak" : cetak })});
    });
/*
    $('#btn_cetak6').click(function() {
        $.ajax({
            url: "<?=active_module_url('upload_nop/cetak_bank_text')?>",
            type: "POST",
            data: "data=" + encodeURIComponent(JSON.stringify(saved)),
            success: function (msg) {
                if(msg!='No Data') {
                    var rpt = window.open("", "Cetak");
                    if (!rpt)
                        alert('You have a popup blocker enabled. Please allow popups for this site.');
                    else {
                        $('#btn_cetak6').attr('disabled', 'disabled');
                        $(rpt.document.body).html(msg);
                    }
                } else alert(msg);
            }
        });
    });

    $('#btn_cetak5').click(function() {
        $("#rptform").attr("action", "<?echo active_module_url('upload_nop/cetak_bank');?>");
        $('#rptform').submit();
        $(this).attr('disabled', 'disabled');
    });

    $('#btn_cetak2').click(function() {
        $("#rptform").attr("action", "<?echo active_module_url('upload_nop/cetak_pdf');?>");
        $('<input type="hidden" name="sttsno"/>').val("").appendTo('#rptform');
        $('#rptform').submit();
        $(this).attr('disabled', 'disabled');
    });

    $('#btn_cetak3').click(function() {
        $("#rptform").attr("action", "<?echo active_module_url('upload_nop/cetak_pdf');?>");
        $('<input type="hidden" name="sttsno"/>').val("2").appendTo('#rptform');
        $('#rptform').submit();
        $(this).attr('disabled', 'disabled');
    });
    
    $('#btn_cetak4').click(function() {
        $("#rptform").attr("action", "<?echo active_module_url('upload_nop/cetak_pdf');?>");
        $('<input type="hidden" name="sttsno"/>').val("3").appendTo('#rptform');
        $('#rptform').submit();
        $(this).attr('disabled', 'disabled');
    });
*/

    $('input[type=file]').bootstrapFileInput();
});

$(document).keypress(function(event){
    if (event.which == '13') {
        event.preventDefault();
    }
});
$('#rptform').submit(function(){
    return false;
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
            <li class="active"><a data-toggle="tab" href="#transaksi"><strong>Cetak STTS by Upload NOP</strong></a></li>
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
            <?php echo form_open($faction, array('id'=>'myform', 'enctype'=>'multipart/form-data', 'style'=>'margin: 0px;'));?>
                <div class="row">
                    <div class="span4">
                        <span class="staticfont">File Sumber</span>
                        <!--input class="input" type="file" name="userfile[]" multiple /-->
                        <input class="input" type="file" id="userfile" name="userfile[]" />
                        <span class="staticfont">&nbsp;</span>
                        <button type="submit" class="btn btn-info" id="btn_upload" name="btn_upload">Upload</button>
                    </div>

                    <div class="progress span4" style="float:right">
                        <div class="bar"></div >
                        <div class="percent">0%</div >
                    </div>
                </div>
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

        </table>
    </div>
</div>
<?  $this->load->view('_foot'); ?>
