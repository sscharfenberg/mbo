<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import Icon from "Components/UI/Icon.vue";
import PopOver from "Components/UI/PopOver.vue";
/** Programmatically hides the user menu popover by its DOM id. */
const closePopover = () => {
    const dialog = document.getElementById("userMenu");
    if (dialog !== null) dialog.hidePopover();
};
</script>

<template>
    <pop-over
        icon="cards"
        :aria-label="$t('header.user.label')"
        class-string="popover-button--rounded"
        reference="siteMenu"
        width="10rem"
    >
        <ul class="popover-list popover-list--short">
            <li>
                <Link class="popover-list-item" href="/collection" @click="closePopover">
                    <icon name="collection" :size="1" />
                    {{ $t("pages.collection.link") }}
                </Link>
            </li>
            <li>
                <Link class="popover-list-item" href="/decks" @click="closePopover">
                    <icon name="deck" :size="1" />
                    {{ $t("pages.decks.link") }}
                </Link>
            </li>
        </ul>
    </pop-over>
</template>

<style lang="scss" scoped>
@use "Abstracts/mixins" as m;

.popover {
    @include m.mq("landscape") {
        display: none;
    }
}
</style>
