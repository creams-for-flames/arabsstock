import Vue from "vue";
import App from "./App.vue";
import Swal from 'sweetalert2'


import * as Sentry from "@sentry/browser";
import { Vue as VueIntegration } from "@sentry/integrations";

if (process.env.NODE_ENV === "production") {
    Sentry.init({
        dsn: "https://a3ef3c52d7e04f829ea5a623a2bdf77c@o127645.ingest.sentry.io/5220909",
        integrations: [new VueIntegration({ Vue, attachProps: true })]
    });
}
console.log("Arabsstock in ", process.env.NODE_ENV);

import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

Vue.component("v-select", vSelect);

Vue.config.productionTip = false;
Vue.config.devtools = true;
Vue.config.debug = true
Vue.config.silent = true
window.swal=Swal;
window.dataStore = {
    images: []
};

let locales;
const locales_ar = {
    Status: "الحالة",
    "Admin's Category": "تصنفيات المدير",
    "Admin's Collection": "تجميعة المدير",
    Collection : "البائعين",
    Folder: "المجلد",
    Sort: "الترتيب",
    "Select All": "تحديد الكل",
    "Reset Filters": "تفريغ الفلاتر",
    "Reset Selections": "تفريغ المحدد",
    "- Arabic Version": "- النسخة العربية",
    "- English Version": "- النسخة الإنجليزية",
    Edit: "تحرير",
    Title: "العنوان",
    Description: "التفاصيل",
    Tags: "الأوسمة",
    Categories: "التصنيفات",
    "(Optional)": "(اختياري)",
    "Admin's Categories": "تصنيفات المدير",
    Submit: "حفظ",
    Save: "حفظ",
    Cancel: "إلغاء",
    "Showing :start to :end of :total entries": "عرض :start إلى :end من :total مدخل",
    "Processing...": "الرجاء الانتظار...",
    "No data available in table": "لا يوجد بيانات",
    First: "الأول",
    Previous: "السابق",
    Next: "التالي",
    Last: "الأخير",
    "Select Page Size": "اختر حجم الصفحة",
    "Sorry, no matching options": "عذرا, لم نجد ما تبحث عنه",
    "Your tag here": "اكتب الوسم هنا",
    "Your work has been saved": "تم الحفظ بنجاح",
    "You have mixed value in your form inputs, you will lose old data": "لديك قيم مختلفة في حالة الحفظ سيتم فقد بياناتك القديمة",
    "Suggested Tags": "أوسمة مقترحة",
    "Paste Tags here": "ألصق الأوسمة هنا",
    "Sync Those Tags": "مزامنة الأوسمة",
    Close: "إغلاق",
    "There is no any edit to send": "لا يوجد تعديل على  البيانات المدخلة",
    "There is an unfinished edit to tags or categories": "يوجد حالة كتابة غير منتهية في الأوسمة أو التصنيفات",
    "Please accept clipboard permission": "الرجاء قبول الإذن باستخدام الحافظة",
    ":count images": "{0} لم تحدد أي عنصر|{1} عنصر واحدة|{2} عنصران|{3} :count عناصر|{4} :count عنصر|{5} :count عنصر",
    "No Images Selected": "لم يتم اختيار أي عنصر",
    "Delete": "حذف",
    "Delete Selection":"حذف المحدد",
    "Are you sure ?": "هل أنت متأكد ؟",
    "Once deleted, you will not be able to recover those :count selected files!": "لن يمكنك استرجاع الملفات المحددة (:count) بعد الحذف!",
    "Once submitted, you will not be able to edit those :count selected files!": "لن يمكنك تعديل هذه الملفات بعد الإرسال",
    "Releases": "هل يتضمن محتواك أشخاص أو ممتلكات ؟  يجب إرفاق نماذج الموافقه بالضرورة:",
    "For recognizable people or property.": "وثائق للأشخاص أو الممتلكات.",
    "Upload a new release": "ارفع وثيقة أخرى",
    "Attach releases": "أرفق وثائق",
    "Add new release": "أضف وثيقة جديدة",
    "Release name": "اسم الوثيقة",
    "Release Type": "نوع الوثيقة",
    "Model release": "وثيقة لشخص",
    "Property release": "وثيقة لممتلكات",
    "Download a release form": "تحميل نماذج الوثيقة",
    "Choose file": "اختر ملفا",
    "Validation error, please try again.": "الرجاء التأكد من صحة البيانات.",
    "Please enter all required fields.": "الرجاء أدخال كافة الحقول المطلوبة .",
    "Submit For Review": "إرسال للمراجعة",
    "Submit For Data Entry": "إرسال لإدخال البيانات",
    "Submit For Data Entry by Arabstock":  "يمكنك ارسال المحتوى لعربستوك دون ادخال كافة الحقول باستثناء ادخال العنوان باللغة العربية او اللغة الانجليزية، و سيتم ادخال البيانات من قبل فريق محتوى عربستوك",
    "note content have a city or a tourist attraction":"ملاحظة : في حال كان المحتوى لمدينة او معلم سياحي  ينصح بكتابة العنوان باسم المعلم السياحي او المدينة  لتوضيح ذلك للمختصين في عربستوك .",
    "To submit": "للإرسال",
    "Pending": "قيد المراجعة",
    "Reviewed": "بعد المراجعة",
    "images": "الصور",
    "videos": "الفيديو",
    "vectors": "الفيكتور",
    "Status: ": "الحالة: ",
    "new": "جديد",
    "data_entry": "جاري الإدخال",
    "review": "قيد المراجعة",
    "reject": "مرفوض",
    "hard_reject": "مرفوض نهائيًا",
    "publish": "تم النشر",
    "processing": "قيد المعالجة ",
    "more": "المزيد",
    "and": "و",
    "ok": "نعم",
    "requirements": "المتطلبات",
    "license": "الترخيص",
    "add": "اضافة",
};
const locales_en = {
    Status: "Status",
    "Admin's Category": "Admin's Category",
    "Admin's Collection": "Admin's Collection",
    Collection : "Seller",
    Folder: "Folder",
    Sort: "Sort",
    "Select All": "Select All",
    "Reset Filters": "Reset Filters",
    "Reset Selections": "Reset Selections",
    "- Arabic Version": "- Arabic Version",
    "- English Version": "- English Version",
    Edit: "Edit",
    Title: "Title",
    Description: "Description",
    Tags: "Tags",
    Categories: "Categories",
    "(Optional)": "(Optional)",
    "Admin's Categories": "Admin's Categories",
    Submit: "Submit",
    Save: "Save",
    Cancel: "Cancel",
    "Showing :start to :end of :total entries": "Showing :start to :end of :total entries",
    "Processing...": "Processing...",
    "No data available in table": "No data available in table",
    First: "First",
    Previous: "Previous",
    Next: "Next",
    Last: "Last",
    "Select Page Size": "Select Page Size",
    "Sorry, no matching options": "Sorry, no matching options",
    "Your tag here": "Your tag here",
    "Your work has been saved": "Your work has been saved",
    "You have mixed value in your form inputs, you will lose old data": "You have mixed value in your form inputs, you will lose old data",
    "Suggested Tags": "Suggested Tags",
    "Paste Tags here": "Paste Tags here",
    "Sync Those Tags": "Sync Those Tags",
    Close: "Close",
    "There is no any edit to send": "There is no any edit to send",
    "Please accept clipboard permission": "Please accept clipboard permission",
    ":count images": "{0} no items selected|{1} Item|{2} :count Items",
    "No Images Selected": "No Items Selected",
    "Delete": "Delete",
    "Delete Selection":"Delete Selection",
    "Are you sure ?": "Are you sure ?",
    "Once deleted, you will not be able to recover those :count selected files!": "Once deleted, you will not be able to recover those :count selected files!",
    "Once submitted, you will not be able to edit those :count selected files!": "Once submitted, you will not be able to edit those :count selected files!",
    "Releases": "Does your content include people or property? The approval forms must be attached necessarily:",
    "For recognizable people or property.": "For recognizable people or property.",
    "Upload a new release": "Upload a new release",
    "Attach releases": "Attach releases",
    "Add new release": "Add new release",
    "Release name": "Release name",
    "Release Type": "Release Type",
    "Model release": "Model release",
    "Property release": "Property release",
    "Download a release form": "Download a release form",
    "Choose file": "Choose file",
    "Validation error, please try again.": "Validation error, please try again.",
    "Please enter all required fields.": "Please Enter all required fields.",
    "Submit For Review": "Submit For Review",
    "Submit For Data Entry": "Submit For Data Entry",
    "Submit For Data Entry by Arabstock": "You can send the content to Arabsstock without entering all the fields except for entering the title in arabic or english, and the data will be entered by  arabsstock content team",
    "note content have a city or a tourist attraction": "Note: If the content is for a city or a tourist attraction, it is recommended to write the address in the name of the tourist attraction or the city to clarify this to the specialists in Arabsstock.",
    "To submit": "To submit",
    "Pending": "Pending",
    "Reviewed": "Reviewed",
    "Images": "Images",
    "Videos": "Videos",
    "Vectors": "Vector",
    "Status: ": "Status: ",
    "new": "New",
    "data_entry": "Content editing",
    "review": "Under Review",
    "reject": "Reject",
    "hard_reject": "Hard Reject",
    "publish": "Published",
    "processing": "Processing",
    "more": "More",
    "and": "and",
    "ok": "ok",
    "requirements": "requirements",
    "license": "License",
    "add": "add",
};


