<?php
/**
 * Created by PhpStorm.
 * User: vidhi_BSP
 * Date: 10/31/2018
 * Time: 11:11 AM
 *
 */
//$url ='https://suite.social/coder/call/bsp/';
$url ='http://train.social.com/';
session_start();
if(isset($_SESSION['username']) && !empty($_SESSION['username'])){

}else{
    header("Location: $url");
}

?>


<!DOCTYPE html>

<html lang="en">
<!-- Start Head Section -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Social Suite - Dialer Demo</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/bsp.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs4/dt-1.10.18/datatables.min.css"/>
 <link href="assets/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<!-- End Head Section -->

<body id="page-top">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" role="navigation">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $url;?>">Social Suite - Dialer Demo</a>
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#exCollapsingNavbar">
            &#9776;
        </button>
        <div class="collapse navbar-collapse" id="exCollapsingNavbar">
            <ul class="nav navbar-nav flex-row justify-content-between ml-auto">
		<li class="nav-item"><a class="btn btn-block btn_button_font" href="<?php echo $url;?>" class="nav-link">Dialer</a></li>
                <li class="nav-item"><a class="btn btn-block dialer_color_admin"  href="<?php echo $url;?>admin.php" class="nav-link">Admin</a></li>
                <li class="mg-10">
                    <form class="form" id="logoutForm" method="post" role="form">
                        <input type="hidden" name="action" value="logout_admin" />
                        <div class="form-group">
                            <button type="submit" class="btn btn-block logout_admin">Logout</button>
                        </div>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
<section id="about">
    <div class="container">

        <div class="alert alert-danger" style="display: none;">
            <strong>Danger!</strong> All ready exist mobile number.
        </div>
      <!--Add new contact start-->
        <form method="post" id="form_submit" enctype="multipart/form-data" action="<?php echo $url;?>ajax.php">
            <input type="hidden" name="action" value="add_new_contact" />
            <div class="row margin_image_20">
                <div class="col-lg-3">
                    <div class="from-group">
                        <label>Image</label>
                        <input type="file" name="image"  id="file">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="from-group">
                        <label>Name</label>
                        <input type="text" value="" class="form-control" name="name" id="contact_name"  onkeyup="BSP.only('alpha','contact_name')"/>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="from-group">
                        <label>Phone</label>
                        <input type="text" value="" class="form-control" name="phone" id="contact_phone" onkeyup="BSP.only('digit','contact_phone')"/>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="from-group">
                        <label>URL</label>
                        <input type="text" value="" class="form-control" name="url" id="contact_url" />
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="from-group" style="">
                    <label>&nbsp;</label>
                        <input type="Submit" class="form-control btn btn-primary" value="Add New Contact" />
                    </div>
                </div>
            </div>
        </form>
        <!--Add new contact end-->
        <!--get json data into data.json file-->
<?php
$current_data = file_get_contents('data.json');
$current_data = json_decode($current_data, true);
?>
        <!--display contact details start-->
        <table id="contactsTable" class="table contact_tbl_width">
            <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Phone</th>
                <th>URL</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
 <?php if(isset($current_data) && !empty($current_data)){

     foreach($current_data as $single){
         ?>
            <tr>
                <td><img class="single_image_sec" src="<?php echo $single['photo'];?>" /></td>
                <td><?php echo $single['name'];?></td>
                <td><?php echo $single['phone'];?></td>
                <td><?php echo $single['url'];?></td>
                <td><a class="btn btn-sm btn-danger deleteContact" data-id=<?php echo $single['id'];?> style="color: #fff" > 
                <i class="fa fa-trash-o" aria-hidden="true"></i>
                Delete</a></td>
            </tr>
     <?php
     }

 }?>
            </tbody>
        </table>
        <!--display contact details start-->
    </div>
</section>
<script src="assets/jquery/jquery.min.js"></script>
<script src="assets/bsp_script.js"></script>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/jquery-easing/jquery.easing.min.js"></script>
<script src="https://cdn.datatables.net/v/bs4/dt-1.10.18/datatables.min.js"></script>
<script src="bsp.js"></script>
<script>

</script>

</body>

</html>
