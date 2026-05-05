<template>
    <div class="p-4 border rounded bg-white">

                <h5 class="mt-0 mb-3 pb-3 border-bottom">{{ t("Edit") }} {{ p(":count images", selectList.length, { count: selectList.length }) }}</h5>


        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="onSave">
            <div class="kt-portlet__body">
                    <div class="row mb-4">
                        <div class="col-lg-12 col-xl-12 text-white">
                            <div class="alert alert-info text-capitalize" role="alert">
                                {{ t("Submit For Data Entry by Arabstock") }}
                            </div>
                            <hr/>
                            <div class="alert alert-danger text-capitalize" role="alert">
                            {{ t("note content have a city or a tourist attraction") }}
                            </div>
                        </div>
                    </div>
                <div class="form-group">
                    <label :class="{'text-danger':title_ar === ''}">{{ t("Title") }} {{ t("- Arabic Version") }} * </label>
                    <input @dblclick="enableInput('title_ar')" @change="copyName" @keyup="copyName" @keypress="copyName" v-model="title_ar" type="text" class="form-control" placeholder="" :readonly="title_ar === 'قيم مختلفة'" />
                </div>
                <div class="form-group">
                    <label :class="{'text-danger':title_en === ''}">{{ t("Title") }} {{ t("- English Version") }} *</label>
                    <input @dblclick="enableInput('title_en')" v-model="title_en" type="text" class="form-control" placeholder="" :readonly="title_en === 'mix value'" />
                </div>
                <div class="form-group">
                    <label :class="{'text-danger':tags_ar.length === 0}">{{ t("Tags") }} {{ t("- Arabic Version") }} * </label>

                    <v-select  v-model="tags_ar" @input="lowerCaseTags('tags_ar', $event)" taggable multiple :close-on-select="false" :dir="layoutDirection" :push-tags="true">
                        <div slot="no-options">{{ t("Your tag here") }}</div>
                    </v-select>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button class="btn btn-secondary" type="button" data-clipboard="true" :data-clipboard-text="tags_ar.toString()"><i class="far fa-copy"></i></button>
                            <button class="btn btn-secondary" type="button" @click="paste('tags_ar')"><i class="far fa-clipboard"></i></button>
                            <button class="btn btn-secondary" type="button" @click="fireExternalSourceTags('tags_ar')"><i class="far fa-link"></i></button>

                             <button class="btn btn-secondary" type="button" @click="showComputerVisionTagsModal('tags_ar')"><i class="far fa-info-circle"></i></button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label :class="{'text-danger':tags_en.length === 0}">{{ t("Tags") }} {{ t("- English Version") }} *</label>
                    <v-select  v-model="tags_en" @input="lowerCaseTags('tags_en', $event)" taggable multiple :close-on-select="false" :dir="layoutDirection" :push-tags="true">
                        <div slot="no-options">{{ t("Your tag here") }}</div>
                    </v-select>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button class="btn btn-secondary" type="button" data-clipboard="true" :data-clipboard-text="tags_en.toString()"><i class="far fa-copy"></i></button>
                            <button class="btn btn-secondary" type="button" @click="paste('tags_en')"><i class="far fa-clipboard"></i></button>
                            <button class="btn btn-secondary" type="button" @click="fireExternalSourceTags('tags_en')"><i class="far fa-link"></i></button>

                             <button class="btn btn-secondary" type="button" @click="showComputerVisionTagsModal('tags_ar')"><i class="far fa-info-circle"></i></button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label :class="{'text-danger':categories.length === 0}" >{{ t("Categories") }} *</label>
                    <v-select  v-model="categories" multiple :options="formOptions.categories" :dir="layoutDirection">
                        <div slot="no-options">{{ t("Sorry, no matching options") }}</div>
                    </v-select>
                </div>

                <div class="separator separator-dashed my-8"></div>
                <div class="form-group mb-4" v-if="formOptions.licenses"  >
                    <label >{{ t("license") }} </label>
                <div class="separator separator-dashed"></div>
                    <template v-for="(item,index) in formOptions.licenses" >
                        <div class="form-check form-check-inline" :key="index">
                        <input class="form-check-input" :value="item.name" v-model="selectLicense" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                        <label class="form-check-label" for="flexRadioDefault1">
                           {{item.title}}
                        </label>
                        </div>
                    </template>
                </div>
                <div class="form-group mb-4" v-if="selectLicense  == 'commercial' && formOptions.licenses">


                            <label class="option">
                                <span class="option-label">
                                    <span class="option-head">
                                        <span class="option-title">{{t("Releases")}}</span>
                                        <span @click="showReleasesModal" class="option-focus border pr-1 pl-1 pt-2 text-capitalize " style="cursor: pointer;display: inline-flex;height: 36px;"><i class="far fa-plus p-1"></i> {{t("add")}}</span>
                                    </span>
                                    <span class="option-body"></span>
                                    <span class="option-body"></span>
                                    <div v-for="release in releases" :key="release.id" class="alert alert-secondary alert-dismissible fade show" role="alert">
                                        <a :href="release.file">{{release.label}}</a>
                                        <span  @click.prevent="deleteRelease(release)"  class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </span>
                                    </div>

                                     <span class="option-head "><span @click="downloadFile" class="option-title" style="cursor: pointer;text-decoration: underline;color: #20d598;"  target="_blank">{{t("Download a release form")}}</span></span>


                                </span>
                            </label>


                </div>

                <div class="modal fade" id="releases-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ t("Attach releases") }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <v-select v-model="releases" :options="formOptions.releases" multiple :close-on-select="false" :dir="layoutDirection">
                                    <div slot="no-options">{{ t("Sorry, no matching options") }}</div>
                                </v-select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal" >{{ t("Save") }}</button>
                                <button @click.prevent="showCreateReleaseModal" type="button" class="btn btn-primary">{{ t("Upload a new release") }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="suggested-tags-ar-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ t("Suggested Tags") }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <v-select v-model="computer_vision_tags_ar" :options="computer_vision_tags_ar_options" multiple :close-on-select="false" :dir="layoutDirection">
                                    <div slot="no-options">{{ t("Sorry, no matching options") }}</div>
                                </v-select>
                            </div>
                            <div class="modal-footer">
                                <button @click.prevent="addComputerVisionTagsAr" type="button" class="btn btn-primary">{{ t("Save") }}</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ t("Close") }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="suggested-tags-en-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ t("Suggested Tags") }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <v-select v-model="computer_vision_tags_en" :options="computer_vision_tags_en_options" multiple :close-on-select="false" :dir="layoutDirection">
                                    <div slot="no-options">{{ t("Sorry, no matching options") }}</div>
                                </v-select>
                            </div>
                            <div class="modal-footer">
                                <button @click.prevent="addComputerVisionTagsEn" type="button" class="btn btn-primary">{{ t("Save") }}</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ t("Close") }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-5 col-xl-5">
                            <button type="submit" class="btn btn-primary btn-block">{{ t("Submit") }}</button>
                        </div>
                        <div class="col-lg-2 col-xl-2"></div>

                    </div>
                    <div class="separator my-8"></div>
                    <div class="row"></div>
                    <div class="row"></div>
                    <div class="row">
                        <div class="col-lg-12 col-xl-12">
                            <a @click="submitHandler('review')" class="btn btn-primary btn-block text-white"> {{ t("Submit For Review") }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <ReleaseForm :save-release="saveRelease" :status="createReleaseFormStatus"/>
        <!--end::Form-->
    </div>
</template>

<script>
import { isEqual, uniq, findIndex, difference, differenceBy } from "lodash";
import { Changeset } from "validated-changeset";
import help_clipboard from "./help_clipboard.png";
import ReleaseForm from "./ReleaseForm";

export default {
    components: {
        ReleaseForm,
    },
    props: {
        formOptions: {},
        selectIndex: {},
        selectList: {},
        updateSelectedTitle: {},
        deleteSelections: {},
        doUpdateMulti:{},
        user: {},
        saveRelease: {},
        doSubmit: {},
        doReSubmit: {},
        stage: {},
        selectListPreview: {},
        dataType:{},
    },
    watch: {
        selectList() {
            this.changeFormProps();
        },
        selectListPreview: {
            immediate: true,
            handler() {
            var items  = this.selectListPreview.filter(function(obj) { return obj.action_delete === false });
             this.itemsCanDelete  = this.selectListPreview.filter(function(obj) { return obj.action_delete === true });

            this.statusDelete = (items.length === 0 && this.itemsCanDelete .length > 0) ?true:false;

            },
        },
    },
    created() {
        this.changeFormProps();
        this.$nextTick(function () {
            window.$("#suggested-tags-ar-modal").on("hide.bs.modal", this.closeComputerVisionTagsModal);
            window.$("#suggested-tags-en-modal").on("hide.bs.modal", this.closeComputerVisionTagsModal);
            window.$("#releases-modal").on("hide.bs.modal", this.closeReleasesModal);
        });
    },
    data() {
        return {
            createReleaseFormStatus: false,
            mixedValueWarning: false,
            statusDelete:false,
            title_ar: "",
            selectLicense:'commercial',
            title_en: "",
            description_ar: "",
            description_en: "",
            tags_ar: [],
            tags_en: [],
            categories: [],
            admin_categories: "",
            admin_categories_is_mix: "",
            computer_vision_tags_ar: [],
            computer_vision_tags_en: [],
            computer_vision_tags_ar_old: [],
            computer_vision_tags_en_old: [],
            computer_vision_tags_ar_options: [],
            computer_vision_tags_en_options: [],
            releases: [],
            itemsCanDelete:[],
        };
    },
    methods: {
        deleteRelease: function(release) {
        this.releases.splice(this.releases.indexOf(release), 1);
        },
        lowerCaseTags(key, tags) {
            if (key === "tags_ar") {
                this.tags_ar = uniq(tags.map(tag => tag.toLowerCase()));
            } else if (key === "tags_en") {
                this.tags_en = uniq(tags.map(tag => tag.toLowerCase()));
            }
        },
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
        onSave() {
            if(!this.checkUnfinishedEditTagsorCategories())
                return false;
           var dataWithStatus =  this.readyDataSaveWithStatusChange();

            if (dataWithStatus.noChanges) {
                window.swal.fire({
                    position: "top-right",
                    type: "error",
                    title: this.t("There is no any edit to send"),
                    showConfirmButton: false,
                    timer: 3000
                });
                return false;
            }
            // TODO check when empty array and dont send request
            this.doUpdateMulti(dataWithStatus.changedData, dataWithStatus.options)

            
        },
        checkUnfinishedEditTagsorCategories(){
            let vSelectIsValid = true
            let vSelectInputs = document.querySelectorAll(".vs__search");
            for (let i = 0; i < vSelectInputs.length; i++) {
                if (vSelectInputs[i].value !== "") {
                    vSelectIsValid = false
                }
            }

            if (!vSelectIsValid) {
            window.swal.fire({
            position: "top-right",
            type: "error",
            title: this.t("There is an unfinished edit to tags or categories"),
            showConfirmButton: false,
            timer: 3000
            });
            }
            return vSelectIsValid;
        },
        readyDataSaveWithStatusChange(){

            let readyData = {
                tags_ar: this.tags_ar.sort(),
                tags_en: this.tags_en.sort(),
                category_ids: this.categories.sort((a, b) => a.id - b.id),
                category_admin_id: this.admin_categories,
            };

            if( this.dataType != 'vectors' ){
                if (this.selectLicense == 'commercial') {
                    readyData.release_ids = this.releases.sort((a, b) => a.id - b.id);
                    readyData.license = this.selectLicense;
                }else{
                    readyData.license = this.selectLicense;
                }
            }


            let options = {
                tags_ar_delete_old: 1,
                tags_en_delete_old: 1,
                category_ids_delete_old: 1,
                release_ids_delete_old: 1,
                license : this.selectLicense
            };

            // mix value for array mean dont delete old before insert
            if (this.tags_ar.indexOf("قيم مختلفة") > -1) {
                options.tags_ar_delete_old = 0;
                readyData.tags_ar = readyData.tags_ar.filter(tag => tag !== "قيم مختلفة");
            }

            if (this.tags_en.indexOf("mix value") > -1) {
                options.tags_en_delete_old = 0;
                readyData.tags_en = readyData.tags_en.filter(tag => tag !== "mix value");
            }

            if (this.categories.map(x => x.id).indexOf(0) > -1) {
                options.category_ids_delete_old = 0;
                readyData.category_ids = readyData.category_ids.filter(cat => cat.id !== 0);
            }
            if(this.dataType !== "vectors"){
                if (this.releases.map(x => x.id).indexOf(0) > -1) {
                    options.release_ids_delete_old = 0;
                    readyData.release_ids = readyData.release_ids.filter(cat => cat.id !== 0);
                }

            }

            if (this.title_ar !== "قيم مختلفة") {
                readyData.title_ar = this.title_ar;
            }

            if (this.title_en !== "mix value") {
                readyData.title_en = this.title_en;
            }

            if (this.description_ar !== "قيم مختلفة") {
                readyData.description_ar = this.description_ar;
            }

            if (this.description_en !== "mix value") {
                readyData.description_en = this.description_en;
            }

            let noChanges = true;
            let changedData = {};
            for (let i = 0; i < this.selectList.length; i++) {
                let temp = {};
                let image = this.selectIndex[this.selectList[i]];
                let changeset = Changeset(image);
                let keys = Object.keys(readyData);
                for (let k = 0; k < keys.length; k++) {
                    if (keys[k] === "category_ids") {
                        if (
                            !isEqual(
                                readyData.category_ids,
                                image.category_ids.sort((a, b) => a.id - b.id)
                            )
                        ) {
                            if (options.category_ids_delete_old === 1) {
                                changeset.set(keys[k], readyData[keys[k]]);
                            } else {
                                let catIds = image.category_ids.map(cat => cat.id);
                                changeset.set(
                                    keys[k],
                                    readyData[keys[k]].filter(cat => catIds.indexOf(cat.id) === -1)
                                );
                            }
                        }
                    } else if (keys[k] === "release_ids") {
                        if (
                            !isEqual(
                                readyData.release_ids,
                                image.release_ids.sort((a, b) => a.id - b.id)
                            )
                        ) {
                            if (options.release_ids_delete_old === 1) {
                                changeset.set(keys[k], readyData[keys[k]]);
                            } else {
                                let catIds = image.release_ids.map(cat => cat.id);
                                changeset.set(
                                    keys[k],
                                    readyData[keys[k]].filter(cat => catIds.indexOf(cat.id) === -1)
                                );
                            }
                        }
                    } else if (keys[k] === "tags_en") {
                        if (!isEqual(readyData.tags_en, image.tags_en.sort())) {
                            if (options.tags_en_delete_old === 1) {
                                changeset.set(keys[k], readyData[keys[k]]);
                            } else {
                                changeset.set(
                                    keys[k],
                                    readyData[keys[k]].filter(tag => image.tags_en.indexOf(tag) === -1)
                                );
                            }
                        }
                    } else if (keys[k] === "tags_ar") {
                        if (!isEqual(readyData.tags_ar, image.tags_ar.sort())) {
                            if (options.tags_ar_delete_old === 1) {
                                changeset.set(keys[k], readyData[keys[k]]);
                            } else {
                                changeset.set(
                                    keys[k],
                                    readyData[keys[k]].filter(tag => image.tags_ar.indexOf(tag) === -1)
                                );
                            }
                        }
                    } else if (keys[k] === "category_admin_id") {
                        if (readyData.category_admin_id === "") {
                            readyData.category_admin_id = null;
                        }
                        changeset.set(keys[k], readyData[keys[k]]);
                    } else {
                        changeset.set(keys[k], readyData[keys[k]]);
                    }
                }
                if (changeset.changes.length) {
                    if (options.tags_ar_delete_old === 0) {
                        temp.tags_ar = image.tags_ar;
                    }
                    if (options.tags_en_delete_old === 0) {
                        temp.tags_en = image.tags_en;
                    }
                    if (options.category_ids_delete_old === 0) {
                        temp.category_ids = image.category_ids;
                    }
                    if (options.release_ids_delete_old === 0) {
                        temp.release_ids = image.release_ids;
                    }

                    changeset.save();

                    if (image.title_ar && image.title_en && image.category_ids.length && image.tags_en.length && image.tags_ar.length) {
                        changeset.set("stage_edit", 2);
                        changeset.save();
                    } else if (image.stage_edit === 0) {
                        changeset.set("stage_edit", 1);
                        changeset.save();
                    }

                    changedData[this.selectList[i]] = changeset.changes
                        .filter(change => {
                            if (change.key === "category_admin_id") {
                                return !!change.value;
                            }
                            return true;
                        })
                        .map(change => {
                            if (change.key === "category_admin_id") {
                                return { [change.key]: change.value.id };
                            }
                            if (change.key === "category_ids") {
                                return { [change.key]: change.value.map(item => item.id) };
                            }
                            if (change.key === "release_ids") {
                                return { [change.key]: change.value.map(item => item.id) };
                            }
                            return { [change.key]: change.value };
                        });
                    noChanges = false;

                    if (temp.tags_ar) {
                        image.tags_ar = temp.tags_ar.concat(image.tags_ar).sort();
                    }
                    if (temp.tags_en) {
                        image.tags_en = temp.tags_en.concat(image.tags_en).sort();
                    }
                    if (temp.category_ids) {
                        image.category_ids = temp.category_ids.concat(image.category_ids).sort((a, b) => a.id - b.id);
                    }
                    if (temp.release_ids) {
                        image.release_ids = temp.release_ids.concat(image.release_ids).sort((a, b) => a.id - b.id);
                    }
                }
            }  
        return { 'noChanges':noChanges,'changedData':changedData,'options':options};          
        },
        checkTokenSeparators() {
            // TODO tags to support  ',', '&' as token separators
        },
        changeFormProps() {
            this.admin_categories_is_mix = "";
            let tags_ar = [];
            let tags_en = [];
            let computer_vision_tags_ar_options = [];
            let computer_vision_tags_en_options = [];
            let categories = [];
            let releases = [];
            let firstItem = this.selectList[0] ? this.selectIndex[this.selectList[0]] : {};
            let title_ar = firstItem.title_ar;
            let title_en = firstItem.title_en;
            let description_ar = firstItem.description_ar;
            let description_en = firstItem.description_en;
            this.selectLicense = firstItem.license;

            for (let i = 0; i < this.selectList.length; i++) {
                let item = this.selectIndex[this.selectList[i]];
                tags_ar = tags_ar.concat(item.tags_ar);
                tags_en = tags_en.concat(item.tags_en);
                categories = categories.concat(item.category_ids);
                releases = releases.concat(item.release_ids);
            }
            tags_ar = uniq(tags_ar);
            tags_en = uniq(tags_en);
            categories = this.uniqueNested(categories, "id");
            if(this.dataType !== "vectors")
            releases = this.uniqueNested(releases, "id");

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

            let isReleasesEqual = true;
            if(this.dataType !== "vectors")
            {
                for (let i = 0; i < this.selectList.length; i++) {
                    for (let k = 0; k < this.selectList.length; k++) {
                        if (
                            i !== k &&
                            !isEqual(
                                this.selectIndex[this.selectList[i]].release_ids.sort((a, b) => a.id - b.id),
                                this.selectIndex[this.selectList[k]].release_ids.sort((a, b) => a.id - b.id)
                            )
                        ) {
                            isReleasesEqual = false;
                        }
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
                this.description_ar = description_ar;
                this.description_en = description_en;
                this.tags_ar = tags_ar;
                this.tags_en = tags_en;
                this.categories = categories;
                this.releases = releases;
                this.admin_categories = firstItem.category_admin_id;
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

                if (isReleasesEqual) {
                    this.releases = releases;
                } else {
                    this.releases = [{ id: 0, label: "mix value" }];
                }

                if (isCategoryAdminEqual) {
                    this.admin_categories = firstItem.category_admin_id;
                } else {
                    this.admin_categories = [];
                }
            }
        },
        uniqueNested(array, key) {
            return array.filter((elem, pos, arr) => {
                return findIndex(arr, { [key]: elem[key] }) === pos;
            });
        },
        fireExternalSourceTags(key) {
            window.Swal.fire({
                title: this.t("Paste Tags here"),
                input: "text",
                inputAttributes: {
                    autocapitalize: "off",
                    autocorrect: "off"
                },
                confirmButtonText: this.t("Submit"),
                showLoaderOnConfirm: true
            }).then(result => {
                console.log(result);
                if (result && result.value) {
                    let newTags = this.convertTextToTags(result.value);
                    this[key] = uniq(this[key].concat(newTags));
                }
            });
        },
        convertTextToTags(value) {
            let newTags = [];
            value = value.trim();

            if (value.split(",").length) {
                if (value.split("،").length > value.split(",").length){
                newTags = value.split("،");
                }else{
                newTags = value.split(",");
                }
            } else if (value.split("،").length) {
                newTags = value.split("،");
            } else if (value.split(" ").length) {
                newTags = value.split(" ");
            } else {
                newTags = [value];
            }

            return newTags.filter(tag => tag).map(tag => tag.toLowerCase());
        },
        paste(key) {
            navigator.clipboard
                .readText()
                .then(text => {
                    let newTags = this.convertTextToTags(text);
                    this[key] = uniq(this[key].concat(newTags));
                })
                .catch(err => {
                    console.log("Something went wrong", err);
                    window.swal.fire({
                        imageUrl: help_clipboard,
                        imageWidth: 360,
                        imageHeight: 400,
                        animation: true,
                        title: this.t("Please accept clipboard permission")
                    });
                });
        },
        showComputerVisionTagsModal(key) {
            this.computer_vision_tags_ar = this.computer_vision_tags_ar_options.filter(item => this.tags_ar.indexOf(item.value) > -1);
           
            this.computer_vision_tags_en = this.computer_vision_tags_en_options.filter(item => this.tags_en.indexOf(item.value) > -1);
          
            this.computer_vision_tags_ar_old = this.computer_vision_tags_ar;
            this.computer_vision_tags_en_old = this.computer_vision_tags_en;

           

            if (key === "tags_ar") {
                window.$("#suggested-tags-ar-modal").modal("show");
            } else if (key === "tags_en") {
                window.$("#suggested-tags-en-modal").modal("show");
            }
        },
        showReleasesModal() {
            // alert('wef');
            // alert(this.releases.length);
            // if(this.releases == [] || this.releases.length == 0 ){
            //     window.$("#create-release-modal").modal("show");
            // }else{
            window.$("#releases-modal").modal("show");
            this.createReleaseFormStatus = true
            //  }
        },
        closeReleasesModal() {
            window.$("#releases-modal").modal("show");
            this.createReleaseFormStatus = false
        },
        showCreateReleaseModal() {
            window.$("#create-release-modal").modal("show");
        },
        closeComputerVisionTagsModal() {
            // reset to old
            this.computer_vision_tags_ar = this.computer_vision_tags_ar_old;
            this.computer_vision_tags_en = this.computer_vision_tags_en_old;
        },
        addComputerVisionTagsEn() {
            let idsToRemove = differenceBy(this.computer_vision_tags_en_old, this.computer_vision_tags_en, "id").map(tag => tag.id);
            let idsToAdd = differenceBy(this.computer_vision_tags_en, this.computer_vision_tags_en_old, "id").map(tag => tag.id);

            if (idsToRemove.length) {
                let oldTags = this.computer_vision_tags_en_options.filter(tag => idsToRemove.indexOf(tag.id) > -1).map(tag => tag.value);
                this.tags_en = difference(this.tags_en, oldTags);

                oldTags = this.computer_vision_tags_ar_options.filter(tag => idsToRemove.indexOf(tag.id) > -1).map(tag => tag.value);
                this.tags_ar = difference(this.tags_ar, oldTags);
            }

            if (idsToAdd.length) {
                let newTags = this.computer_vision_tags_en_options.filter(tag => idsToAdd.indexOf(tag.id) > -1).map(tag => tag.value);
                this.tags_en = this.tags_en.concat(newTags);

                newTags = this.computer_vision_tags_ar_options.filter(tag => idsToAdd.indexOf(tag.id) > -1).map(tag => tag.value);
                this.tags_ar = this.tags_ar.concat(newTags);
            }
            this.computer_vision_tags_en_old = this.computer_vision_tags_en;
            window.$("#suggested-tags-en-modal").modal("hide");
        },
        addComputerVisionTagsAr() {
            let idsToRemove = differenceBy(this.computer_vision_tags_ar_old, this.computer_vision_tags_ar, "id").map(tag => tag.id);
            let idsToAdd = differenceBy(this.computer_vision_tags_ar, this.computer_vision_tags_ar_old, "id").map(tag => tag.id);

            if (idsToRemove.length) {
                let oldTags = this.computer_vision_tags_en_options.filter(tag => idsToRemove.indexOf(tag.id) > -1).map(tag => tag.value);
                this.tags_en = difference(this.tags_en, oldTags);

                oldTags = this.computer_vision_tags_ar_options.filter(tag => idsToRemove.indexOf(tag.id) > -1).map(tag => tag.value);
                this.tags_ar = difference(this.tags_ar, oldTags);
            }

            if (idsToAdd.length) {
                let newTags = this.computer_vision_tags_en_options.filter(tag => idsToAdd.indexOf(tag.id) > -1).map(tag => tag.value);
                this.tags_en = this.tags_en.concat(newTags);

                newTags = this.computer_vision_tags_ar_options.filter(tag => idsToAdd.indexOf(tag.id) > -1).map(tag => tag.value);
                this.tags_ar = this.tags_ar.concat(newTags);
            }
            this.computer_vision_tags_ar_old = this.computer_vision_tags_ar;
            window.$("#suggested-tags-ar-modal").modal("hide");
        },
        deleteHandler() {
            window.swal.fire({
                type: "warning",
                title: this.t("Are you sure ?"),
                text: this.t("Once deleted, you will not be able to recover those :count selected files!", {count: this.selectList.length}),
                showConfirmButton: true,
                showCancelButton: true,
                cancelButtonText: this.t("Cancel"),
            }).then(({value}) => {
                if (value === true) {
                    this.deleteSelections()
                }
            });
        },

        downloadFile(){
            if(window.type == 'vectors')
            return ;

            window.open(window.active_ar_routes.releases_forms);

        },
        submitHandler(type) {
            // if(this.checkForm()){
                window.swal.fire({
                    type: "warning",
                    title: this.t("Are you sure ?"),
                    text: this.t("Once submitted, you will not be able to edit those :count selected files!", {count: this.selectList.length}),
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: this.t("ok"),
                    cancelButtonText: this.t("Cancel"),
                }).then(({value}) => {
                    if (value === true) {
                        var dataWithStatus =  this.readyDataSaveWithStatusChange();
                        var noChanges = dataWithStatus.noChanges;
                        var changedData = noChanges?null:dataWithStatus.changedData;
                        var options = noChanges?null:dataWithStatus.options;
                        if (this.stage === 3) {
                            this.doReSubmit(noChanges,changedData,options)
                        } else {
                            this.doSubmit(type,noChanges,changedData,options)
                            
                        }
                    }
                });

            // }
        },
        checkForm(){
         var value =   (this.title_ar === ""  ||
            this.title_en === "" ||
            this.tags_ar.length === 0 || 
            this.tags_en.length === 0 ||
            this.categories.length === 0) ? false:true;
            if(!value){
                window.swal.fire({
                type: "error",
                title: this.t("Please enter all required fields."),
                confirmButtonText: this.t("ok"),
                showConfirmButton: true,
                });
            }
            return value;
        },
    }
};
</script>

<style>
.separator {
    height: 0;
}
.separator.separator-dashed {
    border-bottom: 1px dashed #EBEDF3;
}
.m-0 {
    margin: 0 !important;
}
.mb-8, .my-8 {
    margin-bottom: 2rem !important;
}
.mt-8, .my-8 {
    margin-top: 2rem !important;
}
.option {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    padding: 1.4em;
    border: 1px solid #EBEDF3;
    border-radius: 0.42rem;
}
.option .option-control {
    width: 2.7rem;
    padding-top: 0.1rem;
}
.option .option-label {
    width: 100%;
}
.option .option-label .option-head {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
}
.option .option-label .option-body {
    display: block;
    padding-top: 0.7rem;
    font-size: 0.9rem;
    color: #B5B5C3;
}
    .vs--searchable .vs__dropdown-toggle {cursor: text;
    height: 100%;
    padding: 0.65rem 1rem;
    border-radius: 0px;
    border: 1px solid #e2e5ec;
    margin-bottom: 2px;
    }
</style>
