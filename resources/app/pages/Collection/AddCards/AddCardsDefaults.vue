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
    /** Current form value: selected foil type. */
    foilType: string;
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
            <paragraph style="margin-top: 0">
                {{ $t("pages.add_cards.defaults.explanation") }}
                <span v-if="hasSavedDefaults"
                    ><br />
                    <strong>{{ $t("pages.add_cards.defaults.saved_defaults") }}:</strong>
                    {{ $t("form.fields.amount") }}={{ savedDefaults.amount ?? "-" }},
                    {{ $t("form.fields.language") }}={{
                        displayValue(savedDefaults.language, "enums.card_languages.")
                    }}, {{ $t("form.fields.condition") }}={{
                        displayValue(savedDefaults.condition, "enums.conditions.")
                    }}, {{ $t("form.fields.foil_type") }}={{
                        displayValue(savedDefaults.foilType, "enums.foil_types.")
                    }} </span
                ><br />
                <strong>{{ $t("pages.add_cards.defaults.current_settings") }}:</strong>
                {{ $t("form.fields.amount") }}={{ amount }}, {{ $t("form.fields.language") }}={{
                    displayValue(language, "enums.card_languages.")
                }}, {{ $t("form.fields.condition") }}={{ displayValue(condition, "enums.conditions.") }},
                {{ $t("form.fields.foil_type") }}={{ displayValue(foilType, "enums.foil_types.") }}
            </paragraph>
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
