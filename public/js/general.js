const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let auth_render = false;
jQuery(document).ready(function ($) {
    $(document).on('click', '.pull-bs-canvas-right, .pull-bs-canvas-left', function () {
        $('body').prepend('<div class="bs-canvas-overlay bg-dark position-fixed w-100 h-100"></div>');
        if ($(this).hasClass('pull-bs-canvas-right'))
            $('.bs-canvas-right').addClass('mr-0');
        else
            $('.bs-canvas-left').addClass('ml-0');
        return false;
    });

    $(document).on('click', '.bs-canvas-close, .bs-canvas-overlay', function () {
        var elm = $(this).hasClass('bs-canvas-close') ? $(this).closest('.bs-canvas') : $('.bs-canvas');
        elm.removeClass('mr-0 ml-0');
        $('.bs-canvas-overlay').remove();
        return false;
    });
    $(document).on('click', '#create_collection', function () {
        var link = '';
        var type = $(this).data('collection');
        var collection = '';
        switch (type) {
            case 'Video':
                link = VIDEO_COLLECTION_STORE;
                collection = 'videos';
                break;
            case 'Image':
                link = IMAGE_COLLECTION_STORE;
                collection = 'images';
                break;
            case 'Vector':
                link = VECTOR_COLLECTION_STORE;
                collection = 'vectors';
                break;

        }
        var title = $('#collection-model #recipient-name').val();
        $.ajax({
            type: "POST",
            url: link,
            data: {title: title},
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
            }
            ,
            success: function (data) {
                if (data.success == true) {
                    $('#collection-model #recipient-name').val('');
                    addToCollection(image_id, collection, data.id, data.title);
                }
            },
            error: function (error) {
                alertError("", error)
            }
        });
    });
});

function onRecaptchaLoadCallback() {
    window.recaptClientId = grecaptcha.render('inline-signup-badge', {
        'sitekey': reCAPTCHA_site_key,
        'badge': 'inline',
        'size': 'invisible',
        'expired-callback': recaptExpCallback
    });
    grecaptcha.ready(function () {
        grecaptcha.execute(window.recaptClientId, {
            action: 'signup'
        });
    });
}

var recaptExpCallback = function () {
    grecaptcha.reset();
};

/*------------------------------------------------------------
Register the user form site
-------------------------------------------------------------*/
function register() {
    var form = $('#signup #regForm');
    var url = form.attr('action');
    var isValid = $('#signup #regForm')[0].checkValidity();

    if (!isValid) {
        $('#signup #regForm')[0].reportValidity();
        return false;
    }
    var errors = '';
    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function (data) {
            if (data.status == true) {
                location.reload();
            }
        },
        error: function (response) {
            grecaptcha.reset();
            grecaptcha.execute(window.recaptClientId, {
                action: 'signup'
            });
            errors = '';
            $errors = JSON.parse(response.responseText).errors;
            var indx = 1;
            $('#signup .wrap_validation').html('');
            $.each($errors, function (key, value) {
                errors += value + '<br>';
            });
            $('#signup .devide-wrap_validation').show();
            $('#signup .devide-wrap_validation .alert').attr('class', 'alert alert-danger');
            $('#signup .wrap_validation').html(errors);
        },
    });
}

/*------------------------------------------------------------
Login user from model log in
-------------------------------------------------------------*/

function login() {

    var form = $('#login #loginForm');
    var url = form.attr('action');
    var isValid = $('#login #loginForm')[0].checkValidity();

    if (!isValid) {
        $('#login #loginForm')[0].reportValidity();
        return false;
    }
    var errors = '';
    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function (data) {

            if (data.status == true) {
                location.reload();

                //  return false;
            } else if (data.status === 'admin') {
                $(location).attr('href', data.url);
            } else {
                errors += data.status + '<br>';

                $('#login .devide-wrap_validation').show();
                $('#login .devide-wrap_validation .alert').attr('class', 'alert alert-danger');
                $('#login .wrap_validation').html(errors);
            }


        },
        error: function (response) {
            $errors = JSON.parse(response.responseText).errors;
            var indx = 1;
            $('#login .wrap_validation').html('');
            $.each($errors, function (key, value) {
                errors += value + '<br>';
            });
            $('#login .devide-wrap_validation').show();
            $('#login .devide-wrap_validation .alert').attr('class', 'alert alert-danger');
            $('#login .wrap_validation').html(errors);
        },
    });
}

