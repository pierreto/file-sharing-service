// share.js
// author: Pierre Quang Linh To
// Show share code
// Allow user to copy the sharing URL

$(function(){   
    $("#shareButton").click(function(){
        if (confirm('Uploading and removing files will be disable. Confirm file sharing?')) {
            // Show url
            $('#urlContainer').show("slow");

            // Disable share button
            $("#shareButton").prop("disabled", true);

            // Disable file upload
            $("#dragDropFiles").hide("slow");

            // Disable progress bar
            $("#progressBar").hide("slow");

            // Disable file remove
            $("th", event.delegateTarget).remove(":nth-child(4)");
            $("td", event.delegateTarget).remove(":nth-child(4)");
            $("#fileName").css("width", 400);
        }
    });
});

function copyToClipboard() {
    var copyText = $("#shareCode").text();

    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(copyText).select();
    document.execCommand("copy"); // copy to clipboard
    $temp.remove();

    var tooltip = document.getElementById("myTooltip");
    tooltip.innerHTML = "Copied link";
}

function copyToClipboardOnMouse() {
    var tooltip = $("#myTooltip");
    tooltip.innerHTML = "Copy to clipboard";
}