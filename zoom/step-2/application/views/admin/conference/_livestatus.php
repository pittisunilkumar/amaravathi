<?php
$url = "";
    $extracls="";
if ($live->purpose == "meeting") {
    $name = ($live->create_by_surname == "") ? $live->create_by_name : $live->create_by_name . " " . $live->create_by_surname;
} else {
    $name = ($live->create_for_surname == "") ? $live->create_for_name : $live->create_for_name . " " . $live->create_for_surname;
}
if ($api_Response->status == "waiting") {
    $st_label = "label label-warning";
} elseif ($api_Response->status == "started") {
    $st_label = "label label-success";
}
?>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="modal-header zoommodal-title">
            <h4 class="modal-title"><?php echo $live->title; ?></h4>
        </div>
    </div>
    <div class="col-lg-4 col-md-4">
        <label>
            <span class="labalblock"> <?php echo $this->lang->line('host'); ?></span> <span class="text-dark robo-normal"><?php echo $name; ?></span>
        </label>
    </div>

    <div class="col-lg-4 col-md-4">
        <label>
            <span class="labalblock"> <?php echo $this->lang->line('date'); ?></span> <span class="text-dark robo-normal"><?php echo $live->date; ?></span>
        </label>
    </div>

    <div class="col-lg-4 col-md-4">
        <label>
            <span class="labalblock"> <?php echo $this->lang->line('duration'); ?></span> <span class="text-dark robo-normal"><?php echo $live->duration; ?></span>
        </label>
    </div>

</div>
 
<?php
if ($live->status == 0) {
    if ($live->created_id == $logged_staff_id && ($live->purpose == "meeting")) {
        $label_display = $this->lang->line('start')." ".$this->lang->line('now');
        $label_type = 'label-success';
    }if ($live->created_id != $logged_staff_id && ($live->purpose == "meeting")) {
        $label_display = $this->lang->line('join')." ".$this->lang->line('now');
        $label_type = 'label-success';
    }if (($live->purpose == "class") && ($api_Response->status == "started")) {
        $label_display = $this->lang->line('join')." ".$this->lang->line('now');
        $label_type = 'label-success';
    }if (($live->purpose == "class") && ($api_Response->status == "waiting")) {
        $label_display = $this->lang->line('start')." ".$this->lang->line('now');
        $label_type = 'label-success';
    }
}

if ($api_Response->status == "waiting") {
    $target = "";
    if ($conference_setting->use_zoom_app) {
        $target = "_blank";
        if ($live->purpose == "class") {
            $url = $live_url->start_url;
          
        } elseif ($live->purpose == "meeting") {
            if ($live->created_id == $logged_staff_id) {
                $url = $live_url->start_url;
            } else {
              
                $url = $live_url->join_url;
            }
        }
    } else {

        if ($live->purpose == "class") {

            $url = site_url('admin/conference/join/class' . '/' . $live->id);
        } elseif ($live->purpose == "meeting") {

            $url = site_url('admin/conference/join/meeting' . '/' . $live->id);
        }
    }
}

if ($api_Response->status == "started") {
    $target = "";
    if ($conference_setting->use_zoom_app) {
        $target = "_blank";
        if ($live->purpose == "class") {
                $extracls="join-btn";
            $url = $live_url->start_url;
        } elseif ($live->purpose == "meeting") {
            if ($live->created_id == $logged_staff_id) {
                $url = $live_url->start_url;
            } else {
                    $extracls="join-btn";
                $url = $live_url->join_url;
            }
        }
    } else {

        if ($live->purpose == "class") {

            $url = site_url('admin/conference/join/class' . '/' . $live->id);
        } elseif ($live->purpose == "meeting") {

            $url = site_url('admin/conference/join/meeting' . '/' . $live->id);
        }
    }
}
?>


<?php
if ($api_Response->status == "waiting") {
    if (($live->created_id == $logged_staff_id) && $live->purpose == "meeting") {
        ?>
   <div class="zoommodal-border">
            <label class="pull-left minus7top">
                <span class="labalblock"><?php echo $this->lang->line('status'); ?></span><span class="font-w-normal <?php echo $st_label; ?>"><i class="fa fa-video-camera"></i> <?php echo $this->lang->line($api_Response->status); ?></span>
            </label>
        <a data-placement="left" href="<?php echo $url; ?>" class="btn btn-outline-success btn-sm pull-right" target="<?php echo $target; ?>">
            <i class="fa fa-video-camera"></i> <?php echo $label_display; ?>
        </a>
      </div>
        <?php
    } elseif (($live->created_id != $logged_staff_id) && $live->purpose == "meeting") {

        echo $this->lang->line('meeting_not_started');
    }

    if ($live->purpose == "class") {
        ?>
        <div class="zoommodal-border">
            <label class="pull-left minus7top">
                <span class="labalblock"><?php echo $this->lang->line('status'); ?></span><span class="font-w-normal <?php echo $st_label; ?>"><i class="fa fa-video-camera"></i> <?php echo $this->lang->line($api_Response->status); ?></span>
            </label>
            <a data-placement="left" href="<?php echo $url; ?>" class="btn btn-outline-success btn-sm pull-right" target="<?php echo $target; ?>">
                <i class="fa fa-video-camera"></i> <?php echo $label_display; ?>
            </a>
        </div>
        <?php
    }
}

if ($api_Response->status == "started") {

    if (($live->created_id == $logged_staff_id) && $live->purpose == "meeting") {

        ?>
      <div class="zoommodal-border">
            <label class="pull-left minus7top">
                <span class="labalblock"><?php echo $this->lang->line('status'); ?></span><span class="font-w-normal <?php echo $st_label; ?>"><i class="fa fa-video-camera"></i> <?php echo $this->lang->line($api_Response->status); ?></span>
            </label>
            <a data-placement="left" href="<?php echo $url; ?>" class="btn btn-outline-success btn-sm pull-right" target="<?php echo $target; ?>">
                <i class="fa fa-video-camera"></i> <?php echo $this->lang->line('join'); ?>
            </a>
        </div>
        <?php
        
    } elseif (($live->created_id != $logged_staff_id) && $live->purpose == "meeting") {
        ?>
      <div class="zoommodal-border">
            <label class="pull-left minus7top">
                <span class="labalblock"><?php echo $this->lang->line('status'); ?></span><span class="font-w-normal <?php echo $st_label; ?>"><i class="fa fa-video-camera"></i> <?php echo $this->lang->line($api_Response->status); ?></span>
            </label>
            <a data-placement="left" href="<?php echo $url; ?>" class="btn btn-outline-success btn-sm pull-right <?php echo $extracls; ?>" data-id="<?php echo $live->id;?>" target="<?php echo $target; ?>">
                <i class="fa fa-video-camera"></i> <?php echo $label_display; ?>
            </a>
        </div>
        <?php
    } else if ($live->purpose == "class") {
          ?>
        <div class="zoommodal-border">
            <label class="pull-left minus7top">
                <span class="labalblock"><?php echo $this->lang->line('status'); ?></span><span class="font-w-normal <?php echo $st_label; ?>"><i class="fa fa-video-camera"></i> <?php echo $this->lang->line($api_Response->status); ?></span>
            </label>
            <a data-placement="left" href="<?php echo $url; ?>" class="btn btn-outline-success btn-sm pull-right" target="<?php echo $target; ?>">
                <i class="fa fa-video-camera"></i> <?php echo $label_display; ?>
            </a>
        </div>
        <?php
    }
}
?>