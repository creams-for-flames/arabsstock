<template>
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-group">
                <div class="btn-group">
                    <ul class="nav nav-tabs nav-fill" role="tablist">
                       <li class="nav-item" v-for="stage in stages" :key="stage.id" @click="changeStage(stage.id)">
                           <a class="nav-link" :class="{active: stage.active}">{{t(stage.label)}}</a>
                       </li>

                       <li class="nav-item dropdown">
                           <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{{t(dataType)}}</a>

                       </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-group">
                <div class="btn-group">
                    <div class="btn-group" v-if="selectList.length > 0">
                        <!-- <button type="button" class="btn btn-danger btn-sm" @click="deleteHandler">
                        <i class="far fa-trash"></i>
                        {{ t("Delete Selection") }}  
                        -
                        {{`(${selectList.length})`}} 
                        </button> -->
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary btn-sm" @click="selectAll">
                            {{ t("Select All") }}
                        </button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary btn-sm" @click="resetSelections">
                            {{ t("Reset Selections") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { findIndex } from "lodash";
export default {
    props: {
        filters: {},
        selectAll: {},
        selectFilter: {},
        resetSelections: {},
        dataType: {},
        setStage: {},
        setDataType: {},
        selectList: {},
        dataList:{},
        deleteAll:{},

    },
    data() {
        return {
            stages: [
                {
                  id: 1,
                  label: "To submit",
                  active: true,
                },
                // {
                //   id: 2,
                //   label: "Pending",
                //   active: false,
                // },
                // {
                //   id: 3,
                //   label: "Reviewed",
                //   active: false,
                // },
            ],
        };
    },
    methods: {
        activeOption(filter) {
            let option = filter.options.filter(item => item.active)[0];
            return option && option.label;
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
                     this.deleteAll()
                }
            });
        },
        changeStage(id) {
            this.stages.map(stage => {
                stage.active = false
                return stage
            })
            var index = findIndex(this.stages, {id: id});
            this.stages[index].active =  true
            this.setStage(id)
        },
        changeData(id) {
            this.setDataType(id)
        }
    }
};
</script>

<style>
.truncate-100 {
    width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.dropdown-menu {
    max-height: 450px;
    overflow-y: auto;
}
</style>
