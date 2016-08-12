<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title><?php echo $config['cur_sys_title']; ?></title>
    <meta charset="utf-8">

    <!-- Bootstrap CSS -->
    <link href="resource/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <!-- Jquery -->
    <script src="resource/js/jquery-1.10.2.min.js"></script>

    <!-- artDialog -->
    <link href="resource/artDialog/css/ui-dialog.css" rel="stylesheet" type="text/css">
    <script src="resource/artDialog/dist/dialog-min.js"></script>

    <!-- Bootstrap JS -->
    <script src="resource/bootstrap/js/bootstrap.min.js"></script>

    <!-- Select2 -->
    <link href="resource/select2/select2.min.css" rel="stylesheet"/>
    <script src="resource/select2/select2.min.js"></script>

</head>

<body>

<!-- Fixed navbar -->
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" style="color: #fff;" href="">
                <span class="glyphicon glyphicon-menu-hamburger"></span>
                <?php echo $config['cur_sys_title']; ?>
            </a>
        </div>
        <div class="navbar-collapse collapse">

        </div>
    </div>
</div>

<div style="height:70px;"></div>