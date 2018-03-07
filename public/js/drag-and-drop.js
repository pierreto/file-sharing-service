// drag-and-drop.js
// author: Pierre Quang Linh To
// Drag-and-drop behavior and file select
// Allow user to remove file before sharing

'use strict';

;( function( $, window, document, undefined )
{
    // feature detection for drag&drop upload (check if browser support HTML5 Drag and Drop API)
    var isAdvancedUpload = function()
        {
            var div = document.createElement( 'div' );
            return ( ( 'draggable' in div ) || ( 'ondragstart' in div && 'ondrop' in div ) ) && 'FormData' in window && 'FileReader' in window;
        }();


    // applying the effect for every form
    $( '.box' ).each( function()
    {
        var $form		 = $( this ),
            $input		 = $form.find( 'input[type="file"]' ),
            droppedFiles = false,
            $table       = $("#filesUploaded")

        // letting the server side to know we are going to make an Ajax request
        $form.append( '<input type="hidden" name="ajax" value="1" />' );

        // automatically submit the form on file select
        $input.on( 'change', function( e )
        {
            $form.trigger( 'submit' );
        });

        // drag&drop files if the feature is available
        if( isAdvancedUpload )
        {
            $form
            .addClass( 'has-advanced-upload' ) // letting the CSS part to know drag&drop is supported by the browser
            .on( 'drag dragstart dragend dragover dragenter dragleave drop', function( e )
            {
                // preventing the unwanted behaviours
                e.preventDefault();
                e.stopPropagation();
            })
            .on( 'dragover dragenter', function() //
            {
                $form.addClass( 'is-dragover' );
            })
            .on( 'dragleave dragend drop', function()
            {
                $form.removeClass( 'is-dragover' );
            })
            .on( 'drop', function( e )
            {
                droppedFiles = e.originalEvent.dataTransfer.files; // the files that were dropped
                $form.trigger( 'submit' ); // automatically submit the form on file drop
            });
        }
        // drag&drop files if the feature is not available 
        else {
            $(".box__dragndrop").get(0).innerText = "Click to choose a file.";
        }

        // if the form was submitted
        $form.on( 'submit', function( e )
        {
            // preventing the duplicate submissions if the current one is in progress
            if( $form.hasClass( 'is-uploading' ) ) return false;

            $form.addClass( 'is-uploading' ).removeClass( 'is-error' );

            if( isAdvancedUpload ) // ajax file upload for modern browsers
            {
                e.preventDefault();

                // gathering the form data
                var ajaxData = new FormData( $form.get( 0 ) );
                if( droppedFiles )
                {
                    $.each( droppedFiles, function( i, file )
                    {
                        ajaxData.append( $input.attr( 'name' ), file );
                    });
                }

                // provide short code
                ajaxData.append("shareCode", $("#shareCode").attr( 'name' ));

                var bar = $('.bar');
                var percent = $('.percent');

                // ajax request
                $.ajax(
                {
                    url: 			$form.attr( 'action' ),
                    type:			$form.attr( 'method' ),
                    data: 			ajaxData,
                    dataType:		'json',
                    cache:			false,
                    contentType:	false,
                    processData:	false,
                    // Init progress bar
                    beforeSend: function() {
                        var percentVal = '0%';
                        bar.width(percentVal);
                        percent.html(percentVal);
                    },
                    // Update progress bar
                    xhr: function() {
                        var xhr = new XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentVal = Math.trunc(((evt.loaded / evt.total) * 100)) + '%';
                                bar.width(percentVal);
                                percent.html(percentVal);
                            }
                       }, false);
                       return xhr;
                    },
                    complete: function()
                    {
                        $form.removeClass( 'is-uploading' );
                    },
                    success: function( data )
                    {
                        $form.addClass( data.success == true ? 'is-success' : 'is-error' );
                        if( !data.success ) alert(data.error);

                        // Show uploaded files
                        $("#fileContainer").show("slow");

                        // Activate share button
                        $("#shareButton").prop("disabled", false);

                        // Files were not dropped, but we still want to show the info
                        if ( !droppedFiles ) {
                            droppedFiles = $input[0].files;
                        }

                        // Show uploaded files info
                        $.each(droppedFiles, function (i, file) {
                            $table.append("<tr>" +
                                            "<td>" + file["name"] + "</td>" +
                                            "<td>" + file["type"] + "</td>" +
                                            "<td>" + formatSizeUnits(file["size"]) + "</td>" +
                                            "<td><button type=\"button\" onclick=\"deleteFile('" + data.save_file_name + "', this)\"><img src=\"img/delete_icon.png\" alt=\"delete_icon\" /></button></td>" +
                                          "</tr>");
                        });

                        // Reset form element (clear files in input)
                        $form.get(0).reset();
                        droppedFiles = false;
                        ajaxData = null;

                        // Prevent form submission
                        e.stopPropagation();
                        e.preventDefault();
                    },
                    error: function()
                    {
                        alert( 'Error. Please, contact the webmaster!' );
                    }
                });
            }
            else // fallback Ajax solution upload for older browsers
            {
                var iframeName	= 'uploadiframe' + new Date().getTime(),
                    $iframe		= $( '<iframe name="' + iframeName + '" style="display: none;"></iframe>' );

                $( 'body' ).append( $iframe );
                $form.attr( 'target', iframeName );

                $iframe.one( 'load', function()
                {
                    var data = $.parseJSON( $iframe.contents().find( 'body' ).text() );
                    $form.removeClass( 'is-uploading' ).addClass( data.success == true ? 'is-success' : 'is-error' ).removeAttr( 'target' );
                    if( !data.success ) alert( data.error );
                    $iframe.remove();
                });
            }
        });

        // Firefox focus bug fix for file input
        $input
        .on( 'focus', function(){ $input.addClass( 'has-focus' ); })
        .on( 'blur', function(){ $input.removeClass( 'has-focus' ); });
    });

})( jQuery, window, document );

// Filesize converter
function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824)
    {
        $bytes = ($bytes / 1073741824).toFixed(2) + ' GB';
    }
    else if ($bytes >= 1048576)
    {
        $bytes = ($bytes / 1048576).toFixed(2) + ' MB';
    }
    else if ($bytes >= 1024)
    {
        $bytes = ($bytes / 1024).toFixed(2) + ' KB';
    }
    else if ($bytes > 1)
    {
        $bytes = $bytes + ' bytes';
    }
    else if ($bytes == 1)
    {
        $bytes = $bytes + ' byte';
    }
    else
    {
        $bytes = '0 bytes';
    }

    return $bytes;
}

// Delete file from server and database
function deleteFile(file, elem) {
    var order = 'filename=' + file;

    // POST request
    $.post("delete.php", order, function(response, status, xhr) {
        if (status == "success") {
            console.log(response);
        }
        else if (status == "error") {
            alert('File could not be deleted.'); 
        }
    });

    // Remove file row
    $(elem).closest("tr").remove();
}