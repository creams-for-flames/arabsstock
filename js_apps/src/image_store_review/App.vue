<template>
    <div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid" id="app">
        <div class="row row-cols-2">
            <div class="col-12">
                <div class="kt-portlet">
                    <ImageStorePortletHead :filters="filterList" :select-filter="selectFilter" :select-all="selectAll" :reset-filters="resetFilters" :reset-selections="resetSelections" />
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
                <div class="kt-portlet" style="box-shadow: unset;">
                    <div class="kt-portlet__body" style="background-color: #f2f3f8;">
                        <div class="kt-portlet__content">
                            <div class="row row-cols-3">
                                <ImageStoreImageCard v-for="item in dataList" :key="item" :item="dataIndex[item]" :check-image="checkImage" :global-title="globalTitle" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4" v-if="selectList.length">
                <ImageStoreFormReview :update-selected-title="updateSelectedTitle" :form-options="formOptions" :select-list="selectList" :select-index="selectIndex" />
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
import ImageStoreFormReview from "./../components/ImageStoreFormReview";

export default {
    name: "App",
    components: {
        ImageStorePortletHead,
        ImageStoreImageCard,
        Pagination,
        ImageStoreFormReview
    },
    created() {
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
                return { id: this.selectIndex[item].id, thumbnail: this.selectIndex[item].thumbnail };
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
            fetch(window.ar_routes.index + "?" + queryString)
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
            fetch(window.ar_routes.filters + "?lang=" + this.lang)
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
        fetchOptions() {
            function handleErrors(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response.json();
            }
            this.appLoading = true;
            fetch(window.ar_routes.options + "?lang=" + this.lang)
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
            var self = this;
            this.dataList = map(this.dataList, item => {
               // console.log(item);
                this.dataIndex[item].checked = true;
                self.checkImage(item,true);
                return item;
            });
        },        
        resetFilters() {
            this.paginationParams.page = 1;
            /* this.paginationParams.perPage = 20; */
            this.filterList = this.filterList.map(filter => {
                if (filter.id !== "sort_by") {
                    filter.options = filter.options.map(option => {
                        option.active = false;
                        return option;
                    });
                }
                return filter;
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
        }
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
