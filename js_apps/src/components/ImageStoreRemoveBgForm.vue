<template>
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">{{ t("Edit") }} {{ p(":count images", selectList.length, { count: selectList.length }) }}</h3>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" >
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label>{{ t("Title") }} {{ t("- Arabic Version") }}</label>
                    <input disabled @dblclick="enableInput('title_ar')" @change="copyName" @keyup="copyName" @keypress="copyName" v-model="title_ar" type="text" class="form-control" placeholder="" :readonly="title_ar === 'قيم مختلفة'" />
                </div>
                    <div class="btn-group col p-0 mt-3">
                        <template
                            v-if="removeBgStatusDone"
                        >
                        <button
                            type="button"
                            class="btn btn-success font-weight-bold"
                            @click="onSaveRemoveBgDisplay"

                        >
                           اعتماد {{selectList.length?selectList.length:''}}
                        </button>
                        <button
                            type="button"
                            class="btn btn-info font-weight-bold"
                            @click="onSave"
                        >
                            {{ t("removebg") }} {{selectList.length?selectList.length:''}}
                        </button>     
                        </template>
                        <template
                            v-else
                        >
                        <button
                            type="button"
                            class="btn btn-info font-weight-bold"
                            @click="onSave"
                        >
                            {{ t("removebg") }} {{selectList.length?selectList.length:''}}
                        </button>                        

                        </template>
                    </div>


            </div>


        </form>

        <!--end::Form-->
    </div>
</template>

<script>
import { isEqual, uniq, findIndex } from "lodash";

function lowerCaseComputerVisionTags(tag) {
    tag.value = tag.value.toLowerCase();
    return tag;
}