/*------------------------------------------------------------
Forget pass from the model forget
-------------------------------------------------------------*/
function forgetPass() {

    //  $('#forgetPassword .devide-wrap_validation').html('');

    // $('#forgetPassword .devide-wrap_validation').hide();
    var form = $('#forgetPassword #forgetForm');
    var url = form.attr('action');
    var isValid = $('#forgetPassword #forgetForm')[0].checkValidity();

    if (!isValid) {
        $('#forgetPassword .devide-wrap_validation').hide();
        $('#forgetPassword #forgetForm')[0].reportValidity();
        return false;
    }
    var errors = '';
    var email = '';
    var span = '';

    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(), // serializes the form's elements.
        beforeSend: function () {
            $('#forgetPassword #forgetpasswordbutton').attr('disabled', true);
            // $('#forgetPassword .devide-wrap_validation').show();
        },
        success: function (data) {
            console.log("data");
            console.log(data);
            if (data.status == false) {
                email = data.msg.email;
                span = 'alert alert-danger';
                $('#forgetPassword #forgetpasswordbutton').attr('disabled', false);
            } else {
                email = data.msg.email;
                span = 'alert alert-success';
                // $('#login').modal('show');
                //  $('#forgetPassword').modal('hide');
            }
            $('#forgetPassword .devide-wrap_validation').show();
            $('#forgetPassword .devide-wrap_validation .alert').attr('class', span);
            $('#forgetPassword .wrap_validation').html(email);
            $('#forgetPassword #email').val();
        },
        error: function (data) {
            $('#forgetPassword #forgetpasswordbutton').attr('disabled', false);
            $.each(data.responseJSON.errors, function (index, value) {
                // alert(index + ": " + value);
                errors += value + '<br>';
            });

            if (typeof data.responseJSON.errors != "undefined") {

            } else {
                errors = data.responseJSON.message;
            }


            $('#forgetPassword .devide-wrap_validation').show();
            $('#forgetPassword .devide-wrap_validation .alert').attr('class', 'alert alert-danger');
            $('#forgetPassword .wrap_validation').html(errors);

            $('#forgetPassword #email').val('');
        },
    });
    return;
}

$('#forgetPassword').on('hidden.bs.modal', function () {

    $('#forgetPassword .wrap_validation').html('');
    $('#forgetPassword .devide-wrap_validation').hide();

    $('#forgetPassword #email').val('');
    $('#forgetPassword #forgetpasswordbutton').attr('disabled', false);
});

function unicodeEscape(str) {
    return str.replace(/[&\/\\#,()\-=+$~%._'":*?<>{}]/g, function (character) {
        return '\%' + ('0' + character.charCodeAt().toString(16)).slice(-2);
    });
}

function validation() {
    if (document.getElementById("search").value.length > 49) {
        $('#search-alert').show();
        return false;
    } else {
        $('#search-alert').hide();
        return false;
    }
}

function create_img_tag(elem) {
    var imgElm = document.createElement("img");
    imgElm.setAttribute("src", elem.dataset.thumbnail);
    return imgElm;
}


function create_video_tag(src) {
    var videlem = document.createElement("video");
    videlem.setAttribute("width", "100%");
    videlem.setAttribute("height", "100%");
    videlem.setAttribute("preload", "auto");
    videlem.setAttribute("muted", "");
    videlem.setAttribute("autoplay", "");
    videlem.setAttribute("loop", true);
    var sourceMP4 = document.createElement("source");
    sourceMP4.type = "video/mp4";
    sourceMP4.src = src;
    videlem.appendChild(sourceMP4);
    return videlem;
}

