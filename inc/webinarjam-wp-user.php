<?php

$current_user = wp_get_current_user();

if ( is_user_logged_in() || $atts['onlyusers'] == 'no' ) {

    if( isset($_POST['webinarjam_wp_name']) ) {
        $webinarjam_wp_posted_name = $_POST['webinarjam_wp_name'];
    }

    if(isset($_POST['webinarjam_wp_email'])) {
        $webinarjam_wp_posted_email = $_POST['webinarjam_wp_email'];
    }

    if ( is_user_logged_in() || ( isset($webinarjam_wp_posted_name) && isset($webinarjam_wp_posted_email) ) )   {

        if (isset($webinarjam_wp_posted_name) && isset($webinarjam_wp_posted_email)) {
            $webinarjam_wp_to_api_name = $webinarjam_wp_posted_name;
            $webinarjam_wp_to_api_email = $webinarjam_wp_posted_email;
        }
        else {
            $webinarjam_wp_to_api_name = $current_user->display_name;
            $webinarjam_wp_to_api_email = $current_user->user_email;
        }

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
        $post = array("api_key" => $atts['api_key'], "webinar_id" => $atts['webicode'], "memberid" => $atts['memberid'], "name" => $webinarjam_wp_to_api_name, "email" => $webinarjam_wp_to_api_email, "schedule" => $atts['schedule'] );
        curl_setopt ($ch,CURLOPT_POSTFIELDS,$post);
        $result = curl_exec($ch);
        $jamData = json_decode($result);
        $webinarjam_live_link = $jamData->user->live_room_url;
        if ( $result !== false ) {
            if ($jamData->status !== "success") {
                echo "Failed to get live feed - this was the error message:<br><br>" . $jamData->status . ": " . $jamData->message;
            }
            if ($result == "Unauthorized") {
                echo "Failed to get live feed - it looks like an error in the API key.";
            }
        }
        else {
            echo "The WebinarJam API appears to be down at the moment!";
        }

    }

    ?>

<form id="webinarjam_register_live" name="webinarjam_register_live" method="post" action="<?php echo get_permalink(); ?>">
    <div id="webinarjam-wp-button-wrapper" class="webinarjam-wp-button-wrapper" <?php if ( is_user_logged_in()  || ( isset($webinarjam_wp_posted_name) && isset($webinarjam_wp_posted_email) ) ) { echo 'style="display:none";'; } ?>>

        <table width="100%"><tr width="100%">
                <td width="30%"></td>
                <td width="40%">
                    <div class="wj-input-group" >
                        <div class="input-group">
                            <span class="fontawesome-user"></span><input type="text" id="webinarjam_wp_name" name="webinarjam_wp_name" class="webinarjam-wp-input" value="<?php if ( is_user_logged_in() ) { echo $current_user->display_name; } ?>" required="" autofocus="">
                        </div>

                        <div class="input-group">
                            <span class="fontawesome-envelope-alt"></span><input type="text" id="webinarjam_wp_email" name="webinarjam_wp_email" class="webinarjam-wp-input" value="<?php if ( is_user_logged_in() ) { echo $current_user->user_email; } ?>" required="">
                        </div>
                    </div>

                    <input id="webinarjam-wp-button" type="submit" class="webinarjam-wp-button" value="<?php echo $atts['buttontext'] ?>" ></input>
                </td>
                <td width="30%"></td>
        </tr></table>

    </div>
</form>

    <div id="webinarjam-wp-loader-wrapper" class="webinarjam-wp-loader-wrapper" style="display:none; width: 100%;">
        <img src="<?php echo plugins_url( 'webinarjam-wp/img/webinarjam-wp-loading.gif' ); ?>" id="webinarjam-wp-loader" class="webinarjam-wp-loader" style="margin-left: auto; margin-right: auto;">
    </div>

    <div name="webinarjamframediv" id="webinarjamframediv" style="position: relative; height: 0; overflow: hidden; padding-bottom: 56.25%; display: inline-block;"><iframe name="webinarjamframe" id="webinarjamframe" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"  width="300" height="150" allowfullscreen="allowfullscreen"></iframe></div>

    <script type="text/javascript">

        (function() {

            var webinarjam_live_link = "<?php echo $webinarjam_live_link ?>";

            if ( webinarjam_live_link !== "" ) {
                var wnd = window.open('https://app.webinarjam.net/auto-register?webicode=<?php echo $atts['webicode'] ?>&memberid=<?php echo $atts['memberid'] ?>&firstname=<?php echo $webinarjam_wp_to_api_name ?>&email=<?php echo $webinarjam_wp_to_api_email ?>&schedule=<?php echo $atts['schedule'] ?>', '_blank', 'toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no,left=10000, top=10000, width=1, height=1, visible=none');
                setTimeout(function () {
                    wnd.close();
                }, 5000);
            }

            setTimeout(function() {
                if ( webinarjam_live_link !== "" ) {
                    document.getElementById("webinarjam-wp-loader-wrapper").style.display = "none";
                    document.getElementById("webinarjam-wp-button-wrapper").style.display = "none";
                    document.getElementById("webinarjamframediv").style.display = "block";
                    document.getElementById("webinarjamframe").src = "<?php echo $webinarjam_live_link ?>";
                }
            }, 6000);
            return false;

        })();

    </script>

    <script type="text/javascript">

        (function() {

            document.getElementById("webinarjam-wp-button").onclick = function() {
                document.getElementById("webinarjam-wp-button-wrapper").style.display = "none";
                document.getElementById("webinarjam-wp-loader-wrapper").style.display = "block";
                document.getElementById("webinarjam_register_live").submit();
            };

        })();

    </script>

<?php
}
else {
    echo "This webinar is only for logged in users. Please login to join the webinar.";
}
?>
