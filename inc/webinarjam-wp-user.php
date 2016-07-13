<?php

$current_user = wp_get_current_user();

if ( is_user_logged_in() || $atts['onlyusers'] == 'no' ) {

    $webinarjam_live_link = "";
    $apiUrl = "https://app.webinarjam.com/api/v2/register";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $timeout = 30;
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,         10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,  $timeout );
    curl_setopt ($ch, CURLOPT_POST, true);
    $post = array("api_key" => $atts['api_key'], "webinar_id" => $atts['webicode'], "memberid" => $atts['memberid'], "name" => $current_user->display_name, "email" => $current_user->user_email, "schedule" => $atts['schedule'] );
    curl_setopt ($ch,CURLOPT_POSTFIELDS,$post);
    $result = curl_exec($ch);
    $jamData = json_decode($result);
//    var_dump($jamData);
    $webinarjam_live_link = $jamData->user->live_room_url;

?>

<div id="webinarjam-wp-button-wrapper" class="webinarjam-wp-button-wrapper">

    <table width="100%"><tr width="100%">
            <td width="30%"></td>
            <td width="40%">
                <div class="wj-input-group" <?php if ( is_user_logged_in() ) { echo 'style="display:none";'; } ?>>
                    <div class="input-group">
                        <div class="webinarjam-wp-input-label">Name: </div>
                        <input type="text" id="webinarjam_wp_name" name="webinarjam_wp_name" class="webinarjam-wp-input" value="<?php if ( is_user_logged_in() ) { echo $current_user->display_name; } ?>" required="" autofocus="">
                    </div>

                    <div class="input-group">
                        <div class="webinarjam-wp-input-label">Email: </div>
                        <input type="text" id="webinarjam_wp_email" name="webinarjam_wp_email" class="webinarjam-wp-input" value="<?php if ( is_user_logged_in() ) { echo $current_user->user_email; } ?>" required="">
                    </div>
                </div>

                <div id="webinarjam-wp-button" type="button" class="webinarjam-wp-button"><?php echo $atts['buttontext'] ?></div>
            </td>
            <td width="30%"></td>
    </tr></table>

</div>

<div id="webinarjam-wp-loader-wrapper" class="webinarjam-wp-loader-wrapper" style="display:none;">
    <img src="<?php echo plugins_url( 'webinarjam-wp/img/webinarjam-wp-loading.gif' ); ?>" id="webinarjam-wp-loader" class="webinarjam-wp-loader">
</div>

<div name="webinarjamframediv" id="webinarjamframediv" style="position: relative; height: 0; overflow: hidden; padding-bottom: 56.25%; display: inline-block;"><iframe name="webinarjamframe" id="webinarjamframe" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"  width="300" height="150" allowfullscreen="allowfullscreen"></iframe></div>

    <script type="text/javascript">

        (function() {

            setTimeout(function() {
                var webinarjam_live_link = "<?php echo $webinarjam_live_link ?>"
                if ( webinarjam_live_link !== "" ) {
                    document.getElementById("webinarjam-wp-loader-wrapper").style.display = "none";
                    document.getElementById("webinarjamframediv").style.display = "block";
                    document.getElementById("webinarjamframe").src = "<?php echo $webinarjam_live_link ?>";
                }
            }, 1000);
            return false;

        })();

    </script>


    <script type="text/javascript">

        (function() {

            document.getElementById("webinarjam-wp-button").onclick = function() {
                document.getElementById("webinarjam-wp-button-wrapper").style.display = "none";
                document.getElementById("webinarjam-wp-loader-wrapper").style.display = "block";
                <?php if ( is_user_logged_in() ) { ?>
                <?php } ?>
                var wnd = window.open("https://app.webinarjam.net/auto-register?webicode=<?php echo $atts['webicode'] ?>&memberid=<?php echo $atts['memberid'] ?>&schedule=<?php echo $atts['schedule'] ?>&firstname=" + document.getElementById('webinarjam_wp_name').value + "&email=" + document.getElementById('webinarjam_wp_email').value,"_blank", "toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no,left=10000, top=10000, width=1, height=1, visible=none");
                setTimeout(function() {
                    wnd.close();
                    document.getElementById("webinarjam-wp-loader-wrapper").style.display = "none";
                    document.getElementById("webinarjamframediv").style.display = "block";
                    document.getElementById("webinarjamframe").src = "https://app.webinarjam.net/live/<?php echo $atts['memberid'] ?>/<?php echo $atts['webicode'] ?>/<?php echo $atts['schedule'] ?>";
                }, 7000);
                return false;
            };

        })();

    </script>

<?php
}
else {
    echo "This webinar is only for logged in users. Please login to join the webinar.";
}
?>