function hideVideo(elem) {
    elem.find('video').addClass('d-none')[0].pause();
}

function hoverVideo(elem) {
    var vidEl = elem.find('video');
    elem.find('.card-video').removeClass('border-file');
    vidEl.removeClass('d-none')[0].play();
}

function addVideoHoverEventListener() {
    $(document).on("mouseover", ".video-item .over", function () {
        hoverVideo($(this).closest('.video-item'));
    });
    $(document).on("mouseout", ".video-item .over", function () {
        hideVideo($(this).closest('.video-item'));
    });
}

var Load = [];
addVideoHoverEventListener();

if ($('#imageslandpage .item').length) {
    $('#imageslandpage').flexImages({rowHeight: 300, maxRows: 2});
}
if ($('#imageslandpageVector .item').length) {
    $('#imageslandpageVector').flexImages({rowHeight: 300, maxRows: 2});
}
if (!IS_IN_VIDEO_SITE && $('#imagesFlex').length) {
    $('#imagesFlex').flexImages({rowHeight: 300, truncate: 1});
}

if ($('#videogridlandpage .item').length) {
    $('#videogridlandpage').flexImages({object: '.arabs-video', rowHeight: 300, truncate: 1, maxRows: 2});
}
if ($('#videogrid .video-item').length !== 0) {
    $('#videogrid').flexImages({object: '.arabs-video', rowHeight: 300, truncate: 1});
}


/*------------------------------------------------------------
    Back to top
-------------------------------------------------------------*/

if ($('#back-to-top').length) {
    var scrollTrigger = 100, // px
        backToTop = function () {
            var scrollTop = $(window).scrollTop();
            if (scrollTop > scrollTrigger) {
                $('#back-to-top').addClass('show');
            } else {
                $('#back-to-top').removeClass('show');
            }
        };
    backToTop();
    $(window).on('scroll', function () {
        backToTop();
    });
    $('#back-to-top').on('click', function (e) {
        e.preventDefault();
        $('html,body').animate({
            scrollTop: 0,
        }, 700);
    });
}
//Loader
// $(window).on('load', function () {
//  $('.loader').delay(350).fadeOut('slow');
//  $('body').delay(350).css({ 'overflow': 'visible' });
// })


/*------------------------------------------------------------
    Owl Carousel
-------------------------------------------------------------*/

var THEMEMASCOT = {};
THEMEMASCOT.isRTL = {
    check: function () {
        if ($("html").attr("dir") === "rtl") {
            return true;
        } else {
            return false;
        }
    },
};


/*------------------------------------------------------------
    Toggle Tags
-------------------------------------------------------------*/
$("#toggle").click(function () {
    var elem = $(this).attr('data-slide');
    if (elem == "open") {
        $("#toggle").text(lang.global.show_less);
        $(this).attr('data-slide', 'close');
    } else {
        $("#toggle").text(lang.global.show_more);
        $(this).attr('data-slide', 'open');
    }

    $(".moretag").slideToggle();

});


/*------------------------------------------------------------
    to run the like button on image any where
-------------------------------------------------------------*/

var image_id;
var video_id;


$('body').on("click", '.btnAddCart', function (e) {

    if (!window.user) {
        checkAuth();
        return false;
    }
    var element = $(this);

    var video_id = $('#videos-radio-list').find('input:checked').val();
    var data = {video_id: video_id};

    e.preventDefault();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: "POST",
        url: '/:locale/videos/cart/add'.replace(':locale', window.app_locale),
        data: data,
        success: function (result) {
            if (result.success === true) {
                element.find('i').attr('class', 'fal fa-cart-arrow-down');
                $('.cart-badge-count').text(result.total_in_cart);
                $('#videos-radio-list').html(result.table_html);
                $('#modalAfterCart').modal('show');

            }

            if (result.success === false) {
                $('#videos-radio-list').html(result.table_html);
                $('#modalAfterCart').modal('show');
            }

        },//<-- RESULT
    });//<--- AJAX


});//<----- CLICK


