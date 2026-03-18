<script setup lang="ts">
import FormLegend from "Components/Form/FormLegend.vue";
</script>

<template>
    <form-legend
        :items="[
            { slot: 'general', icon: 'info' },
            { slot: 'set', icon: 'collection' },
            { slot: 'number', icon: 'star' },
            { slot: 'combined', icon: 'deck' }
        ]"
    >
        <template #general>
            <i18n-t keypath="card.search.tips.general" scope="global">
                <template #query
                    ><strong>{{ $t("card.search.tips.query") }}</strong></template
                >
                <template #result
                    ><strong>{{ $t("card.search.tips.result") }}</strong></template
                >
            </i18n-t>
        </template>
        <template #set>
            <i18n-t keypath="card.search.tips.set" scope="global">
                <template #set_codes
                    ><strong>{{ $t("card.search.tips.set_codes") }}</strong></template
                >
                <template #set_example
                    ><strong>{{ $t("card.search.tips.set_example") }}</strong></template
                >
                <template #set_result
                    ><strong>{{ $t("card.search.tips.set_result") }}</strong></template
                >
            </i18n-t>
        </template>
        <template #number>
            <i18n-t keypath="card.search.tips.cn" scope="global">
                <template #cn_codes
                    ><strong>{{ $t("card.search.tips.cn_codes") }}</strong></template
                >
                <template #cn_example
                    ><strong>{{ $t("card.search.tips.cn_example") }}</strong></template
                >
                <template #cn_result
                    ><strong>{{ $t("card.search.tips.cn_result") }}</strong></template
                >
            </i18n-t>
        </template>
        <template #combined>
            <i18n-t keypath="card.search.tips.combined" scope="global">
                <template #combined_example
                    ><strong>{{ $t("card.search.tips.combined_example") }}</strong></template
                >
                <template #combined_result
                    ><strong>{{ $t("card.search.tips.combined_result") }}</strong></template
                >
            </i18n-t>
        </template>
    </form-legend>
</template>
