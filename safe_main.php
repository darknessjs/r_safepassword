<?php
/*
Plugin Name:r_safepassword
Plugin URI: http://www.darkjs.com/
Description: 安全登录
Version: 1.0.0
Author: Darkness
Author URI: http://www.darkjs.com/
License: GPL
*/
function loginactioncode(){	?>
    <input type="hidden" name="safecode" id="safecode">
    <script language="JavaScript">


        var safecode=parseInt(Math.random()*10000000%1000000);
        $.post("<?php echo WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__)); ?>/wp-safe.php",{randomcode:safecode},function(data){
            $("#safecode").val(data)
        });
        var base64encodechars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
        function base64encode(str) {
            var out, i, len;
            var c1, c2, c3;
            len = str.length;
            i = 0;
            out = "";
            while (i < len) {
                c1 = str.charCodeAt(i++) & 0xff;
                if (i == len) {
                    out += base64encodechars.charAt(c1 >> 2);
                    out += base64encodechars.charAt((c1 & 0x3) << 4);
                    out += "==";
                    break;
                }
                c2 = str.charCodeAt(i++);
                if (i == len) {
                    out += base64encodechars.charAt(c1 >> 2);
                    out += base64encodechars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xf0) >> 4));
                    out += base64encodechars.charAt((c2 & 0xf) << 2);
                    out += "=";
                    break;
                }
                c3 = str.charCodeAt(i++);
                out += base64encodechars.charAt(c1 >> 2);
                out += base64encodechars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xf0) >> 4));
                out += base64encodechars.charAt(((c2 & 0xf) << 2) | ((c3 & 0xc0) >> 6));
                out += base64encodechars.charAt(c3 & 0x3f);
            }
            return out;
        }

        $("#loginform").submit(function(e){
            var str=$("#user_pass").val();
            var n="";
            var l="";
            for(var i=0;i<str.length;i++){
                var s=str.charCodeAt(i);
                l=l+""+(""+s*safecode).length;
                n=n+""+s*safecode;
            }
            var result=n+"-"+l;
            $("#user_pass").val(base64encode(result));
        });
    </script>

<?php }
add_action( 'login_form', 'loginactioncode');

function postsavecode(){
    if ( ! empty($_POST['pwd']) ){
        $decodepwd=base64_decode($_POST['pwd']);
        $pwdarr1=explode("-",$decodepwd);
        $pwdresult="";
        $pwdnowpoint=0;
        for($i=0;$i<strlen($pwdarr1[1]);$i++){
            $pwdresult=$pwdresult.chr(substr($pwdarr1[0],$pwdnowpoint,substr($pwdarr1[1],$i,1))/$_SESSION['safecode']);

            $pwdnowpoint=$pwdnowpoint+substr($pwdarr1[1],$i,1);
        }
        echo $pwdresult;
        $_POST['pwd']=$pwdresult;

    }
    ?>
    <?php
}
add_action( 'login_form_login', 'postsavecode');

