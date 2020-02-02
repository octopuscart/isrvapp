<?php
$this->load->view('layout/headerAdmin');
?>

<style>
    .small_table td, .small_table th {
        padding: 0px 5px;
        line-height: 18px;
    }
</style>
<section class="sub-bnr" data-stellar-background-ratio="0.5" >
    <div class="position-center-center">
        <div class="container">
            <h4>Notification Management</h4>

        </div>
    </div>
</section>

<!-- begin #content -->
<div id="content" class="content" ng-controller="ServiceController">
    <!-- begin breadcrumb -->


    <!-- begin panel -->
    <div class="panel panel-inverse" data-sortable-id="form-stuff-5" style="    margin-bottom: 15px;">

        <div class="panel-body">
            <button type="button" class="btn btn-default"  data-toggle="modal" data-target="#addModal"><i class="fa fa-edit"></i> Add Notification</button>

        </div>
    </div>
    <div class="panel panel-inverse">

        <div class="panel-body">

            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Image</th>
                        <th>Date Time</th>
                    </tr>
                    <?php
                    foreach ($notificationlist as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $value['title']; ?></td>
                            <td><?php echo $value['message']; ?></td>
                            <td>
                                <img src="<?php echo base_url(); ?>assets/serviceimage/<?php echo $value['image']; ?>" class="img-fluid" alt="Responsive image" style="height: 50px;">

                            </td>
                            <td><?php echo $value['datetime']; ?></td>
                        </tr>
                        <?php
                    }
                    ?>

                </table>
            </div>


        </div>
    </div>




    <div class="modal fade" tabindex="-1" role="dialog" id="addModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="#" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">


                        <div class="form-group">
                            <label for="exampleInputEmail1">Title</label>
                            <input type="text" class="form-control"  placeholder="Enter Service Name" value="" required="" name="title">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Description</label>
                            <textarea name="message" class="form-control"  placeholder="Description"></textarea>
                        </div>

                        <div class="btn-group" role="group" aria-label="..." >
                            <span class="btn btn-success col fileinput-button" ">
                                <i class="fa fa-plus"></i>
                                <span>Add files...</span>
                                <input type="file" name="file"  file-model="filemodel" accept="image/*">
                            </span>
                        </div>

                        <div class="col-md-12">
                            <span style="font-size: 10px;">  Attach File From Here (JPG, PNG Allowed)</span>

                            <h2 style="    font-size: 12px;">{{filemodel.name}}</h2>
                            <input type="hidden" name="file_real_name" value="{{filemodel.name}}"/>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add_data" class="btn btn-sm btn-primary m-r-5"><i class="fa fa-save"></i> Save Now</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- end panel -->
</div>
<!-- end #content -->



<?php
$this->load->view('layout/footerAdmin');
?>
<script>
    $(function () {
        $(".editable").editable();
    })
</script>