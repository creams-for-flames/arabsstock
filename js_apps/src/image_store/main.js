import Vue from "vue";
import App from "./App.vue";

// import * as Sentry from "@sentry/browser";
// import { Vue as VueIntegration } from "@sentry/integrations";

if (process.env.NODE_ENV === "production") {
    // Sentry.init({
    //     dsn: "https://a3ef3c52d7e04f829ea5a623a2bdf77c@o127645.ingest.sentry.io/5220909",
    //     integrations: [new VueIntegration({ Vue, attachProps: true })]
    // });
}
console.log("Arabsstock in ", process.env.NODE_ENV);

import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

Vue.component("v-select", vSelect);

Vue.config.productionTip = false;
// Vue.config.devtools = true;
// Vue.config.debug = true
// Vue.config.silent = true

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
    page: "رقم الصفحة",
    Contributor: "المساهم",
    "Select All": "تحديد الكل",
    "search": "بحــث",
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
    CategoriesContributer: "تصنيفات المساهمين",
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
    "Releases": "موافقة بالظهور",
    "For recognizable people or property.": "وثائق للأشخاص أو الممتلكات.",
    "Upload a new release": "ارفع وثيقة أخرى",
    "Attach releases": "أرفق وثائق",
    "Add new release": "أضف وثيقة جديدة",
    "Release name": "اسم الوثيقة",
    "Release Type": "نوع الوثيقة",
    "Model release": "وثيقة لشخص",
    "Property release": "وثيقة لممتلكات",
    "Download a release form": "نزّل نموذج لوثيقة",
    "Choose file": "اختر ملفا",
    "Validation error, please try again.": "الرجاء التأكد من صحة البيانات.",
    "Submit For Review": "إرسال للمراجعة",
    "Submit For Data Entry": "إرسال لإدخال محتوى",
    "Notes": "الملاحظات",
    "Reject": "رفض",
    "publisher": "المشرف",
    "publisher_type": "نوع الناشر",
    "removebg_status":"حالة التفريغ",
    "Hard Reject": "رفض نهائي",
    "Publish": "نشر",
    "RePublish":"اعادة نشر",
    "Are you sure ?": "هل أنت متأكد?",
    "Please enter the Note field required.": "الرجاء إدخال حقل الملاحظات المطلوبة.",
    "Please enter all required fields.": "الرجاء أدخال كافة الحقول المطلوبة .",
    "\":action\" those :count selected files!": "\":action\" العناصر :count المحددة!",
    "YouMustVideoHaveCategories":'عذرا لا يمكن النشر ,يجب اختيار التصنيفات للملفات المحددة ',
    "license": "الترخيص",
    "Viewer consent forms":"نماذج موافقة العارض",
    "Notes: To modify the content status of the completed modifications":"ملاحظات : لتعديل حالة  المحتوى لمكتمل التعديلات",
    "The title in English is not equal to the title in Arabic and vice versa.":"العنوان باللغة الانجليزية لا يساوي العنوان باللغة العربية و العكس ايضا .",
    "The title in Arabic does not contain English letters.":"العنوان باللغة العربية لا يحتوي على احرف اللغة الانجليزية .",
    "The title should not contain characters except for regex and it is preferable not to use them frequently.":"يجب الا يحتوي العنوان رموز  باستناء  '%$#,,_, )( \"' و يفضل  عدم استخدامها بكثرة .",
    "Preferably write the title in Arabic without movements.":"يفضل كتابة العنوان باللغة العربية  دون حركات .",
    "The file must contain tags in English and Arabic.":"يجب ان يحتوي الملف على اوسمة باللغة الانجليزية و العربية .",
    "The file must have a category .":"يجب ان يحتوي الملف على تصنيف ",
    "RejectionReason":"اسباب الرفض",
    "Agree to the edit":"موافقة على التعديل",
    "live":"لايف عربستوك",
    "SubmitToSaveContributorData":"حفظ تعديلات المساهم قبل الموافقة",
    "removebg":"تفريغ الصورة",
    "no":"لا",
    "yes":"نعم",
    'accreditation_status':"حالة الاعتماد",
    'removebg_type':"نوع التفريغ",
    'free':'مجاني',
    'paid':'مدفوع',
    'queue':'تفريغ',
    'processing':'قيد المعالجة',
    'active':'تم التفريغ',
    "removebg_status_disply":"حالة اعتماد التفريغ",

};
const locales_en = {
    Status: "Status",
    "Admin's Category": "Admin's Category",
    "Admin's Collection": "Admin's Collection",
    Folder: "Folder",
    Sort: "Sort",
    Contributor: "Contributor",
    "Select All": "Select All",
    "search":"search",
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
    CategoriesContributer: "Categories Contributer",
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
    "Releases": "Releases",
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
    "Submit For Review": "Submit For Review",
    "Submit For Data Entry": "Submit For Data Entry",
    "Notes": "Notes",
    "Reject": "Reject",
    "publisher": "publisher",
    "publisher_type": "publisher type",
    "removebg_status":'removebg status',
    "Hard Reject": "Hard",
    "Publish": "Publish",
    "RePublish":"RePublish",
    "Are you sure ?": "Are you sure ?",
    "Please enter the Note field required.": "Please enter the Notes field required.",
    "Please enter all required fields.": "Please Enter all required fields.",
    "\":action\" those :count selected files!": "\":action\" those :count selected files!",
    YouMustVideoHaveCategories:'Sorry , it\'s not possible to publish , Categories must be selected for the specfied files ',
    "license": "License",
    "Viewer consent forms":"Viewer consent forms",
    "Notes: To modify the content status of the completed modifications":"Notes: To modify the content status of the completed modifications",
    "The title in English is not equal to the title in Arabic and vice versa.":"The title in English is not equal to the title in Arabic and vice versa.",
    "The title in Arabic does not contain English letters.":"The title in Arabic does not contain English letters.",
    "The title should not contain characters except for regex and it is preferable not to use them frequently.":"The title should not contain characters except for '%$#,,_, )( \"' and it is preferable not to use them frequently.",
    "Preferably write the title in Arabic without movements.":"Preferably write the title in Arabic without movements.",
    "The file must contain tags in English and Arabic.":"The file must contain tags in English and Arabic.",
    "The file must have a category .":"The file must have a category .",
    "RejectionReason":"Rejection Reasons",
    "Agree to the edit":"Agree to the edit",
    "live":"live  Arabsstock",
    "SubmitToSaveContributorData":"Submit To Save Contributor Data",
    "removebg":"Remove background",
    "no":"No",
    "yes":"Yes",
    'accreditation_status':"Accreditation Status",
    'removebg_type':"removebg type",
    'free':"free",
    'paid':"paid",
    'queue':"removebg",
    'processing':"processing",
    'active':"done",
    "removebg_status_disply":"removebg status disply",



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