$('body').on("click", '.removeItemFromCart', function (e) {

    if (!window.user) {
        checkAuth();
        return false;
    }
    var element = $(this);

    var video_token = element.attr('data-token');
    var data = {video_token: video_token};

    e.preventDefault();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: "POST",
        url: '/:locale/videos/cart/remove'.replace(':locale', window.app_locale),
        data: data,
        success: function (result) {
            if (result.success === true) {
                $('.cart-badge-count').text(result.total_in_cart);
                $('#videos-radio-list').html(result.table_html);
            }

            if (result.success === false) {
                // alert(result.message);
            }

        },//<-- RESULT
    });//<--- AJAX


});//<----- CLICK


/*------------------------------------------------------------
    to show model add collections and data collection for user
-------------------------------------------------------------*/

function showModal(id, type, img, title) {
    if (!window.user) {
        checkAuth();
        return false;
    }
    var button = document.getElementById("create_collection")

    $('#collection-model #myCollections').text('');
    var link = '';
    var collection = '';
    button.dataset.collection = type;
    switch (type) {
        case 'Video':
            link = VIDEO_COLLECTION;
            collection = "videos";
            break;
        case 'Image':
            link = IMAGE_COLLECTION;
            collection = "images";
            break;
        case 'Vector':
            link = VECTOR_COLLECTION;
            collection = "vectors";
            break;

    }
    var link = link.replace('/0/', `/${id}/`);

    $.ajax({
        type: "POST",
        url: link,
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
        },
        success: function (data) {

            var my_collection = '';
            var active_class = '';

            for (var i = 0; i < data.length; i++) {

                if (data[i].in_collection == 1) {
                    active_class = 'active';
                    my_collection += '<li id="li_' + data[i].id + '" onclick="addToCollection(' + id + ',' + `'${collection}'` + ',' + data[i].id + ',\'' + data[i].title + '\')" class="disabled">' + data[i].title + '</li>';
                } else {
                    active_class = '';
                    my_collection += '<li id="li_' + data[i].id + '" onclick="addToCollection(' + id + ',' + `'${collection}'` + ',' + data[i].id + ',\'' + data[i].title + '\')" class="' + active_class + '">' + data[i].title + '</li>';
                }


            }
            $('#collection-model #myCollections').append(my_collection);

            $('#collection-model #imageCard').attr('src', img);
            $('#collection-model').modal('show');
            image_id = id;
        },
        error: function (error) {
            alertError("", error)
        }
    });


}


/*------------------------------------------------------------
    send request to save photo on collection
-------------------------------------------------------------*/


/*------------------------------------------------------------
      create new collection and add photo on it
-------------------------------------------------------------*/


/*------------------------------------------------------------
  Send request to save video om collection
-------------------------------------------------------------*/


/*------------------------------------------------------------
    create new collection and add photo on it
-------------------------------------------------------------*/


