<template>
    <div class="kt-pagination kt-pagination--brand kt-margin-t-10">
        <template v-if="hasPages()">
            <ul class="kt-pagination__links">
                <li v-if="onFirstPage()" class="kt-pagination__link--first" :class="'kt-pagination__link--disabled'">
                    <a href="javascript:;" rel="prev"><i :class="'far fa-angle-double-' + right"></i></a>
                </li>
                <li v-else class="kt-pagination__link--first">
                    <a @click.prevent="changePage(1)" :href="previousPageUrl()" rel="prev"><i :class="'far fa-angle-double-' + right"></i></a>
                </li>
                <li v-if="onFirstPage()" class="kt-pagination__link--next kt-pagination__link--disabled">
                    <a href="javascript:;" rel="prev"><i :class="'far fa-angle-' + right"></i></a>
                </li>
                <li v-else class="kt-pagination__link--next">
                    <a @click.prevent="changePage(params.page - 1)" :href="previousPageUrl()" rel="prev"><i :class="'far fa-angle-' + right"></i></a>
                </li>

                <li v-for="item in prevPageCount" :key="`prev${item}`">
                    <a @click.prevent="changePage(params.page - 1 - (prevPageCount - item))" :href="url(params.page - 1 - (prevPageCount - item))">{{ params.page - 1 - (prevPageCount - item) }}</a>
                </li>

                <li v-for="item in nextPageCount" :key="`next${item}`" :class="params.page - 1 + item === params.page ? 'kt-pagination__link--active' : ''">
                    <a v-if="params.page - 1 + item === params.page" href="javascript:;">{{ params.page - 1 + item }}</a>
                    <a v-else @click.prevent="changePage(params.page - 1 + item)" :href="url(params.page - 1 + item)">{{ params.page - 1 + item }}</a>
                </li>

                <li v-if="hasMorePages()" class="kt-pagination__link--prev">
                    <a @click.prevent="changePage(params.page + 1)" :href="nextPageUrl()" rel="next"><i :class="'far fa-angle-' + left"></i></a>
                </li>
                <li v-else class="kt-pagination__link--prev kt-pagination__link--disabled">
                    <a href="javascript:;"><i :class="'far fa-angle-' + left"></i></a>
                </li>
                <li v-if="hasMorePages()" class="kt-pagination__link--last">
                    <a @click.prevent="changePage(lastPage())" :href="nextPageUrl()" rel="next"><i :class="'far fa-angle-double-' + left"></i></a>
                </li>
                <li v-else class="kt-pagination__link--last kt-pagination__link--disabled">
                    <a href="javascript:;"><i :class="'far fa-angle-double-' + left"></i></a>
                </li>
            </ul>
        </template>
        <template v-else>
            <ul class="kt-pagination__links"></ul>
        </template>
        <div class="kt-pagination__toolbar">
            <select class="form-control" style="width: 60px;" @change="changePerPage">
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="500">500</option>
                <option value="1000">1000</option>
            </select>
            <span class="pagination__desc">
                {{ t("Showing :start to :end of :total entries", { start: getStart(), end: getEnd(), total: params.total }) }}
            </span>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        changePage: {},
        changePerPage: {
            type: Function,
            default: function () {
                return {};
            }
        },
        params: {
            type: Object,
            default: function () {
                return {
                    total: 30,
                    perPage: 10,
                    page: 1,
                    baseUrl: "https://arabsstock.com/data"
                };
            }
        }
    },
    computed: {
        prevPageCount() {
            let otherCount = 3 - this.nextPageCount > 0 ? 3 - this.nextPageCount : 0;
            return this.params.page > otherCount + 2 ? otherCount + 2 : this.params.page - 1;
        },
        nextPageCount() {
            let prevPageCount = this.params.page > 2 ? 2 : this.params.page - 1;
            let otherCount = 3 - prevPageCount > 0 ? 3 - prevPageCount : 1;
            return this.lastPage() - this.params.page >= otherCount + 2 ? otherCount + 2 : this.lastPage() - this.params.page + 1;
        }
    },
    data() {
        return {};
    },
    methods: {
        url(page) {
            return this.params.baseUrl + "?page=" + page;
        },
        hasPages() {
            return this.params.page != 1 || this.params.total > this.params.perPage;
        },
        hasMorePages() {
            return this.params.page < this.lastPage();
        },
        onFirstPage() {
            return this.params.page <= 1;
        },
        previousPageUrl() {
            if (this.params.page > 1) {
                return this.url(this.params.page - 1);
            }
        },
        nextPageUrl() {
            if (this.lastPage() > this.params.page) {
                return this.url(this.params.page + 1);
            }
        },
        lastPage() {
            return Math.max(Math.ceil(this.params.total / this.params.perPage), 1);
        },
        getStart() {
            let start = (this.params.page - 1) * this.params.perPage + 1;
            return this.params.total > start ? start : this.params.total;
        },
        getEnd() {
            let end = this.getStart() + this.params.perPage;
            return this.params.total > end ? end : this.params.total;
        }
    }
};
</script>

<style>
.kt-pagination.kt-pagination--brand .kt-pagination__links .kt-pagination__link--disabled,
.kt-pagination.kt-pagination--brand .kt-pagination__links .kt-pagination__link--disabled:hover {
    color: #93a2dd;
    background: #f0f3ff;
    opacity: 0.3;
}
.kt-pagination.kt-pagination--brand .kt-pagination__links li:hover a i {
    color: #93a2dd !important;
}
</style>
