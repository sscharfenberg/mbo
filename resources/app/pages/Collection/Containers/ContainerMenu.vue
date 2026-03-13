<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { useId } from "vue";
import Icon from "Components/UI/Icon.vue";
import PopOver from "Components/UI/PopOver.vue";
const closePopover = () => {
    const dialog = document.getElementById(refId);
    if (dialog !== null) dialog.hidePopover();
};
const refId = useId();
defineProps<{ containerId: string }>();
</script>

<template>
    <pop-over
        icon="more"
        :aria-label="$t('header.user.label')"
        class-string="popover-button--rounded container-context-menu"
        :reference="refId"
        width="16ch"
    >
        <ul class="popover-list popover-list--short">
            <li>
                <Link
                    class="popover-list-item"
                    :href="`/collection/containers/${containerId}/edit`"
                    @click="closePopover"
                >
                    <icon name="edit" :size="1" />
                    {{ $t("pages.edit_container.link") }}
                </Link>
            </li>
        </ul>
    </pop-over>
</template>

<style lang="scss" scoped>
.popover {
    margin-right: 1ch;
}
</style>
