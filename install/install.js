var KissgoInstaller = {
    validate : {
        'setup' : false,
        'database' : false
    },
    done : false,
    nUI : null,
    installing : false,
    envOk : false,
    url : window.location.href.replace(/\?.*/, ''),
    init : function(nUI) {
        var me = this;
        this.nUI = nUI;
        $('.nav-tabs li a').click(function(e) {
            e.preventDefault();
            if (!me.envOk) {
                return;
            }
            if (me.installing) {
                return;
            }
            if (me.done) {
                window.location.href = $('body').attr('data-siteurl');
                return;
            }
            var step = $(this).attr('rel');
            if (!step) {
                return;
            }
            if (step == 'overview' && (!me.validate['setup'] || !me.validate['database'])) {
                return;
            }
            var currentStep = $('.nav-tabs li.active a').attr('rel');
            if (step != currentStep) {
                me.loadPage(step, currentStep);
            }
        });        
        this.viewPage('env');
    },
    viewPage : function(step, check) {
        var me = this;
        if (step == 'welcome') {
            return;
        }
        if (check) {
            me.envOk = $('table span.label-danger').length == 0;
        }

        $('#body').empty();
        $.ajax(me.url + '?step=' + step, {
            type : 'get',
            dataType : 'html',
            success : function(data) {
                $('#body').html(data);
                $('.nav-tabs li').removeClass('active');
                if (step == 'install' || step == 'done') {
                    $('#' + step).parent().addClass('active');
                } else {
                    $('.nav-tabs li a[rel="' + step + '"]').parent().addClass('active');
                }
                me.installing = false;
                me.done = false;
                if (step == 'install') {
                    $('#progressbar').data('pbVal', 0);
                    me.installing = true;
                    me.process('init');
                } else if (step == 'done') {
                    me.done = true;
                }
            }
        });
    },
    loadPage : function(step, pre) {
        if (pre == 'setup' || pre == 'database') {
            var data = [], me = this;
            var form = $('#' + pre + 'Form');
            if (form.length == 1) {
                data = form.serializeArray();
            }
            data.push({
                name : 'step',
                value : 'save'
            });
            data.push({
                name : 'op',
                value : pre
            });
            $.ajax(me.url, {
                type : 'post',
                dataType : 'json',
                data : data,
                success : function(data) {
                    me.validate[pre] = false;
                    if (data.success) {
                        me.viewPage(step);
                        me.validate[pre] = true;
                    } else if (data.errors) {
                        me.nUI.setFormError(data.errors, false, true);
                    } else if (data.msg) {
                        alert(data.msg);
                    }
                }
            });
        } else {
            this.viewPage(step);
        }
    },
    process : function(step, data) {
        var me = this, progressbar = $('#progressbar');
        data = data || {};
        data['step'] = 'process';
        data['op'] = step;
        $.ajax(me.url, {
            type : 'post',
            dataType : 'json',
            data : data,
            success : function(data) {
                if (data.success) {
                    $('#result tr.status_' + step).addClass('suc');
                    var value = progressbar.data('pbVal') + data.progres;
                    progressbar.data('pbVal', value);
                    progressbar.css('width', value + "%");
                    $('#process').html(value + "%");
                    if (data.next != 'done') {
                        $('#result').prepend($('<tr class="status_' + data.next + '"><td>' + data.name + '</td><td class="status"></td></tr>'));
                        me.process(data.next, data.data || {});
                    } else {
                        setTimeout(function() {
                            me.viewPage('done');
                        }, 2000);
                    }
                } else {
                    $('#result tr.status_' + step).addClass('err');
                    alert(data.msg);
                    me.viewPage('done');
                }
            },
            error : function() {
                $('#result tr.status_' + step).addClass('err');
                me.viewPage('done');
            }
        });
    }
};
