<!DOCTYPE html>
<!-- Coding By CodingNepal - www.codingnepalweb.com -->
<html>
<head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width,initial-scale=1" name="viewport">
	<meta name="keywords" content="">
	<meta name="description" content="Amaravathi">
	<meta name="author" content="Amaravathi">
	<title>Amaravathi</title>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>uploads/school_content/admin_small_logo/<?php echo $this->setting_model->getAdminsmalllogo();?>">

    <link rel="stylesheet" href="<?php echo base_url(); ?>newlogin/css/style.css">
</head>
<!-- <body style="background: url('<?php echo base_url(); ?>uploads/school_content/login_image/<?php echo $school['admin_login_page_background']; ?>') no-repeat; background-size:cover"> -->
<body style="background: url('<?php echo base_url(); ?>newlogin/images/hero-bg.jpg') no-repeat; background-size:cover">
    <div class="wrapper">
    <div class="forgot-header">
        <h4><i class="fas fa-fingerprint"></i> Password Restoration</h4>
        Enter your email and you will receive reset instructions
    </div>
    <form action="<?php echo site_url('site/forgotpassword') ?>" method="post" accept-charset="utf-8">
      <h2><?php echo $this->lang->line('password_reset');?></h2>
      <div class="input-field">
        <!-- <input type="text" name="username" value="<?php echo set_value('username') ?>" > -->

        <input type="text" name="email" required>
        <label><?php echo $this->lang->line('email'); ?></label>
      </div><br/>
      <!-- <div class="input-field">
        <input type="password" name="password" required>
        <label><?php echo $this->lang->line('password'); ?></label>
      </div> -->

      <!-- <div class="forget">
        <label for="remember">
          <input type="checkbox" name="remember" id="remember">
          <p><?php echo $this->lang->line('remember_me');?></p>
        </label>
        <a href="<?php echo site_url('site/forgotpassword') ?>"><?php echo $this->lang->line('forgot_password');?>?</a>
      </div> -->

      <button type="submit"><?php echo $this->lang->line('send');?></button>
      <!-- <div class="register">
        <p>Don't have an account? <a href="#">Register</a></p>
      </div> -->
        <br/>
        <div class="text-center">
            <a href="<?php echo site_url('site/login') ?>"><i class="fas fa-long-arrow-alt-left"></i> Back To Login</a>
        </div>
    </form>
  </div>
  <?php
    include base_url() . "security/config.php";
    include base_url() . "security/project-security.php";
?>
</body>
</html>