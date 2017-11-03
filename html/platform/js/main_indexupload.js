/*
 * jQuery File Upload Plugin JS Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* global $, window */

$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials:true},
        url: 'server/php/indexUploadHandler.php' //サーバーサイドのスクリプトの場所
        /*dataType:'json',
          done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo(document.body);
            });
        }*/


    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );


     $('.calc').on('click',function(){
        var calcStart=$.now();
            $.ajax({
                
                url:'server/php/calculate.php',
                data:{
                    button:'indexUpload'
                },
                beforeSend: function(data){
                 $('.calc').attr('disabled',true);
                 $("#calcCheck").text("Calculating...");
                 },
                 complete: function(data) {
                // ボタンを有効化し、再送信を許可
                $('.calc').attr('disabled', false);
                },
                success:function(data) {
                    var endTime=$.now()-calcStart;
                    var now = new Date();
                    var y = now.getFullYear();
                    var m = now.getMonth() + 1;
                    var d = now.getDate();
                    var h = now.getHours();
                    var mi = now.getMinutes();
                    var s = now.getSeconds();
                    var nowTime=y + "/" + m + "/" + d + " " + h + ":" + mi + ":" + s ;
                    $('#getContent').html('<ul></ul>');
                    $('#getContent').append('<li>処理時間:'+endTime/1000+'[s]</li>');
                    $('#getContent').append('<li>最終更新:'+nowTime+'</li>');
                    $('#calcCheck').text('Calc success!');

                },
                error:function(data){
                    $('#calcCheck').text('calc failed');

                }
            });
        });

   if (window.location.hostname === 'blueimp.github.io') {
        // Demo settings:
        $('#fileupload').fileupload('option', {
            url: '//jquery-file-upload.appspot.com/',
            // Enable image resizing, except for Android and Opera,
            // which actually support image resizing, but fail to
            // send Blob objects via XHR requests:
            disableImageResize: /Android(?!.*Chrome)|Opera/
                .test(window.navigator.userAgent),
            maxFileSize: 999000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
        });
        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                url: '//jquery-file-upload.appspot.com/',
                type: 'HEAD'
            }).fail(function () {
                $('<div class="alert alert-danger"/>')
                    .text('Upload server currently unavailable - ' +
                            new Date())
                    .appendTo('#fileupload');
            });
        }
    } else {
        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
           // xhrFields: {withCredentials: false},
            url: $('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});

        });
    }

});
