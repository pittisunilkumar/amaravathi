<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">

<!-- Mirrored from wowdash.wowtheme7.com/demo/sign-in.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 31 Jul 2024 08:26:40 GMT -->
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $this->customlib->getAppName(); ?></title>
  <link rel="icon" type="image/png" href="<?php echo $this->customlib->getBaseUrl(); ?>uploads/school_content/admin_small_logo/<?php echo $this->setting_model->getAdminsmalllogo();?>" sizes="16x16">
  <!-- remix icon font css  -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/remixicon.css">
  <!-- BootStrap css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/bootstrap.min.css">
  <!-- Apex Chart css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/apexcharts.css">
  <!-- Data Table css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/dataTables.min.css">
  <!-- Text Editor css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/editor-katex.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/editor.atom-one-dark.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/editor.quill.snow.css">
  <!-- Date picker css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/flatpickr.min.css">
  <!-- Calendar css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/full-calendar.css">
  <!-- Vector Map css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/jquery-jvectormap-2.0.5.css">
  <!-- Popup css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/magnific-popup.css">
  <!-- Slick Slider css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/lib/slick.css">
  <!-- main css -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
</head>
  <body>

<section class="auth bg-base d-flex flex-wrap">  
    <div class="auth-left d-lg-block d-none">
        <div class="d-flex align-items-center flex-column h-100 justify-content-center">
            <img src="<?php echo base_url(); ?>assets/images/auth/auth-img.png" alt="">
        </div>
    </div>
    <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
        <div class="max-w-464-px mx-auto w-100">
            <div>
                <a href="index.html" class="mb-40 max-w-290-px">
                    <img src="<?php echo $this->customlib->getBaseUrl(); ?>uploads/school_content/admin_logo/<?php echo $this->setting_model->getAdminlogo() . img_time();?>" alt="<?php echo $this->customlib->getAppName() ?>">
                </a>
                <?php
                    if (isset($error_message)) {
                        echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                    }
                ?>
                <h6 class="mb-12"><?php $app_name = $this->setting_model->get();
echo $app_name[0]['name'];?></h6>
                <p class="mb-32 text-secondary-light text-lg">Sign In to your Account</p>
            </div>
            <form action="<?php echo site_url('site/userlogin') ?>" method="post" accept-charset="utf-8" >
            <?php echo $this->customlib->getCSRF(); ?>
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="mage:email"></iconify-icon>
                    </span>
                    <!-- <input type="text" name="username" placeholder="<?php echo $this->lang->line('username'); ?>" value="<?php echo set_value('username') ?>" class="form-username form-control" id="form-username"> -->

                    <input type="" name="username" value="<?php echo set_value('username') ?>" class="form-control h-56-px bg-neutral-50 radius-12" placeholder="<?php echo $this->lang->line('username'); ?>">
                </div>
                <span class="text-danger"><?php echo form_error('username'); ?></span>
                <div class="position-relative mb-20">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span> 
                        <!-- <input type="password" value="<?php echo set_value('password') ?>" name="password" placeholder="<?php echo $this->lang->line('password'); ?>" class="form-control h-56-px bg-neutral-50 radius-12" id="form-password"> -->

                        <input type="password" name="password" class="form-control h-56-px bg-neutral-50 radius-12" id="your-password" placeholder="<?php echo $this->lang->line('password'); ?>">
                    </div>
                    <span class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light" data-toggle="#your-password"></span>
                </div>
                <span class="text-danger"><?php echo form_error('password'); ?></span>
                <div class="">
                    <div class="d-flex justify-content-between gap-2">
                        <div class="form-check style-check d-flex align-items-center">
                            <input class="form-check-input border border-neutral-300" type="checkbox" value="" id="remeber">
                            <label class="form-check-label" for="remeber">Remember me </label>
                        </div>
                        <a href="<?php echo site_url('site/ufpassword') ?>" class="text-primary-600 fw-medium">Forgot Password?</a>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32"> Sign In</button>

                <!-- <div class="mt-32 center-border-horizontal text-center">
                    <span class="bg-base z-1 px-4">Or sign in with</span>
                </div>
                <div class="mt-32 d-flex align-items-center gap-3">
                    <button type="button" class="fw-semibold text-primary-light py-16 px-24 w-50 border radius-12 text-md d-flex align-items-center justify-content-center gap-12 line-height-1 bg-hover-primary-50"> 
                        <iconify-icon icon="ic:baseline-facebook" class="text-primary-600 text-xl line-height-1"></iconify-icon>
                        Google
                    </button>
                    <button type="button" class="fw-semibold text-primary-light py-16 px-24 w-50 border radius-12 text-md d-flex align-items-center justify-content-center gap-12 line-height-1 bg-hover-primary-50"> 
                        <iconify-icon icon="logos:google-icon" class="text-primary-600 text-xl line-height-1"></iconify-icon>
                        Google
                    </button>
                </div> -->
                <div class="mt-32 text-center text-sm">
                    <p class="mb-0">If You Have Staff account? <a href="<?php echo site_url('site/login') ?>" class="text-primary-600 fw-semibold">Staff account</a></p>
                </div>
                
            </form>
        </div>
    </div>
</section>

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

<script>
      // ================== Password Show Hide Js Start ==========
      function initializePasswordToggle(toggleSelector) {
        $(toggleSelector).on('click', function() {
            $(this).toggleClass("ri-eye-off-line");
            var input = $($(this).attr("data-toggle"));
            if (input.attr("type") === "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    }
    // Call the function
    initializePasswordToggle('.toggle-password');
  // ========================= Password Show Hide Js End ===========================
</script>

</body>

<!-- Mirrored from wowdash.wowtheme7.com/demo/sign-in.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 31 Jul 2024 08:26:41 GMT -->
</html>
