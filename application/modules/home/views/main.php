<?$this->load->view('_head'); ?>
<div class="container-fluid">
<div class="jumbotron">
  <h1>openSIPKD Modules</h1>
    <ul>
      <?php foreach ($apps as $app): ?>
              <li><a class="btn btn-default" href="<?php echo site_url($app->app_path); ?>"><?php echo $app->nama;?></a></li>
      <?php endforeach; ?>
    <ul>
</div>

</div>
<? $this->load->view('_foot'); ?>