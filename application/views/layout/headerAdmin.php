
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <?php
        meta_tags();
        ?>
        <!-- Bootstrap core CSS -->
        <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <link href="<?php echo base_url(); ?>assets/admin/style.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>assets/angular/angular.min.js"></script>
    </head>

    <body ng-app="App" class="stretched">
        <div ng-controller="AdminController" id="wrapper" class="clearfix">
            <script>
                var App = angular.module('App', []).config(function ($interpolateProvider, $httpProvider) {
                //$interpolateProvider.startSymbol('{$');
                //$interpolateProvider.endSymbol('$}');
                $httpProvider.defaults.headers.common = {};
                $httpProvider.defaults.headers.post = {};
                });
                var baseurl = "<?php echo base_url(); ?>index.php/";
                var avaiblecredits = 0;
            </script>

            <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
                <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#" style="    text-align: center;">
                </a>
                <ul class="navbar-nav px-3">
                    <li class="nav-item text-nowrap">
                        <?php
                        $session_data = $this->session->userdata('logged_in');
                        if ($session_data) {
                            ?>
                            <a class="nav-link" href="<?php echo site_url('Admin/logout') ?>">Sign out</a>
                            <?php
                        }
                        ?>
                    </li>
                </ul>
            </nav>

            <div class="container-fluid">
                <div class="row">
                    <?php
                    if ($session_data) {
                        ?>
                        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                            <div class="sidebar-sticky">
                                <ul class="nav flex-column">
                                  
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo site_url("Admin/downloadReport") ?>">
                                            <i class="fa fa-list-ol"></i>
                                            Download Reports
                                        </a>
                                    </li>
                                  
                                   
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo site_url("Admin/sendNotification"); ?>">
                                            <i class="fa fa-share"></i>
                                            Send Notification
                                        </a>
                                    </li>
                                  
                                    


                                </ul>


                            </div>
                        </nav>
                        <?php
                    }
                    ?>
                    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">