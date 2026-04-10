<script setup lang="ts">
import { useId } from "vue";
import { useI18n } from "vue-i18n";
import ColorIdentity from "Components/Card/ColorIdentity.vue";
import DeckState from "Components/Deck/DeckState.vue";
import Badge from "Components/UI/Badge.vue";
import Icon from "Components/UI/Icon.vue";
import Paragraph from "Components/UI/Paragraph.vue";
import PopOver from "Components/UI/PopOver.vue";
import VisibilityBadge from "Components/UI/VisibilityBadge.vue";
import type { DeckMeta } from "Types/deckPage.ts";
const popoverId = useId();
defineProps<{
    /** Deck metadata (name, format, state, colors, etc.). */
    deck: DeckMeta;
    /** hasCommanders **/
    hasCommanders: boolean;
}>();
/** Close the action popover programmatically. */
function closePopover(): void {
    const dialog = document.getElementById(popoverId);
    if (dialog !== null) dialog.hidePopover();
}
const { t } = useI18n();
</script>

<template>
    <section class="deck-meta">
        <header class="deck-meta__name">
            {{ deck.name.toUpperCase() }}
            <pop-over
                icon="more"
                aria-label="test"
                class-string="popover-button--rounded"
                :reference="popoverId"
                width="14rem"
            >
                <ul class="popover-list">
                    <li v-if="deck.visibility === 'private'">
                        <button class="popover-list-item" @click="closePopover">
                            <icon name="visibility-on" :size="1" />
                            {{ $t("pages.decks.actions.set_public") }}
                        </button>
                    </li>
                </ul>
            </pop-over>
        </header>
        <div class="deck-meta__badges">
            <badge v-if="deck.colors"><color-identity :color-identity="deck.colors" /></badge>
            <badge type="info" v-tooltip="`${t('pages.deck.format')}: ${t('enums.card_formats.' + deck.format)}`"
                ><icon name="spell" :size="1" />{{ $t(`enums.card_formats.${deck.format}`) }}</badge
            >
            <badge
                v-if="hasCommanders && deck.bracket"
                v-tooltip="`${t('pages.deck.bracket')} ${deck.bracket}: ${t('enums.bracket.' + deck.bracket)}`"
            >
                <icon name="swords" :size="1" />{{ deck.bracket }}
            </badge>
            <deck-state :state="deck.state" />
            <badge type="info"><icon name="deck" :size="1" />{{ deck.card_count }}</badge>
            <visibility-badge :visibility="deck.visibility" />
        </div>
        <paragraph v-if="deck.description">{{ deck.description }}</paragraph>
    </section>
</template>

<style lang="scss" scoped>
:deep(.badge) {
    padding: 0.2rem 0.5rem;
}

:deep(p) {
    margin: 0;
}
</style>
