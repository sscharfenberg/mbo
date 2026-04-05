<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import CommanderPicker from "Components/Deck/CommanderPicker/CommanderPicker.vue";
import DeckFormatCapabilities from "Components/Deck/DeckFormatCapabilities.vue";
import FormGroup from "Components/Form/FormGroup.vue";
import MonoSelect from "Components/Form/Select/MonoSelect.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import { useBreadcrumbs } from "Composables/useBreadcrumbs.ts";
import type { FormatCapabilities } from "Types/formatCapabilities";
const props = defineProps<{
    formats: string[];
    capabilities: Record<string, FormatCapabilities>;
    nameMax: number;
    descriptionMax: number;
}>();
const { t } = useI18n();
/** CardFormat options formatted for MonoSelect: `{ value, label }` pairs with translated labels. */
const formatOptions = computed(() => props.formats.map(value => ({ value, label: t(`enums.card_formats.${value}`) })));
/** Currently selected deck format. */
const selectedFormat = ref("");
/** Capabilities for the currently selected format, or null if none selected. */
const selectedCapabilities = computed<FormatCapabilities | null>(
    () => props.capabilities[selectedFormat.value] ?? null
);
/** Whether the commander picker modal is open. */
const commanderPickerOpen = ref(false);
const { setBreadcrumbs } = useBreadcrumbs();
setBreadcrumbs([{ labelKey: "pages.decks.link", href: "/decks" }, { labelKey: "pages.add_deck.link" }]);
</script>

<template>
    <Head>
        <title>{{ $t("pages.add_deck.title") }}</title>
    </Head>
    <headline>
        <icon name="deck" :size="3" />
        {{ $t("pages.add_deck.title") }}
    </headline>
    <Form class="form" action="/decks/add" method="post" #default="{ errors, processing, validating, valid, validate }">
        <form-group :label="$t('form.fields.format')" :required="true">
            <mono-select
                :options="formatOptions"
                :selected="selectedFormat"
                @change="selectedFormat = $event"
                addon-icon="card"
                max="100%"
            />
            <input type="hidden" name="format" :value="selectedFormat" />
            <template v-if="selectedCapabilities" #text>
                <deck-format-capabilities :capabilities="selectedCapabilities" />
            </template>
        </form-group>
        <form-group v-if="selectedCapabilities?.requiresCommander" :label="$t('form.fields.commander')">
            <button type="button" class="btn-default" @click="commanderPickerOpen = true">
                <icon name="register" />
                {{ $t("pages.add_deck.commander.choose") }}
            </button>
        </form-group>
        <form-group
            for-id="deck_name"
            :label="$t('form.fields.container.name')"
            :error="errors.container_name ?? ''"
            :invalid="!!errors?.container_name"
            :validated="valid('container_name')"
            :validating="validating"
            :required="true"
            addon-icon="container-name"
        >
            <input
                type="text"
                name="deck_name"
                id="deck_name"
                class="form-input"
                :maxlength="nameMax"
                @change="validate('container_name')"
            />
        </form-group>
        <form-group
            for-id="deck_description"
            :label="$t('form.fields.container.description')"
            :error="errors.deck_description ?? ''"
            :invalid="!!errors?.deck_description"
            :validated="valid('deck_description')"
            :validating="validating"
        >
            <div class="form-input__textarea-addon"><icon name="text" /></div>
            <div class="form-input form-input__textarea">
                <textarea
                    name="deck_description"
                    id="deck_description"
                    :maxlength="props.descriptionMax"
                    @change="validate('container_description')"
                />
            </div>
        </form-group>
        <form-group>
            <button type="submit" class="btn-primary" :disabled="processing">
                <icon name="save" />{{ $t("pages.add_deck.submit") }}
            </button>
        </form-group>
    </Form>
    <commander-picker v-if="commanderPickerOpen" @close="commanderPickerOpen = false" />
</template>
