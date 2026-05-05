<template>
    <div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid" id="app">
        <div class="row row-cols-2">
            <div class="col-12">
                <div class="kt-portlet">
                    <ImageStorePortletHead :filters="filterList" :select-filter="selectFilter"  :select-all="selectAll" :reset-filters="resetFilters" :reset-selections="resetSelections" :fetch-images="fetchImages" />
                </div>
            </div>
            <div class="col-8">
                <div class="kt-portlet">
                    <div class="kt-portlet__foot kt-portlet__foot--md">
                        <div class="kt-portlet__foot-wrapper">
                            <div class="kt-portlet__foot-info">

                                <div class="kt-media-group">
                                    <a v-for="item in selectListPreview" :key="item.id" href="javascript:;" class="kt-media kt-media--lg kt-media--circle" data-toggle="kt-tooltip" data-skin="brand" data-placement="top">
                                        <img :src="item.thumbnail" />
                                    </a>
                                    <a v-if="selectList.length === 0" href="javascript:;" class="kt-media kt-media--lg kt-media--circle" data-toggle="kt-tooltip" data-skin="brand" data-placement="top">
                                        <span style="width: 100%;">{{ t("No Images Selected") }}</span>
                                    </a>
                                    <a v-if="selectList.length > 10" href="javascript:;" class="kt-media kt-media--lg kt-media--circle kt-media--dark" data-toggle="kt-tooltip" data-skin="brand" data-placement="top">
                                        <span>+{{ selectList.length - 10 }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-danger alert-dismissible fade show row" role="alert">
                    <strong> 
                            {{ t("Notes: To modify the content status of the completed modifications") }}
                    </strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="col-12">
                        <ul>
                            <li>{{ t("The title in English is not equal to the title in Arabic and vice versa.") }}</li>
                            <li>{{ t("The title in Arabic does not contain English letters.") }}</li>
                            <li>{{ t("The title should not contain characters except for regex and it is preferable not to use them frequently.") }}</li>
                            <li>{{ t("Preferably write the title in Arabic without movements.") }}</li>
                            <li>{{ t("The file must contain tags in English and Arabic.") }}</li>
                            <li>{{ t("The file must have a category .") }}</li>
                        </ul>
                    </div>
                </div>
                <div class="kt-portlet" style="box-shadow: unset;">
                    <div class="kt-portlet__body" style="background-color: #f2f3f8;">
                        <div class="kt-portlet__content">
                            <div class="row row-cols-3" v-if="dataList.length">
                                <ImageStoreImageCard v-for="item in dataList" :key="item" :item="dataIndex[item]" :check-image="checkImage" :global-title="globalTitle" :data-type="dataType" />
                            </div>
                            <div class="row"  v-else>
                                <div class="alert alert-dark col" role="alert">
                                    {{t("No data available in table")}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4" v-if="selectList.length">
                <ImageStoreForm  v-if="department === 'arabsstock'" :update-selected-title="updateSelectedTitle" :form-options="formOptions" :select-list="selectList" :select-index="selectIndex" :do-update-multi="doUpdateMulti" :do-submit="doSubmit" :select-list-preview="selectListPreview" />
                <ContributorImageStoreForm v-if="department === 'contributor_reviews'"  :update-selected-title="updateSelectedTitle" :form-options="formOptions" :select-list="selectList" :select-index="selectIndex" :save-release="saveRelease" :do-update-multi="doUpdateMulti" :do-submit="doSubmit" :select-list-preview="selectListPreview" :status-submit="canResubmit(selectListPreview,[8])" :update-after-publish="updateAfterPublish" />
            </div>
        </div>

        <Pagination :params="paginationParams" :change-page="changePage" :change-per-page="changePerPage" />
    </div>
</template>

<script>
import { map, filter, slice, throttle, keyBy } from "lodash";
import * as Sentry from '@sentry/browser';

import Pagination from "./../components/Pagination";
import ImageStorePortletHead from "./../components/ImageStorePortletHead";
import ImageStoreImageCard from "./../components/ImageStoreImageCard";
import ImageStoreForm from "./../components/ImageStoreForm";
import ContributorImageStoreForm from "./../components/ContributorImageStoreForm";

export default {
    name: "App",
    components: {
        ImageStorePortletHead,
        ImageStoreImageCard,
        Pagination,
        ImageStoreForm,
        ContributorImageStoreForm,
    },
    created() {
        // department must be within expected values
        let departments = [
            "arabsstock",
            "contributor_reviews",
        ]

        this.department = window.department
        if (departments.indexOf(this.department) === -1) {
            this.department = "";
            alert("something is wrong please contact arabsstock, code 0xDEPARTMENT_ERROR")
            setTimeout(() => {
                return this.$el.remove()
            }, 500);
        }

        let dataTypes = [
            'images',
            'videos',
            'vectors'
        ]
        this.dataType = window.dataType
        if (dataTypes.indexOf(this.dataType) === -1) {
            this.dataType = "";
            alert("something is wrong please contact arabsstock, code 0xARABSSTOCK_TYPE_ERROR")
            setTimeout(() => {
                return this.$el.remove()
            }, 500);
        }
        this.ar_routes = window.ar_routes[this.dataType][this.department]


        this.setUser();
        this.fetchOptions();
        this.fetchFilters();

        window.addEventListener("scroll", throttle(this.handleScroll, 300));

        new window.ClipboardJS("[data-clipboard=true]").on("success", function (e) {
            e.clearSelection();
            /* alert('Copied!'); sweetalert or toast*/
        });
    },
    data() {
        return {
            dataType: 'images',
            keyword: undefined,
            department: "",
            ar_routes: {},
            user: {
                id: 0,
                email: "",
                api_token: "",
            },
            currentChunkIndex: 0,
            formOptions: {},
            filterList: [],
            selectIndex: [],
            selectList: [],
            selectListPreview: [],
            dataIndex: {},
            dataList: [],
            globalTitle: -1,
            paginationParams: {
                total: 10,
                perPage: 20,
                page: 1,
                baseUrl: "https://arabsstock.com/data"
            }
        };
    },
    methods: {
        handleScroll() {
            if (document.querySelector("body").scrollHeight <= window.scrollY + window.innerHeight + 700) {
                /* console.log("bottom", document.querySelector("body").scrollHeight, window.scrollY); */
            }
        },
        changePage(page) {
            this.paginationParams.page = page;
            window.scrollTo({
                top: 0,
                left: 0,
                behavior: "smooth"
            });
            setTimeout(() => {
                this.fetchImages();
            }, 1000);
        },
        changePerPage(e) {
            this.paginationParams.page = 1;
            let perPage = parseInt(e.target.value);
            this.paginationParams.perPage = perPage;
            window.scrollTo({
                top: 0,
                left: 0,
                behavior: "smooth"
            });
            setTimeout(() => {
                this.fetchImages();
            }, 1000);
        },
        checkImage(id, value) {
            this.dataList = map(this.dataList, item => {
                if (this.dataIndex[item].id === id) {
                    this.dataIndex[item].checked = value;
                }

                return item;
            });

            if (value) {
                this.selectIndex[id] = this.dataIndex[id];
                if(!this.selectList.includes(id))
                this.selectList.push(id);
            } else {
                this.selectList = filter(this.selectList, item => {
                    return item !== id;
                });
                delete this.selectIndex[id];
            }
            this.selectListPreview = slice(this.selectList, 0, 10).map(item => {
                return {
                    id: this.selectIndex[item].id,
                    owner: this.selectIndex[item].owner??'',
                    status: this.selectIndex[item].status??'',
                    contributor_stage: this.selectIndex[item].contributor_stage??null,
                    thumbnail: this.selectIndex[item].thumbnail,
                    status_contributor_file:this.selectIndex[item].status_contributor_file,
                     };
            });
            this.globalTitle = -1;
        },
        selectFilter(id, value,type="select", fetchAgain = true) {
            this.paginationParams.page = 1;
            this.paginationParams.perPage = 20;
            let all_filters =  this.flatten(this.filterList);


            let filter = all_filters.find(item => item.id === id);

            if (type === "select" && typeof filter === "object") {
                if (id === "publisher_type" && filter.value) {
                    // filter.status = true;
                    let filter_value_id = filter.value.id;
                    switch (filter_value_id) {
                        case "supervisor":
                            all_filters.map(element => {
                                if (element.id === "contributor"){
                                    element.status = true;
                                    element.value=null;
                                }
                                if (element.id === "publisher"){
                                    element.status = false;
                                }
                                return element;
                            });
                            break;
                        case "contributor":
                            all_filters.map(element => {
                                if (element.id === "publisher"){
                                    element.status = true;
                                    element.value=null;
                                }
                                if (element.id === "contributor"){
                                    element.status = false;
                                }
                                return element;
                            });
                            break;
                            default:
                            all_filters.map(element => {
                                if (element.id === "publisher") element.status = false;
                                if (element.id === "contributor") element.status = false;
                                return element;
                            });                                
                                break;
                    
                 
                    }

                }
                filter.options = filter.options.map(item => {
                    item.active = item.id === value;
                    return item;
                });
                
            }
            fetchAgain && this.fetchImages();
        },
        DeSelectFilter(id, value, fetchAgain = true) {
            this.paginationParams.page = 1;
            this.paginationParams.perPage = 20;

            let all_filters = this.flatten(this.filterList);

            let filter = all_filters.filter(item => item.id === id)[0];

            filter.options = filter.options.map(item => {
                item.active = item.id === value?false:true;
                return item;
            });
            fetchAgain && this.fetchImages();
        },
        fetchImages() {
            let all_filters = this.flatten(this.filterList);
           
            if (this.department === "") {
                return ;
            }
            let queryString = [
                ["page", this.paginationParams.page],
                ["perpage", this.paginationParams.perPage]
            ].concat(
                    map(all_filters, item => {
                        if (item.type === "select") {
                            let option = filter(item.options, option => {
                                return option.active;
                            })[0];
                            option = item.value?option && option.id:'';
                            return [item.id, option];
                        }else if(item.type === "input"){
                            return [item.id,item.value];
                        }
                    })
                )
                .filter(value => value[1])
                .map(function (item) {
                    return item[0] + "=" + item[1];
                })
                .join("&");

            function handleErrors(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            }
            this.appLoading = true;
            const queryParams = new URLSearchParams(window.location.search)
            let status = queryParams.get('status');

            fetch(this.ar_routes.index + "?" + queryString + "&api_token=" + this.user.api_token+`&status=${status}`)
                .then(handleErrors.bind(this))
                .then(response => {
                    this.appLoading = false;
                    this.paginationParams.total = response.meta.total;

                    /* window.dataStore.images = chunk( */
                    this.dataIndex = keyBy(
                        response.data.map(item => {
                            item.checked = this.selectList.indexOf(item.id) > -1;
                            item.tags_ar = item.tags_ar.sort();
                            item.tags_en = item.tags_en.sort();
                            item.category_ids = item.category_ids.sort((a, b) => a.id - b.id);
                            return item;
                        }),
                        "id"
                    );
                    this.dataList = response.data.map(item => item.id);
                    /* this.currentChunkIndex = -1; */
                    this.setDataList();
                })
                .catch(error => {
                    console.log(error);
                    Sentry.captureException(error);
                    alert("An error occured, please contact your system administrator:1 " + error);
                    this.appLoading = false;
                    console.log(error);
                });
        },
        saveRelease(file, name, type, callback) {
            const formData = new FormData()
            formData.append('file', file)
            formData.append('name', name)
            formData.append('type', type)

            function handleErrors(response) {
                if (response.status === 422) {
                    return response.json();
                }
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            }
            this.appLoading = true;

            fetch(this.ar_routes.releases + "?api_token2=" + this.user.api_token, {
                method: "POST",
                body: formData,
                }).then(handleErrors.bind(this))
                .then(response => {
                    this.appLoading = false;
                    console.log([response])
                    if (response.status === 422) {
                        window.swal.fire({
                            type: "error",
                            title: this.t("Validation error, please try again."),
                            showConfirmButton: true,
                        });

                    } else {
                        this.formOptions.releases.push({
                            id: response.id,
                            label: response.name,
                        })
                        callback()
                    }
                })
                .catch(error => {
                    Sentry.captureException(error);
                    alert("An error occured, please contact your system administrator: 2 " + error);
                    this.appLoading = false;
                    console.log(error);
                });
        },
        setDataList() {
            /* this.currentChunkIndex++; */
            /* if (window.dataStore.images[this.currentChunkIndex]) { */
            /*     this.dataList = clone(window.dataStore.images[this.currentChunkIndex]); */
            /* } */
        },
        fetchFilters() {
            let mapFilterNameToLocale = { type: "Status", categories: "Categories", categories_admin: "Admin's Category", collection: "Admin's Collection", folder: "Folder", sort_by: "Sort", contributor: "Contributor" ,search:"search",publisher_type:"publisher_type",publisher:"publisher",removebg_status:'removebg_status',removebg_status_disply:"removebg_status_disply",removebg_type:"removebg_type"};

            function handleErrors(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            }
            this.appLoading = true;
            fetch(this.ar_routes.filters + "?lang=" + this.lang + "&api_token=" + this.user.api_token)
                .then(handleErrors.bind(this))
                .then(response => {
                    this.appLoading = false;
                    let data = response.filters;
                    data.forEach(element => {
                        let keys = Object.keys(element);
                        let section = new Array;
                        for (const key of keys) {
                            let filter = element[key].data;
                            let filterKeys = Object.keys(filter);
                            section.push({
                                id: key,
                                value:null,
                                status: false,
                                type:element[key].type,
                                name: mapFilterNameToLocale[key],
                                options: filterKeys.map(item => {
                                    return {
                                        id: item,
                                        label: filter[item],
                                        filterId: key,
                                        active: false
                                    };
                                })
                            });
                        }
                            this.filterList.push(section);

                        
                    });

                    this.selectFilter("sort_by", "id","select", false);
                    this.fetchImages();
                })
                .catch(error => {
                    Sentry.captureException(error);
                    alert("An error occured, please contact your system administrator: 3 " + error);
                    this.appLoading = false;
                    console.log(error);
                });
        },
        setUser() {
            this.user = window.user;
        },
        fetchOptions() {
            function handleErrors(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            }
            this.appLoading = true;
            fetch(this.ar_routes.options + "?lang=" + this.lang + "&api_token=" + this.user.api_token)
                .then(handleErrors.bind(this))
                .then(response => {
                    this.appLoading = false;
                    this.formOptions = response.options;
                })
                .catch(error => {
                    Sentry.captureException(error);
                    alert("An error occured, please contact your system administrator:  4 " + error);
                    this.appLoading = false;
                    console.log(error);
                });
        },
        selectAll() {
            this.dataList = map(this.dataList, item => {
                this.dataIndex[item].checked = true;
                this.checkImage(item,true);
                return item;
            });

        },
        resetFilters() {
            this.paginationParams.page = 1;
            /* this.paginationParams.perPage = 20; */

            this.filterList = this.filterList.forEach(element => {
                element.map(filter => {
                    if (filter.id !== "sort_by") {
                        filter.options = filter.options.map(option => {
                            option.active = false;
                            return option;
                        });
                    }
                    return filter;
                });
                
            });
            this.fetchImages();
        },
        resetSelections() {
            this.selectListPreview = [];
            this.selectList = [];
            this.selectIndex = {};
            this.globalTitle = -1;
            this.dataList = map(this.dataList, item => {
                this.dataIndex[item].checked = false;
                return item;
            });
        },
        updateSelectedTitle(title) {
            this.globalTitle = title;
        },
        doUpdateMulti(changedData, options) {
            let status_update= this.canResubmit(this.selectListPreview,[8]);
            var self = this;
            function handleErrors(response) {
                if (!response.ok) {
               return  response.json().then(res => {
                   var error = res && res.errors && res.errors.message?res.errors.message:"An error occured, please contact your system administrator: 6";
                   throw new Error(error)
                   })
                }
                return response.json();
            }
            this.appLoading = true;
            fetch(this.ar_routes.update_multi + "?api_token=" + this.user.api_token, {
                method: "POST",
                body: JSON.stringify({ data: changedData, options: options,status_update:status_update })
            })
                .then(handleErrors.bind(this))
                .then(response => {

                    if (self.formOptions.licenses && response) {
                        self.selectList.forEach(i => {
                        self.dataIndex[i].license = self.formOptions.licenses[options.license].name;
                        self.dataIndex[i].license_title = self.formOptions.licenses[options.license].title;
                        });
                    }

                    this.appLoading = false;

                    window.swal.fire({
                        position: "top-right",
                        type: "success",
                        title: this.t("Your work has been saved"),
                        showConfirmButton: false,
                        timer: 3000
                    });
                })
                .catch(error => {
                    Sentry.captureException(error);
                    console.log("update",error);
                    window.swal.fire({
                    type: "error",
                    title: (String(error)).replace("Error:",""),
                    confirmButtonText: this.t("Ok"),
                    showConfirmButton: true,
                    timer: 3000
                    });
                    // alert("An error occured, please contact your system administrator: 5 " + error);
                    this.appLoading = false;
                });
        },
        doSubmit(type, notes,categories=[],change_url= 0) {
            function handleErrors(response) {
                if (!response.ok) {
               return  response.json().then(res => {
                   var error = res && res.errors && res.errors.message?res.errors.message:"An error occured, please contact your system administrator: 6";
                   throw new Error(error)
                   })
                }
                return response.json();
            }
            this.appLoading = true;
            var endPoint = change_url === 1?this.ar_routes.change_status:this.ar_routes.submit;
            var body = JSON.stringify({ ids: this.selectList.join(','), type: type, notes: notes,categories:categories.map(v => v.id)})
            fetch(endPoint + "?api_token=" + this.user.api_token, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: body
            })
                .then(handleErrors.bind(this))
                .then(response => {
                    console.log("ok", response);
                    this.appLoading = false;
                    this.resetSelections()
                    this.fetchImages();

                })
                .catch(error => {
                    Sentry.captureException(error);
                    console.error(error);
                    window.swal.fire({
                    type: "error",
                    title: (String(error)).replace("Error:",""),
                    confirmButtonText: this.t("Ok"),
                    showConfirmButton: true,
                    timer: 3000
                    });
                   // alert("An error occured, please contact your system administrator: 6" + error);
                    this.appLoading = false;
                });
        },
        flatten(arr) {
            var flat = [];
            for (var i = 0; i < arr.length; i++) {
                flat = flat.concat(arr[i]);
            }
            return flat;
        },
        canResubmit(data,disableItem=[4,5]){
                    var items = filter(data, item => {
                        return disableItem.includes(item.contributor_stage);
                    });
                return items.length?false:true;


        },
        updateAfterPublish(val) {
            function handleErrors(response) {
                if (!response.ok) {
               return  response.json().then(res => {
                   var error = res && res.errors && res.errors.message?res.errors.message:"An error occured, please contact your system administrator: 6";
                   throw new Error(error)
                   })
                }
                return response.json();
            }
            this.appLoading = true;
            var endPoint = this.ar_routes.update_after_publish;
            var body = JSON.stringify({ ids: this.selectList.join(','),status_update:val})
            fetch(endPoint + "?api_token=" + this.user.api_token, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: body
            })
                .then(handleErrors.bind(this))
                .then(response => {
                    console.log("ok", response);
                    this.appLoading = false;
                    window.swal.fire({
                        position: "top-right",
                        type: "success",
                        title: this.t("Your work has been saved"),
                        showConfirmButton: false,
                        timer: 3000
                    });
                    this.resetSelections()
                    this.fetchImages();

                })
                .catch(error => {
                    Sentry.captureException(error);
                    console.error(error);
                    window.swal.fire({
                    type: "error",
                    title: (String(error)).replace("Error:",""),
                    confirmButtonText: this.t("Ok"),
                    showConfirmButton: true,
                    timer: 3000
                    });
                   // alert("An error occured, please contact your system administrator: 6" + error);
                    this.appLoading = false;
                });
        },
    }
};
</script>

<style>
.kt-media.kt-media--circle {
    border-radius: 50%;
}
.kt-media.kt-media--circle img {
    border-radius: 50%;
}
.kt-media.kt-media--circle span {
    border-radius: 50%;
}
</style>
