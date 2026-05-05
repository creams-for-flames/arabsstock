<template>
    <div class="modal fade" id="create-release-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ t("Add new release") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="kt-form" @submit.prevent="onSave">
                        <div class="kt-portlet__body">
                            <div class="form-group">
                                <div class="custom-file">
                                  <input type="file" class="custom-file-input" id="customFile" ref="file" v-on:change="handleFilesUpload">
                                  <label class="custom-file-label" for="customFile">{{ label }}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>{{ t("Release name") }} </label>
                                <input v-model="name" type="text" class="form-control" :placeholder="t('Release name')" />
                            </div>
                            <div class="form-group">
                                <label>{{ t("Release Type") }}</label>
                                <div class="form-check">
                                    <div class="form-check">
                                      <label class="form-check-label">
                                          <input v-model="type" type="radio" class="form-check-input" value="model">{{t("Model release")}}
                                      </label>
                                    </div>
                                    <div class="form-check">
                                      <label class="form-check-label">
                                        <input v-model="type" type="radio" class="form-check-input" value="property">{{t("Property release")}}
                                      </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button @click="save" type="button" class="btn btn-primary">{{ t("Save") }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ t("Close") }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        saveRelease: {},
        status: {},
    },
    watch: {
        status() {
            this.label = "Choose file";
            this.name = "";
            this.type = "";
            this.file = "";
        }
    },
    created() {
        this.label = this.t(this.label);
    },
    data() {
        return {
            label: "Choose file",
            name: "",
            type: "",
            file: "",
        };
    },
    methods: {
        save() {
            this.saveRelease(
              this.$refs.file.files[0],
              this.name,
              this.type,
              this.closeModal
            )
        },
        handleFilesUpload(){
            this.label = this.$refs.file.files[0].name
            this.name = this.label.split('.')[0]
        },
        closeModal(){
            window.$("#create-release-modal").modal("hide");
        },
    }
};
</script>

<style></style>

