var LMS = {
	initialize: function() {

		if (!jQuery().jGrowl) {
			document.write('<script src="' + LMSConfig.jsUrl + 'lib/jquery.jgrowl.min.js"><\/script>');
		}

		if (!jQuery().ajaxForm) {
			document.write('<script src="' + LMSConfig.jsUrl + 'lib/jquery.form.min.js"><\/script>');
		}

		$(document).on('click', '.modules a, .tests a', function(e) {
			e.preventDefault();
			LMS.module.loadModule($(this).data('id'));
		});

		$(document).on('click', '.close', function() {
			LMS.module.closePlayer();
		});

		$(document).on('submit', '#studentForm', function(e) {
			LMS.student.create(this, $(this).find('[type="submit"]')[0]);
			e.preventDefault();
			return false;
		});

		$(document).on('click', '#updateStudentForm a', function(e) {
			e.preventDefault();
			var action = $(this).data('action');
			$('input[name="action"]').val(action);
			$(this).closest('form').submit();
		});

		$(document).on('submit', '#updateStudentForm', function(e) {
			LMS.student.process(this);
			e.preventDefault();
			return false;
		});

		$(document).on('change', '.uploadForm input[type="file"]', function(e) {
			LMS.file.upload($(this).parent());
			e.preventDefault();
			return false;
		});

		$(document).on('click', '.delete_img', function(e) {
			if ($(this).children('img').first().attr('src')) {
				LMS.file.delete($(this));
			}
			e.preventDefault();
		});
	}

	,module: {
		loadModule: function(module_id, course_id) {
			$.ajax({
				data: {action: 'module/load', id: module_id}
				,url: LMSConfig.actionUrl
				,dataType: 'json'
				,method: 'POST'
				,success: function(response) {
					var element = $('#module_content');
					if (response.success) {
						$('#bb').modal('show');
						element.html(response.data.module).show();
						//LMS.module.regHandler();
					}
					else {
						element.html('').hide();
						LMS.Message.error(response.message);
					}
				}
			});
		}

		,regHandler: function() {
		
		}

		,closePlayer: function() {
			$('.modal').hide();
			$('#module_content').hide();
		}
	}

	,test: {
		processResults: function(awarded, passing) {
			$.ajax({
				data: {action: 'test/process_results', awarded: awarded, passing: passing}
				,url: LMSConfig.actionUrl
				,dataType: 'json'
				,success: function(response) {
					var element = $('#module_content');
					if (response.success) {
						LMS.Message.success(response.message);
					}
					else {
						LMS.Message.error(response.message);
					}
				}
			});
		}
	}

	,student: {
		create: function(form, button) {
			$(form).ajaxSubmit({
				data: {action: 'student/create'}
				,url: LMSConfig.actionUrl
				,form: form
				,button: button
				,dataType: 'json'
				,beforeSubmit: function() {
					$(button).attr('disabled','disabled');
					$(form).find(".has-error").removeClass("has-error").find(".help-block").text('');
					return true;
				}
				,success: function(response) {
					if (response.success) {
						var data = response.data;
						LMS.Message.success(response.message);
						var table = $('#users').DataTable();
						var inner = $(data.output).html();
						var id = $(data.output).attr('id');
						var row = $('<tr>').attr('id', id).append(inner);
						table.row.add(row).draw();
						//$('#updateStudentForm').prepend(response.data['output']);
					}
					else {
						if (response.data) {
							var i, field;
							for (i in response.data) {
								field = response.data[i];
								LMS.Message.error(field.message);
								//$(form).find('[name="' + field.field + '"]').parent().addClass('has-error').find('.help-block').text(field.message)
							}
						}
						LMS.Message.error(response.message);
					}
					$(button).removeAttr('disabled');
				}
			});
		}
		,process: function(form){
			$(form).ajaxSubmit({
				url: LMSConfig.actionUrl
				,form: form
				,dataType: 'json'
				,success: function(response) {
					if (response.success) {
						LMS.Message.success(response.message);
						var i, id;
						var users = response.data['users'];
						var table = $('#users').DataTable();
						switch(response.data['action']) {
							case 'student/removemultiple':
								for (i in users) {
									id = users[i];
									table.row('#row'+id).remove().draw();
									//$(form).find('input[name="id[]"][value='+id+']').parent().remove();
								}
								break
							case 'student/deactivatemultiple':
								for (i in users) {
									id = users[i];
									table.cell('#status' + id)
										.data('<i class="fa fa-times red"></i><span style="display: none;">0</span>');
								}
								break
							case 'student/activatemultiple':
								for (i in users) {
									id = users[i];
									table.cell('#status' + id)
										.data('<i class="fa fa-check green"></i><span style="display: none;">1</span>');
								}
								break
						}
					}
					else {
						LMS.Message.error(response.message);
					}
				}
			});
		}
	}

	,file: {
		upload: function(form) {
			$(form).ajaxSubmit({
				data: {action: 'file/upload'}
				,url: LMSConfig.actionUrl
				,form: form
				,dataType: 'json'
				,success: function(response) {
					if (response.success) {
						LMS.Message.success(response.message);
						data = response.data;
						$(form).parent().find('img').attr('src', data.location + "?" + new Date().getTime());
						setTimeout(location.reload(), 2000);
					}
					else {
						LMS.Message.error(response.message);
					}
				}
			});			
		}
		,delete: function(link) {
			var params = $(link).attr('href').split('?');
			$.ajax({
				url: LMSConfig.actionUrl + '?' + params[1]
				,dataType: 'json'
				,success: function(response) {
					if (response.success) {
						LMS.Message.success(response.message);
						$(link).children('img').first().attr('src', '');
						$(link).children('img:nth-child(2)').remove();
						setTimeout(location.reload(), 2000);
					}
					else {
						LMS.Message.error(response.message);
					}
				}
			});
		}
	}

	,utils: {
		calcCenter: function(element) {
			var width = element.width();
			var w = window.innerWidth;
			var leftMargin = (w - width) / 2;
			element.css('margin-left', leftMargin+'px');
		}
	}
};

LMS.Message = {
	success: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'lms-message-success',position: 'center',});
		}
	}
	,error: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'lms-message-error', position: 'center',});
		}
	}
	,info: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'lms-message-info', position: 'center',});
		}
	}
	,close: function() {
		$.jGrowl('close');
	}
};

LMS.initialize();