
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                





                <?php
                    $side_list = side_menu_list(1);

                    if (!empty($side_list)) {
                        foreach ($side_list as $side_list_key => $side_list_value) {

                            $module_permission = access_permission_sidebar_remove_pipe($side_list_value->access_permissions);
                            $module_access     = false;
                            if (!empty($module_permission)) {
                                foreach ($module_permission as $m_permission_key => $m_permission_value) {
                                    $cat_permission = access_permission_remove_comma($m_permission_value);

                                    if ($this->rbac->hasPrivilege($cat_permission[0], $cat_permission[1])) {
                                        $module_access = true;
                                        break;
                                    }
                                }
                            }






                        if ($module_access) {
                            if ($this->module_lib->hasModule($side_list_value->short_code) && $this->module_lib->hasActive($side_list_value->short_code)) {

                ?>

                    <li class="submenu <?php echo activate_main_menu($side_list_value->activate_menu); ?>">

                        <!-- <a href="#">
                            <i class="<?php echo $side_list_value->icon; ?>"></i> 
                            <span><?php echo $this->lang->line($side_list_value->lang_key); ?></span>
                             <i class="fa fa-angle-left pull-right"></i>
                        </a> -->

                        <!-- <a href="#"><i class="fab fa-get-pocket"></i> <span><?php echo $this->lang->line($side_list_value->lang_key); ?></span> <span
                            class="menu-arrow"></span></a> -->

                        <a href="#"><i class="<?php echo $side_list_value->icon; ?>"></i> <span><?php echo $this->lang->line($side_list_value->lang_key); ?></span> <span
                            class="menu-arrow"></span></a>


                        <?php
                            if (!empty($side_list_value->submenus)) {
                        ?>
                        <ul>
                            <?php
                                foreach ($side_list_value->submenus as $submenu_key => $submenu_value) {

                                $sidebar_permission = access_permission_sidebar_remove_pipe($submenu_value->access_permissions);
                                $sidebar_access     = false;

                                if (!empty($sidebar_permission)) {
                                    foreach ($sidebar_permission as $sidebar_permission_key => $sidebar_permission_value) {
                                        $sidebar_cat_permission = access_permission_remove_comma($sidebar_permission_value);

                                        if ($submenu_value->addon_permission != "") {
                                            if ($this->rbac->hasPrivilege($sidebar_cat_permission[0], $sidebar_cat_permission[1])
                                                && $this->auth->addonchk($submenu_value->addon_permission, false)) {
                                                $sidebar_access = true;
                                                break;
                                            }
                                        } else {
                                            if ($this->rbac->hasPrivilege($sidebar_cat_permission[0], $sidebar_cat_permission[1])) {
                                                $sidebar_access = true;
                                                break;
                                            }
                                        }
                                    }
                                }

                            if ($sidebar_access) {
                                if (!empty($submenu_value->permission_group_id)) {
                                    if (!$this->module_lib->hasActive($submenu_value->short_code)) {
                                        continue;
                                    }
                                }

                            ?>
                            <li><a href="<?php echo site_url($submenu_value->url); ?>"><?php echo $this->lang->line($submenu_value->lang_key); ?></a></li>
                            <!-- <li class="<?php echo activate_submenu($submenu_value->activate_controller, explode(',', $submenu_value->activate_methods)); ?>"><a href="<?php echo site_url($submenu_value->url); ?>"><i class="fa fa-angle-double-right"></i><?php echo $this->lang->line($submenu_value->lang_key); ?></a></li> -->


                          <?php
                        }

                        }

                        ?>
                        </ul>

                            <?php

                }
                ?>
                                </li>
                            <?php
}
        }







                        }
                    }
                ?>


            </ul>
        </div>
    </div>
</div>
