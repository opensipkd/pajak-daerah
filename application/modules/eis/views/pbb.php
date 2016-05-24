<?$this->load->view('_head'); ?>

 <!-- $this->load->view('_navbar'); ?-->
<!--link href="<?=base_url()?>assets/css/dashboard.css" rel="stylesheet" type="text/css" /-->
<script src="<?=base_url()?>assets/chart/Chart.js" type="text/javascript"></script>
      
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button class="navbar-toggle collapsed" aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" type="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">EIS-PBB KOTA TANGERANG</a>
    </div>
    <!--div id="navbar" class="navbar-collapse collapse">
      <form class="navbar-form navbar-right">
        <div class="form-group">
          <input class="form-control" type="text" placeholder="Email">
        </div>
        <div class="form-group">
          <input class="form-control" type="password" placeholder="Password">
        </div>
        <button class="btn btn-success" type="submit">Sign in</button>
      </form>
    </div-->
  </div>
</nav>


<div class="container-fluid">
  <h2 class="sub-header">&nbsp;&nbsp;Realisasi PBB Tahun <?=$tahun?></h2>
 
  
  
  <div class="container-fluid">
     <div class="row">
      <div class="form-group">
        <label class="col-md-1 control-label">Kecamatan</label>
        <div class="col-md-3 inputGroupContainer">
            <div class="input-group">
                 <select id="kecamatan" name="kecamatan" class="form-control"> 
                  <option value="000">Semua Kecamatan</option>
                </select> 

            </div>
        </div>
      </div>
     </div>
     <div class="row">
      
      <div class="form-group">
        <label class="col-md-1 control-label">Kelurahan</label>
        <div class="col-md-3 inputGroupContainer">
            <div class="input-group">
                 <select id="kelurahan" name="kelurahan" class="form-control"> 
                  <option value="000000">Semua Kelurahan</option>
                </select> 
            </div>
        </div>
      </div>    
    </div>   
    <div class="row">
    <div class="col-md-6">
            <div class="panel panel-warning">
              <div class="panel-heading">
                  <h3 class="panel-title">Penerimaan</h3>
              </div>
              <div class="panel-body"> 
                <div class="row">
                <div class="col-md-5">
                  <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title">Transaksi</h3>
                    </div>
                    <div class="panel-body"> 
                      <ul class="list-group">
                        <li class="list-group-item list-group-item-success">
                          <h2 align="right" id="cnt_today">Calculated..</h2>
                        </li>
                        <li class="list-group-item list-group-item-info">
                          <h2 align="right" id="cnt_week">Calculated..</h2>
                        </li>
                        <li class="list-group-item list-group-item-warning">
                          <h2 align="right" id="cnt_month">Calculated..</h2>
                        </li>
                        <li class="list-group-item list-group-item-danger">
                          <h2 align="right" id="cnt_year">Calculated..</h2>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-7">
                  <div class="panel panel-primary">
                    <div class="panel-heading">
                      <h3 class="panel-title">Nominal</h3>
                    </div>
                    <div class="panel-body"> 
                      <ul class="list-group">
                        <li class="list-group-item list-group-item-success">
                          <h2 align="right" id="sum_today">Calculated..</h2>
                        </li>
                        <li class="list-group-item list-group-item-info">
                          <h2 align="right" id="sum_week">Calculated..</h2>
                        </li>
                        <li class="list-group-item list-group-item-warning">
                          <h2 align="right" id="sum_month">Calculated..</h2>
                        </li>
                        <li class="list-group-item list-group-item-danger">
                          <h2 align="right" id="sum_year">Calculated..</h2>
                        </li>
                      </ul>
                    </div>
                  </div>          
                </div>
                </div>
                <div class="row">
                  <center>
                  <span class="label label-success">Hari Ini</span>
                  <span class="label label-info">Minggu Ini</span>
                  <span class="label label-warning">Bulan Ini</span>
                  <span class="label label-danger">Tahun Ini</span>
                  </center>
                </div>
                
              </div>
            
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="panel panel-success">
                  <div class="panel-heading">
                      <h3 class="panel-title">Realisasi per Buku</h3>
                  </div>
                  <div class="panel-body"> 
                    <div class="col-md-9" id="myBookCont">
                      <center>
                          <canvas id="myBook"></canvas>
                      </center>
                    </div>
                    <div class="col-md-3">
                      <h5>Keterangan</h5>
                      <div id="legend"></div>                   
                  </div>
                </div>
              </div>  
            </div>
          </div>
          
          
          </div>
          <div class="col-md-6">
            <div class="row">
              <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">Realisasi per Bulan <?=$num_ext?></h3>
                </div>
                <div class="panel-body"> 
                  <div class="row">
                      <div class="row">
                      <div class="col-md-1">
                      </div>
                      <div class="col-md-11" id="myMonthCont">
                          <center>
                              <canvas id="myMonth"></canvas>
                          </center>
                      </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Realisasi per Wilayah  <?=$num_ext?></h3>
                </div>
                <div class="panel-body"> 
                  <div class="row">
                      <div class="row">
                      <div class="col-md-1">
                      </div>
                      <div class="col-md-11" id="myKecCont">
                          <center>
                              <canvas id="myKec"></canvas>
                          </center>
                      </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>

