<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>openSIPKD - Application</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link href="<?=base_url()?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />    
    <link href="<?=base_url()?>assets/datatables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?=base_url()?>assets/css/opensipkd.css" rel="stylesheet" type="text/css" />
    
    <link rel="shortcut icon" href="<?=base_url()?>assets/img/favicon.ico">
    
    <script src="<?=base_url()?>assets/jquery/jquery.min.js" type="text/javascript"></script>
    <script src="<?=base_url()?>assets/jquery/jquery-ui.min.js" type="text/javascript"></script>
    <script src="<?=base_url()?>assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?=base_url()?>assets/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="<?=base_url()?>assets/datatables/media/js/dataTables.bootstrap.min.js"></script>
    
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      //$.widget.bridge('uibutton', $.ui.button);
    </script>
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button class="navbar-toggle collapsed" aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" type="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?=base_url()?>home">openSIPKD</a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <!-- display if not loggged in -->
      <?$this->load->view('_navbar');?>
      <?if(is_login()=='') :?>
      <form class="navbar-form navbar-right" accept-charset="utf-8" method="post" action="<?=base_url()?>login">
        <div class="form-group">
          <input class="form-control" type="text" placeholder="Email" name="userid">
        </div>
        <div class="form-group">
          <input class="form-control" type="password" placeholder="Password" name="passwd">
        </div>
        <button class="btn btn-success" type="submit">Masuk</button>
      </form>
      <?endif;?>
      
      <!-- display if loggged in -->
      <?if(is_login()) :?>
      <div class="navbar-right">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!--img src="<?=base_url()?>assets/img/user.png" class="user-image" alt="User Image"/-->
                <span class="hidden-xs"><? echo $this->session->userdata('username');?></span>
            </a>
            
            <!-- User image -->
            <ul class="dropdown-menu">
              <li class="user-header">
                  <? echo $this->session->userdata('last_login');?>
              </li>

              <li class="user-footer">
                <div class="pull-left">
                  <a class="" href="<?echo base_url().'home/auth/changepwd/'.app_user_id();?>">Ubah Password</a>
                </div>
                <div class="pull-left">
                  <a class="btn btn-default btn-flat" href="<?echo base_url().'logout';?>">Logout</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <?endif;?>                
    </div>
  </div>
</nav>

<script>
    /*
    var timer;
    var wait=10;
    document.onkeypress=resetTimer;
    document.onmousemove=resetTimer;

    function resetTimer() {
    clearTimeout(timer);
    timer=setTimeout("logout()", 60000*wait);
    }

    */

    function logout() {
        window.location.href='<?=base_url()?>logout';
    }

    $(document).ready(function() {

        $('#app_id').change( function() {
            window.location = '<?=base_url();?>change_module/'+$('#app_id').val();
        });

        $('#msg_helper').delay(5000).fadeOut('slow');

        $('#modalform').on('hidden', function() {
            $(this).removeData('modal');
        });

    });
</script>
