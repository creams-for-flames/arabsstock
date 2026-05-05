// Class definition

var KTBootstrapDatepicker = function () {

    var arrows;
    if (KTUtil.isRTL()) {
        arrows = {
            leftArrow: '<i class="la la-angle-right"></i>',
            rightArrow: '<i class="la la-angle-left"></i>'
        }
    } else {
        arrows = {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        }
    }

    // Private functions
    var demos = function () {
        // minimum setup
        if ($('#kt_datepicker_1, #kt_datepicker_1_validate').length) {
            $('#kt_datepicker_1, #kt_datepicker_1_validate').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                templates: arrows
            });
        }

        // minimum setup for modal demo
        if ($('#kt_datepicker_1_modal').length) {
            $('#kt_datepicker_1_modal').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                templates: arrows
            });
        }

        // input group layout
        if ($('#kt_datepicker_2, #kt_datepicker_2_validate').length) {
            $('#kt_datepicker_2, #kt_datepicker_2_validate').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                templates: arrows
            });
        }

        // input group layout for modal demo
        if ($('#kt_datepicker_2_modal').length) {
            $('#kt_datepicker_2_modal').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                templates: arrows
            });
        }

        // enable clear button
        if ($('#kt_datepicker_3, #kt_datepicker_3_validate').length) {
            $('#kt_datepicker_3, #kt_datepicker_3_validate').datepicker({
                rtl: KTUtil.isRTL(),
                todayBtn: "linked",
                clearBtn: true,
                todayHighlight: true,
                templates: arrows
            });
        }

        // enable clear button for modal demo
        if ($('#kt_datepicker_3_modal').length) {
            $('#kt_datepicker_3_modal').datepicker({
                rtl: KTUtil.isRTL(),
                todayBtn: "linked",
                clearBtn: true,
                todayHighlight: true,
                templates: arrows
            });
        }

        // orientation
        if ($('#kt_datepicker_4_1').length) {
            $('#kt_datepicker_4_1').datepicker({
                rtl: KTUtil.isRTL(),
                orientation: "top left",
                todayHighlight: true,
                templates: arrows
            });
        }

        if ($('#kt_datepicker_4_2').length) {
            $('#kt_datepicker_4_2').datepicker({
                rtl: KTUtil.isRTL(),
                orientation: "top right",
                todayHighlight: true,
                templates: arrows
            });
        }

        if ($('#kt_datepicker_4_3').length) {
            $('#kt_datepicker_4_3').datepicker({
                rtl: KTUtil.isRTL(),
                orientation: "bottom left",
                todayHighlight: true,
                templates: arrows
            });
        }

        if ($('#kt_datepicker_4_4').length) {
            $('#kt_datepicker_4_4').datepicker({
                rtl: KTUtil.isRTL(),
                orientation: "bottom right",
                todayHighlight: true,
                templates: arrows
            });
        }

        // range picker
        if ($('#kt_datepicker_5').length) {
            $('#kt_datepicker_5').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                templates: arrows
            });
        }

        // inline picker
        if ($('#kt_datepicker_6').length) {
            $('#kt_datepicker_6').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                templates: arrows
            });
        }
    }

    return {
        // public functions
        init: function() {
            demos();
        }
    };
}();

jQuery(document).ready(function () {
    KTBootstrapDatepicker.init();
});
