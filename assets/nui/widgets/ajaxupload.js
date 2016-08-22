(function(nUI, $) {
	nUI.uploaders = new nUI.Map();
	var defaultOpts = {
		c : false,
		runtimes : 'html5,html4',
		max_file_count : 1,
		chunk_size : '512k',
		chunks : true,
		multi_selection : false,
		max_file_size : '20mb',
		filters : [ {
			title : "*.*",
			extensions : "jpg,gif,png,jpeg"
		} ]
	};

	if (typeof (plupload) != undefined) {
		plupload.addI18n({
			"Init error." : "初始化错误。",
			"HTTP Error." : "HTTP 错误。",
			"File size error." : "文件大小错误。",
			"Error: Invalid file extension:" : "错误：无效的文件扩展名:",
			"Runtime ran out of available memory." : "运行时已消耗所有可用内存。",
			"File extension error." : "文件扩展名错误。",
			"Error: File too large:" : "错误: 文件太大:"
		});
	}
	var previewTpl = '<div class="up-file"><input class="f-name" type="hidden"/><input class="f-size" type="hidden"/><input class="f-width" type="hidden"/><input class="f-height" type="hidden"/>\
						<a class="close">×</a><div class="img-wrap"><img class="f-img"/></div>\
						<div class="f-notes">\
						<label class="input">\
							<input type="text" class="input-xs"/>\
						</label>\
						<label class="textarea"><i class="icon-prepend fa fa-info"></i>\
							<textarea class="custom-scroll" rows="2"></textarea>\
						</label>\
						<div class="progress progress-micro">\
							<div style="width: 0;" role="progressbar" class="progress-bar"></div>\
						</div>\
						</div>\
					</div>';
	var PFile = function(file, id) {
		this.file = file;
		this.id = id;
		this.name = file.name;
		this.previewable = /.+\.(png|gif|jpg|jpeg|bmp)/i.test(this.name);
		this.preview = function() {
			if (this.previewable) {
				var img = $('#' + this.id + ' .f-img').get(0);
				var reader = new FileReader();
				reader.onload = function(evt) {
					img.src = evt.target.result;
				};
				reader.readAsDataURL(this.file);
			} else {
				$('#' + this.id + ' .f-img').attr('src', KissCms.assetsURL + 'nopic.gif');
			}
		};
	};
	function prepareForPreview(ipt, files) {
		if (ipt.files && ipt.files[0]) {
			for ( var i in ipt.files) {
				for ( var j in files) {
					if (files[j].name == ipt.files[i].name) {
						files[j].pfile = new PFile(ipt.files[i], files[j].id);
						break;
					}
				}
			}
		}
	}
	var SingleUploader = function(me, uploader) {
		var cls = 'txt-color-orange fa-spinner fa-spin';
		var name = me.inputElm.attr('name'),size=name+'_size',width = name+'_width',height=name+'_height';
		if(/\[[^\]]+\]/i.test(name)){
			size = name.replace('[','_size[');
			width = name.replace('[','_width[');
			height = name.replace('[','_height[');
		}
		var sizer = $('<input type="hidden" name="'+size+'" value=""/>');
		var widthr = $('<input type="hidden" name="'+width+'" value=""/>');
		var heightr = $('<input type="hidden" name="'+height+'" value=""/>');
		me.inputElm.after(sizer);
		me.inputElm.after(widthr);
		me.inputElm.after(heightr);
		uploader.bind('FilesAdded', function(up, files) {
			var fs = up.files;
			up.disableBrowse(true);
			me.pImg.hide();
			me.uploaderElm.addClass('disabled');
			me.uploaderElm.find('i').removeClass('fa-cloud-upload').addClass(cls);
			for ( var j in fs) {
				if (fs[j].id != files[0].id) {
					up.removeFile(fs[j]);
				}
			}
			me.uploader.start();
		});
		
		uploader.bind('FileUploaded', function(up, file, resp) {
			up.disableBrowse(false);
			me.uploaderElm.removeClass('disabled');
			me.uploaderElm.find('i').addClass('fa-cloud-upload').removeClass(cls);
			if (file.status == plupload.DONE) {
				try {
					var result = eval('(' + resp.response + ')');
					var rst = result.result;
					if (rst) {
						if (me.fullURL) {
							me.inputElm.val(result.url).change();
						} else {
							me.inputElm.val(result.url1).change();
						}
						sizer.val(result.size);
						widthr.val(result.width);
						heightr.val(result.height);
					} else if (result.error) {
						nUI.errorTip(result.error.message);
					} else {
						nUI.errorTip('无法解析服务器响应:<br/>' + resp.response);
					}
				} catch (e) {
					nUI.errorTip('无法解析服务器响应:<br/>' + resp.response);
				}
			} else {
				nUI.errorTip('出错啦.');
			}
		});

		uploader.bind('Error', function(up, file) {
			up.disableBrowse(false);
			me.uploaderElm.removeClass('disabled');
			me.uploaderElm.find('i').addClass('fa-cloud-upload').removeClass(cls);
			if(file.response){
				var result = eval('(' + file.response + ')');
				var rst = result.error;
				nUI.errorTip(rst.message);
			}else{
				nUI.errorTip(file.message);
			}
		});
	};
	var MultiUploader = function(me, uploader, elem) {
		var $this = this;

		this.wrapper = elem.parents('.m-ajax-uploador');
		this.varName = elem.data('name');
		this.auto = elem.attr('data-auto') ? elem.attr('data-auto') : true;

		if (this.wrapper.length == 0) {
			return;
		}
		var AddFile = function(f) {
			$f = $(previewTpl);
			$f.attr('id', f.id);
			$f.find('a.close').data('fid', f.id);
			$f.find('.f-img').attr('src', me.loadgif);
			$f.find('.f-name').attr('name', $this.varName + '[]');
			$f.find('.f-size').attr('name', $this.varName + '_size[]');
			$f.find('.f-width').attr('name', $this.varName + '_width[]');
			$f.find('.f-height').attr('name', $this.varName + '_height[]');
			var alt = f.name.split('.');
			alt.pop();
			$f.find('.input-xs').attr('name', $this.varName + '_alt[]').val(alt.join('.'));
			$f.find('textarea').attr('name', $this.varName + '_desc[]');
			$f.insertBefore(elem);
			if (f.pfile) {
				f.pfile.preview();
			} else {
				$f.find('.f-img').attr('src', me.nopicURL);
			}
		};
		var RemoveFile = function(id) {
			var f = uploader.getFile(id);
			if (f) {
				uploader.removeFile(f);
			}
		};
		if (this.auto !== true && $this.auto) {
			$(this.auto).on('click', function() {
				uploader.start();
			});
		}
		this.wrapper.on('click', '.up-file a.close', function() {
			var fid = $(this).data('fid');
			if (fid) {
				RemoveFile(fid);
			}
			$(this).parent().remove();
		});
		uploader.bind('FilesAdded', function(up, files) {
			var fileInput = document.getElementById(up.id + '_' + up.runtime);
			prepareForPreview(fileInput, files);
			if ($this.auto === true) {
				up.disableBrowse(true);
			}
			for ( var i in files) {
				AddFile(files[i]);
			}
			if ($this.auto === true) {
				up.start();
			}
		});
		uploader.bind('UploadProgress', function(up, file) {
			var id = file.id;
			$('#' + id + ' .progress-bar').addClass('bg-color-green');
			$('#' + id + ' .progress-bar').css('width', file.percent + '%');
		});
		uploader.bind('FileUploaded', function(up, file, resp) {
			var id = file.id, idx = '#' + id;
			if (file.status == plupload.DONE) {
				$(idx + ' .progress-bar').css('width', '100%').removeClass('progress-bar-primary').addClass('bg-color-green');
				try {
					var result = eval('(' + resp.response + ')');
					var rst = result.result;
					if (rst) {
						$(idx + ' .f-name').val($this.fullURL ? result.url : result.url1);
						$(idx + ' .f-size').val(result.size);
						$(idx + ' .f-width').val(result.width);
						$(idx + ' .f-height').val(result.height);
					} else{
						$(idx + ' .progress-bar').removeClass('bg-color-green').addClass('bg-color-red');
					}
				} catch (e) {

				}
			} else {
				$(idx + ' .progress-bar').removeClass('bg-color-green').addClass('bg-color-red');
			}
		});
		uploader.bind('UploadComplete', function(up, files) {
			up.disableBrowse(false);
			for ( var i in up.files) {
				var f = up.getFile(up.files[i].id);
				if (f) {
					up.removeFile(f);
				}
			}
			up.files = [];
			up.refresh();
			if (me.cb && $.isFunction(me.cb)) {
				me.cb.call(me, up);
			}
		});
		uploader.bind('Error', function(up, file) {
			var id = file.file?file.file.id:file.id;
			$('#' + id + ' .progress-bar').removeClass('bg-color-green').addClass('bg-color-red');
			$('#' + id + ' input.f-name').remove();
			if(file.response && typeof console){
				var result = eval('(' + file.response + ')');
				var rst = result.error;
				console.log(rst.message);
			}
		});
	};

	var nuiAjaxUpload = function(uploader) {
		var me = this;
		uploader.data('uploaderObj', this);

		if (!uploader.attr('for')) {
			return;
		}
		var oup = nUI.uploaders.get(uploader.attr('for'), this);
		if (oup) {
			oup.uploader.stop();
			oup.uploader.unbindAll();
			nUI.uploaders.remove(uploader.attr('for'));
			oup = null;
		}
		this.cb = uploader.data('UploadCompleteCallback');
		this.uploaderElm = uploader;
		this.mediaURL = KissCms.MediaURL;
		this.nopicURL = KissCms.assetsURL + 'nopic.gif';
		this.loadgif = KissCms.assetsURL + 'bootstrap/img/ajax-loader.gif';
		this.inputElm = $(uploader.attr('for'));
		this.pImg = $('a[for="' + this.inputElm.attr('id') + '"]');
		if (this.inputElm.length > 0 && this.pImg.length > 0) {
			this.inputElm.change(function() {
				var src = $(this).val();
				if ($.superbox && src && /.+\.(png|gif|jpg|jpeg|bmp)/i.test(src)) {
					me.pImg.attr('href', me.getPreviewURL(src));
					me.pImg.show();
				} else {
					me.pImg.hide();
				}
			});
			if ($.superbox && !$(this).data('superboxInited')) {
				if (me.pImg.attr('rel') != 'superbox[image]') {
					me.pImg.attr('rel', 'superbox[image]');
				}
				$(this).data('superboxInited', true);
			}
			if (this.inputElm.val()) {
				this.inputElm.change();
			}
		}
		this.fullURL = uploader.attr('data-full-url') === 'true' ? true : false;
		var opts = {
			'drop_element' : this.inputElm
		};
		if (uploader.attr('data-max-file-size')) {
			opts.max_file_size = uploader.attr('data-max-file-size');
		}
		if (uploader.attr('data-extensions')) {
			opts.filters = [ {
				title : "*.*",
				extensions : uploader.attr('data-extensions')
			} ];
		}
		if (uploader.data('multi-upload')) {
			opts.max_file_count = 100;
			opts.chunks = true;
			opts.chunk_size = '512k';
			opts.multi_selection = true;
		}
		this.options = $.extend({}, defaultOpts, opts);
		if (typeof (plupload) != undefined) {
			var water = uploader.data('water'), locale = uploader.data('locale'), ut = uploader.data('usertype');
			this.options.browse_button = uploader.attr('id');
			this.options.url = KissCms.AjaxUploadURL;

			this.options.multipart_params = {
				'water' : water === 0 ? 0 : 1,
				'locale' : locale ? locale : 0,
				'userType' : (ut ? ut : '')
			};

			this.uploader = new plupload.Uploader(this.options);
			this.uploader.init();
			nUI.uploaders.put(uploader.attr('for'), this);
			if (this.options.multi_selection) {
				var mup = new MultiUploader(this, this.uploader, uploader);
			} else {
				
				var sup = new SingleUploader(this, this.uploader, uploader);
			}
		}
	};
	nuiAjaxUpload.prototype.getPreviewURL = function(url) {
		if (/^(https?|ftps?):\/\/.+/.test(url)) {
			return url;
		} else {
			return this.mediaURL + url;
		}
	};
	$.fn.nuiAjaxUpload = function(options) {
		if (KissCms.AjaxUploadURL) {
			return $(this).each(function(i, elm) {
				var uploader = $(elm);
				if (!uploader.data('uploaderObj')) {
					var up = new nuiAjaxUpload(uploader);
				}
			});
		} else {
			return $(this);
		}
	};
})(window.nUI, jQuery);