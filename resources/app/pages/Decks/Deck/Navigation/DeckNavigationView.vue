<script setup lang="ts">
import { useId } from "vue";
import Icon from "Components/UI/Icon.vue";
import PopOver from "Components/UI/PopOver.vue";
import type { DeckMeta } from "Types/deckPage.ts";
const popoverId = useId();
defineProps<{
    /** Deck metadata (name, format, state, colors, etc.). */
    deck: DeckMeta;
}>();
/** Close the action popover programmatically. */
function closeViewPopover(): void {
    const dialog = document.getElementById(popoverId);
    if (dialog !== null) dialog.hidePopover();
}
</script>

<template>
    <pop-over
        icon="visibility-on"
        :aria-label="$t('pages.deck.navigation.view.label')"
        class-string="popover-button--rounded"
        :reference="popoverId"
        width="10rem"
        :label="$t('pages.deck.navigation.view.label')"
    >
        <ul class="popover-list">
            <li>
                <button class="popover-list-item" @click="closeViewPopover">
                    <icon name="text" />
                    {{ $t("pages.deck.navigation.view.text") }}
                </button>
            </li>
            <li>
                <button class="popover-list-item" @click="closeViewPopover">
                    <icon name="card" />
                    {{ $t("pages.deck.navigation.view.cards") }}
                </button>
            </li>
        </ul>
    </pop-over>
</template>

<style scoped lang="scss">
.popover {
    margin-left: auto;
}
</style>
