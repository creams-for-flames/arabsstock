<template>
    <div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid" id="app">

        <div class="row row-cols-2">
            <div class="col-12">
                <div class="kt-portlet">
                    <ContributorImageStorePortletHead :filters="filterList" :select-filter="selectFilter" :select-all="selectAll" :reset-selections="resetSelections" :stage="stage" :data-type="dataType" :set-stage="setStage" :set-data-type="setDataType" :select-list="selectList" :data-list="dataList" :delete-all="deleteAll" />
                </div>
            </div>
            <div class="col-md-8">
                <div v-if="stage === 1">



                                <div class="kt-media-group mb-3">
                                    <a v-for="item in selectListPreview" :key="item.id" href="javascript:;" class="kt-media kt-media--lg kt-media--circle" data-toggle="kt-tooltip" data-skin="brand" data-placement="top">
                                        <img :src="item.thumbnail" />
                                    </a>
                                    <div v-if="selectList.length === 0" href="javascript:;" class="col-12 alert alert-warning p-4 mb-0" role="alert">
                            <span style="width: 100%;">{{ t("No Images Selected") }}</span>
                            <i class="fal fa-engine-warning tx-20"></i>
                        </div>
                                    <!-- <a v-if="selectList.length === 0" href="javascript:;" class="kt-media kt-media--lg kt-media--circle" data-toggle="kt-tooltip" data-skin="brand" data-placement="top">
                                        <span style="width: 100%;">{{ t("No Images Selected") }}</span>
                                    </a> -->
                                    <a v-if="selectList.length > 10" href="javascript:;" class="kt-media kt-media--lg kt-media--circle kt-media--dark" data-toggle="kt-tooltip" data-skin="brand" data-placement="top">
                                        <span>+{{ selectList.length - 10 }}</span>
                                    </a>
                                </div>



                </div>


                <div class="row row-cols-3">
                    <ImageStoreImageCard v-for="item in dataList" :key="item" :item="dataIndex[item]" :check-image="checkImage" :global-title="globalTitle" />
                </div>

            </div>
            <div class="col-md-4" v-if="selectList.length  && canResubmit(selectIndex)" >
                <ContributorImageStoreForm :update-selected-title="updateSelectedTitle" :form-options="formOptions" :select-list="selectList" :select-index="selectIndex" :delete-selections="deleteSelections" :user="user" :save-release="saveRelease" :do-submit="doSubmit" :do-re-submit="doReSubmit"  :stage="stage" :select-list-preview="selectListPreview" :do-update-multi="doUpdateMulti" :data-type="dataType" />
            </div>
        </div>

        <Pagination :params="paginationParams" :change-page="changePage" :change-per-page="changePerPage" />
    </div>
</template>

<script>
import { map, filter, slice, throttle, keyBy } from "lodash";
import * as Sentry from '@sentry/browser';
import ContributorImageStorePortletHead from "./../components_contributor/ContributorImageStorePortletHead";
import Pagination from "./../components_contributor/Pagination";
import ImageStoreImageCard from "./../components_contributor/ImageStoreImageCard";
import ContributorImageStoreForm from "./../components_contributor/ContributorImageStoreForm";

