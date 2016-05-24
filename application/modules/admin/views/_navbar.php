<?if(is_login()) :?>

  <ul class="nav navbar-nav">
      <li <?echo $current=='beranda' ? 'class="active"' : '';?>><a class="brand" href="admin">ADMIN</a></li>
      <li class="dropdown <?echo $current=='pengaturan' ? 'active' : '';?>">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Pengaturan <strong class="caret"></strong></a>
          <ul class="dropdown-menu">
              <li><a href="<?=base_url('admin/apps');?>">Aplikasi</a></li>
          </ul>
      </li>
      <li class="dropdown <?echo $current=='pengaturan' ? 'active' : '';?>">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">User &amp; Privileges <strong class="caret"></strong></a>
          <ul class="dropdown-menu">
              <li><a href="<?=base_url('admin/users');?>">Users</a></li>
              <li><a href="<?=base_url('admin/groups');?>">Group Users</a></li>
              <li><a href="<?=base_url('admin/privileges');?>">Group Privileges</a></li>
          </ul>
      </li>
      
  </ul>

<?endif;?>

