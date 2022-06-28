jQuery(document).ready(function($) {

    $(".payamito-awesome-support-open-modal ").click(function() {
        debugger;
        $('#payamito-awesome-support-modal').modal();
    })

    $('.payamito-tag-modal').click(function(){
        $(this).CopyToClipboard();
     
        });

        $('.payamito-tag-modal').jTippy({trigger:'click' ,theme: 'green',position:'bottom', size: 'small',title:'کپی شد'});
});