export default {
    name: "App",
    components: {
        ContributorImageStorePortletHead,
        ImageStoreImageCard,
        Pagination,
        ContributorImageStoreForm
    },
    created() {

        this.dataType= window.type ;

        
        window.active_ar_routes = window.ar_routes
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
            stage: 1,
            dataType: 'images',
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
        canResubmit(data){
            if([2,3].includes(this.stage)){
                    var items = filter(data, item => {
                        var disableItem = [4,5,8];
                        return disableItem.includes(item.contributor_stage);
                    }); 
                    console.log(items);
                return items.length?false:true;

            }else{
                return true;
            }

        },
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
                return { id: this.selectIndex[item].id, thumbnail: this.selectIndex[item].thumbnail ,action_delete:this.selectIndex[item].action_delete};
            });
            this.globalTitle = -1;
        },
        selectFilter(id, value, fetchAgain = true) {
            this.paginationParams.page = 1;
            this.paginationParams.perPage = 20;

            let filter = this.filterList.filter(item => item.id === id)[0];

            filter.options = filter.options.map(item => {
                item.active = item.id === value;
                return item;
            });
            fetchAgain && this.fetchImages();
        },
        fetchImages() {
            let queryString = [
                ["stage", this.stage],
                ["page", this.paginationParams.page],
                ["perpage", this.paginationParams.perPage]
            ]
                .concat(
                    map(this.filterList, item => {
                        let option = filter(item.options, option => {
                            return option.active;
                        })[0];
                        option = option && option.id;
                        return [item.id, option];
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

            fetch(window.active_ar_routes.index + "?" + queryString + "&api_token=" + this.user.api_token+"&user_id=" + this.user.id+ "&lang=" + this.lang)
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
                    this.dataList = Object.keys(this.dataIndex).reverse();
                    /* this.currentChunkIndex = -1; */
                    this.setDataList();
                })
                .catch(error => {
                    Sentry.captureException(error);
                    alert("An error occured, please contact your system administrator: " + error);
                    this.appLoading = false;
                    console.log(error);
                });
        },
        saveRelease(file, name, type, callback) {
            console.log(file) ;
            const formData = new FormData()
            formData.append('file', file)
            formData.append('name', name)
            formData.append('type', type)

            function handleErrors(response) {
            console.log('232', response)
                if (response.status === 422) {
                    return response.json();
                }
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            }
            this.appLoading = true;

            fetch(window.active_ar_routes.releases + "?api_token=" + this.user.api_token+"&user_id=" + this.user.id, {
                method: "POST",
                body: formData,
                }).then(handleErrors.bind(this))
                .then(response => {
                    console.log('qdqf');
                    console.log(response);
                    this.formOptions.releases.push({
                    id: response.id,
                    label: response.name,
                    });
                    this.appLoading = false;
                    console.log([response])
                    if (response.status === 422) {

                        window.swal.fire({
                            type: "error",
                            title: response.description,//this.t("Validation error, please try again."),
                            showConfirmButton: true,
                        });

                    } else {
                        // this.formOptions.releases.push({
                        //     id: response.id,
                        //     label: response.name,
                        // })
                        //here test
                        callback()
                    }
                })
                .catch(error => {
                    Sentry.captureException(error);
                    alert("An error occured, please contact your system administrator: " + error);
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
            let mapFilterNameToLocale = { type: "Status", categories: "Categories", categories_admin: "Admin's Category", collection: "Admin's Collection", folder: "Folder", sort_by: "Sort" };

            function handleErrors(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            }
            this.appLoading = true;
            fetch(window.active_ar_routes.filters + "?lang=" + this.lang + "&api_token=" + this.user.api_token+"&user_id=" + this.user.id)
                .then(handleErrors.bind(this))
                .then(response => {
                    this.appLoading = false;
                    const keys = Object.keys(response.filters);
                    for (const key of keys) {
                        let filter = response.filters[key];
                        const filterKeys = Object.keys(filter);
                        this.filterList.push({
                            id: key,
                            name: mapFilterNameToLocale[key],
                            options: filterKeys.map(item => {
                                return {
                                    id: item,
                                    label: filter[item],
                                    active: false
                                };
                            })
                        });
                    }

                    this.selectFilter("sort_by", "id", false);
                    this.fetchImages();
                })
                .catch(error => {
                    Sentry.captureException(error);
                    alert("An error occured, please contact your system administrator: " + error);
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
            fetch(window.active_ar_routes.options + "?lang=" + this.lang + "&api_token=" + this.user.api_token+"&user_id=" + this.user.id)
                .then(handleErrors.bind(this))
                .then(response => {
                    this.appLoading = false;
                    this.formOptions = response.options;
                })
                .catch(error => {
                    Sentry.captureException(error);
                    alert("An error occured, please contact your system administrator: " + error);
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
        deleteSelections() {

            function handleErrors(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            }
            this.appLoading = true;

            var queryString = "ids=" + this.selectList.join(',') + "&api_token=" + this.user.api_token+"&user_id=" + this.user.id
            this.resetSelections()
            fetch(window.active_ar_routes.delete + "?" + queryString)
                .then(handleErrors.bind(this))
                .then(response => {
                    console.log(response);
                    this.appLoading = false;
                    this.fetchImages();
                })
                .catch(error => {
                    Sentry.captureException(error);
                    alert("An error occured, please contact your system administrator: " + error);
                    this.appLoading = false;
                    console.log(error);
                });

        },
        deleteAll() {

            function handleErrors(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            }
            this.appLoading = true;

            var queryString = "ids=" + this.selectList.join(',') + "&api_token=" + this.user.api_token+"&user_id=" + this.user.id
            // this.resetSelections()
            fetch(window.active_ar_routes.delete_all + "?" + queryString)
                .then(handleErrors.bind(this))
                .then(response => {
                    console.log(response);
                    this.appLoading = false;
                    this.fetchImages();
                    this.resetSelections();

                })
                .catch(error => {
                    Sentry.captureException(error);
                    alert("An error occured, please contact your system administrator: " + error);
                    this.appLoading = false;
                    console.log(error);
                });

        },
        /* s:doUpdateMulti */
                doUpdateMulti(changedData, options) {
            var self = this;
            function handleErrors(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            }
            this.appLoading = true;
            fetch(window.active_ar_routes.update_multi + "?api_token=" + this.user.api_token+"&user_id=" + this.user.id, {
                method: "POST",
                body: JSON.stringify({ data: changedData, options: options })
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
                    console.log(error);
                    Sentry.captureException(error);
                    // alert("An error occured, please contact your system administrator: 5 " + error);
                    this.appLoading = false;
                });
        },
        /* e:doUpdateMulti */
        doSubmit(type,noChanges,changedData,options) {

            function handleErrors(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            }
            this.appLoading = true;
            var  body= JSON.stringify({ids: this.selectList.join(','), type: type , data: changedData, options: options ,noChanges:noChanges})
            fetch(window.active_ar_routes.submit + "?api_token=" + this.user.api_token+"&user_id=" + this.user.id+"&lang="+window.lang, {
                method: "POST",
                body: body
            })
                .then(handleErrors.bind(this))
                .then(response => {
                    // console.log("ok", response);
                        this.appLoading = false;
                    if(response.status ===  422){
                        window.swal.fire({
                        type: "error",
                        title: response.message,
                        showConfirmButton: true,
                        confirmButtonText: this.t("ok"),
                        });
                    }else{
                        this.fetchImages();
                        this.resetSelections()

                    }
                })
                .catch(error => {
                    Sentry.captureException(error);
                    alert("An error occured, please contact your system administrator: " + error);
                    this.appLoading = false;
                    console.log(error);
                });
        },
        doReSubmit(noChanges,changedData,options) {
            function handleErrors(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            }
            this.appLoading = true;
            var item = this.dataIndex[this.selectList[0]];
            var status_rejected = item.status_file_rejected_publish??false;
              var  body= JSON.stringify({id: this.selectList[0], data: changedData, options: options ,noChanges:noChanges,status_rejected:status_rejected});
            this.resetSelections()
            fetch(window.active_ar_routes.resubmit + "?api_token=" + this.user.api_token+"&user_id=" + this.user.id, {
                method: "POST",
                body: body
            })
                .then(handleErrors.bind(this))
                .then(response => {
                    console.log("ok", response);
                    this.appLoading = false;
                    this.fetchImages();
                })
                .catch(error => {
                    Sentry.captureException(error);
                    alert("An error occured, please contact your system administrator: " + error);
                    this.appLoading = false;
                    console.log(error);
                });
        },
        setStage(stage) {
            this.stage = stage
            this.resetSelections()
            this.fetchImages();
        },
        setDataType(value) { 

            this.dataType = value;

        },
        changeTypeUrl(type='images'){
            this.fetchOptions();
            if (window.history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?type='+type;
                window.history.pushState({path:newurl},'',newurl);
            }
        }
    },

    mounted(){
    // this.dataType ;

    this.setDataType(this.dataType);

   
  }
};
</script>

<style>
.kt-media.kt-media--circle {
    border-radius: 50%;
}
.kt-media.kt-media--circle img {
    border-radius: 50%;
        object-fit: cover;
}
.kt-media.kt-media--circle span {
    border-radius: 50%;
}
.blockui {
    background: #fff;
    -webkit-box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.1);
    box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.1);
    display: table;
    table-layout: fixed;
}
</style>
