<?php
// debug($user);
?>
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <?php echo $this->element('aside/user-panel'); ?>

        <!-- search form -->
        <?php // echo $this->element('search'); ?>
        <!-- /.search form -->

        <?php echo $this->element('aside/sidebar-menu'); ?>

    </section>
    <!-- /.sidebar -->
</aside>