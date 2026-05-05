"use strict";
// TODO move to js file and build it in plugins.bundle.js
(function () {
    var PolyfillEvent = eventConstructor();

    function eventConstructor() {
        if (typeof window.CustomEvent === "function") return window.CustomEvent;

// IE<=9 Support
        function CustomEvent(event, params) {
            params = params || {bubbles: false, cancelable: false, detail: undefined};
            var evt = document.createEvent("CustomEvent");
            evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
            return evt;
        }

        CustomEvent.prototype = window.Event.prototype;
        return CustomEvent;
    }

    function buildHiddenInput(name, value) {
        var input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        input.value = value;
        return input;
    }

    function handleClick(element) {
        var to = element.getAttribute("data-to"),
            method = buildHiddenInput("_method", element.getAttribute("data-method")),
            csrf = buildHiddenInput("_token", element.getAttribute("data-csrf")),
            form = document.createElement("form"),
            target = element.getAttribute("target");

        form.method = element.getAttribute("data-method") === "get" ? "get" : "post";
        form.action = to;
        form.style.display = "hidden";

        if (target) form.target = target;

        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }

    function handleConfirm(message, callback, element) {
        swal.fire("", message, "warning").then(function (result) {
            if (result.value) {
                callback(element);
            }
        });
    }

    window.addEventListener(
        "click",
        function (e) {
            var element = e.target;

            while (element && element.getAttribute) {
                if (element.getAttribute("data-method")) {
                    console.log("1111111");
                    var message = element.getAttribute("data-confirm");
                    if (message) {
                        handleConfirm(message, handleClick, element);
                    } else {
                        handleClick(element);
                    }
                    e.preventDefault();
                    console.log("1231231");
                    return false;
                } else {
                    element = element.parentNode;
                }
            }
        },
        false
    );
})();

/**
 * sends a request to the specified url from a form. this will change the window location.
 * @param {string} path the path to send the post request to
 * @param {object} params the paramiters to add to the url
 * @param {string} [method=post] the method to use on the form
 */

function post(path, params, method) {
    method = method || 'post'

// The rest of this code assumes you are not using a library.
// It can be made less wordy if you use one.
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = path;

    const hiddenField = document.createElement('input');
    hiddenField.type = 'hidden';
    hiddenField.name = '_method';
    hiddenField.value = method;
    form.appendChild(hiddenField);

    for (const key in params) {
        if (params.hasOwnProperty(key)) {
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = key;
            hiddenField.value = params[key];

            form.appendChild(hiddenField);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

$.fn.KTDatatable.defaults.translate = {
    records: {
        processing: "الرجاء الانتظار...",
        noRecords: "لا يوجد بيانات"
    },
    toolbar: {
        pagination: {
            items: {
                default: {
                    first: "الأول",
                    prev: "السابق",
                    next: "التالي",
                    last: "الأخير",
                    more: "المزيد",
                    input: "رقم الصفحة",
                    select: "اختر حجم الصفحة"
                },
                info: "عرض {{start}} - {{end}} من {{total}} حقل"
            }
        }
    }
};
swal = swal.mixin({confirmButtonText: "نعم", cancelButtonText: "إغلاق"});
$.fn.select2.defaults.set("placeholder", "اختر قيمة");
$.fn.select2.defaults.set("allowClear", true);
$.fn.select2.defaults.set("language", "ar");
$.fn.select2.defaults.set("dir", "rtl");
$.fn.select2.defaults.set("language", {
    errorLoading: function () {
        return "لا يمكن تحميل النتائج";
    },
    inputTooLong: function (options) {
        return "الرجاء حذف " + (options.input.length - options.maximum) + " عناصر";
    },
    inputTooShort: function (options) {
        return "الرجاء إضافة " + (options.minimum - options.input.length) + " عناصر";
    },
    loadingMore: function () {
        return "جاري تحميل نتائج إضافية...";
    },
    maximumSelected: function (options) {
        return "تستطيع إختيار " + options.maximum + " بنود فقط";
    },
    noResults: function () {
        return "لم يتم العثور على أي نتائج";
    },
    searching: function () {
        return "جاري البحث…";
    },
    removeAllItems: function () {
        return "قم بإزالة كل العناصر";
    }
});
$('.select2-input').select2();

$('.dpicker').datepicker({
    rtl: true,
    todayHighlight: true,
    orientation: "bottom left",
    format: 'dd-mm-yyyy',
    templates: {
        leftArrow: '<i class="la la-angle-right"></i>',
        rightArrow: '<i class="la la-angle-left"></i>'
    }
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
