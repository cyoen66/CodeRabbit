layui.define(['tool','employeepicker','tinymce'], function (exports) {
	let form = layui.form;
	let table = layui.table;
	let laydate = layui.laydate;
	let dropdown = layui.dropdown;
	let employeepicker = layui.employeepicker;
	let tinymce = layui.tinymce;
	let obj = {
		//文本
		text: function (id, name, real_txt, editPost) {
			let that = this;
			layer.open({
				type: 1,
				title: '请输入内容',
				area: ['500px', '158px'],
				content: '<div style="padding:5px;"><input class="layui-input" id="goguEditInput" value="' + real_txt + '"/></div>',
				btnAlign: 'c',
				btn: ['提交保存'],
				yes: function () {
					let newval = $("#goguEditInput").val();
					if (newval != '') {
						editPost(id, name, newval, newval);
					} else {
						layer.msg('请输入内容');
					}
				}
			})
		},
		//文本
		textarea: function (id, name, real_txt, editPost) {
			let that = this;
			layer.open({
				type: 1,
				title: '请输入内容',
				area: ['800px', '360px'],
				content: '<div style="padding:5px;"><textarea class="layui-textarea" id="goguEditTextarea" style="width: 100%; height: 240px;">' + real_txt + '</textarea></div>',
				btnAlign: 'c',
				btn: ['提交保存'],
				yes: function () {
					let newval = $("#goguEditTextarea").val();
					if (newval != '') {
						editPost(id, name, newval, newval);
					} else {
						layer.msg('请输入内容');
					}
				}
			})
		},
		//员工单选
		employee_one: function (id, name, show_txt, real_txt, editPost) {
			let that = this;
			employeepicker.init({
				ids: real_txt.toString(),
				names: show_txt,
				department_url: "/api/index/get_department_tree",
				employee_url: "/api/index/get_employee",
				type: 0,
				callback: function (ids, names) {
					editPost(id, name, names, ids);
				}
			});
		},
		//员工多选
		employee_more: function (id, name, show_txt, real_txt, editPost) {
			let that = this;
			let ids = [];
			let names = [];
			if (real_txt != '') {
				ids = real_txt.toString().split(',');
				names = show_txt.split(',');
			}
			employeepicker.init({
				ids: ids,
				names: names,
				department_url: "/api/index/get_department_tree",
				employee_url: "/api/index/get_employee",
				type: 1,
				callback: function (ids, names) {
					editPost(id, name, names.join(','), ids.join(','));
				}
			});
		},
		//ajax表格单选
		select_table: function (id, name, real_val, url, editPost) {
			let that = this;
			let selectTable;
			layer.open({
				title: '请选择',
				area: ['600px', '580px'],
				type: 1,
				content: '<div class="picker-table">\
					<form class="layui-form pb-2">\
						<div class="layui-input-inline" style="width:480px;">\
						<input type="text" name="keywords"  placeholder="项目名称" class="layui-input" autocomplete="off" />\
						</div>\
						<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="search_form">提交搜索</button>\
					</form>\
					<div id="selectTable"></div></div>',
				success: function () {
					selectTable = table.render({
						elem: '#selectTable'
						, url: url
						, page: true //开启分页
						, limit: 10
						, cols: [[
							{ type: 'radio', title: '选择' }
							, { field: 'id', width: 100, title: '编号', align: 'center' }
							, { field: 'title', title: '项目名称' }
						]]
					});
					//项目搜索提交
					form.on('submit(search_form)', function (data) {
						selectTable.reload({ where: { keywords: data.field.keywords }, page: { curr: 1 } });
						return false;
					});
				},
				btn: ['确定'],
				yes: function () {
					var checkStatus = table.checkStatus(selectTable.config.id);
					var data = checkStatus.data;
					if (data.length > 0) {
						editPost(id, name, data[0].title, data[0].id);
					}
					else {
						layer.msg('请选择');
						return false;
					}
				}
			})
		},
		//表格单选
		select_type: function (id, name, real_val, data, editPost) {
			let that = this;
			let i = data.length;
			while (i--) {
				if (data[i].id == real_val) {
					data.splice(i, 1);
				}
			}
			if (data.length == 0) {
				layer.msg('无可选择的内容');
				return false;
			}
			layer.open({
				title: '请选择',
				type: 1,
				area: ['500px', '360px'],
				content: '<div style="padding:16px 16px 0"><div id="selectBox"></div></div>',
				success: function () {
					selectable = table.render({
						elem: '#selectBox',
						cols: [
							[{
								type: 'radio',
								title: '选择',
								width: 80
							}, {
								field: 'title',
								title: '选项'
							}]
						],
						data: data
					});
				},
				btn: ['确定'],
				btnAlign: 'c',
				yes: function () {
					var checkStatus = table.checkStatus(selectable.config.id);
					var data = checkStatus.data;
					if (data.length > 0) {
						editPost(id, name, data[0].title, data[0].id);
					}
					else {
						layer.msg('请选择');
					}
				}
			})
		},
		//下拉选择
		dropdown: function (id, name, real_val, data, editPost, is_cancel) {
			let that = this;
			let i = data.length;
			while (i--) {
				if (data[i].id == real_val) {
					data.splice(i, 1);
				}
			}
			if (data.length == 0) {
				layer.msg('无可关联的内容');
				return false;
			}
			if (is_cancel) {
				data.push({ id: 0, title: '<span style="color:#FF5722">取消关联</span>' });
			}
			dropdown.render({
				elem: '#' + name + '_' + id
				, show: true
				, data: data
				, click: function (data, othis) {
					editPost(id, name, data.title, data.id);
				}
			});
		},
		//日期
		date: function (id, name, real_txt, editPost) {
			let that = this;
			laydate.render({
				elem: '#' + name + '_' + id
				, showBottom: false
				, show: true //直接显示
				, value: real_txt
				, done: function (value, date) {
					editPost(id, name, value, value);
				}
			});
		},
		editor:function (id, name, real_txt, editPost){
			let that = this,index = Date.now();;
			layer.open({
				type: 1,
				title: '请输入内容',
				zIndex:20,
				area: ['900px', '600px'],
				content: '<div style="padding:5px;"><textarea class="layui-textarea" id="goguEditTextarea'+index+'" style="width: 100%;">' + real_txt + '</textarea></div>',
				btnAlign: 'c',
				btn: ['提交保存'],
				success:function(){					
					var edit = tinymce.render({
						selector: "#goguEditTextarea"+index,
						images_upload_url: '/api/index/upload/sourse/tinymce',//图片上传接口
						height: 480
					});
				},
				yes: function () {
					let newval = tinyMCE.editors['goguEditTextarea'+index].getContent();
					if (newval != '') {
						editPost(id, name, newval, newval);
					} else {
						layer.msg('请输入内容');
					}
				}
			})

		}
	};
	exports('oaEdit', obj);
});  