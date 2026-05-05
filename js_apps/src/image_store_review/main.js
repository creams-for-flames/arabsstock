import Vue from "vue";
import App from "./App.vue";

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

window.dataStore = {
    images: []
};

let locales;
const locales_ar = {
    Status: "الحالة",
    "Admin's Category": "تصنفيات المدير",
    "Admin's Collection": "تجميعة المدير",
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
    Refuse:"رفض",
    refuse_reason :"سبب الرفض",
    Publish:"نشر",
    RePublish:"اعادة نشر",
};
const locales_en = {
    Status: "Status",
    "Admin's Category": "Admin's Category",
    "Admin's Collection": "Admin's Collection",
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
    Refuse:"Refuse",
    refuse_reason:"Refuse Reason",
    Publish:"Publish",
    RePublish:"RePublish"
};

if (document.querySelector("html").getAttribute("dir") === "rtl") {
    locales = locales_ar;
} else {
    locales = locales_en;
}

Vue.mixin({
    data() {
        return {
            appLoading: false,
            layoutDirection: document.querySelector("html").getAttribute("dir") === "rtl" ? "rtl" : "ltr",
            lang: document.querySelector("html").getAttribute("dir") === "rtl" ? "ar" : "en",
            right: document.querySelector("html").getAttribute("dir") === "rtl" ? "right" : "left",
            left: document.querySelector("html").getAttribute("dir") === "rtl" ? "left" : "right"
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
        }
    },
    watch: {
        appLoading: function (val) {
            if (val) {
                window.KTApp.blockPage({
                    overlayColor: "#000000",
                    type: "v2",
                    state: "success",
                    message: this.t("Processing...")
                });
            } else {
                window.KTApp.unblockPage();
            }
        }
    }
});


new Vue({
    render: h => h(App)
}).$mount("#app");
