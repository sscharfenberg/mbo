<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { useId } from "vue";
import { useI18n } from "vue-i18n";
import type { DeckRow } from "@/pages/Decks/Decks.vue";
import ColorIdentity from "Components/Card/ColorIdentity.vue";
import Badge from "Components/UI/Badge.vue";
import Icon from "Components/UI/Icon.vue";
import PopOver from "Components/UI/PopOver.vue";
import VisibilityBadge from "Components/UI/VisibilityBadge.vue";
import { useFormatting } from "Composables/useFormatting.ts";

defineProps<{
    /** A single deck row from the controller. */
    deck: DeckRow;
}>();

const { t } = useI18n();
const { formatDateTime } = useFormatting();
const popoverId = useId();

/** Close the action popover programmatically. */
function closePopover(): void {
    const dialog = document.getElementById(popoverId);
    if (dialog !== null) dialog.hidePopover();
}

/** Map deck state to Badge component type. */
function mapStateToBadge(state: string): "info" | "success" | "warning" {
    if (state === "built") return "success";
    if (state === "archived") return "warning";
    return "info";
}

/** Map deck state to icon name. */
function mapStateToIcon(state: string): string {
    if (state === "built") return "finished";
    return state;
}
</script>

<template>
    <Link class="decklist__link" :href="`/decks/${deck.id}`">
        <color-identity class="decklist__colors" :color-identity="deck.colors" />
        <span class="decklist__name">{{ deck.name }}</span>
        <badge class="decklist__state" :type="mapStateToBadge(deck.state)">
            <icon :name="mapStateToIcon(deck.state)" />
            <span>{{ t(`enums.deck_state.${deck.state}`) }}</span>
        </badge>
        <span class="decklist__cards">
            <icon name="deck" />
            {{ deck.card_count }}
            <span>{{ t("pages.decks.card_count_noun", deck.card_count) }}</span>
        </span>
        <time class="decklist__timestamp" :datetime="deck.last_activity">
            {{ formatDateTime(deck.last_activity) }}
        </time>
        <visibility-badge :visibility="deck.visibility" />
        <pop-over
            icon="more"
            :aria-label="t('pages.decks.actions.label')"
            class-string="popover-button--rounded"
            :reference="popoverId"
            width="14rem"
        >
            <ul class="popover-list">
                <li v-if="deck.visibility === 'private'">
                    <button class="popover-list-item" @click="closePopover">
                        <icon name="visibility-on" :size="1" />
                        {{ t("pages.decks.actions.set_public") }}
                    </button>
                </li>
                <li v-else>
                    <button class="popover-list-item" @click="closePopover">
                        <icon name="visibility-off" :size="1" />
                        {{ t("pages.decks.actions.set_private") }}
                    </button>
                </li>
                <li v-if="deck.state !== 'planned'">
                    <button class="popover-list-item" @click="closePopover">
                        <icon name="edit" :size="1" />
                        {{ t("pages.decks.actions.set_planned") }}
                    </button>
                </li>
                <li v-if="deck.state !== 'built'">
                    <button class="popover-list-item" @click="closePopover">
                        <icon name="check" :size="1" />
                        {{ t("pages.decks.actions.set_built") }}
                    </button>
                </li>
                <li v-if="deck.state !== 'archived'">
                    <button class="popover-list-item" @click="closePopover">
                        <icon name="storage" :size="1" />
                        {{ t("pages.decks.actions.archive") }}
                    </button>
                </li>
                <li v-else>
                    <button class="popover-list-item" @click="closePopover">
                        <icon name="storage" :size="1" />
                        {{ t("pages.decks.actions.unarchive") }}
                    </button>
                </li>
                <li>
                    <button class="popover-list-item popover-list-item--error" @click="closePopover">
                        <icon name="delete" :size="1" />
                        {{ t("pages.decks.actions.delete") }}
                    </button>
                </li>
            </ul>
        </pop-over>
    </Link>
</template>

<style lang="scss" scoped>
/** styles can be found in
 * resources/app/styles/components/deck/_decklist.scss
 */
@use "Abstracts/mixins" as m;

:deep(.visibility-badge) {
    display: none;

    @include m.mq("landscape") {
        display: inline-flex;
    }
}

:deep(.popover) {
    justify-self: end;
}

:deep(.badge) {
    font-size: 0.8em;

    span {
        display: none;

        @include m.mq("landscape") {
            display: inline-flex;
        }
    }
}
</style>
