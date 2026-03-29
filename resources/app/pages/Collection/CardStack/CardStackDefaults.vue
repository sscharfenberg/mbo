<script setup lang="ts">
import { useI18n } from "vue-i18n";
import ButtonGroup from "Components/Form/ButtonGroup.vue";
import Accordion from "Components/UI/Accordion.vue";
import Icon from "Components/UI/Icon.vue";
import Paragraph from "Components/UI/Paragraph.vue";
import type { AddCardsDefaults } from "Composables/useAddCardsDefaults.ts";
defineProps<{
    /** Current form value: number of copies. */
    amount: number;
    /** Current form value: selected language code. */
    language: string;
    /** Current form value: selected condition. */
    condition: string;
    /** Current form value: selected finish. */
    finish: string;
    /** The raw saved defaults object from localStorage. */
    savedDefaults: AddCardsDefaults;
    /** Whether any user defaults are currently saved. */
    hasSavedDefaults: boolean;
}>();
defineEmits<{
    /** Emitted when the user clicks "Save as defaults". */
    save: [];
    /** Emitted when the user clicks "Clear defaults". */
    clear: [];
}>();
const { t } = useI18n();
/** Format a value for display, falling back to "-" for empty strings. */
function displayValue(value: string | number | undefined, translationPrefix?: string): string {
    if (value === undefined || value === "") return "-";
    if (translationPrefix) return t(`${translationPrefix}${value}`);
    return String(value);
}
</script>

<template>
    <accordion>
        <template #head>{{ $t("pages.add_cards.defaults.title") }}</template>
        <template #body>
            <paragraph style="margin: 0">
                {{ $t("pages.add_cards.defaults.explanation") }}
            </paragraph>
            <div class="defaults">
                <ul v-if="hasSavedDefaults">
                    <li>{{ $t("pages.add_cards.defaults.saved_defaults") }}</li>
                    <li>
                        {{ $t("form.fields.amount") }} <span>{{ savedDefaults.amount ?? "-" }}</span>
                    </li>
                    <li>
                        {{ $t("form.fields.language") }}
                        <span>{{ displayValue(savedDefaults.language, "enums.card_languages.") }}</span>
                    </li>
                    <li>
                        {{ $t("form.fields.condition") }}
                        <span>{{ displayValue(savedDefaults.condition, "enums.conditions.") }}</span>
                    </li>
                    <li>
                        {{ $t("form.fields.finish") }}
                        <span>{{ displayValue(savedDefaults.finish, "enums.finishes.") }}</span>
                    </li>
                </ul>
                <ul>
                    <li>{{ $t("pages.add_cards.defaults.current_settings") }}</li>
                    <li>
                        {{ $t("form.fields.amount") }} <span>{{ amount }}</span>
                    </li>
                    <li>
                        {{ $t("form.fields.language") }}
                        <span>{{ displayValue(language, "enums.card_languages.") }}</span>
                    </li>
                    <li>
                        {{ $t("form.fields.condition") }}
                        <span>{{ displayValue(condition, "enums.conditions.") }}</span>
                    </li>
                    <li>
                        {{ $t("form.fields.finish") }}
                        <span>{{ displayValue(finish, "enums.finishes.") }}</span>
                    </li>
                </ul>
            </div>
            <button-group>
                <button type="button" class="btn-primary" @click="$emit('save')">
                    <icon name="save" />
                    {{ $t("pages.add_cards.defaults.save") }}
                </button>
                <button v-if="hasSavedDefaults" type="button" class="btn-default" @click="$emit('clear')">
                    <icon name="clear" />
                    {{ $t("pages.add_cards.defaults.clear") }}
                </button>
            </button-group>
        </template>
    </accordion>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;

.defaults {
    display: flex;
    align-items: flex-start;
    flex-direction: column;

    margin: 0.5rem 0;
    gap: 0.5rem;
}

ul {
    display: inline-flex;
    flex-wrap: wrap;

    overflow: hidden;

    padding: map.get(s.$pages, "defaults", "border");
    border: 0;
    margin: 0;
    gap: map.get(s.$pages, "defaults", "border");

    background-color: map.get(c.$pages, "defaults", "border");

    list-style: none;
    border-radius: map.get(s.$pages, "defaults", "radius");

    li {
        padding: map.get(s.$pages, "defaults", "padding");

        background-color: map.get(c.$components, "accordion", "background");

        span {
            font-weight: bold;
        }
    }
}
</style>
