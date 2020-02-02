<?php
$this->load->view('layout/headerAdmin');
?>

<style>

    .small_table{
        width: 100%;
    }
    .small_table td, .small_table th {
        padding: 0px 5px;
        line-height: 18px;
    }
</style>
<section class="sub-bnr" data-stellar-background-ratio="0.5" style="margin-bottom: 10px;">
    <div class="position-center-center">
        <div class="container">
            <h4>Booking Reports</h4>

        </div>
    </div>
</section>


<!-- Inner Page Banner Area End Here -->
<!-- Login Registration Page Area Start Here -->
<div class="login-registration-page-area" style="padding: 20px 0;">
    <div class="container">
        <div class="row">
            <table id="tableDataOrder" class="table table-bordered  tableDataOrder">
                <thead>
                    <tr>

                        <th style="width:50px">S. No.</th>
                        <th style="width:300px">Manufacturer</th>
                        <th style="width:300px">Model No.</th>
                        <th style="width: 300px">UUID</th>                   
                        <th style="width:150px">Date/Time</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($bookinglist)) {
                        $count = 1;
                        foreach ($bookinglist as $key => $value) {
                            ?>
                            <tr style="border-bottom: 1px solid #000;"  class="">

                                <td>

                                    <?php echo $key + 1; ?>


                                </td>


                                <td>
                                    <?php
                                    echo $value->manufacturer . "<br/>";
                                    ?>
                                </td>



                                <td>
                                    <?php
                                    echo $value->model . "<br/>";
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo $value->uuid . "<br/>";
                                    ?>
                                </td>


                                <td>
                                    <?php
                                    echo $value->datetime;
                                    ?>
                                </td>

                            </tr>
                            <?php
                            $count++;
                        }
                    } else {
                        ?>
                    <h4><i class="fa fa-warning"></i> No order found</h4>
                    <?php
                }
                ?>

                </tbody>
            </table>

        </div>
    </div>
</div>
<!-- Login Registration Page Area End Here -->




<?php
$this->load->view('layout/footerAdmin');
?>