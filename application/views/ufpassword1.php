
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width,initial-scale=1" name="viewport">
	<meta name="keywords" content="">
	<meta name="description" content="Ramom School Management System">
	<meta name="author" content="RamomCoder">
	<title>Password Restoration</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>uploads/school_content/admin_small_logo/<?php echo $this->setting_model->getAdminsmalllogo();?>">

    <!-- Web Fonts  -->
	<link href="http://fonts.googleapis.com/css?family=Signika:300,400,600,700" rel="stylesheet"> 
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/bootstrap/css/bootstrap.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/font-awesome/css/all.min.css">
	<script src="<?php echo base_url(); ?>assets/vendor/jquery/jquery.js"></script>
	
	<!-- sweetalert js/css -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/sweetalert/sweetalert-custom.css">
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
                                <img src="<?php echo base_url(); ?>uploads/school_content/admin_logo/<?php echo $this->setting_model->getAdminlogo();?>" height="60" alt="RamomCoder School">
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
                            <div class="sign-hader pt-md">
                                <img src="<?php echo base_url(); ?>uploads/school_content/admin_logo/<?php echo $this->setting_model->getAdminlogo();?>" height="54" alt="" >
                                <h2>Amaravathi</h2>
                            </div>
                                                                <div class="forgot-header">
                                    <h4><i class="fas fa-fingerprint"></i> Password Restoration</h4>
                                    Enter your email and you will receive reset instructions
                                </div>
                                <form action="<?php echo site_url('site/ufpassword') ?>" method="post" accept-charset="utf-8">
                                <?php echo $this->customlib->getCSRF(); ?>
                                
                                
                                <div class="form-group <?php if (form_error('email')) echo 'has-error'; ?>">
                                    <div class="input-group input-group-icon">
                                        <span class="input-group-addon">
                                            <span class="icon">
                                                <i class="fas fa-unlock-alt"></i>
                                            </span>
                                        </span>
                                        <input type="text" name="username" placeholder="<?php echo $this->lang->line('email'); ?>" class="form-username form-control" id="form-username">
                                    </div>
                                    <span class="error"><?php echo form_error('username'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label class="radio-inline">
                                        <input  name="user[]" type="radio" value="student" <?php echo set_radio('user[]', 'student'); ?>>
                                        <?php echo $this->lang->line('student'); ?>
                                    </label>
                                    <label class="radio-inline">
                                        <input  name="user[]" type="radio" value="parent" <?php echo set_radio('user[]', 'parent'); ?>>
                                        <?php echo $this->lang->line('parent'); ?>
                                    </label>
                                    <span class="error"><?php echo form_error('user[]'); ?></span>
                                </div>


                                <div class="form-group">
                                    <button type="submit" id="btn_submit" class="btn btn-block ladda-button btn-round">
                                        <i class="far fa-paper-plane"></i> Forgot</button>
                                </div>
                                <div class="text-center">
                                    <a href="<?php echo site_url('site/userlogin') ?>"><i class="fas fa-long-arrow-alt-left"></i><?php echo $this->lang->line('user_login'); ?></a>
                                </div>
                                
                            </form>                        </div>
                    </div>
                </div>
            </div>
        </div>

		<script src="<?php echo base_url(); ?>assets/vendor/bootstrap/js/bootstrap.js"></script>
		<script src="<?php echo base_url(); ?>assets/vendor/jquery-placeholder/jquery-placeholder.js"></script>
		<!-- backstretch js -->
		<script src="<?php echo base_url(); ?>ssets/login_page/js/jquery.backstretch.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/login_page/js/custom.js"></script>
	</body>
</html>