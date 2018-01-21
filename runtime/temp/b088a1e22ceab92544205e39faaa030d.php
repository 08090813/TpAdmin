<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"E:\phpStudy\WWW\youquanhua\public/../app/admin\view\menus\menuslist.html";i:1516536696;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>菜单列表</title>
    <link rel="stylesheet" href="/static/public/layui/css/layui.css">
    <link rel="stylesheet" href="/static/public/iconfont/css/iconfont.css">
    <style>
        @media screen and (max-width: 450px) {
            .layui-form-item .layui-input-inline{
                margin: 0 0 10px 0px;
            }
            #searchBtn{
                width: 100%;
            }
        }
    </style>
</head>
<body>
<fieldset class="layui-elem-field layui-field-title">
    <blockquote class="layui-elem-quote layui-quote-nm">
        <div class="layui-fluid">
            <div class="layui-row">
                <div class="layui-col-xs12 layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="请输入关键字" class="layui-input">
                            </div>
                            <div class="layui-input-inline">
                                <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="菜单创建时间" class="layui-input">
                            </div>
                            <button class="layui-btn" id="searchBtn">立即搜索</button>
                            <button class="layui-btn" id="add">添加菜单</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </blockquote>
</fieldset>
<table class="layui-hide" id="LAY_table_user" lay-filter="user" lay-size="sm"></table>
<script src="/static/public/layui/layui.js"></script>
<script src="/static/admin/js/common.js"></script>
<script type="text/javascript">
    layui.use(['jquery','layer','element','form','table'],function () {
        var $ = layui.layer,
            layer = layui.layer,
            element = layui.element,
            form = layui.form;
        var table = layui.table;

        //方法级渲染
        table.render({
            elem: '#LAY_table_user'
            ,url: '/demo/table/user/'
            ,cols: [[
                {checkbox: true, fixed: true}
                ,{field:'id', title: '编号', width:80, sort: true, fixed: true}
                ,{field:'username', title: '名称', width:120}
                ,{field:'sex', title: '模块', width:120}
                ,{field:'city', title: '方法', width:120}
                ,{field:'sign', title: '备注', width:200}
                ,{field:'experience', title: '类型', width:120}
                ,{field:'score', title: '创建时间', sort: true, width:160}
                ,{fixed: 'right', width:150, align:'center', toolbar: '#barDemo'} //这里的toolbar值是模板元素的选择器
            ]]
            ,id: 'testReload'
            ,page: true
            ,height: 315
        });

        var $ = layui.$, active = {
            reload: function(){
                var demoReload = $('#demoReload');

                //执行重载
                table.reload('testReload', {
                    page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    ,where: {
                        key: {
                            id: demoReload.val()
                        }
                    }
                });
            }
        };

        $('.demoTable .layui-btn').on('click', function(){
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });
    })
</script>
</body>
</html>