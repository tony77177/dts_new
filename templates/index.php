
<?php require_once('templates/common/header.php');?>

<hr style=""/>

<div class="container">

    <div class="row" style="margin: 0;min-height:300px;margin-top: 50px;">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                    <span class="glyphicon glyphicon-globe"></span>
                    &nbsp;归属地查询
                </a>
            </li>
            <li role="presentation">
                <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                    <span class="glyphicon glyphicon-eye-open"></span>
                    &nbsp;敏感信息查询
                </a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                <div class="jumbotron search-box" id="single_search_box">
                    <p><span class="glyphicon glyphicon-triangle-bottom"></span> 请输入IP地址或者域名：</p>
                    <div class="input-group">
                        <input placeholder="比如：114.114.114.114 或 www.baidu.com"
                               type="text" id="search_info" class="form-control" value="">
                    <span class="input-group-btn scan-but-span">
                        <button class="btn btn-info" id="btn_search" type="button">
                            <span class="glyphicon glyphicon-search"></span>
                            &nbsp;查询
                        </button>
                    </span>
                    </div>

                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="profile">
                <div class="jumbotron search-box" id="single_search_box">

                    <p><span class="glyphicon glyphicon-triangle-bottom"></span> 请输入查询信息：</p>
                    <select class="threat_option">
                        <option></option>
                        <option value="email">邮箱</option>
                        <option value="domain">域名</option>
                        <option value="ip">IP地址</option>
                        <option value="hash">Hash</option>
                        <option value="antivirus">Antivirus</option>
                    </select>
                    <div class="input-group">
                        <input placeholder="请输入查询信息"
                               type="text" id="threat_info" class="form-control" value="">
                        <span class="input-group-btn scan-but-span">
                            <button class="btn btn-info" id="btn_threat" type="button">
                                <span class="glyphicon glyphicon-search"></span>
                                &nbsp;查询
                            </button>
                        </span>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

<script>

    $(document).ready(function() {

        $(".threat_option").select2({
            placeholder: "请选择查询类别",
            minimumResultsForSearch: Infinity,
            width: '150px'
        });

        $("#btn_search").click(function(){
            var search_info = $("#search_info").val();
            if(search_info==''){
                var d = dialog({
                    content: '请输入查询内容'
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                }, 1500);
                return false;
            }

            //loading事件
            dialog({
                id:'result_info',
                title:'查询中，请稍后...',
                width:150
            }).show();

            var tmp = skipEmptyElementForArray(search_info.split(' '));
            var url = 'manage/search.php?flag=location_search';

            if (tmp.length > 1) {
                url = 'manage/search.php?flag=location_search&multi=1&_search_info='+tmp;
                download_file(url);
                dialog.get('result_info').close();
                return;
            }

            $.get(url, {_search_info: search_info}, function (msg) {
                if (msg == 'fail') {
                    var d = dialog({
                        title: '结果',
                        content: '查询失败，请确认输入数据的正确性。'
                    });
                    d.show();
                    dialog.get('result_info').close();
                } else {
                    dialog.get('result_info').width('auto');
                    dialog.get('result_info').title('查询结果');
                    dialog.get('result_info').content(msg);
                }
            });

        });

        $("#btn_threat").click(function () {
            var threat_option = $(".threat_option").val();
            var threat_info = $.trim($("#threat_info").val());
            if (threat_option == '' || threat_info == '') {
                var d = dialog({
                    content: '请输入查询内容'
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                }, 1500);
                return false;
            }

            //loading事件
            dialog({
                id: 'result_info',
                title: '查询中，请稍后...',
                width: 150
            }).show();

//            var tmp = skipEmptyElementForArray(search_info.split(' '));
            var url = 'manage/search.php?flag=threat_search';

//            if (tmp.length > 1) {
//                url = 'manage/search.php?flag=threat_search&multi=1&_search_info=' + tmp;
//                download_file(url);
//                dialog.get('result_info').close();
//                return;
//            }

            $.get(url, {_threat_option: threat_option, _threat_info: threat_info}, function (msg) {
                if (msg == 'fail') {
                    var d = dialog({
                        title: '结果',
                        content: '查询失败，请确认输入数据的正确性。'
                    });
                    d.show();
                    dialog.get('result_info').close();
                } else {
                    alert((msg));
//                    dialog.get('result_info').width('auto');
//                    dialog.get('result_info').title('查询结果');
//                    dialog.get('result_info').content(Process(msg));
//                    dialog.get('result_info').content(msg);
                }
            });

        });


        /*通过iframe下载文件*/
        function download_file(_url) {
            //var url = _url;
            var download_iframe = document.createElement("iframe");
            document.body.appendChild(download_iframe)
            download_iframe.src = _url;
            download_iframe.style.display = "none";
        }

        /*去掉多余的空字符串*/
        function skipEmptyElementForArray(arr) {
            var a = [];
            $.each(arr, function (i, v) {
                var data = $.trim(v);
                if ('' != data) {
                    a.push(data);
                }
            });
            return a;
        }

        function format(txt, compress/*是否为压缩模式*/) {/* 格式化JSON源码(对象转换为JSON文本) */
            var indentChar = '    ';
            if (/^\s*$/.test(txt)) {
                alert('数据为空,无法格式化! ');
                return;
            }
            try {
                var data = eval('(' + txt + ')');
            }
            catch (e) {
                alert('数据源语法错误,格式化失败! 错误信息: ' + e.description, 'err');
                return;
            }
            ;
            var draw = [], last = false, This = this, line = compress ? '' : '\n', nodeCount = 0, maxDepth = 0;

            var notify = function (name, value, isLast, indent/*缩进*/, formObj) {
                nodeCount++;
                /*节点计数*/
                for (var i = 0, tab = ''; i < indent; i++)tab += indentChar;
                /* 缩进HTML */
                tab = compress ? '' : tab;
                /*压缩模式忽略缩进*/
                maxDepth = ++indent;
                /*缩进递增并记录*/
                if (value && value.constructor == Array) {/*处理数组*/
                    draw.push(tab + (formObj ? ('"' + name + '":') : '') + '[' + line);
                    /*缩进'[' 然后换行*/
                    for (var i = 0; i < value.length; i++)
                        notify(i, value[i], i == value.length - 1, indent, false);
                    draw.push(tab + ']' + (isLast ? line : (',' + line)));
                    /*缩进']'换行,若非尾元素则添加逗号*/
                } else if (value && typeof value == 'object') {/*处理对象*/
                    draw.push(tab + (formObj ? ('"' + name + '":') : '') + '{' + line);
                    /*缩进'{' 然后换行*/
                    var len = 0, i = 0;
                    for (var key in value)len++;
                    for (var key in value)notify(key, value[key], ++i == len, indent, true);
                    draw.push(tab + '}' + (isLast ? line : (',' + line)));
                    /*缩进'}'换行,若非尾元素则添加逗号*/
                } else {
                    if (typeof value == 'string')value = '"' + value + '"';
                    draw.push(tab + (formObj ? ('"' + name + '":') : '') + value + (isLast ? '' : ',') + line);
                }
                ;
            };
            var isLast = true, indent = 0;
            notify('', data, isLast, indent, false);
            return draw.join('');
        }

        /* 响应回车事件 */
        /*document.onkeydown = function(event){
         if(event.keyCode==13) {
         document.getElementById("btn_search").click();
         return false;
         }
         };*/


    });

</script>


<?php require_once('templates/common/footer.php');?>