$('#btnItems').keydown(function (event) {

    var keypressed = event.keyCode || event.which;
    if (keypressed == 13) {
        var search = $(this).val().replace(/\#+/gi, '%23');
        var target = URL_BASE + '/search/' + search;
        if (trim(search).length < 2 || trim(search).length == 0 || trim(search).length > 100) {
            return false;
        } else {
            window.location.href = target;
        }
    }
});

function goToVideo(url) {

    $(location).attr('href', url);
}

function unicodeEscape(str) {
    return str.replace(/[&\/\\#,()\-=+$~%._'":*?<>{}]/g, function (character) {
        return '\%' + ('0' + character.charCodeAt().toString(16)).slice(-2);
    });
}

$(function () {

});


function validationnavbar() {
    if (document.getElementById("search").value.length > 49) {
        $('#search-alert').show();
        return false;
    } else {
        $('#search-alert').hide();
        return true;
    }
}


$('select[name=choices-single-default]').on('change', function (e) {
    var value = e.target.value;
    $('.featured-keyword__videos, .featured-keyword__images , .featured-keyword__vectors').addClass('d-none');
    $('.featured-keyword__' + value).removeClass('d-none');
    ris_dropzone.options.url = $(e.target).attr('data-' + value + '-ris').replace('/0', '');
    $('#image-search-modal .modal-header h2').text($(e.target).attr('data-' + value + '-ris-modal-title'));
    $('#image-search-modal [data-dz-message] p:first-child').text($(e.target).attr('data-' + value + '-ris-description'));
    // console.log(value)
    // if (value == 'videos') {
    //     ris_dropzone.options.acceptedFiles = "image/jpeg,image/jpg,image/png,video/mp4,video/avi";
    // }else{
    //     ris_dropzone.options.acceptedFiles = "image/jpeg,image/jpg,image/png";
    // }
});


document.addEventListener("load", function () {
    var input = document.querySelector("#mobileR");
    window.intlTelInputGlobals.loadUtils("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js");
    iti = intlTelInput(input, {
        allowExtensions: true,
        autoFormat: false,
        autoHideDialCode: false,
        customPlaceholder: function (selectedCountryPlaceholder, selectedCountryData) {
            return selectedCountryPlaceholder;
        },
        defaultCountry: "auto",
        ipinfoToken: "yolo",
        nationalMode: false,
        separateDialCode: false,
        numberType: "MOBILE",
        preventInvalidNumbers: true,
        initialCountry: "sa",
    });
    $("#share").jsSocials({
        shares: ["email", "twitter", "facebook", "pinterest"],
    });
});

function goToCategory(slug) {
    var link = urls.image_category;
    link = link.replace(':slug', slug);
    window.location.href = link;
}

function goToCategoryVideo(slug) {
    var link = urls.video_category;
    link = link.replace(':slug', slug);
    window.location.href = link;
}


function goToCategoryVector(slug) {
    var link = urls.vector_category;
    link = link.replace(':slug', slug);
    window.location.href = link;
}

function validate_number(evt) {
    var theEvent = evt || window.event;

    // Handle paste
    if (theEvent.type === 'paste') {
        key = event.clipboardData.getData('text/plain');
    } else {
        // Handle key press
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode(key);
    }
    var regex = /[0-9]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}

function goToSearch() {
    var search_val = $('#search').val().trim();
    if (!search_val) {
        return false;
    }
    if (!validationnavbar())
        return false;
    var search_type = $('select[name=choices-single-default]').val();
    str = search_val.replace(/ +(?= )/g, '');

    Cookies.set('search', str);
    str = unicodeEscape(str);

    str = str.replace(/\s/g, "+");


    str = str.replace(/_/g, "+");
    str = str.replace(/-/g, "+");


    var url = {
        images: urls.images_search,
        videos: urls.videos_search,
        vectors: urls.vectors_search,
    };

    window.location.href = url[search_type].replace('query', str);
}

$(document).on('hidden.bs.modal', function () {
    if ($('.modal.show').length) {
        $('body').addClass('modal-open');
    }
});

$("#search").on('keyup', function () {
    // Number 13 is the "Enter" key on the keyboard
    if (event.keyCode === 13) {
        event.preventDefault();
        goToSearch();
    }

});
$('.btn-search:not(.btn-image-search)').on('click', goToSearch);
$("#search").on('keyup', function () {
    // Number 13 is the "Enter" key on the keyboard
    if (event.keyCode === 13) {
        event.preventDefault();
        goToSearch();
    }
});

$(document).on("click", '.likeButton', function (e) {
    if (!window.user) {
        checkAuth();
        return false;
    }
    let element = $(this);
    let type = element.attr("data-type");
    console.log('type', type);
    let id = element.attr("data-id");
    let like = element.attr('data-like');
    let like_active = element.attr('data-unlike');
    let data = 'id=' + id;
    e.preventDefault();
    element.blur();
    element.find('i').addClass('fa-heart');
    let msg = '';
    if (element.hasClass('active')) {
        element.removeClass('active');
        element.find('i').removeClass('far fa-heart').addClass('fas fa-heart');
        element.find('.textLike').html(like);
        msg = lang.misc.unlike_photo_video;
    } else {
        element.addClass('active');
        element.find('i').removeClass('fas fa-heart').addClass('far fa-heart');
        element.find('.textLike').html(like_active);
        msg = lang.misc.like_photo_video;
    }

    var url = '';
    switch (type) {
        case "Image":
            url = '/:locale/photos/ajax/like'.replace(':locale', window.app_locale);
            break;
        case "Video":
            url = '/:locale/videos/ajax/like'.replace(':locale', window.app_locale);
            break;
        case "Vector":
            url = '/:locale/vectors/ajax/like'.replace(':locale', window.app_locale);
            break;
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        type: "POST",
        url: url,
        data: data,
        success: function (result) {
            jQuery.notify({
                title: '<strong>' + msg + '</strong>',
                icon: 'glyphicon glyphicon-star',
                message: "",
            }, {
                type: 'info',
                animate: {
                    enter: 'animated fadeInUp',
                    exit: 'animated fadeOutRight',
                },
                placement: {
                    from: "bottom",
                    align: "center",
                },
                offset: 40,
                spacing: 30,
                z_index: 10000000000000000,
                allow_dismiss: true,
                newest_on_top: false,
                showProgressbar: false,
            });
            if (result == '') {
                window.location.reload();
                element.removeClass('likeButton');
                element.removeClass('active');
            } else {
                element.find('i').removeClass('icon-spinner2 fa-spin');
            }
        },
    });
});

