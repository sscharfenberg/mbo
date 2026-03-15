<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { ref, useId } from "vue";
import ContainersDeleteModal from "@/pages/Collection/Containers/ContainersDeleteModal.vue";
import Icon from "Components/UI/Icon.vue";
import PopOver from "Components/UI/PopOver.vue";
import type { Container } from "Types/container";
const closePopover = () => {
    const dialog = document.getElementById(refId);
    if (dialog !== null) dialog.hidePopover();
};
const refId = useId();
defineProps<{ container: Container }>();
const showDeleteModal = ref(false);
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
                    :href="`/collection/containers/${container.id}/edit`"
                    @click="closePopover"
                >
                    <icon name="edit" :size="1" />
                    {{ $t("pages.edit_container.link") }}
                </Link>
            </li>
            <li>
                <button
                    class="popover-list-item"
                    @click="
                        showDeleteModal = true;
                        closePopover;
                    "
                >
                    <icon name="delete" :size="1" />
                    {{ $t("pages.containers.delete.link") }}
                </button>
            </li>
        </ul>
    </pop-over>
    <containers-delete-modal v-if="showDeleteModal" @close="showDeleteModal = false" :container="container" />
</template>

<style lang="scss" scoped>
.popover {
    margin-right: 1ch;
}
</style>
