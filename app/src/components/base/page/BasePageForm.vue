<template>
    <div>
        <base-page-body class="form-body" :is-loading="isLoading" :hide-if-loading="true" :has-loading-more="false">
            <div class="p-3">
                <slot name="body">

                </slot>
            </div>
        </base-page-body>

        <base-page-body class="form-footer">
            <div class="footer" v-if="!isLoading">
                <slot name="footer">

                </slot>
            </div>
        </base-page-body>
    </div>
</template>

<script>
import BasePageBody from '@/components/base/page/BasePageBody'
export default {
  name: 'BasePageForm',
  components: { BasePageBody },
  props: [
    'isLoading'
  ]
}
</script>

<style scoped lang="scss">
    .form-body {
        background: white;
    }
    .form-footer .footer {
        height: 50px;
    }
    @media (max-width: 767px) {
        .form-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #f3f3f3;

            .footer {
                padding: 0 12px;
            }
        }
    }
</style>
