var Discussion = function () {

    var content = $('.inbox-content');
    var loading = $('.inbox-loading');
    var listListing = '';
    var unreadMemo = null;
    
    var readInboxState = function (id){
        $.ajax({
            type: "POST",
            url: '/memo/read',
            dataType: 'json',
            data: {id: id},
            success: function(res) 
            {
                if ($('table', content).size() > 0){
                    var tabel = $('table', content).DataTable();
                    tabel.draw(false);
                    countUnread(true, 'menu');
                }
                countUnread(true, 'header');
            }
        });
    }
    
    var countUnread = function(isWrite, contextMenu){
        $.ajax({
            type: "GET",
            url: '/memo/countunread',
            dataType: 'json',
            success: function(res) 
            {
                if (typeof(isWrite) != "undefined" && isWrite){
                    if (contextMenu == 'menu')
                        writeInbox(res);
                    else if (contextMenu == 'header')
                        writeInboxHeader(res);
                }
                unreadMemo = res;
                return res;
            }
        });
    }
    
    var writeInbox = function(jml){
        if (jml > 0){
            $("li.inbox a.btn").text('Inbox ('+jml+')');
        }else{
            $("li.inbox a.btn").text('Inbox ');
        }
    }
    
    var writeInboxHeader = function(jml){
        var containerMenu = $("#header_inbox_bar");
        $("span.badge", containerMenu).remove();
        $("li.external h3 span", containerMenu).text(jml+" Unread");
        if (jml > 0){
            $("a", containerMenu).first().append($('<span>').addClass('badge').addClass('badge-default').text(jml));
        }
        
        $.ajax({
            type: "GET",
            url: '/memo/unread',
            dataType: 'json',
            success: function(res) 
            {
                var result = '';
                for (var i = 0; i < res.length; i++){
                    result += '<li>'+
        					'<a href="javascript:Memo.readMessage('+res[i].id+');">'+
        					'<span class="photo">'+
        					'<img src="'+res[i].memo_from.image+'" class="img-circle" alt="">'+
        					'</span>'+
        					'<span class="subject">'+
        					'<span class="from">'+
        					res[i].memo_from.name+' </span>'+
        					'<span class="time">'+res[i].tgl_memo+' </span>'+
        					'</span>'+
        					'<span class="message">'+
        					res[i].memo_subject+' </span>'+
        					'</a>'+
        				'</li>';
                }
                $("ul.dropdown-menu-list", containerMenu).html(result);
            }
        });
    }

    var loadInbox = function (el, name) {
        var url = '/discussion/inbox';
        var urlDataTable = '/discussion/inbox/getdatatable';
        var kolom = [
                        {data: 'from', name: 'from'},
                        {data: 'subject', name: 'subject'},
                        {className: "text-right", data: 'time', name: 'time'}
                    ];
        var title = $('.inbox-nav > li.' + name + ' a').attr('data-title');
        if (title == 'Sent'){
            url = '/discussion/sent';
            urlDataTable = '/discussion/sent/getdatatable';
            kolom = [
                    {data: 'to', name: 'to'},
                    {data: 'discussion_subject', name: 'discussion_subject'},
                    {className: "text-right", data: 'time', name: 'time'}
                ];
        }
        listListing = name;

        loading.show();
        content.html('');
        toggleButton(el);

        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            success: function(res) 
            {
                toggleButton(el);

                $('.inbox-nav > li.active').removeClass('active');
                $('.inbox-nav > li.' + name).addClass('active');
                $('.inbox-header > h1').text(title);

                loading.hide();
                content.html(res);
                if (Layout.fixContentHeight) {
                    Layout.fixContentHeight();
                }
                Metronic.initUniform();
                $('table', content).DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": urlDataTable,
                        "type": "POST"
                    },
                    "columns": kolom,
                    "order": [
                        [2, 'desc']
                    ],
                    "lengthMenu": [
                        [10, 20, -1],
                        [10, 20, "All"]
                    ],
                    "pageLength": 20,
                    "dom": "<'row'<'col-md-6 col-sm-12 title'><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                    "initComplete": function( settings ) {
                        $(this).closest(".dataTables_wrapper").find("div.title").append($("<h1>").addClass("no-margin").text(title));
                    },
                    "createdRow": function( row, data, dataIndex ) {
                        if (title == 'Inbox' && data.read_at == null){
                            $(row).addClass( 'unread' );
                        }
                        if (title == 'Inbox'){
                            $('td', row).addClass('view-message');
                        }else if (title == 'Sent'){
                            $('td', row).addClass('view-message-sent');
                        }
                        $(row).attr('data-messageid', data.id);
                    }
                });
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
                toggleButton(el);
            },
            async: false
        });
    }

    var loadMessage = function (el, src) {

        var message_id = el.parent('tr').attr("data-messageid");
        var url = '/discussion/view/'+message_id;

        loading.show();
        content.html('');
        toggleButton(el);  
        
        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            data: {src: src},
            success: function(res) 
            {
                toggleButton(el);
                $('.inbox-header > h1').text('View Message');

                loading.hide();
                content.html(res);
                Layout.fixContentHeight();
                Metronic.initUniform();
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
                toggleButton(el);
            },
            async: false
        });
    }
    
    var openMessage = function (id){
        readInboxState(id);
        window.open('/memo/message/'+id, '_blank', 'fullscreen=yes');
    }

    var loadReply = function (el, src) {
        var messageid = $(el).attr("data-messageid");
        var url = '/discussion/reply/' + messageid;
        
        loading.show();
        content.html('');
        toggleButton(el);

        // load the form via ajax
        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            data: {src: src},
            success: function(res) 
            {
                toggleButton(el);

                $('.inbox-nav > li.active').removeClass('active');
                $('.inbox-header > h1').text('Reply');

                loading.hide();
                content.html(res);

                handleCCInput();
                var the = $('.inbox-compose .mail-to .inbox-cc');
                var input = $('.inbox-compose .input-cc');
                if ($('select :selected', input).length == 0){
                    the.show();
                    input.hide();
                }            

                initWysihtml5();
                Layout.fixContentHeight();
                Metronic.initUniform();
                
                $(".select2", content).select2();
                $(".select2-container-multi .select2-choices").css('border', '0');
                
                var form = $('form', content);
                var error = $('.alert-danger', form);
                var success = $('.alert-success', form);
            
                form.validate({
                    errorElement: 'span',
                    errorClass: 'help-block help-block-error',
                    focusInvalid: false,
                    ignore: "",
                    rules: {
                        subject: {
                            required: true
                        },
                        message: {
                            required: true
                        },
                        "receiver[]": {
                            required: true
                        }
                    },
            
                    invalidHandler: function (event, validator) {              
                        success.hide();
                        error.show();
                        Metronic.scrollTo(error, -200);
                    },
    
                    errorPlacement: function (error, element) { // render error placement for each input type
                        if (element.parent(".input-group").size() > 0) {
                            error.insertAfter(element.parent(".input-group"));
                        } else if (element.attr("data-error-container")) { 
                            error.appendTo(element.attr("data-error-container"));
                        } else if (element.parents('.radio-list').size() > 0) { 
                            error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                        } else if (element.parents('.radio-inline').size() > 0) { 
                            error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                        } else if (element.parents('.checkbox-list').size() > 0) {
                            error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                        } else if (element.parents('.checkbox-inline').size() > 0) { 
                            error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                        } else {
                            error.insertAfter(element); // for other inputs, just perform default behavior
                        }
                    },
            
                    highlight: function (element) {
                        $(element)
                            .closest('.inbox-form-group').removeClass("has-success").addClass('has-error');   
                    },
            
                    unhighlight: function (element) {
                        $(element)
                            .closest('.inbox-form-group').removeClass("has-error").addClass('has-success');
                    },
            
                    success: function (label, element) {
                        $(element).closest('.inbox-form-group').removeClass('has-error').addClass('has-success');
                    },
            
                    submitHandler: function (form) {
                        $.ajax({
                            type: "POST",
                            url: '/discussion/send',
                            dataType: "json",
                            data: $(form).serialize(),
                            success: function(data)
                            {
                                $('.inbox-nav > li.inbox > a').click();
                            }
                        });
                    }
                });
                $('select', form).change(function () {
                    form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
                });
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
                toggleButton(el);
            },
            async: false
        });
    }

    var initWysihtml5 = function () {
        $('.inbox-wysihtml5').wysihtml5({"color": true, "stylesheets": ["/assets/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"]});
    }

    var loadCompose = function (el) {
        var url = '/discussion/compose';

        loading.show();
        content.html('');
        toggleButton(el);

        // load the form via ajax
        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            success: function(res) 
            {
                toggleButton(el);

                $('.inbox-nav > li.active').removeClass('active');
                $('.inbox-header > h1').text('Compose');

                loading.hide();
                content.html(res);

                initWysihtml5();

                $('.inbox-wysihtml5').focus();
                Layout.fixContentHeight();
                Metronic.initUniform();
                
                $(".select2", content).select2();
                $(".select2-container-multi .select2-choices").css('border', '0');
                
                var form = $('form', content);
                var error = $('.alert-danger', form);
                var success = $('.alert-success', form);
            
                form.validate({
                    errorElement: 'span',
                    errorClass: 'help-block help-block-error',
                    focusInvalid: false,
                    ignore: "",
                    rules: {
                        subject: {
                            required: true
                        },
                        message: {
                            required: true
                        },
                        "receiver[]": {
                            required: true
                        }
                    },
            
                    invalidHandler: function (event, validator) {              
                        success.hide();
                        error.show();
                        Metronic.scrollTo(error, -200);
                    },
    
                    errorPlacement: function (error, element) { // render error placement for each input type
                        if (element.parent(".input-group").size() > 0) {
                            error.insertAfter(element.parent(".input-group"));
                        } else if (element.attr("data-error-container")) { 
                            error.appendTo(element.attr("data-error-container"));
                        } else if (element.parents('.radio-list').size() > 0) { 
                            error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                        } else if (element.parents('.radio-inline').size() > 0) { 
                            error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                        } else if (element.parents('.checkbox-list').size() > 0) {
                            error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                        } else if (element.parents('.checkbox-inline').size() > 0) { 
                            error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                        } else {
                            error.insertAfter(element); // for other inputs, just perform default behavior
                        }
                    },
            
                    highlight: function (element) {
                        $(element)
                            .closest('.inbox-form-group').removeClass("has-success").addClass('has-error');   
                    },
            
                    unhighlight: function (element) {
                        $(element)
                            .closest('.inbox-form-group').removeClass("has-error").addClass('has-success');
                    },
            
                    success: function (label, element) {
                        $(element).closest('.inbox-form-group').removeClass('has-error').addClass('has-success');
                    },
            
                    submitHandler: function (form) {
                        $.ajax({
                            type: "POST",
                            url: '/discussion/send',
                            dataType: "json",
                            data: $(form).serialize(),
                            success: function(data)
                            {
                                $('.inbox-nav > li.inbox > a').click();
                            }
                        });
                    }
                });
                $('select', form).change(function () {
                    form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
                });
    
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
                toggleButton(el);
            },
            async: false
        });
    }

    var handleCCInput = function () {
        var the = $('.inbox-compose .mail-to .inbox-cc');
        var input = $('.inbox-compose .input-cc');
        the.hide();
        input.show();
        $('.close', input).click(function () {
            input.hide();
            the.show();
            $("select :selected", input).removeAttr('selected').prop('selected', false);
            $("select", input).select2().trigger('change');
            $(".select2-container-multi .select2-choices").css('border', '0');
        });
    }

    var toggleButton = function(el) {
        if (typeof el == 'undefined') {
            return;
        }
        if (el.attr("disabled")) {
            el.attr("disabled", false);
        } else {
            el.attr("disabled", true);
        }
    }

    return {
        //main function to initiate the module
        init: function () {

            // handle compose btn click
            $('.inbox').on('click', '.compose-btn a', function () {
                loadCompose($(this));
            });

            // handle discard btn
            $('.inbox').on('click', '.inbox-discard-btn', function(e) {
                e.preventDefault();
                loadInbox($(this), listListing);
            });

            // handle reply and forward button click
            $('.inbox').on('click', '.reply-btn', function () {
                loadReply($(this), 'reply');
            });
            $('.inbox').on('click', '.reply-fwd', function () {
                loadReply($(this), 'fwd');
            });

            // handle view message
            $('.inbox-content').on('click', '.view-message', function () {
                loadMessage($(this), 'inbox');
            });
            $('.inbox-content').on('click', '.view-message-sent', function () {
                loadMessage($(this), 'sent');
            });

            // handle inbox listing
            $('.inbox-nav > li.inbox > a').click(function () {
                loadInbox($(this), 'inbox');
            });

            // handle sent listing
            $('.inbox-nav > li.sent > a').click(function () {
                loadInbox($(this), 'sent');
            });

            // handle draft listing
            $('.inbox-nav > li.draft > a').click(function () {
                loadInbox($(this), 'draft');
            });

            // handle trash listing
            $('.inbox-nav > li.trash > a').click(function () {
                loadInbox($(this), 'trash');
            });

            //handle compose/reply cc input toggle
            $('.inbox-content').on('click', '.mail-to .inbox-cc', function () {
                handleCCInput();
            });

            //handle compose/reply bcc input toggle
            $('.inbox-content').on('click', '.mail-to .inbox-bcc', function () {
                handleBCCInput();
            });

            //handle loading content based on URL parameter
            if (Metronic.getURLParameter("a") === "view") {
                loadMessage();
            } else if (Metronic.getURLParameter("a") === "compose") {
                loadCompose();
            } else {
               $('.inbox-nav > li.inbox > a').click();
            }

        }

    };

}();