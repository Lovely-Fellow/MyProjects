<?php
/**
 * Created by PhpStorm.
 * User: vidhi_BSP
 * Date: 10/31/2018
 * Time: 11:11 AM
 *
 */
session_start();
/*button hide show based on session inset value or not*/

//$url="https://suite.social/coder/call/bsp/";
$url="http://train.social.com/";
//$_SESSION['username']="admin";
if(isset($_SESSION['username']) && !empty($_SESSION['username'])){
 $display ="none";
 $admin_display ="block";
}else{
    $display ="block";
    $admin_display ="none";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Social Suite - Dialer Demo</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/bsp.css" rel="stylesheet">
    <link href="assets/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">

</head>

<body id="page-top" class='homepage'>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" role="navigation">
    <div class="container">
        <!--a class="navbar-brand" href="https://suite.social/coder/call/bsp/">Social Suite - Dialer Demo</a-->
        <a class="navbar-brand" href="http://train.social.com/">Social Suite - Dialer Demo</a>
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#exCollapsingNavbar">
            &#9776;
        </button>
        <div class="collapse navbar-collapse" id="exCollapsingNavbar">
            <ul class="nav navbar-nav flex-row justify-content-between ml-auto">
                <li class="nav-item" style="display: <?php echo $admin_display;?> "><a class="btn btn-block nav-link" href="<?php echo $url;?>admin.php" >Admin</a></li>
                <li class="dropdown order-1" style="display:none">
                    <button type="button" id="dropdownMenu1" data-toggle="dropdown"
                            style="display: <?php echo $display;?>"
                            class="btn btn-outline-secondary dropdown-toggle">Login <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right mt-2">
                        <li class="px-3 py-2">
                            <form class="form" id="loginForm" method="post" role="form">
                                <input type="hidden" name="action" value="login_admin"/>

                                <div class="form-group">
                                    <input id="usernameInput" name="username" placeholder="Username" autocomplete="off"
                                           class="form-control form-control-sm" type="text" required="">
                                </div>
                                <div class="form-group">
                                    <input id="passwordInput" name="password" placeholder="Password" autocomplete="off"
                                           class="form-control form-control-sm" type="password" required="">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                                </div>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section id="about" >
    <div class="container">
        <div class="row">
            <div class="col-lg-5 mx-auto">
                <div class="row mainWrapper" id="dialerWrapper">
                    <!--Contact list display start-->
                    <div class="col-md-12 contactsWrapper" >

                        <ul class="contactList" id="contactList">
                            <?php
                            $current_data = file_get_contents('data.json');
                            $current_data = json_decode($current_data, true);
                            ?>
                            <?php if (isset($current_data) && !empty($current_data)) {
                                //print_r($current_data);exit;
                                foreach ($current_data as $single) {
                                    ?>
                                    <li class="contactEach" data-number="<?php echo $single['phone'];?>"
                                        data-url="<?php echo $single['url'];?>">
                                        <a href="#"
                                           data-url="<?php echo $single['url'];?>"
                                           data-photo="<?php echo $single['photo'];?>"
                                           data-name="<?php echo $single['name'];?>"
                                           data-phone="<?php echo $single['phone'];?>" class="contact-action">

                                            <img src="<?php echo $single['photo'];?>"/>
                                            <div class="contactDetail">
                                                <p class="contactName"><?php echo $single['name'];?></p>

                                                <p class="contactNo"><?php echo $single['phone'];?></p>
                                            </div>
                                        </a>
                                    </li>
                                <?php
                                }

                            } ?>

                        </ul>
                    </div>
                    <!--Contact list display end-->
                    <!--Dialer details display start-->
                    <div class="col-md-12 dialerWrapper" style="display: none;" >

                        <div class="dialedWrap clearfix">
                            <div class="dialedNo"></div>
                            <div class="dialedErase"><</div>
                        </div>

                        <div data-number=1 class="numberPad">
                            <span class="dialerNumber">1</span>
				<span class="dialerLetter"><span class="dialerLetter">
                        </div>
                        <div data-number=2 class="numberPad">
                            <span class="dialerNumber">2</span>
				<span class="dialerLetter">
					ABC<span class="dialerLetter">
                        </div>
                        <div data-number=3 class="numberPad">
                            <span class="dialerNumber">3</span>
				<span class="dialerLetter">
					DEF<span class="dialerLetter">
                        </div>
                        <div data-number=4 class="numberPad">
                            <span class="dialerNumber">4</span>
				<span class="dialerLetter">


					GHI




									<span class="dialerLetter">
                        </div>
                        <div data-number=5 class="numberPad">
                            <span class="dialerNumber">5</span>
				<span class="dialerLetter">JKL<span class="dialerLetter">
                        </div>
                        <div data-number=6 class="numberPad">
                            <span class="dialerNumber">6</span>
				<span class="dialerLetter">
					MNO	<span class="dialerLetter">
                        </div>
                        <div data-number=7 class="numberPad">
                            <span class="dialerNumber">7</span>
				<span class="dialerLetter">PQRS
                    <span class="dialerLetter">
                        </div>
                        <div data-number=8 class="numberPad">
                            <span class="dialerNumber">8</span>
				<span class="dialerLetter">
					TUV
                    <span class="dialerLetter">
                        </div>
                        <div data-number=9 class="numberPad">
                            <span class="dialerNumber">9</span>
				<span class="dialerLetter">WXYZ
                    <span class="dialerLetter">
                        </div>
                        <div class="numberPad" data-number='*'>
                            <span class="dialerNumber">*</span>
                        </div>
                        <div class="numberPad" data-number=0>
                            <span class="dialerNumber">0</span>
                            <span class="dialerLetter">+</span>
                        </div>
                        <div class="numberPad" data-number='#'>
                            <span class="dialerNumber">#</span>
                        </div>
                        <a id="close"><i class="fa fa-times-circle"></i></a>

                    </div>
                    <!--Dialer details display end-->
                    <div class="col-md-12 view-contact" style="display: none; " >
                        <div class="row view-contact_div">
                            <div class="col-md-12" id="back">
                                <a href="" ><i class="fa fa-arrow-left back_button_arrow"></i></a>
                            </div>
                        </div>
                        <div class="col-md-12 text-center view_details" >

                                <img src=""  id="view_photo">

                        </div>

                            <div class="col-md-12 text-center view_details" >
                               <h3 id="view_name"></h3>
                            </div>


                            <div class="col-md-12 text-center view_details" >
                                <span id="view_mobile"></span>
                            </div>


                            <div class="col-md-12 text-center view_details" >
                                <a href="" class="back_button_arrow" <span id="view_url"></span></a>
                            </div>

                    </div>
                    <!--dialer-->
                    <div class="phonedial" id="opendial">
                        <i class="fa fa-phone phonedial_phone"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="assets/jquery/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/jquery-easing/jquery.easing.min.js"></script>

<script src="bsp.js"></script>
<script>
    $(document).ready(function() {

            $('.contact-action').click(function(){
                $(".dialerWrapper").css("display", "none");
                $(".view-contact").css("display", "block");
                $(".contactsWrapper").css("display", "none");
                $('#view_mobile').html($(this).data('phone'));
                //$('#view_mobile').attr('href','tel:'+ $(this).data('phone'));
                $('#view_name').html($(this).data('name'));
                $('#view_url').html($(this).data('url'));
                $('#view_url').attr('href',$(this).data('url'));
                $('#view_url').attr('target', '_BLANK');
                $('#view_photo').attr('src',$(this).data('photo'));
                $("#opendial").css("display", "none");
            });
            $('#opendial').click(function(){
                hideshowDiv()
            });

            $('#close').click(function(){
                $("#opendial").css("display", "block");
                hideshowDiv()
            });
            $('#back').click(function(){
                $(".view-contact").css("display", "none");
                $(".dialerWrapper").css("display", "none");
                $("#opendial").css("display", "block");
                $(".contactsWrapper").css("display", "block");
            });
    });
   //div hide show function
    function hideshowDiv(){
        if($('#dialerWrapper').hasClass('dialeron'))
        {
            $("#opendial").css("display", "block");
            $(".dialerWrapper").css("display", "none");
            $('#dialerWrapper').removeClass('dialeron');
        }
        else{
            $("#opendial").css("display", "none");
            $(".dialerWrapper").css("display", "block");
            $('#dialerWrapper').addClass('dialeron');
        }
    }

</script>
</body>

</html>
