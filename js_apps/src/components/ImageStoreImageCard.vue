<template>
    <div class="col">
        <!--begin::Portlet-->
        <div class="kt-portlet">
            <div class="kt-blog-grid" :class="{active: item.checked}">
                <div class="kt-blog-grid__head">
                    <a class="kt-blog-grid__thumb-link" href="javascript:;" @click="checkImage(item.id, !item.checked)">
                        <img :src="item.thumbnail" alt="" class="kt-blog-grid__image" />
                    </a>
                </div>
                <div class="kt-portlet__body pb-0" @click="checkImage(item.id, !item.checked)">
                    <div class="kt-widget kt-widget--general-4">
                        <a href="javascript:;" class="kt-widget__title" data-toggle="tooltip" :data-original-title="item.original_name">
                            <div class="btn-group">
                                <label class="kt-checkbox kt-checkbox--single kt-checkbox--solid" @click.prevent.stop="checkImage(item.id, !item.checked)">
                                    <input type="checkbox" value="" class="kt-checkable" :checked="item.checked" />
                                    <span></span>
                                </label>
                            </div>
                            {{ item.original_name }}
                        </a>

                        <div class="kt-widget__desc" v-if="item.checked && globalTitle !== -1">
                            {{ globalTitle }}
                        </div>
                        <div class="kt-widget__desc" v-else>
                            {{ item.title_ar }}
                        </div>



                    </div>
                </div>
                <div class="kt-blog-grid__head">
                    <div class="row">
                        <div class="col-12 text-center widget__actions">
                                <a  v-if="(item.removebg_status && item.removebg_status.value === 'done')" :href="item.removebg_watermark" target="_blank" ><i class="far fa-image  fa-xl pr-2 removebg pb-0"></i></a>
                                <a :href="item.preview" target="_blank" @click.stop="showImage"><i class="flaticon2-expand kt-icon-lg kt-font-brand"></i></a>
                                <a v-if="item.post_link" :href="item.post_link" target="_blank" ><i class="fas fa-link kt-icon-lg kt-font-brand p-2"></i></a>
                                
                        </div>

                        <div class="col-12 text-center kt-widget__actions mb-2">
                                <span title="owner" class="  badge badge-info">{{ item.owner }}</span>
                                <span title="file status" class="  badge " :class="{'badge-success':item.status === 'active','badge-warning':item.status === 'pending'}" >{{ item.status }}</span>
                                <span title="contributor status file " class=" mr-1 badge badge-danger" v-if="item.status_contributor_file_lable" >{{ item.status_contributor_file_lable }}</span>
                                <span title="license" class=" badge badge-primary" v-if="item.license ">{{ item.license_title }}</span>
                        
                        </div>
                        <div class="col-12 text-center kt-widget__actions mb-2">
                        <span title="status file" class="col badge badge-info">{{ item.status_file }}</span>
                        </div>
                        <template v-if="dataType !== 'vectors'">
                            <div class="col-12 text-center kt-widget__actions mb-2" v-if="item.release_ids != undefined && item.release_ids.length">
                            <div class="col-12 text-center kt-widget__actions mb-2">
                            <span title="status file" class="col badge badge-danger">{{ t("Viewer consent forms")}} <span class="badge badge-warning">{{item.release_ids.length}}</span></span>
                            </div>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                    <a :href="release.file" target="_blank" v-for="release in item.release_ids" :key="release.id" class="btn btn-light active rounded m-1" :title="release.label" >{{release.label | truncate(0,5)}}</a>

                                </div>
                            </div>
                            <div class="col-12 text-center kt-widget__actions mb-2" v-else>
                            <span title="status file" class="col badge badge-danger">{{ t("Viewer consent forms")}} <span class="badge badge-warning"> 0 </span></span>
                                
                            </div>
                            
                        </template>
                        <div class="col" v-if="item.removebg_status">
                                <div class="alert alert-info mb-0 p-0" role="alert">
                                       <span class="text-center w-100">
                                            {{t('removebg_status')}} : {{item.removebg_status.title}}
                                       </span>
                                </div> 
                                <div class="alert  mb-0 p-0" 
                                :class="{'alert-success':item.removebg_status_disply === 'active','alert-warning':item.removebg_status_disply === 'pending'}"
                                role="alert">
                                       <span class="text-center w-100">
                                            {{t('removebg_status_disply')}} : {{item.removebg_status_disply}}
                                       </span>
                                </div>                           
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--end::Portlet-->
    </div>
</template>

<script>
export default {
    props: {
        item: {},
        globalTitle: {},
        dataType: {
        type: String,
        default: ''
        },
        checkImage: {
            type: Function,
            default: function () {
                return {};
            }
        }
    },
    components: {},
    created() {
        this.$nextTick(function () {
            window.$('[data-toggle="tooltip"]').tooltip();
        });
    },
    data() {
        return {};
    },
    methods: {
        showImage() {
            // dummy to fire href and not make checkbox active
        }
    },
    filters: {
        truncate: function(value,from,count) {
                value = value.substring(from, count) + '...';
            return value
        }
    }
};
</script>

<style>
.kt-widget.kt-widget--general-4 .kt-widget__head {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: start;
    -ms-flex-align: start;
    align-items: flex-start;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    margin-bottom: 1.5rem;
}
.kt-widget.kt-widget--general-4 .kt-widget__title {
    display: inline-block;
    font-size: 1.2rem;
    font-weight: 600;
    color: #595d6e;
    -webkit-transition: color 0.3s ease;
    transition: color 0.3s ease;
    margin-bottom: 1rem;
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kt-widget.kt-widget--general-4 .kt-widget__desc {
    font-size: 1rem;
    color: #74788d;
    margin-bottom: 1.5rem;
}
.kt-widget.kt-widget--general-4 .kt-widget__actions {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
}
.kt-widget__img {
    width: 100%;
    height: 100%;
    max-width: 100%;
    margin-bottom: 1.5rem;
}

.kt-blog-grid .kt-blog-grid__head {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
}

.kt-blog-grid .kt-blog-grid__head .kt-blog-grid__thumb-link {
    -webkit-box-flex: 1;
    -ms-flex: 1;
    flex: 1;
}
.kt-blog-grid .kt-blog-grid__head .kt-blog-grid__thumb-link .kt-blog-grid__image {
    width: 100%;
    height: 100%;
}

.kt-blog-grid.active:before {
    background: #5867dd;
}
.kt-blog-grid:before {
    position: absolute;
    display: block;
    border-radius: 2px;
    width: 95%;
    height: 8px;
    top: 93%;
    content: ""
}
a i.removebg{
    font-size: x-large;
}
</style>