function addToCollection(img_id, collection_type, collection_id, title) {
    var link = '';
    switch (collection_type) {
        case 'images':
            link = '/:locale/collection/:collection_id/i/:img_id'
                .replace(':locale', window.app_locale)
                .replace(':collection_id', collection_id)
                .replace(':img_id', img_id);
            break;
        case 'videos':
            link = '/:locale/videos/collection/:collection_id/i/:video_id'
                .replace(':locale', window.app_locale)
                .replace(':collection_id', collection_id)
                .replace(':video_id', img_id);
            break;
        case 'vectors':
            link = '/:locale/vectors/collection/:collection_id/i/:vector_id'
                .replace(':locale', window.app_locale)
                .replace(':collection_id', collection_id)
                .replace(':vector_id', img_id);
            break;

    }
    $.ajax({
        type: "GET",
        url: link,
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
        },
        success: function (data) {
            $('#li_' + collection_id).addClass('active');
            $('#collection-model').modal('toggle');
            if (window.user) {
                jQuery.notify({
                        title: '<strong>:msg</strong>'.replace(':msg', lang.misc.add_successfully),
                        icon: 'glyphicon glyphicon-star',
                        message: "<a href='/:locale/account/collection/:type/:collection_id'>:title</a>".replace(':type', collection_type)
                            .replace(':collection_id', collection_id).replace(':title', title).replace(':locale', window.app_locale),
                    },
                    {
                        type: 'info',
                        animate: {
                            enter: 'animated fadeInUp',
                            exit: 'animated fadeOutRight',
                        },
                        placement: {
                            from: "bottom",
                            align: "center",
                        },
                        offset: 40,
                        spacing: 30,
                        z_index: 10000000000000000,
                        allow_dismiss: true,
                        newest_on_top: false,
                        showProgressbar: false,
                    });
            }
        },
        error: function (error) {
            alertError("", error)
        },
    });
}


function alertError(element, error) {
    if (element && element != "") {
        element.removeClass('likeButton');
        element.removeClass('active');
        element.find('i').removeClass('icon-spinner2 fa-spin');
    }
    jQuery.notify({
        title: '<strong > <center>' + error.responseJSON.msg + '</center></strong>',
        icon: 'glyphicon glyphicon-star',
        message: "",
    }, {
        type: 'danger',
        animate: {
            enter: 'animated fadeInUp',
            exit: 'animated fadeOutRight',
        },
        placement: {
            from: "bottom",
            align: "center",
        },
        offset: 40,
        spacing: 30,
        z_index: 10000000000000000,
        allow_dismiss: true,
        newest_on_top: false,
        showProgressbar: false,
    });
}

