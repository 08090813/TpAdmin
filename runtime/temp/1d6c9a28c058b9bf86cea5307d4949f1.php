<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:68:"E:\phpStudy\WWW\youquanhua\public/../app/admin\view\index\index.html";i:1516533617;s:56:"E:\phpStudy\WWW\youquanhua\app\admin\view\Base\base.html";i:1516536171;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>通用-后台管理系统</title>
    <link rel="stylesheet" href="/static/public/layui/css/layui.css">
    <link rel="stylesheet" href="/static/public/iconfont/css/iconfont.css">
    <link rel="stylesheet" href="/static/admin/css/common.css">
</head>
<body>

<div class="layui-layout layui-layout-admin">
    
        <div class="layui-header">
            <div class="layui-logo">通用-后台管理系统</div>
            <ul class="layui-nav layui-layout-left menus-tag hide-menus-tags-sm">
                <li class="layui-nav-item">
                    <a href="javascript:void(0);">
                        <i class="layui-icon icon iconfont icon-caidan" style="font-size: 2rem;"></i>
                    </a>
                </li>
            </ul>
            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <img src="http://t.cn/RCzsdCq" class="layui-nav-img">
                        Admin
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="">基本资料</a></dd>
                        <dd><a href="">安全设置</a></dd>
                    </dl>
                </li>
            </ul>
        </div>
    

    
    <div class="layui-side layui-bg-black" id="left-nav">
        <div class="layui-side-scroll">
            <ul class="layui-nav layui-nav-tree"  lay-filter="menus-btn">
                <li class="layui-nav-item layui-nav-itemed">
                    <a class="" href="javascript:;">
                        <i class="layui-icon">&#xe614;</i>
                        <span>权限管理</span>
                    </a>
                    <dl class="layui-nav-child">
                        <dd>
                            <a href="javascript:;" data-url="<?php echo url('Menus/menusList'); ?>" lay-id="1">
                                <i class="layui-icon">&#xe63c;</i>
                                <span>菜单列表</span>
                            </a>
                        </dd>
                        <dd>
                            <a href="javascript:;" data-url="<?php echo url('index/setting'); ?>" lay-id="2">
                                <i class="layui-icon">&#xe63c;</i>
                                <span>角色列表</span>
                            </a>
                        </dd>
                        <dd>
                            <a href="javascript:;" data-url="<?php echo url('index/setting'); ?>" lay-id="3">
                                <i class="layui-icon">&#xe63c;</i>
                                <span>管理员列表</span>
                            </a>
                        </dd>
                    </dl>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="layui-body" id="body-box">
        <!--顶部tab标签-->
        <div class="top-tab-tags">

        </div>
        <div class="layui-tab layui-tab-brief tab-list-tags" lay-filter="tab-list" lay-allowClose="true" style="height: 97%;">
            <ul class="layui-tab-title">
                <li class="layui-this">
                    <i class="layui-icon" style="top: 2px; font-size: 16px;">&#xe68e;</i>
                    <cite>系统主页</cite>
                </li>
            </ul>
            <div class="layui-tab-content" style="height: 85%">
                <div class="layui-tab-item layui-show" style="height: 100%;">
                    <iframe src="" frameborder="0" width="100%" height="100%"></iframe>
                </div>
            </div>
        </div>
    </div>
    
        <div class="site-tree-mobile layui-hide">
            <i class="layui-icon"></i>
        </div>
        <div class="site-mobile-shade"></div>
        <div class="layui-footer">
            <!-- 底部固定区域 -->
            © zhangchao.name - 版权所有
        </div>
    
</div>
<script src="/static/public/layui/layui.js"></script>
<script src="/static/admin/js/common.js"></script>
<script type="text/javascript">
    layui.use(["jquery",'layer'],function () {
        var indexMain = "<?php echo url('Index/main'); ?>";
        var $ = layui.jquery,
            layer = layui.layer;
        $.ajax({
            async:false,
            type:"GET",
            url:indexMain,
            beforeSend:function () {
                layui.layer.load(0, {shade: false});
            },
            success:function (res) {
                if (res.code&&res.code==404){
                    layer.msg(res.msg,{icon:2});
                    return false;
                }else{
                    $("iframe").attr("src",indexMain);
                    layer.closeAll();
                }
            }
        });
    });
</script>
</body>
</html>