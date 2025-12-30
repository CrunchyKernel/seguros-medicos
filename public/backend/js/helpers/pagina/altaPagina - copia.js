
var altaPagina = altaPagina || {
  
};

;(function($, window, undefined){
    "use strict";

    $.extend(altaPagina, {   
        init: function(){
            
        },
        
    });
    $("#newsletter-builder-area-center-frame-buttons-add").hover(
        function() {
            $("#newsletter-builder-area-center-frame-buttons-dropdown").fadeIn(200);
        }, function() {
            $("#newsletter-builder-area-center-frame-buttons-dropdown").fadeOut(200);
        }
    );
    $("#newsletter-builder-area-center-frame-buttons-dropdown").hover(
        function() {
            $(".newsletter-builder-area-center-frame-buttons-content").fadeIn(200);
        }, function() {
            $(".newsletter-builder-area-center-frame-buttons-content").fadeOut(200);
        }
    );
    $("#add-header").hover(function() {
        $(".newsletter-builder-area-center-frame-buttons-content-tab[data-type='header']").show()
        $(".newsletter-builder-area-center-frame-buttons-content-tab[data-type='content']").hide()
        $(".newsletter-builder-area-center-frame-buttons-content-tab[data-type='footer']").hide()
    });
    $("#add-content").hover(function() {
        $(".newsletter-builder-area-center-frame-buttons-content-tab[data-type='header']").hide()
        $(".newsletter-builder-area-center-frame-buttons-content-tab[data-type='content']").show()
        $(".newsletter-builder-area-center-frame-buttons-content-tab[data-type='footer']").hide()
    });
    $("#add-footer").hover(function() {
        $(".newsletter-builder-area-center-frame-buttons-content-tab[data-type='header']").hide()
        $(".newsletter-builder-area-center-frame-buttons-content-tab[data-type='content']").hide()
        $(".newsletter-builder-area-center-frame-buttons-content-tab[data-type='footer']").show()
    });
    $(".newsletter-builder-area-center-frame-buttons-content-tab").hover(
        function() {
            $(this).append('<div class="newsletter-builder-area-center-frame-buttons-content-tab-add"><i class="fa fa-plus"></i>&nbsp;Insert</div>');
            $('.newsletter-builder-area-center-frame-buttons-content-tab-add').click(function() {
                $("#newsletter-builder-area-center-frame-content").prepend($("#newsletter-preloaded-rows .sim-row[data-id='"+$(this).parent().attr("data-id")+"']").clone());
                hover_edit();
                perform_delete();
                $("#newsletter-builder-area-center-frame-buttons-dropdown").fadeOut(200);
            })
        }, function() {
            $(this).children(".newsletter-builder-area-center-frame-buttons-content-tab-add").remove();
        }
    );
    $("#newsletter-builder-area-center-frame-content").sortable({
        revert: true
    });
    $(".sim-row").draggable({
        connectToSortable: "#newsletter-builder-area-center-frame-content",
        //helper: "clone",
        revert: "invalid",
        handle: ".sim-row-move"
    });
})(jQuery, window);

jQuery(document).ready(function() {
    altaPagina.init();
});

function hover_edit(){
    $(".sim-row-edit").hover(
        function() {
            $(this).append('<div class="sim-row-edit-hover"><i class="fa fa-pencil" style="line-height:30px;"></i></div>');
            $(".sim-row-edit-hover").click(function(e){
                e.preventDefault()
            });
            $(".sim-row-edit-hover i").click(function(e) {
                e.preventDefault();
                big_parent = $(this).parent().parent();
                //edit image
                if(big_parent.attr("data-type")=='image'){
                    $("#sim-edit-image .image").val(big_parent.children('img').attr("src"));
                    $("#sim-edit-image").fadeIn(500);
                    $("#sim-edit-image .sim-edit-box").slideDown(500);

                    $("#sim-edit-image .sim-edit-box-buttons-save").click(function() {
                        $(this).parent().parent().parent().fadeOut(500)
                        $(this).parent().parent().slideUp(500)
                        big_parent.children('img').attr("src",$("#sim-edit-image .image").val());
                    });
                }
                //edit link
                if(big_parent.attr("data-type")=='link'){
                    $("#sim-edit-link .title").val(big_parent.text());
                    $("#sim-edit-link .url").val(big_parent.attr("href"));
                    $("#sim-edit-link").fadeIn(500);
                    $("#sim-edit-link .sim-edit-box").slideDown(500);

                    $("#sim-edit-link .sim-edit-box-buttons-save").click(function() {
                        $(this).parent().parent().parent().fadeOut(500)
                        $(this).parent().parent().slideUp(500)
                        big_parent.text($("#sim-edit-link .title").val());
                        big_parent.attr("href",$("#sim-edit-link .url").val());
                    });
                }
                //edit title
                if(big_parent.attr("data-type")=='title'){
                    $("#sim-edit-title .title").val(big_parent.text());
                    $("#sim-edit-title").fadeIn(500);
                    $("#sim-edit-title .sim-edit-box").slideDown(500);

                    $("#sim-edit-title .sim-edit-box-buttons-save").click(function() {
                        $(this).parent().parent().parent().fadeOut(500)
                        $(this).parent().parent().slideUp(500)
                        big_parent.text($("#sim-edit-title .title").val());
                    });
                }
                //edit text
                if(big_parent.attr("data-type")=='text'){
                    $("#sim-edit-text .text").val(big_parent.text());
                    $("#sim-edit-text").fadeIn(500);
                    $("#sim-edit-text .sim-edit-box").slideDown(500);

                    $("#sim-edit-text .sim-edit-box-buttons-save").click(function() {
                        $(this).parent().parent().parent().fadeOut(500)
                        $(this).parent().parent().slideUp(500)
                        big_parent.text($("#sim-edit-text .text").val());
                    });
                }
                //edit icon
                if(big_parent.attr("data-type")=='icon'){
                    $("#sim-edit-icon").fadeIn(500);
                    $("#sim-edit-icon .sim-edit-box").slideDown(500);

                    $("#sim-edit-icon i").click(function() {
                        $(this).parent().parent().parent().parent().fadeOut(500)
                        $(this).parent().parent().parent().slideUp(500)
                        big_parent.children('i').attr('class',$(this).attr('class'));
                    });
                }//
            });
        }, function() {
            $(this).children(".sim-row-edit-hover").remove();
        }
    );
}
hover_edit()
function add_delete(){
    $(".sim-row").append('<div class="sim-row-delete"><i class="fa fa-times" ></i></div>');
}
add_delete();

function perform_delete(){
    $(".sim-row-delete").click(function() {
        $(this).parent().remove();
    });
}
perform_delete();