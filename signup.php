<?
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <title>eramba security manager</title>
    <meta charset="UTF-8">

    <meta name="description" content="">
    <meta name="keywords" content="">

    <meta name="author" content="">
    <meta name="Copyright" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=9">
    <meta http-equiv="Pragma" content="no-cache">
<?php
echo"    <script type=\"text/javascript\" src=\"/js/jquery.min.js\"></script>";
echo"    <script type=\"text/javascript\" src=\"/js/jquery-ui.min.js\"></script>";
echo"    <script type=\"text/javascript\" src=\"/js/admin.scripts.js\"></script>";
echo"    <script type=\"text/javascript\" src=\"/js/chosen.jquery.js\"></script>";
echo"    <script type=\"text/javascript\" src=\"/js/accordion.js\"></script>";
echo"    <script type=\"text/javascript\" src=\"/js/input-filters.js\"></script>";
echo"    <link rel=\"stylesheet\" type=\"text/css\" href=\"css/normalize.css\">";
echo"    <link rel=\"stylesheet\" type=\"text/css\" href=\"css/styles.css\">";
echo"    <link rel=\"stylesheet\" type=\"text/css\" href=\"css/chosen.css\">";
echo"    <link rel=\"stylesheet\" type=\"text/css\" href=\"css/chosen.css\">";
echo"    <script type=\"text/javascript\" src=\"css/jsapi\"></script>";
?>
    <link rel="stylesheet" href="css/jquery-ui.css" />
    <script>
    </script>


</head>
<body>

<section id="header-wrapper">
    <div id="header-inner"/>
</section>
<pre></pre>


<section id="content-wrapper">
    <h3>Create a User Authorization for Your Company</h3>
    <div class="tab-wrapper">
        <ul class="tabs">
            <li class="first active">

            </li>
        </ul>

        <div class="tab-content">
            <div class="tab" id="tab">
<?
echo"                <form name=\"system_admin_add\" method=\"GET\" action=\"\">";
?>
                    <label for="name">Name</label>
                    <span class="description">Admin Name</span>
<?echo"                    <input type=\"text\" class=\"filter-text\" name=\"system_admin_name\" id=\"system_admin_name\" value=\"$item[system_admin_name]\">";
?>
                    <label for="name">Mail</label>
                    <span class="description">Authorized mail id</span>
<?echo"                    <input type=\"text\" class=\"filter-text\" name=\"system_admin_mail\" id=\"system_admin_mail\" value=\"$item[system_admin_mail]\">";
?>
                    <label for="name">Login ID</label>
                    <span class="description">Set the login ID for your company. It must be the same login name utilized for all authentications</span>
<?echo"                    <input type=\"text\" class=\"filter-text\" name=\"system_admin_login_id\" id=\"system_admin_login_id\" value=\"$item[system_admin_login_id]\">";
?>
                    <label for="name">Password</label>
                    <span class="description">Set a Password for the Admin!</span>
<?echo"                    <input type=\"password\" class=\"\" name=\"system_admin_pwd\" id=\"system_admin_pwd\" value=\"untouched\">";
?>
                </div>

        </div>
    </div>

    <div class="controls-wrapper">

        <input type="hidden" name="action" value="update">
        <input type="hidden" name="section" value="system">
        <input type="hidden" name="subsection" value="system_authorization_list">
        <a>
            <input type="submit" value="Submit" class="add-btn">
        </a>

        <a href="http://erambacloud.cloudapp.net/" class="cancel-btn">				Cancel
        </a>
    </div>

    </form>

    <br class="clear">

</section>
</body>
</html>