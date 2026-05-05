<template>
    <div class="kt-portlet__head row p-1">
        <div class=" col-9">
            <div v-for="(section,index) in filters" :key="index+1" class="kt-portlet__head-group row">
                <template v-for="filter in section" >
                    <div class=" col"
                        :key="filter.name"
                        :class="{ 'd-none':filter.status }"
                     >
                        <template v-if="filter.type == 'select'">
                        <div
                            :class="{ 'text-danger': filter.options.length === 0,'d-none':filter.status }"
                            class="form-group mb-0"
                        >
                            <label>{{ t(filter.name) }}</label>
                            <v-select
                                label="label"
                                :options="filter.options"
                                @input="setSelected"
                                clearable
                                :ref="filter.id"
                                v-model="filter.value"
                            >
                                <div slot="no-options">
                                    {{ t("Sorry, no matching options") }}
                                </div>
                            </v-select>
                        </div>
                        </template>
                        <template v-else-if="filter.type == 'input'">
                        <div class="form-group mb-0">
                            <label>  {{t(filter.name)}} </label>
                            <input 
                            type="text"
                            class="form-control"
                            v-model="filter.value"
                            :ref="filter.id"
                            :id="filter.id"
                            >

                        </div>
                        </template>

                    </div>

                </template>

            </div>
        </div>
        <div class=" col-3">
            <div class="kt-portlet__head-group">
                <div class="btn-group">
                  
                    <div class="btn-group">
                        <button
                            type="button"
                            class="btn btn-secondary btn-sm"
                            @click="selectAll"
                        >
                            {{ t("Select All") }}
                        </button>
                    </div>
                    <div class="btn-group">
                        <button
                            type="button"
                            class="btn btn-secondary btn-sm"
                            @click="resetSelections"
                        >
                            {{ t("Reset Selections") }}
                        </button>
                    </div>
                    <div class="btn-group">
                        <button
                            type="button"
                            class="btn btn-secondary btn-sm"
                            @click="resetFiltersLocal"
                        >
                            {{ t("Reset Filters") }}
                        </button>
                    </div>
                </div>
                    <div class="btn-group col p-0 mt-3">
                        <button
                            type="button"
                            class="btn btn-primary font-weight-bold"
                            @click="fetchImages"
                        >
                            {{ t("search") }}
                        </button>
                    </div>                  
            </div>
        </div>
    </div>
</template>

<script>
export default {
    created(){
    // this.$emit("option:deselected", this.onClear);

},

    data() {
    return {
         timer:null,
    }
       
    },
    props: {
        filters: {},
        selectAll: {},
        selectFilter: {},
        // DeSelectFilter: {},
        resetFilters: {},
        resetSelections: {},
        fetchImages:{},
    },
    methods: {
        activeOption(filter) {
            let option = filter.options.filter(item => item.active)[0];
            return option && option.label;
        },
        setSelected(value) {
            if (value) this.selectFilter(value.filterId, value.id,"select",false);

        },
        
        resetFiltersLocal() {
            this.filters.forEach(element => {
                for (let index = 0; index < element.length; index++) {
                    element[index].value = null;
                    element[index].status = false;
                }
                
            });

            this.fetchImages();
        },

    },
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
.btn-primary {
    color: #fff !important;
    background-color: #20d598;
    border-color: #20d598;
}
</style>
