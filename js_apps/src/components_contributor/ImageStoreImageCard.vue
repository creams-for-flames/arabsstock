<template>
  <div class="col-md-4 px-2" :title="item.is_uploaded">
    <!--begin::Portlet-->
    <div class="kt-portlet border" :class="{ active: item.checked }">
      <div class="kt-blog-grid" :class="{ active: item.checked }">
        <div class="kt-blog-grid__head rounded">
          <a
            class="kt-blog-grid__thumb-link rounded"
            href="javascript:;"
            @click="checkImage(item.id, !item.checked)"
          >
            <img
              :src="item.thumbnail"
              alt=""
              class="kt-blog-grid__image rounded"
            />
          </a>
        </div>
        <div class="pt-3" @click="checkImage(item.id, !item.checked)">
          <div class="kt-widget kt-widget--general-4 p-1">
            <a
              href="javascript:;"
              class="kt-widget__title"
              data-toggle="tooltip"
              :data-original-title="item.original_name"
            >
              <div class="btn-group">
                <label
                  class="kt-checkbox kt-checkbox--single kt-checkbox--solid"
                  @click.prevent.stop="checkImage(item.id, !item.checked)"
                >
                  <input
                    type="checkbox"
                    value=""
                    class="kt-checkable"
                    :checked="item.checked"
                  />
                  <span></span>
                </label>
              </div>
              {{ item.original_name }}
            </a>

            <div
              class="kt-widget__desc"
              v-if="item.checked && globalTitle !== -1"
            >
              {{ globalTitle }}
            </div>
            <div class="kt-widget__desc" v-else>
              {{ item.title_ar }}
            </div>
            <!-- <div class="kt-widget__desc" v-if="item.contributor_stage > 0">
              {{
                t("Status: ") + t(contributor_stages[item.contributor_stage])
              }}
            </div> -->
            <div
              class="kt-widget__desc badge badge-primary text-white"
              v-if="item.contributor_stage > 0"
            >
              {{
                t("Status: ") + t(contributor_stages[item.contributor_stage])
              }}
            </div>
            <div
              class="kt-widget__desc badge badge-primary text-white"
              v-else-if="item.remove"
            >
              {{ t("Status: ") + t("remove") }}
            </div>

            <div
              class="kt-widget__desc badge badge-warning text-white"
              v-if="item.license_title"
            >
              {{ item.license_title }}
            </div>
            <div
              v-if="item.post_link"
              class="kt-widget__desc badge badge-info text-white"
            >
              <a
                :href="item.post_link"
                target="_blank"
                class="post_link text-white"
                ><i class="fas fa-link kt-icon-lg pr-2 pl-2"></i
              ></a>
            </div>

            <div
              class="kt-widget__desc alert alert-danger p-1 mb-0 text-center"
              v-if="item.review_notes && item.contributor_stage > 0"
            >
              {{ item.review_notes }}
            </div>
            <!-- <div class="kt-widget__actions">
                            <div class="kt-widget__left"></div>
                            <div class="kt-widget__right">
                                <a v-if="item.post_link" :href="item.post_link" target="_blank" ><i class="fas fa-link kt-icon-lg kt-font-brand p-2"></i></a>
                            </div>
                            
                        </div> -->
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
    checkImage: {
      type: Function,
      default: function () {
        return {};
      },
    },
  },
  components: {},
  created() {
    this.$nextTick(function () {
      window.$('[data-toggle="tooltip"]').tooltip();
    });
  },
  data() {
    return {
      contributor_stages: {
        0: "new",
        1: "data_entry",
        2: "review",
        3: "reject",
        4: "hard_reject",
        5: "processing",
        6: "review",
        8: "publish",
      },
    };
  },
  methods: {
    showImage() {
      // dummy to fire href and not make checkbox active
    },
  },
};
</script>

<style scoped>
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
  font-size: 1rem;
  font-weight: 600;
  color: #111;
  -webkit-transition: color 0.3s ease;
  transition: color 0.3s ease;
  width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.kt-widget.kt-widget--general-4 .kt-widget__desc {
  font-size: 0.875rem;
  color: #74788d;
  margin-bottom: 0.5rem;
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
.kt-blog-grid
  .kt-blog-grid__head
  .kt-blog-grid__thumb-link
  .kt-blog-grid__image {
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.kt-blog-grid.active:before {
  background: #20d899;
}

.kt-blog-grid:before {
  position: relative;
  display: block;
  border-radius: 2px;
  width: 100%;
  height: 4px;
  content: "";
  display: none;
}
.kt-blog-grid.active .kt-blog-grid__head.border,
.kt-portlet .active {
  border: 1px solid #20d899 !important;
}
a.post_link:hover {
  color: #fff !important;
}
</style>
