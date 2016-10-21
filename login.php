<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>请登录系统</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Jquery -->
    <script src="resource/js/jquery-1.10.2.min.js"></script>

    <!-- Bootstrap -->
    <link href="resource/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <!-- artDialog -->
    <link href="resource/artDialog/css/ui-dialog.css" rel="stylesheet" type="text/css">
    <script src="resource/artDialog/dist/dialog-min.js"></script>


    <style>
        body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #eee;
        }

        .form-signin {
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
        }

        .form-signin .form-signin-heading,
        .form-signin {
            margin-bottom: 10px;
        }

        .form-signin .form-control {
            position: relative;
            font-size: 16px;
            height: auto;
            padding: 10px;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .form-signin .form-control:focus {
            z-index: 2;
        }

        .form-signin input[type="text"] {
            margin-bottom: -1px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        #info {
            display: none;;
        }

        .form-signin-heading {
            text-align: center;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="form-signin" role="form">
        <h2 class="form-signin-heading">登录系统</h2>
        <input type="text" class="form-control" id="user_name" name="user_name" placeholder="帐&nbsp;号"><br>
        <input type="password" class="form-control" id="password" name="password" placeholder="密&nbsp;码">
        <button class="btn btn-lg btn-primary btn-block" id="login_btn" type="button">登录</button>
        <div id="info"></div>
    </div>

</div>

</body>

<script>
    $(document).ready(function () {

        $("#login_btn").click(function () {
            var user = $("#user_name").val();
            var pwd = $("#password").val();

            if (user == '' || pwd == '') {
                var d = dialog({
                    content: '请输入账号及密码！'
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                }, 1500);
            } else {
                //loading事件
                dialog({
                    id: 'result_info',
                    title: '登录中，请稍后...',
                    width: 150,
                    quickClose: true
                }).show();
                $("#login_btn").html("登录中，请稍后...");
                $("#login_btn").attr('disabled', true);
                $.ajax({
                    url: "manage/login.php",
                    type: "POST",
                    data: {username: user, password: pwd},
                    success: function (msg) {
                        if (msg == 'fail') {
                            dialog.get('result_info').close();
                            var d = dialog({
                                content: '账号或密码错误，请重新输入！'
                            });
                            d.show();
                            setTimeout(function () {
                                d.close().remove();
                            }, 1500);
                            $("#login_btn").html("登录");
                            $("#login_btn").attr('disabled', false);
                            return false;
                        } else {
                            $("#info").html(msg);
                        }
                    },
                    error: function () {
                        dialog.get('result_info').close();
                        var d = dialog({
                            content: '数据库连接失败，请稍后再试！'
                        });
                        d.show();
                        setTimeout(function () {
                            d.close().remove();
                        }, 1500);
                        $("#login_btn").html("登录失败");
                    }
                });
            }
        });

        $(document).keyup(function (event) {
            if (event.keyCode == 13) {
                $("#login_btn").click();
            }
        });
    });
</script>
</html>