</div>

<style>
.toolbar {
    float:left;
    text-align: right;
}
.alignRight { text-align: right; }	
div.container-table {
        width: 90%;
        margin-right: auto; margin-left: auto; 
    }
</style>

<script>
function addCommas(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

var const_devider = <?=$devider?>;
    
var options = {
    animationSteps : 100,
    animationEasing : "easeOutBounce",
    animateRotate : true,
    animateScale : false,
    responsive: true,
    
    maintainAspectRatio: true,
    tooltipTemplate: "<%= datasetLabel %> - <%= addCommas(value) %>",
    scaleLabel : "<%= addCommas(value/const_devider)%>",
};

  $(function(){
      $.getJSON({
          type: "GET",
          url: '<?=base_url("/api/wspbbref/kecamatan")?>',
        }).done(function (html) {
            if(html.status) {
              var txtselect='<option value="000">Semua Kecamatan</option>';
              $.each(html.data, function(i, val) 
              {
                txtselect += '<option value="'+val.kd_kecamatan+'">'+val.nm_kecamatan+'</option>';
              });
              document.getElementById("kecamatan").innerHTML = txtselect;
              var txtselect='<option value="000000">Semua Kelurahan</option>';
              document.getElementById("kelurahan").innerHTML = txtselect;
              $('#kecamatan').val('000');
            }
          }                                
      );
      $().datarefresh();
  });

$( "#kecamatan" ).change(function() {
  var kd_kec = $('#kecamatan').val();
  if (kd_kec=='') kd_kec='000';
  var txtselect='<option value="'+kd_kec+'000">Semua Kelurahan</option>';
  document.getElementById("kelurahan").innerHTML = txtselect;
  $.getJSON({
        type: "GET",
        url: '<?=base_url("/api/wspbbref/kelurahan?kecamatan=")?>'+kd_kec,
      }).done(function (html) {
          
          if(html.status==true) {
            $.each(html.data, function(i, val) 
            {
              txtselect += '<option value="'+val.kd_kelurahan+'">'+val.nm_kelurahan+'</option>';
            });
          }
          document.getElementById("kelurahan").innerHTML = txtselect;
        }                                
    );
    $('#kelurahan').val(kd_kec+'000');
    $().datarefresh();
}); 

$( "#kelurahan" ).change(function() {
  var kd_kel = $('#kelurahan').val();
    $().datarefresh();
}); 
                      
////////////////////////////////////////////////////////////////////////////////////
//TABEL REALISASI
///////////////////////////////////////////////////////////////////////////////////
//generate dara realisasi per kecamatan
$.fn.realisasi = function(){
    var l_url = '<?=base_url("/eis/pbb/realisasi?awal=$awal&akhir=$akhir&aku=$aku&buku=0")?>';
    if ($('#kelurahan').val()=='000000') group='all';
    else if ($('#kelurahan').val().substr(3,3)=='000') group='kec';
    else group='kel';
    l_url += '&group='+group+'&kode='+$('#kelurahan').val();
    
    $.getJSON({
        type: "GET",
        url: l_url,
      }).done(function (html) {
          if(html) {
              document.getElementById("cnt_today").innerHTML = html.cnt_today;
              document.getElementById("cnt_week").innerHTML =  html.cnt_week;
              document.getElementById("cnt_month").innerHTML = html.cnt_month;
              document.getElementById("cnt_year").innerHTML =  html.cnt_year;
                                                                   
              document.getElementById("sum_today").innerHTML = html.sum_today;
              document.getElementById("sum_week").innerHTML =  html.sum_week;
              document.getElementById("sum_month").innerHTML = html.sum_month;
              document.getElementById("sum_year").innerHTML =  html.sum_year;
          }
        }                                
    );
};

////////////////////////////////////////////////////////////////////////////////////
//BAR PER BULAN
///////////////////////////////////////////////////////////////////////////////////
//generate dara realisasi per kecamatan
$.fn.barbulan = function(){
      document.getElementById("myMonthCont").innerHTML = '&nbsp;';
      document.getElementById("myMonthCont").innerHTML = '<center><canvas id="myMonth"></canvas></center>';
    var ctxMonth = $("#myMonth").get(0).getContext("2d");
    var l_url = '<?=base_url("/eis/pbb/rmonth?awal=$awal&akhir=$akhir&aku=$aku&buku=0")?>';
    
    if ($('#kelurahan').val()=='000000') group='all';
    else if ($('#kelurahan').val().substr(3,3)=='000') group='kec';
    else group='kel';
    l_url += '&group='+group+'&kode='+$('#kelurahan').val();
    $.getJSON({
        type: "GET",
        url: l_url,
      }).done(function (html) {
          if(html) {
            var barDataMonth=[];
            var barLabelMonth=[];
            $.each(html, function(i, val) 
            {
              barDataMonth.push(val.pokok);
              barLabelMonth.push(val.bulan);
              
            });
            
            var barMonth = {
                labels: barLabelMonth,
                datasets: [
                    {
                        label: "Realisasi PBB",
                        backgroundColor: "rgba(255,99,132,0.2)",
                        borderColor: "rgba(255,99,132,1)",
                        borderWidth: 1,
                        hoverBackgroundColor: "rgba(255,99,132,0.4)",
                        hoverBorderColor: "rgba(255,99,132,1)",
                        data: barDataMonth,
                    }
                ]
            };
            var myMonthChart = null;
            myMonthChart = new Chart(ctxMonth).Bar(barMonth,options);

          }
        }                                
    );
};

////////////////////////////////////////////////////////////////////////////////////
//BAR PER WILAYAH
///////////////////////////////////////////////////////////////////////////////////
$.fn.barwilayah = function(){    
      document.getElementById("myKecCont").innerHTML = '&nbsp;';
      document.getElementById("myKecCont").innerHTML = '<center><canvas id="myKec"></canvas></center>';
    var ctx = $("#myKec").get(0).getContext("2d");

      var l_url = '<?=base_url("/eis/pbb/rwil?awal=$awal&akhir=$akhir&aku=$aku&buku=0")?>';
      var l_group='kel';
      var l_kode=$('#kelurahan').val().substr(0,3);
      if ($('#kelurahan').val()=='000000') {
        l_group='kec';
        l_kode='00000';
      }
      console.log(l_group);
      console.log(l_kode);
      
      l_url += '&group='+l_group+'&kode='+l_kode;
      $.getJSON({
          type: "GET",
          url: l_url,
      }).done(function (html) {
          if(html) {
            var barData=[];
            var barLabel=[];
            $.each(html, function(i, val) 
            {
              barData.push(val.pokok);
              if (l_group=='kec')  barLabel.push(val.nm_kecamatan);
              else   barLabel.push(val.nm_kelurahan);
              
            });
            
            var barKec = {
                labels: barLabel,
                datasets: [
                    {
                        label: "Realisasi PBB",
                        backgroundColor: "rgba(255,99,132,0.2)",
                        borderColor: "rgba(255,99,132,1)",
                        borderWidth: 1,
                        hoverBackgroundColor: "rgba(255,99,132,0.4)",
                        hoverBorderColor: "rgba(255,99,132,1)",
                        data: barData,
                    }
                ]
            };
            var myKecChart=null ;
            myKecChart = new Chart(ctx).Bar(barKec,options);

          }
        }                                
    );  
  
};

                         
////////////////////////////////////////////////////////////////////////////////////
//LINGKARAN
///////////////////////////////////////////////////////////////////////////////////
$.fn.piebuku = function(){
      document.getElementById("myBookCont").innerHTML = '&nbsp;';
      document.getElementById("myBookCont").innerHTML = '<center><canvas id="myBook"></canvas></center>';

            var ctx2 = $("#myBook").get(0).getContext("2d");
            var options2 = {
                animationSteps : 100,
                animationEasing : "easeOutBounce",
                animateRotate : true,
                animateScale : false,
                tooltipTemplate: "<%= label %> - <%= addCommas(value) %>",
                responsive: true,
            };
          
            var pieData=[];
            var npie = 0 ;
            var colors = ["#FF0000", "#00FF00", "#0000FF", "#FFFF00", "#00FFFF", "#FF8800", "#88FF00"];
            
              var l_url = '<?=base_url("/eis/pbb/rbook?awal=$awal&akhir=$akhir&aku=$aku&buku=1")?>';
              
              if ($('#kelurahan').val()=='000000') group='all';
              else if ($('#kelurahan').val().substr(3,3)=='000') group='kec';
              else group='kel';
              l_url += '&group='+group+'&kode='+$('#kelurahan').val();
              $.getJSON({
                  type: "GET",
                  url: l_url,
                  
              }).done(function (html) {
                  if(html) {
                    $.each(html, function(i, val) 
                    {
                      x = { 
                            "value": parseInt(val.pokok),
                            //"color": colors[npie], 
                            "label": val.uraian,
                            "labelColor" : '#000000',
                            "labelFontSize" : '8'};
                      npie = pieData.push(x);
                    });
                    
                    var myPieChart =null ;
                    myPieChart = new Chart(ctx2).Pie(pieData,options2);
                    document.getElementById('legend').innerHTML = myPieChart.generateLegend();
                  }
                }                                
            );
            
};
                         
$.fn.datarefresh = function(){
    $().realisasi();
    $().barbulan();
    $().barwilayah();
    $().piebuku();
};

//$().datarefresh();

</script>


<? $this->load->view('_foot'); ?>