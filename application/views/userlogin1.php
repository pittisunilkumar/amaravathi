<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width,initial-scale=1" name="viewport">
	<meta name="keywords" content="">
	<meta name="description" content="Amaravathi">
	<meta name="author" content="Amaravathi">
	<title>Login</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>uploads/school_content/admin_small_logo/<?php echo $this->setting_model->getAdminsmalllogo();?>">
    
    <!-- Web Fonts  -->
	<link href="http://fonts.googleapis.com/css?family=Signika:300,400,600,700" rel="stylesheet"> 
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/bootstrap/css/bootstrap.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/font-awesome/css/all.min.css">
	<script src="<?php echo base_url(); ?>assets/vendor/jquery/jquery.js"></script>
	
	<!-- sweetalert js/css -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>/assets/vendor/sweetalert/sweetalert-custom.css">
	<script src="<?php echo base_url(); ?>assets/vendor/sweetalert/sweetalert.min.js"></script>
	<!-- login page style css -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/login_page/css/style.css">
	

    
</head>
	<body style="background: url('<?php echo base_url(); ?>uploads/school_content/login_image/<?php echo $school['user_login_page_background']; ?>') no-repeat; background-size:cover">
        <div class="auth-main">
            <div class="container">
                <div class="slideIn">
                    <!-- image and information -->
                    <div class="col-lg-4 col-lg-offset-1 col-md-4 col-md-offset-1 col-sm-12 col-xs-12 no-padding fitxt-center">
                        <div class="image-area">
                        <div class="content">
                            <div class="image-hader">
                                <h2>Welcome To</h2>
                            </div>
                            <div class="center img-hol-p">
                                <img src="<?php echo base_url(); ?>uploads/school_content/admin_logo/<?php echo $this->setting_model->getAdminlogo();?>" height="60" alt="School">
                            </div>
                            <div class="address">
                                <p></p>
                            </div>			
                            <div class="f-social-links center">
                                <a href="" target="_blank">
                                    <span class="fab fa-facebook-f"></span>
                                </a>
                                <a href="" target="_blank">
                                    <span class="fab fa-twitter"></span>
                                </a>
                                <a href="" target="_blank">
                                    <span class="fab fa-linkedin-in"></span>
                                </a>
                                <a href="" target="_blank">
                                    <span class="fab fa-youtube"></span>
                                </a>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Login -->
                    <div class="col-lg-6 col-lg-offset-right-1 col-md-6 col-md-offset-right-1 col-sm-12 col-xs-12 no-padding">
                        <div class="sign-area">
                            <div class="sign-hader">
                                <img src="<?php echo base_url(); ?>uploads/school_content/admin_logo/<?php echo $this->setting_model->getAdminlogo();?>" height="54" alt="">
                                <h2>Amaravathi</h2>
                            </div>
                            <form action="<?php echo site_url('site/userlogin') ?>" method="post" accept-charset="utf-8">
                                <?php echo $this->customlib->getCSRF(); ?>


                                <div class="form-group  <?php if (form_error('username')) echo 'has-error'; ?>">
                                    <div class="input-group input-group-icon">
                                        <span class="input-group-addon">
                                            <span class="icon">
                                                <i class="far fa-user"></i>
                                            </span>
                                        </span>
                                        <input type="text" name="username" placeholder="<?php echo $this->lang->line('username'); ?>" value="<?php echo set_value('username') ?>" class="form-username form-control" id="form-username">
                                    </div>
                                    <span class="error"><?php echo form_error('username'); ?></span>
                                </div>


                                <div class="form-group <?php if (form_error('password')) echo 'has-error'; ?>">
                                    <div class="input-group input-group-icon">
                                        <span class="input-group-addon">
                                            <span class="icon"><i class="fas fa-unlock-alt"></i></span>
                                        </span>
                                        <input type="password" value="<?php echo set_value('password') ?>" name="password" placeholder="<?php echo $this->lang->line('password'); ?>" class="form-password form-control" id="form-password">
                                    </div>
                                    <span class="error"><?php echo form_error('password'); ?></span>
                                </div>


                                <?php if($is_captcha){ ?>
                                    <div class="form-group has-feedback row"> 
                                        <div class='col-lg-7 col-md-12 col-sm-6'>
                                            <span id="captcha_image"><?php echo $captcha_image; ?></span>
                                            <span title='Refresh Catpcha' class="fa fa-refresh catpcha" onclick="refreshCaptcha()"></span>
                                        </div>
                                        <div class='col-lg-5 col-md-12 col-sm-6'>
                                            <input type="text" name="captcha" placeholder="<?php echo $this->lang->line('captcha'); ?>" class=" form-control" autocomplete="off" id="captcha"> 
                                            <span class="text-danger"><?php echo form_error('captcha'); ?></span>
                                        </div>
                                    </div>
                                <?php } ?>


                                <div class="forgot-text">
                                    <div class="checkbox-replace">
                                        <label class="i-checks"><input type="checkbox" name="remember" id="remember"><i></i> Remember</label>
                                    </div>
                                    <div class="">
                                        <a href="<?php echo site_url('site/ufpassword') ?>">Lose Your Password?</a>
                                    </div>
                                </div>

                                


                                <div class="form-group">
                                    <button type="submit" id="btn_submit" class="btn btn-block btn-round">
                                        <i class="fas fa-sign-in-alt"></i> Login</button>
                                </div>


                                


                            </form>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
        
		<script src="<?php echo base_url(); ?>assets/vendor/bootstrap/js/bootstrap.js"></script>
		<script src="<?php echo base_url(); ?>assets/vendor/jquery-placeholder/jquery-placeholder.js"></script>
		<!-- backstretch js -->
		<script src="<?php echo base_url(); ?>assets/login_page/js/jquery.backstretch.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/login_page/js/custom.js"></script>

        
        <?php

        $message="";

        if (isset($error_message)) {
            echo "<div class='alert alert-danger'>" . $error_message . "</div>";
            $message=$error_message;
            $alertclass = "error";
        }
        ?>

        <?php
        if ($this->session->flashdata('message')) {
            echo "<div class='alert alert-success'>" . $this->session->flashdata('message') . "</div>";
            $this->session->unset_userdata('message'); 
            $message=$this->session->flashdata('message');
        };
        ?>

        <?php
        if ($this->session->flashdata('disable_message')) {
            echo "<div class='alert alert-danger'>" . $this->session->flashdata('disable_message') . "</div>";
            $this->session->unset_userdata('disable_message'); 
            $message=$this->session->flashdata('disable_message');
        };
        ?>

        <?php

        if($message != ''):
			$alert_message = $this->session->flashdata('alert-message-'. $alertclass);
		?>
			<script type="text/javascript">
				swal({
					toast: true,
					position: 'top-end',
					type: '<?php echo $alertclass;?>',
					title: '<?php echo $message;?>',
					confirmButtonClass: 'btn btn-default',
					buttonsStyling: false,
					timer: 8000
				})
			</script>
		<?php endif; ?>


	</body>
</html>