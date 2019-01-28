<?php
$target_title = esc_html('"' . $saved_customizer->name_customizer . '"');
$target_url   = urlencode($url);
?>
<div id="customizer_social_buttons">

    <!-- facebook -->
    <a class="facebook"
       href="https://www.facebook.com/share.php?u=<?php echo $target_url ?>&title=<?php echo $target_title; ?>"
       target="blank"><i class="fab fa-facebook-f"></i></a>

    <!-- twitter -->
    <a class="twitter"
       href="https://twitter.com/intent/tweet?status=<?php echo $target_title; ?>+<?php echo $target_url; ?>"
       target="blank"><i class="fab fa-twitter"></i></a>

    <!-- google plus -->
    <a class="googleplus" href="https://plus.google.com/share?url=<?php echo $target_url; ?>" target="blank"><i
                class="fab fa-google-plus"></i></a>

    <!-- linkedin -->
    <a class="linkedin"
       href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $target_url; ?>&title=<?php echo $target_title; ?>&summary=&source="
       target="blank"><i class="fab fa-linkedin"></i></a>

    <!-- pinterest -->
    <a class="pinterest"
       href="https://pinterest.com/pin/create/bookmarklet/?media=<?php echo $target_title; ?>&url=<?php echo $target_url; ?>&is_video=false&description="
       target="blank"><i class="fab fa-pinterest-p"></i></a>

</div>