if (document.querySelector("html").getAttribute("lang") === "ar") {
    locales = locales_ar;
} else {
    locales = locales_en;
}

Vue.mixin({
    data() {
        return {
            appLoading: false,
            layoutDirection: document.querySelector("html").getAttribute("lang") === "ar" ? "rtl" : "ltr",
            lang: document.querySelector("html").getAttribute("lang") === "ar" ? "ar" : "en",
            right: document.querySelector("html").getAttribute("lang") === "ar" ? "right" : "left",
            left: document.querySelector("html").getAttribute("lang") === "ar" ? "left" : "right"
        };
    },
    methods: {
        trans_choice: function (key, choice, values) {
            values = values || {};
            var result = locales[key].split("|")[choice];
            result = result.substring(result.indexOf("}") + 1).trim();

            const keys = Object.keys(values);
            for (const key of keys) {
                result = result.replace(":" + key, values[key]);
            }

            return result;
        },
        t: function (key, values) {
            values = values || {};

            var result = locales[key];
            if (result === undefined) {
                result = key;
                console.error("Key not defined in locales:", key);
                if (!window.translation_missing_keys) window.translation_missing_keys = {};
                window.translation_missing_keys[key] = key;
            }

            const keys = Object.keys(values);
            for (const key of keys) {
                result = result.replace(":" + key, values[key]);
            }

            return result;
        },
        p: function (key, count, values) {
            // trans_choice helper
            values = values || {};
            var choice = 0;
            if (this.lang === "ar") {
                if (count === 0) {
                    choice = 0;
                } else if (count === 1) {
                    choice = 1;
                } else if (count === 2) {
                    choice = 2;
                } else if (count % 100 >= 3 && count % 100 <= 10) {
                    choice = 3;
                } else if (count < 100) {
                    choice = 4;
                } else {
                    choice = 5;
                }
            } else {
                if (count === 0) {
                    choice = 0;
                } else if (count === 1) {
                    choice = 1;
                } else {
                    choice = 2;
                }
            }

            return this.trans_choice(key, choice, values);
        },
        // block page start
        blockPage: function(options) {
            return this.block('body', options);
        },

        unblockPage: function() {
            return this.unblock('body');
        },
        block: function(target, options) {
            var el = window.$(target);

            options = window.$.extend(true, {
                opacity: 0.05,
                overlayColor: '#000000',
                type: '',
                size: '',
                state: 'brand',
                centerX: true,
                centerY: true,
                message: '',
                shadow: true,
                width: 'auto'
            }, options);

            var html;
            var version = options.type ? 'kt-spinner--' + options.type : '';
            var state = options.state ? 'kt-spinner--' + options.state : '';
            var size = options.size ? 'kt-spinner--' + options.size : '';
            var spinner = '<div class="kt-spinner ' + version + ' ' + state + ' ' + size + '"></div';

            if (options.message && options.message.length > 0) {
                var classes = 'blockui ' + (options.shadow === false ? 'blockui' : '');

                html = '<div class="' + classes + '"><span>' + options.message + '</span><span>' + spinner + '</span></div>';

                el = document.createElement('div');
                window.$('body')[0].prepend(el);
                el.className += ' ' + classes
                el.innerHTML = '<span>' + options.message + '</span><span>' + spinner + '</span>';
                options.width = el.offsetWidth + 10;
                el.remove();

                if (target == 'body') {
                    html = '<div class="' + classes + '" style="margin-left:-' + (options.width / 2) + 'px;"><span>' + options.message + '</span><span>' + spinner + '</span></div>';
                }
            } else {
                html = spinner;
            }

            var params = {
                message: html,
                centerY: options.centerY,
                centerX: options.centerX,
                css: {
                    top: '30%',
                    left: '50%',
                    border: '0',
                    padding: '0',
                    backgroundColor: 'none',
                    width: options.width
                },
                overlayCSS: {
                    backgroundColor: options.overlayColor,
                    opacity: options.opacity,
                    cursor: 'wait',
                    zIndex: (target == 'body' ? 1100 : 10)
                },
                onUnblock: function() {
                    if (el && el[0]) {
                        el[0].style.position = ''
                        el[0].style.zoom = ''
                    }
                }
            };

            params.css.top = '50%';
            window.$.blockUI(params);
        },
        unblock: function(target) {
            if (target && target != 'body') {
                window.$(target).unblock();
            } else {
                window.$.unblockUI();
            }
        },
    },
    watch: {
        appLoading: function (val) {
            if (val) {
                this.blockPage({
                    overlayColor: "#000000",
                    type: "v2",
                    state: "success",
                    message: this.t("Processing...")
                });
            } else {
                this.unblockPage();
            }
        }
    }
});


new Vue({
    render: h => h(App)
}).$mount("#app");
