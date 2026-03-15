<script setup lang="ts">
import { ref } from "vue";
import AccountDeleteModal from "@/pages/Dashboard/Delete/AccountDeleteModal.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import LoadingSpinner from "Components/UI/LoadingSpinner.vue";
import Paragraph from "Components/UI/Paragraph.vue";
const processing = ref(false);
const showModal = ref(false);
</script>

<template>
    <headline :size="3" anchor-id="deleteSection">{{ $t("pages.dashboard.deletion.title") }}</headline>
    <form class="form" @submit.prevent="showModal = true">
        <Paragraph style="margin: 0">
            <i18n-t keypath="pages.dashboard.deletion.explanation" scope="global">
                <template #caution
                    ><strong>{{ $t("pages.dashboard.deletion.caution") }}</strong></template
                > </i18n-t
            ><br />
            <i18n-t keypath="pages.dashboard.deletion.no_soft_deletes" scope="global">
                <template #soft_deletes
                    >'<cite>{{ $t("pages.dashboard.deletion.soft_deletes") }}</cite
                    >'</template
                > </i18n-t
            ><br />
            <strong>{{ $t("pages.dashboard.deletion.reversed") }}</strong>
        </Paragraph>
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="delete" />
                {{ $t("pages.dashboard.deletion.submit") }}
                <loading-spinner v-if="processing" :size="2" />
            </button>
        </form-group>
    </form>
    <account-delete-modal v-if="showModal" @close="showModal = false" />
</template>
