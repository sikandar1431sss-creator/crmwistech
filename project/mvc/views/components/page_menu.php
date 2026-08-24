        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img style="display:block" src="<?=base_url("uploads/images/".$this->session->userdata('photo'));
                                ?>" class="img-circle" alt="" />
                        </div>

                        <div class="pull-left info">
                            <?php
                                $name = $this->session->userdata("name");
                                if(strlen($name) > 11) {
                                   $name = substr($name, 0,11). "..";
                                }
                                echo "<p>".$name."</p>";
                            ?>
                            <a href="<?=base_url("profile/index")?>">
                                <i class="fa fa-hand-o-right color-green"></i>
                                <?=$this->lang->line($this->session->userdata("usertype"))?>
                            </a>
                        </div>
                    </div>

                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <?php $usertype = $this->session->userdata("usertype"); ?>
                    <ul class="sidebar-menu">
                     
                         

                       <?php
                            if($usertype == "Admin"  || $usertype == "Visitor") {
                                echo '<li>';
                                    echo anchor('parentes/index', '<i class="fa fa-user"></i><span>'.$this->lang->line('menu_parent').'</span>');
                                echo '</li>';
                            }
                        ?>

                      <?php /*?>  <?php
                            if($usertype == "Admin") {
                                echo '<li>';
                                    echo anchor('classes/index', '<i class="fa fa-sitemap"></i><span>'.$this->lang->line('menu_classes').'</span>');
                                echo '</li>';
                            }
                        ?>
<?php */?>
                      
   
 

                        <?php if($usertype == "Admin") { ?>
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa icon-account"></i>
                                    <span><?=$this->lang->line('menu_account');?></span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li>
                                        <?php echo anchor('feetype/index', '<i class="fa icon-feetype"></i><span>'.$this->lang->line('menu_feetype').'</span>'); ?>
                                    </li>
                                    <li>
                                        <?php echo anchor('invoice/index', '<i class="fa icon-invoice"></i><span>'.$this->lang->line('menu_invoice').'</span>'); ?>
                                    </li>
                                  
                                   
                                </ul>
                            </li>
                        <?php } ?>

                        <?php if($usertype == "Accountant") { ?>
                            <li>
                                <?php echo anchor('feetype/index', '<i class="fa icon-feetype"></i><span>'.$this->lang->line('menu_feetype').'</span>'); ?>
                            </li>
                            <li>
                                <?php echo anchor('invoice/index', '<i class="fa icon-invoice"></i><span>'.$this->lang->line('menu_invoice').'</span>'); ?>
                            </li>
                            <li>
                                <?php echo anchor('balance/index', '<i class="fa icon-payment"></i><span>'.$this->lang->line('menu_balance').'</span>'); ?>
                            </li>
                            <li>
                                <?php echo anchor('expense/index', '<i class="fa icon-expense"></i><span>'.$this->lang->line('menu_expense').'</span>'); ?>
                            </li>
                        <?php } ?>

                        <?php if($usertype == "Student" || $usertype == "Parent") { ?>
                            <li>
                                <?php echo anchor('invoice/index', '<i class="fa icon-invoice"></i><span>'.$this->lang->line('menu_invoice').'</span>'); ?>
                            </li>
                        <?php } ?>

                    

                      
                      

                        <?php
                            if($usertype && $usertype != 'Receptionist') {
                                echo '<li>';
                                    echo anchor('media/index', '<i class="fa fa-film"></i><span>'.$this->lang->line('menu_media').'</span>');
                                echo '</li>';
                            }
                        ?>
 
 


                        
                        
                        <?php
                            if($this->session->userdata("usertype") == "Admin" || $this->session->userdata("usertype") == "Receptionist") {
                                echo '<li>';
                                    echo anchor('visitorinfo/index', '<i class="fa icon-visitorinfo"></i><span>'.$this->lang->line('menu_visitorinfo').'</span>');
                                echo '</li>';
                            }
                        ?>

                       

                        <?php
                            if($this->session->userdata("usertype") == "Admin") {
                                echo '<li>';
                                    echo anchor('reset_password/index', '<i class="fa icon-reset_password"></i><span>'.$this->lang->line('menu_reset_password').'</span>');
                                echo '</li>';
                            }
                        ?>

                     <?php /*?>   <?php
                            if($this->session->userdata("usertype") == "Admin") {
                                echo '<li>';
                                    echo anchor('systemadmin/index', '<i class="fa icon-systemadmin"></i><span>'.$this->lang->line('menu_systemadmin').'</span>');
                                echo '</li>';
                            }
                        ?><?php */?>

                        <?php
                            if($this->session->userdata("usertype") == "Admin") {
                                echo '<li>';
                                    echo anchor('setting/index', '<i class="fa fa-gears"></i><span>'.$this->lang->line('menu_setting').'</span>');
                                echo '</li>';
                            }
                        ?>

                    </ul>

                </section>
                <!-- /.sidebar -->
            </aside>
