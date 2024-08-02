<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php $app_name = $this->setting_model->get();echo $app_name[0]['name'];?></title>
  <!-- <link href="<?php echo base_url(); ?>uploads/school_content/admin_small_logo/<?php $this->setting_model->getAdminsmalllogo();?>" rel="shortcut icon" type="image/x-icon"> -->
  <link rel="icon" type="image/png" href="<?php echo $this->customlib->getBaseUrl(); ?>uploads/school_content/admin_small_logo/<?php echo $this->setting_model->getAdminsmalllogo();?>" sizes="16x16">
  <!-- remix icon font css  -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/remixicon.css">
  <!-- BootStrap css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/bootstrap.min.css">
  <!-- Text Editor css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/editor-katex.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/editor.atom-one-dark.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/editor.quill.snow.css">
  <!-- Popup css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/magnific-popup.css">
  <!-- Slick Slider css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/slick.css">
  <!-- main css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
</head>
  <body>

<section class="auth forgot-password-page bg-base d-flex flex-wrap">  
    <div class="auth-left d-lg-block d-none">
        <div class="d-flex align-items-center flex-column h-100 justify-content-center">
            <img src="<?php echo base_url(); ?>assets/images/auth/forgot-pass-img.png" alt="">
        </div>
    </div>


    
    <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
        <div class="max-w-464-px mx-auto w-100">
            <div>
            <?php
                if (isset($error_message)) {
                    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                }
            ?>

                <h4 class="mb-12">Password Reset</h4>
                <!-- <p class="mb-32 text-secondary-light text-lg">Enter the email address associated with your account and we will send you a link to reset your password.</p> -->
            </div> 
            <form action="<?php echo site_url('user/resetpassword/' . $role . '/' . $verification_code) ?>" method="post">
            <?php echo $this->customlib->getCSRF(); ?>
                <div class="icon-field">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                    </span>
                   <input type="password" name="password" placeholder="<?php echo $this->lang->line('password'); ?>" class="form-control h-56-px bg-neutral-50 radius-12" id="your-password">

                </div>
                <span class="text-danger"><?php echo form_error('email'); ?></span>

                <div class="icon-field">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                    </span>
                   <input type="password" name="confirm_password" placeholder="<?php echo $this->lang->line('confirm_password'); ?>" class="form-control h-56-px bg-neutral-50 radius-12" id="your-password">
                </div>
                <span class="text-danger"><?php echo form_error('email'); ?></span>
                <!-- <button type="button" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32" data-bs-toggle="modal" data-bs-target="#exampleModal">Continue</button> -->
                <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">Continue</button>

                <div class="text-center">
                    <a href="<?php echo site_url('site/userlogin') ?>" class="text-primary-600 fw-bold mt-24">Back to Sign In</a>
                </div>
                
                <div class="mt-120 text-center text-sm">
                    <p class="mb-0">Already have an account? <a href="<?php echo site_url('site/userlogin') ?>" class="text-primary-600 fw-semibold">Sign In</a></p>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
        <div class="modal-body p-40 text-center">
            <div class="mb-32">
                <img src="<?php echo base_url(); ?>assets/images/auth/envelop-icon.png" alt="">
            </div>
            <h6 class="mb-12">Verify your Email</h6>
            <p class="text-secondary-light text-sm mb-0">Thank you, check your email for instructions to reset your password</p>
            <button type="button" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">Skip</button>
            <div class="mt-32 text-sm">
                <p class="mb-0">Don’t receive an email? <a href="resend.html" class="text-primary-600 fw-semibold">Resend</a></p>
            </div>
        </div>
        </div>
    </div>
</div>

  <!-- jQuery library js -->
  <script src="<?php echo base_url(); ?>assets/js/lib/jquery-3.7.1.min.js"></script>
  <!-- Bootstrap js -->
  <script src="<?php echo base_url(); ?>assets/js/lib/bootstrap.bundle.min.js"></script>
  <!-- Apex Chart js -->
  <script src="<?php echo base_url(); ?>assets/js/lib/apexcharts.min.js"></script>
  <!-- Data Table js -->
  <script src="<?php echo base_url(); ?>assets/js/lib/dataTables.min.js"></script>
  <!-- Iconify Font js -->
  <script src="<?php echo base_url(); ?>assets/js/lib/iconify-icon.min.js"></script>
  <!-- jQuery UI js -->
  <script src="<?php echo base_url(); ?>assets/js/lib/jquery-ui.min.js"></script>
  <!-- Vector Map js -->
  <script src="<?php echo base_url(); ?>assets/js/lib/jquery-jvectormap-2.0.5.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/js/lib/jquery-jvectormap-world-mill-en.js"></script>
  <!-- Popup js -->
  <script src="<?php echo base_url(); ?>assets/js/lib/magnifc-popup.min.js"></script>
  <!-- Slick Slider js -->
  <script src="<?php echo base_url(); ?>assets/js/lib/slick.min.js"></script>
  <!-- main js -->
  <script src="<?php echo base_url(); ?>assets/js/app.js"></script>
 
</body>

<!-- Mirrored from wowdash.wowtheme7.com/demo/forgot-password.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 31 Jul 2024 08:26:44 GMT -->
</html>