function notify(msg, type) {

    if (typeof type == 'undefined')
        type = 'info';
    jQuery.notify({
        title: '<strong class="px-3">' + msg + '</strong>',
        icon: 'glyphicon glyphicon-star',
        message: "",
    }, {
        type: type,
        animate: {
            enter: 'animated fadeInUp',
            exit: 'animated fadeOutRight',
        },
        placement: {
            from: "bottom",
            align: "center",
        },
        offset: 40,
        spacing: 30,
        z_index: 10000000000000000,
        allow_dismiss: true,
        newest_on_top: false,
        showProgressbar: false,
        timer: 1000,
    });
}

function checkAuth() {
    if (!auth_render) {
        call_auth('login');
    } else {
        $('#login').modal('show');
    }
}


function delay(callback, ms) {
    var timer = 0;
    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}

Object.defineProperty(String.prototype, 'capitalize', {
    value: function () {
        return this.charAt(0).toUpperCase() + this.slice(1);
    },
    enumerable: false
});

//-------------------------------Download Options-------------------------------------
$(document).on('click', '#page-content-wrapper > .layer', function () {
    $("#wrapper").toggleClass("toggled");
});
$(document).on('click', "#downloadForm .dropdown-menu a", function () {
    var text = $(this).html();
    $(this).closest('.dropdown').find('[data-toggle="dropdown"]').html(text);
    $(this).closest('.dropdown').find('[data-toggle="dropdown"] input[name="type"]').attr('disabled', !1);
});
$("#downloadForm .dropdown-menu a:first-child").trigger('click');
$(document).on('click', '.downloadBtn', function (e) {
    e.preventDefault();
    var $this = $(this),
        $form = $(this).closest('form');
    $subscription_type = $('input[name="subscription_type"]:checked').val();
    $this.attr('disabled', 1);
    var $url = $('#subscriptions>div[data-url]').attr('data-url');
    if ($('#subscriptions>div[data-can-download]').attr('data-can-download') == 0) {
        window.location.href = $url;
        return;
    }
    window.open($url + ($url.indexOf('?') == -1 ? '?' : '&') + $form.serialize(), '_blank');
    $('button.btn-download').html(`<i class="fal fa-cloud-download-alt pr-2"></i>` + lang.plans.download.new_license).addClass('btn-white').closest('div').removeClass('col-sm').addClass('col-sm-7').next().removeClass('d-none').find('.dropdown-item').removeClass('d-none')
    setTimeout(function () {
        $.get(window.location.href, function (data) {
            $("#sidebar-wrapper").html($(data).find('#sidebar-wrapper').html());
            $(".dropdown-menu a:first-child").trigger('click');
            $('#downloadForm [name="license_type"]:checked').trigger('change');
        });
    }, 1000);
    $('.removebg_link').removeClass('d-none');
    $("#wrapper").toggleClass("toggled");
});
$(document).on('click', 'input[name="subscription_type"]', function () {
    var $type = $('input[name="subscription_type"]:checked').val();
    if ($type == 'team_subscriptions') {
        $('#enhanced_license_type').click();
        $('[name="license_type"][value="standard"]').attr('disabled', true);
        $('[data-team-subscriptions]').fadeIn('fast');
        $('[data-user-subscriptions]').fadeOut('fast');
    } else {
        $('[name="license_type"]').attr('disabled', false);
        $('#standard_license_type').click();
        $('[data-user-subscriptions]').fadeIn('fast');
        $('[data-team-subscriptions]').fadeOut('fast');
    }
});