export default {
    props: {
        formOptions: {},
        selectIndex: {},
        selectList: {},
        updateSelectedTitle: {},
        doUpdateMulti: {},
        doSubmit: {},
        selectListPreview:{},
        removeBgStatusDone:{},
        doUpdateMultiDisplay:{}
    },
      computed: {
    isDisabled(val) {
        console.log('val');
        console.log(val);
      return true;
    }
  },
    watch: {
        selectList() {
            this.changeFormProps();
        },
        selectListPreview: {
            immediate: true,
            handler() {
            var items  = this.selectListPreview.filter(function(obj) { return (obj.status_contributor_file === 'rejected' || obj.status_contributor_file === 'hard_rejected' ) ; });
            (items.length > 0) ?this.statusReject = true:this.statusReject = false;

            },
        },
    },
    created() {
        this.changeFormProps();
    },
    data() {
        return {
            mixedValueWarning: false,
            statusReject:true,
            removebg_can:undefined,
            removebg_status:'',
            removebg_status_disply:'',
            removebg_type:'paid',
            selectLicense:'',
        };
    },
    methods: {
        enableInput(key) {
            if (this[key] === "قيم مختلفة" || this[key] === "mix value") {
                this[key] = "";
            }
        },
        copyName() {
            if (!(this.title_ar === "قيم مختلفة" || this.title_ar === "mix value")) {
                this.updateSelectedTitle(this.title_ar);
            }
        },
        async onSave() {
            const inputOptions = new Promise((resolve) => {
            setTimeout(() => {
            resolve({
            // 'free': this.t("free"),
            'paid': this.t("paid"),
            })
            }, 500)
            })
               const { value: removebg } = await window.swal.fire({
                    type: "info",
                    input: 'radio',
                    allowEscapeKey:false,
                    inputValue: this.removebg_type === 'free'?'paid':this.removebg_type,
                    inputOptions: inputOptions,
                    title: this.t("removebg")+" "+this.selectList.length,
                    showConfirmButton: true,
                    showCancelButton:true,
                    inputValidator: (value) => {
                    if (!value) {
                    return 'You need to choose something!'
                    }
                    }
                })
                let removebg_status = removebg??this.removebg_type;
                if(removebg){
                    this.doUpdateMulti(this.selectList, removebg_status)
                }
  
        },
        async onSaveRemoveBgDisplay() {
                const inputDisplayOptions = new Promise((resolve) => {
                setTimeout(() => {
                resolve({
                'active': this.t("yes"),
                'pending': this.t("no"),
                })
                }, 500)
                })
                const { value: display } = await window.swal.fire({
                    type: "info",
                    input: 'radio',
                    allowEscapeKey:false,
                    allowOutsideClick:false,
                    inputOptions: inputDisplayOptions,
                    title: this.t("removebg")+" اعتماد "+this.selectList.length,
                    showConfirmButton: true,
                    showCancelButton:true,
                    inputValidator: (value) => {
                    if (!value) {
                    return 'You need to choose something!'
                    }
                    }

                })

            if(display){
            this.doUpdateMultiDisplay(this.selectList,display)
            }
        },
        
        changeFormProps() {
            this.admin_categories_is_mix = "";
            let tags_ar = [];
            let tags_en = [];
            let computer_vision_tags_ar_options = [];
            let computer_vision_tags_en_options = [];
            let categories = [];
            let firstItem = this.selectList[0] ? this.selectIndex[this.selectList[0]] : {};
            let title_ar = firstItem.title_ar;
            let title_en = firstItem.title_en;
  
            
            let status = firstItem.status;
            let description_ar = firstItem.description_ar;
            let description_en = firstItem.description_en;
            this.selectLicense = firstItem.license
            for (const key in this.selectList) {
                console.log('key' , key)
                console.log(this.selectIndex[this.selectList[key]])
            }
            for (let i = 0; i < this.selectList.length; i++) {
                let item = this.selectIndex[this.selectList[i]];
                tags_ar = tags_ar.concat(item.tags_ar);
                tags_en = tags_en.concat(item.tags_en);
                computer_vision_tags_ar_options = computer_vision_tags_ar_options.concat(item.computer_vision_tags_ar.map(lowerCaseComputerVisionTags));
                computer_vision_tags_en_options = computer_vision_tags_en_options.concat(item.computer_vision_tags_en.map(lowerCaseComputerVisionTags));
                categories = categories.concat(item.category_ids);
            }
            tags_ar = uniq(tags_ar);
            tags_en = uniq(tags_en);
            categories = this.uniqueNested(categories, "id");

            let isTagsArEqual = true;
            for (let i = 0; i < this.selectList.length; i++) {
                for (let k = 0; k < this.selectList.length; k++) {
                    if (i !== k && !isEqual(this.selectIndex[this.selectList[i]].tags_ar.sort(), this.selectIndex[this.selectList[k]].tags_ar.sort())) {
                        isTagsArEqual = false;
                    }
                }
            }

            let isTagsEnEqual = true;
            for (let i = 0; i < this.selectList.length; i++) {
                for (let k = 0; k < this.selectList.length; k++) {
                    if (i !== k && !isEqual(this.selectIndex[this.selectList[i]].tags_en.sort(), this.selectIndex[this.selectList[k]].tags_en.sort())) {
                        isTagsEnEqual = false;
                    }
                }
            }

            let isCategoriesEqual = true;
            for (let i = 0; i < this.selectList.length; i++) {
                for (let k = 0; k < this.selectList.length; k++) {
                    if (
                        i !== k &&
                        !isEqual(
                            this.selectIndex[this.selectList[i]].category_ids.sort((a, b) => a.id - b.id),
                            this.selectIndex[this.selectList[k]].category_ids.sort((a, b) => a.id - b.id)
                        )
                    ) {
                        isCategoriesEqual = false;
                    }
                }
            }

            let isTitleArEqual = true;
            for (let i = 0; i < this.selectList.length; i++) {
                for (let k = 0; k < this.selectList.length; k++) {
                    if (i !== k && this.selectIndex[this.selectList[i]].title_ar !== this.selectIndex[this.selectList[k]].title_ar) {
                        isTitleArEqual = false;
                    }
                }
            }

            let isTitleEnEqual = true;
            for (let i = 0; i < this.selectList.length; i++) {
                for (let k = 0; k < this.selectList.length; k++) {
                    if (i !== k && this.selectIndex[this.selectList[i]].title_en !== this.selectIndex[this.selectList[k]].title_en) {
                        isTitleEnEqual = false;
                    }
                }
            }

            let isCategoryAdminEqual = true;
            for (let i = 0; i < this.selectList.length; i++) {
                for (let k = 0; k < this.selectList.length; k++) {
                    if (i !== k && !isEqual(this.selectIndex[this.selectList[i]].category_admin_id, this.selectIndex[this.selectList[k]].category_admin_id)) {
                        this.admin_categories_is_mix = "قيم مختلفة";
                        isCategoryAdminEqual = false;
                    }
                }
            }

            let isDescriptionArEqual = true;
            for (let i = 0; i < this.selectList.length; i++) {
                for (let k = 0; k < this.selectList.length; k++) {
                    if (i !== k && this.selectIndex[this.selectList[i]].description_ar !== this.selectIndex[this.selectList[k]].description_ar) {
                        isDescriptionArEqual = false;
                    }
                }
            }

            let isDescriptionEnEqual = true;
            for (let i = 0; i < this.selectList.length; i++) {
                for (let k = 0; k < this.selectList.length; k++) {
                    if (i !== k && this.selectIndex[this.selectList[i]].description_en !== this.selectIndex[this.selectList[k]].description_en) {
                        isDescriptionEnEqual = false;
                    }
                }
            }

            this.computer_vision_tags_ar_options = this.uniqueNested(computer_vision_tags_ar_options, "value");
            this.computer_vision_tags_en_options = this.uniqueNested(computer_vision_tags_en_options, "value");
            this.computer_vision_tags_ar = this.computer_vision_tags_ar_options.filter(item => tags_ar.indexOf(item.value) > -1);
            this.computer_vision_tags_en = this.computer_vision_tags_en_options.filter(item => tags_en.indexOf(item.value) > -1);

            if (this.selectList.length === 1) {
                this.mixedValueWarning = false;
                this.title_ar = title_ar;
                this.title_en = title_en;
                this.status = status;
                this.description_ar = description_ar;
                this.description_en = description_en;
                this.tags_ar = tags_ar;
                this.tags_en = tags_en;
                this.categories = categories;
                this.admin_categories = firstItem.category_admin_id;
                this.removebg_status = firstItem.removebg_status;
                this.removebg_status_disply = firstItem.removebg_status_disply;
                this.removebg_type = firstItem.removebg_type;
                this.removebg_can = firstItem.removebg_can;
                
            } else {
                if (isTitleArEqual) {
                    this.title_ar = title_ar;
                } else {
                    this.title_ar = "قيم مختلفة";
                }

                if (isTitleEnEqual) {
                    this.title_en = title_en;
                } else {
                    this.title_en = "mix value";
                }

                if (isDescriptionArEqual) {
                    this.description_ar = description_ar;
                } else {
                    this.description_ar = "قيم مختلفة";
                }

                if (isDescriptionEnEqual) {
                    this.description_en = description_en;
                } else {
                    this.description_en = "mix value";
                }

                if (isTagsArEqual) {
                    this.tags_ar = tags_ar;
                } else {
                    this.tags_ar = ["قيم مختلفة"];
                }

                if (isTagsEnEqual) {
                    this.tags_en = tags_en;
                } else {
                    this.tags_en = ["mix value"];
                }

                if (isCategoriesEqual) {
                    this.categories = categories;
                } else {
                    this.categories = [{ id: 0, label: "mix value" }];
                }

                if (isCategoryAdminEqual) {
                    this.admin_categories = firstItem.category_admin_id;
                } else {
                    this.admin_categories = [];
                }
            if (this.selectList.length > 1) {
                let removebg_status_array = [];
                let removebg_status_disply_array = [];
                for (const key in this.selectList) {
                        removebg_status_disply_array.push(this.selectIndex[this.selectList[key]].removebg_status_disply);
                        removebg_status_array.push(this.selectIndex[this.selectList[key]].removebg_status);
                } 
                this.removebg_status_disply = removebg_status_disply_array.includes('pending')?"pending":"active";
            }
                this.removebg_status = '';

            }
        },
        uniqueNested(array, key) {
            return array.filter((elem, pos, arr) => {
                return findIndex(arr, { [key]: elem[key] }) === pos;
            });
        },

    }
};
</script>
