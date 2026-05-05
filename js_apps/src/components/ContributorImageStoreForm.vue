<template>
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">{{ t("Edit") }} {{ p(":count images", selectList.length, { count: selectList.length }) }}</h3>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" @submit.prevent="onSave">
            <div class="kt-portlet__body">
                <div :class="{'text-danger':title_ar === ''}" class="form-group">
                    <label>{{ t("Title") }} {{ t("- Arabic Version") }}</label>
                    <input   @dblclick="enableInput('title_ar')" @change="copyName" @keyup="copyName" @keypress="copyName" v-model="title_ar" type="text" class="form-control" placeholder="" :readonly="title_ar === 'قيم مختلفة'" />
                </div>
                <div v-if="!statusSubmit" :class="{'text-info':file_title_ar === ''}" class="form-group">
                    <label>{{ t("Title") }} {{ t("- Arabic Version") }} {{ t("live") }}</label>
                    <textarea name="file_title_ar" id="file_title_ar" disabled v-model="file_title_ar" cols="33" rows="4"></textarea>
                </div>
                <div :class="{'text-danger':title_en === ''}" class="form-group">
                    <label>{{ t("Title") }} {{ t("- English Version") }}</label>
                    <input  @dblclick="enableInput('title_en')" v-model="title_en" type="text" class="form-control" placeholder="" :readonly="title_en === 'mix value'" />
                </div>
                <div v-if="!statusSubmit" :class="{'text-info':file_title_en === ''}" class="form-group">
                    <label>{{ t("Title") }} {{ t("- Arabic Version") }} {{ t("live") }}</label>
                    <textarea name="file_title_en" id="file_title_en" disabled v-model="file_title_en" cols="33" rows="4"></textarea>
                </div>
                <div :class="{'text-danger':tags_ar.length === 0}" class="form-group">
                    <label>{{ t("Tags") }} {{ t("- Arabic Version") }}</label>

                    <v-select  v-model="tags_ar" @input="lowerCaseTags('tags_ar', $event)" taggable multiple :close-on-select="false" :dir="layoutDirection" :push-tags="true">
                        <div slot="no-options">{{ t("Your tag here") }}</div>
                    </v-select>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button class="btn btn-secondary" type="button" data-clipboard="true" :data-clipboard-text="tags_ar.toString()"><i class="fa fa-copy"></i></button>
                            <button class="btn btn-secondary" type="button" @click="paste('tags_ar')"><i class="fa fa-clipboard"></i></button>
                            <button class="btn btn-secondary" type="button" @click="fireExternalSourceTags('tags_ar')"><i class="fa fa-link"></i></button>
                        </div>
                    </div>
                </div>
                <div v-if="!statusSubmit" :class="{'text-danger':file_tags_ar.length === 0}" class="form-group">
                    <label>{{ t("Tags") }} {{ t("- Arabic Version") }} {{ t("live") }}</label>

                    <v-select disabled  v-model="file_tags_ar" @input="lowerCaseTags('file_tags_ar', $event)" taggable multiple :close-on-select="false" :dir="layoutDirection" :push-tags="true">
                        <div slot="no-options">{{ t("Your tag here") }}</div>
                    </v-select>
                </div>
                <div :class="{'text-danger':tags_en.length === 0}" class="form-group">
                    <label>{{ t("Tags") }} {{ t("- English Version") }}</label>
                    <v-select  v-model="tags_en" @input="lowerCaseTags('tags_en', $event)" taggable multiple :close-on-select="false" :dir="layoutDirection" :push-tags="true">
                        <div slot="no-options">{{ t("Your tag here") }}</div>
                    </v-select>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button class="btn btn-secondary" type="button" data-clipboard="true" :data-clipboard-text="tags_en.toString()"><i class="fa fa-copy"></i></button>
                            <button class="btn btn-secondary" type="button" @click="paste('tags_en')"><i class="fa fa-clipboard"></i></button>
                            <button class="btn btn-secondary" type="button" @click="fireExternalSourceTags('tags_en')"><i class="fa fa-link"></i></button>
                        </div>
                    </div>
                </div>
                <div v-if="!statusSubmit" :class="{'text-danger':file_tags_en.length === 0}" class="form-group">
                    <label>{{ t("Tags") }} {{ t("- English Version") }}  {{ t("live") }}</label>

                    <v-select disabled  v-model="file_tags_en" @input="lowerCaseTags('file_tags_en', $event)" taggable multiple :close-on-select="false" :dir="layoutDirection" :push-tags="true">
                        <div slot="no-options">{{ t("Your tag here") }}</div>
                    </v-select>
                </div>
                <div :class="{'text-danger':categories_contributor.length === 0}" class="form-group">
                    <label>{{ t("CategoriesContributer") }}</label>
                    <v-select  v-model="categories_contributor" multiple :options="formOptions.categories_contributor" :dir="layoutDirection">
                        <div slot="no-options">{{ t("Sorry, no matching options") }}</div>
                    </v-select>
                </div>

                <div :class="{'text-danger':categories.length === 0}" class="form-group" v-if="statusSubmit">
                    <label>{{ t("Admin's Categories") }}</label>
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
                        <input class="form-check-input" :disabled="!statusSubmit" :value="item.name" v-model="selectLicense" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                        <label class="form-check-label" for="flexRadioDefault1">
                           {{item.title}}
                        </label>
                        </div>
                    </template>
                </div>

                <div class="form-group m-0" v-if="selectLicense  == 'commercial' && formOptions.licenses">
                    <div class="row">
                        <div class="col-lg-12">
                            <label class="option">
                                <span class="option-label">
                                    <span class="option-head">
                                        <span class="option-title">{{t("Releases")}}</span>
                                        <span @click="showReleasesModal" v-if="statusSubmit" class="option-focus" style="cursor: pointer;"><i class="fa fa-plus"></i></span>
                                    </span>
                                    <span class="option-body">{{t("For recognizable people or property.")}}</span>
                                    <span class="option-body"></span>
                                    <span class="option-head"><a class="option-title" href="/" target="_blank">{{t("Download a release form")}}</a></span>
                                    <span class="option-body"></span>
                                    <a :href="release.file" target="_blank" v-for="release in releases" :key="release.id" class="btn btn-light active rounded" style="margin: 5px 5px;">{{release.label}}</a>
                                </span>
                            </label>
                        </div>
                    </div>
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
                                <button type="button" class="btn btn-primary" data-dismiss="modal" >{{ t("Save") }} rrrrrrrrr</button>
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
                                <button @click.prevent="addComputerVisionTagsAr" type="button" class="btn btn-primary">{{ t("Save") }}cccccccccccccdddd</button>
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
                                <button @click.prevent="addComputerVisionTagsEn" type="button" class="btn btn-primary">{{ t("Save") }} ddddd</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ t("Close") }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-12 col-xl-12 mb-2">
                                <button type="submit" class="btn btn-primary btn-block">{{statusSubmit?t("Submit"): t("SubmitToSaveContributorData") }}</button>
                            </div>
                    </div>
                    <template v-if="!statusSubmit">
                        <div class="row">
                            <div class="col-lg-6 col-xl-6">
                                <button type="button"
                                @click.prevent="saveChange('agree')"
                                class="btn btn-primary btn-block">{{ t("Agree to the edit") }}</button>
                            </div>
                            <div class="col-lg-6 col-xl-6">
                                <button type="button"
                                @click.prevent="saveChange('reject')"
                                class="btn btn-danger btn-block">{{ t("Reject") }}</button>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <!-- <div class="row">
                            <div class="col-lg-12 col-xl-12">
                                <button type="submit" class="btn btn-primary btn-block">{{ t("Submit") }}</button>
                            </div>
                        </div> -->
                        <div class="separator separator-dashed my-8"></div>
                        <div class="row">
                            <div class="col">
                                <div  class="form-group">
                                    <label>{{ t("RejectionReason") }}</label>
                                    <v-select  v-model="reasons_rejection"  :options="formOptions.reasons_rejection" :dir="layoutDirection" @input="setNote">
                                        <div slot="no-options">{{ t("Sorry, no matching options") }}</div>
                                    </v-select>
                                </div>
    
                            </div>
                            <div class="col-lg-12 col-xl-12">
                                <div class="form-group">
                                    <label for="description_ar">{{ t("Notes") }} </label>
                                    <textarea v-model="notes" class="form-control" id="notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row"></div>
                        <div class="row">
                            <div class="col-lg-5 col-xl-5">
                                <a @click.prevent="submitHandler('reject')" class="btn btn-warning btn-block" href="#"> {{ t("Reject") }}</a>
                            </div>
                            <div class="col-lg-2 col-xl-2"></div>
                            <div class="col-lg-5 col-xl-5">
                                <a @click.prevent="submitHandler('hard_reject')" class="btn btn-danger btn-block" href="#"> {{ t("Hard Reject") }}</a>
                            </div>
                        </div>
                        <div class="separator separator-dashed my-8"></div>
                        <div class="row my-8" v-if="statusPublishReject">
                            <template v-if="contributor_stage  && contributor_stage === 6">
                                <div class="col-lg-12 col-xl-12">
                                    <a @click.prevent="submitHandlerAfterRejectPublish('publish')" class="btn btn-primary btn-block reject" href="#"> {{ t("Publish") }}</a>
                                </div>
                            </template>
                            <template v-else>
                                <div class="col-lg-12 col-xl-12">
                                    <a @click.prevent="submitHandler('publish')" class="btn btn-primary btn-block" href="#"> {{ t("Publish") }}</a>
                                </div>
                            </template>
                        </div>

                    </template>
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
        saveRelease: {},
        doUpdateMulti: {},
        doSubmit: {},
        statusSubmit:{},
        selectListPreview: {},
        updateAfterPublish:{},
        
    },
    mounted(){
        console.log('formOptions');
        console.log(this.formOptions);
    },
    watch: {
        selectList() {
            this.changeFormProps();
        },
        selectListPreview: {
            immediate: true,
            handler() {
            var items  = this.selectListPreview.filter(function(obj) { return obj.contributor_stage === 5  || obj.contributor_stage === 2 });
             this.itemsRejected  = this.selectListPreview.filter(function(obj) { return obj.contributor_stage === 6 });

            this.statusPublishReject = (items.length > 0 && this.itemsRejected .length > 0) ?false:true;

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
            statusPublishReject:true,
            selectLicense:'',
            title_ar: "",
            title_en: "",
            file_title_ar:"",
            file_title_en:"",
            contributor_stage:"",
            description_ar: "",
            description_en: "",
            tags_ar: [],
            tags_en: [],
            categories: [],
            reasons_rejection:[],
            categories_contributor:[],
            admin_categories: "",
            admin_categories_is_mix: "",
            computer_vision_tags_ar: [],
            computer_vision_tags_en: [],
            computer_vision_tags_ar_old: [],
            computer_vision_tags_en_old: [],
            computer_vision_tags_ar_options: [],
            computer_vision_tags_en_options: [],
            releases: [],
            itemsRejected:[],
            notes: "",
            type: "",
        };
    },
    methods: {
        setNote(){
            if(this.reasons_rejection)
            this.notes = this.reasons_rejection.description;
            else
            this.notes = "";
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
                return false
            }

            let readyData = {
                tags_ar: this.tags_ar.sort(),
                tags_en: this.tags_en.sort(),
                category_ids: this.categories_contributor.sort((a, b) => a.id - b.id),
                category_admin_id: this.admin_categories
            };

            if(this.formOptions.licenses){
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

            if (this.releases.map(x => x.id).indexOf(0) > -1) {
                options.release_ids_delete_old = 0;
                readyData.release_ids = readyData.release_ids.filter(cat => cat.id !== 0);
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

            if (noChanges) {
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
            this.doUpdateMulti(changedData, options)
        },
        checkTokenSeparators() {
            // TODO tags to support  ',', '&' as token separators
        },
        changeFormProps() {
            this.admin_categories_is_mix = "";
            let tags_ar = [];
            let tags_en = [];
            let file_tags_ar = [];
            let file_tags_en = [];
            let computer_vision_tags_ar_options = [];
            let computer_vision_tags_en_options = [];
            let categories = [];
            // let reasons_rejection=[];
            let releases = [];
            let firstItem = this.selectList[0] ? this.selectIndex[this.selectList[0]] : {};
            let file_title_ar = firstItem.file_title_ar;
            let file_title_en = firstItem.file_title_en;
            let title_ar = firstItem.title_ar;
            let title_en = firstItem.title_en;
            let contributor_stage = firstItem.contributor_stage;
            let description_ar = firstItem.description_ar;
            let description_en = firstItem.description_en;
            this.selectLicense = firstItem.license;


            for (let i = 0; i < this.selectList.length; i++) {
                let item = this.selectIndex[this.selectList[i]];
                tags_ar = tags_ar.concat(item.tags_ar);
                tags_en = tags_en.concat(item.tags_en);
                file_tags_ar = file_tags_ar.concat(item.file_tags_ar);
                file_tags_en = file_tags_en.concat(item.file_tags_en);
                categories = categories.concat(item.category_ids);
                releases = releases.concat(item.release_ids);
            }
            tags_ar = uniq(tags_ar);
            tags_en = uniq(tags_en);
            file_tags_ar = uniq(file_tags_ar);
            file_tags_en = uniq(file_tags_en);
            categories = this.uniqueNested(categories, "id");
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
                this.file_title_ar = file_title_ar;
                this.file_title_en = file_title_en;
                this.title_en = title_en;
                this.contributor_stage = contributor_stage;
                this.description_ar = description_ar;
                this.description_en = description_en;
                this.tags_ar = tags_ar;
                this.tags_en = tags_en;
                this.file_tags_ar = file_tags_ar;
                this.file_tags_en = file_tags_en;
                this.categories_contributor = categories;
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
            window.$("#releases-modal").modal("show");
            this.createReleaseFormStatus = true
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
        submitHandler(type) {

            if(type && (type === "reject" || type === "hard_reject" ) && this.notes == ""){
                window.swal.fire({
                type: "error",
                title: this.t("Please enter the Note field required."),
                confirmButtonText: this.t("ok"),
                showConfirmButton: true,
                timer: 3000
                });
                return;
            }
            if(type && (type === "publish") && !this.CheckForm()){
                window.swal.fire({
                type: "error",
                title: this.t("Please enter all required fields."),
                confirmButtonText: this.t("ok"),
                showConfirmButton: true,
                });
                return;
            
            }

            this.type = type;
            let labels = {
                'reject' : 'Reject',
                'hard_reject' : 'Hard Reject',
                'publish' : 'Publish',
            }
            if(this.categories.length == 0 && labels[type] === 'Publish'){
                window.swal.fire({
                type: "error",
                title: this.t("Validation error, please try again."),
                text:  this.t("YouMustVideoHaveCategories"),
                showConfirmButton: false,
                timer: 3000
            });
            }else{
                window.swal.fire({
                    type: "warning",
                    title: this.t("Are you sure ?"),
                    text:  this.t("\":action\" those :count selected files!", {action: this.t(labels[type]), count: this.selectList.length}),
                    showConfirmButton: true,
                    showCancelButton: true,
                    cancelButtonText: this.t("Cancel"),
                }).then(({value}) => {
                    if (value === true) {
                        this.doSubmit(this.type, this.notes,this.categories)
                    }
                });

            }
        },
        submitHandlerAfterRejectPublish(type){
            let labels = {
                'reject' : 'Reject',
                'hard_reject' : 'Hard Reject',
                'publish' : 'Publish',
            }
                window.swal.fire({
                    type: "warning",
                    title: this.t("Are you sure ?"),
                    text:  this.t("\":action\" those :count selected files!", {action: this.t(labels[type]), count: this.selectList.length}),
                    showConfirmButton: true,
                    showCancelButton: true,
                    cancelButtonText: this.t("Cancel"),
                }).then(({value}) => {
                    if (value === true) {
                         this.doSubmit(type, this.notes,this.categories,1)
                    }
                });

        },
        CheckForm(){
         var value =   (this.title_ar === ""  ||
            this.title_en === "" ||
            this.tags_ar.length === 0 || 
            this.tags_en.length === 0 ||
            this.categories.length === 0 ||
            this.categories_contributor.length === 0
            ) ? false:true;
            return value;
        },
        saveChange(val){
            this.updateAfterPublish(val);
        }
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
</style>