var ris_dropzone;
document.addEventListener("DOMContentLoaded", function () {
    if (typeof Dropzone !== 'undefined') {
        Dropzone.autoDiscover = false;
        var ris_form = $('form.ris-upload');
        ris_dropzone = new Dropzone("form.ris-upload", {
            url: '/:locale/search/ris'.replace(':locale', window.app_locale),
            paramName: "image", // The name that will be used to transfer the file
            maxFilesize: 20, // MB
            maxFiles: 1,
            acceptedFiles: "image/jpeg,image/jpg,image/png",
            dictDefaultMessage: lang.dropzone.dictDefaultMessage,
            dictFallbackMessage: lang.dropzone.dictFallbackMessage,
            dictFallbackText: lang.dropzone.dictFallbackText,
            dictFileTooBig: lang.dropzone.dictFileTooBig,
            dictInvalidFileType: lang.dropzone.dictInvalidFileType,
            dictResponseError: lang.dropzone.dictResponseError,
            dictCancelUpload: lang.dropzone.dictCancelUpload,
            dictCancelUploadConfirmation: lang.dropzone.dictCancelUploadConfirmation,
            dictRemoveFile: lang.dropzone.dictRemoveFile,
            dictMaxFilesExceeded: lang.dropzone.dictMaxFilesExceeded,
            clickable: '.image-search-modal .choose-file',
            processing: function (file) {
                $('#image-search-modal .alert').hide();
                $('.image-search-modal .choose-file').attr('disabled', !0);
            },
            error(file, message) {
                this.removeFile(file);
                $('.dz-preview').remove();
                ris_form.removeClass('dz-started');
                if (typeof message === 'string')
                    $('#image-search-modal .alert').show().find('span.text').text(message);
                else
                    $('#image-search-modal .alert').show().find('span.text').text('خطأ غير معروف 😞 ،، جاري المتابعة..');
            },
            success: function (file, response) {
                if (response.status) {
                    $('.ris-upload .dz-preview .dz-progress').html('<div class="spinner-border text-light" role="status"><span class="sr-only">Loading...</span></div>')
                    window.location.href = response.redirect;
                    return;
                }
                this.removeFile(file);
                $('#image-search-modal .alert').show().find('span.text').text(response.message)
            },
        });
    }
    $('select[name=choices-single-default]').trigger('change');
});

function call_auth(type){
    var link = `${url_auth}?type=${type}`;
    $.ajax({
        url: link,
        success: function(result) {
            $('#auth_component').html(result.data);
            $(`#${type}`).modal('show');
            auth_render = true;
        },
        error: function(res) {
            console.error(res)
        }
    });

}

$(document).ready(function () {
    if ($('[data-download-options]').length) {
        $(document).on('change', '[data-download-options] .changeable', function (e) {
            $.ajax({
                type: "GET",
                url: $('[data-download-options]').attr('data-download-options'),
                data: {
                    license: $('#downloadForm [name="license_type"]:checked').val(),
                    subscription_type: $('#downloadForm [name="subscription_type"]:checked').val(),
                    removebg: $('#removebg:checked').length,
                    raw: $('#download_raw:checked').length,
                },
                datatype: 'json',
                success: function (result) {
                    $('#subscriptions').html(result.html);
                    $('.downloadBtn').text($('#subscriptions>div[data-btn-text]').attr('data-btn-text')).attr('disabled', false);
                    $('h2.credits-count').text(result.credits);
                },
            });
        });
    }
    $(document).on('click', '.btn-download', function () {
        $('#downloadForm [name="license_type"]:checked').trigger('change');
    });
    $('.search-panel .dropdown-menu').find('a').click(function (e) {
        e.preventDefault();
        var param = $(this).attr("href").replace("#", "");
        var concept = $(this).text();
        $('.search-panel span#search_concept').text(concept);
        $('.input-group #search_param').val(param);
    });
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 5,
        autoplay: true,
        responsiveClass: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 2,
                nav: true,
            },
            600: {
                items: 3,
                nav: false,
            },
            1000: {
                items: 8,
                nav: true,
                loop: false,
                margin: 0,
            },
        },
    });
    $('.auth-link').click(function(event) {
        event.preventDefault();
        var type = $(this).data('type');
        if (!auth_render) {
            call_auth(type);
        }
    });
});

function delay(callback, ms) {
    var timer = 0;
    return function() {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}
