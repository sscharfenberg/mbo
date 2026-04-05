<script setup lang="ts">
import { computed } from "vue";
import FormLegend from "Components/Form/FormLegend.vue";
import Accordion from "Components/UI/Accordion.vue";
import type { FormatCapabilities } from "Types/formatCapabilities";
const props = defineProps<{
    capabilities: FormatCapabilities;
}>();
/** Deck size is "exact" when min and max are equal (e.g. Commander 100/100). */
const isExactDeckSize = computed(
    () => props.capabilities.maxDeckSize !== null && props.capabilities.maxDeckSize === props.capabilities.minDeckSize
);
/** FormLegend items, assembled from the capabilities bag. Only truly applicable rules are included. */
const items = computed(() => {
    const entries: { slot: string; icon: string; modifier?: string }[] = [
        { slot: "deckSize", icon: "deck", modifier: "info" },
        { slot: "copies", icon: "card", modifier: "info" }
    ];
    if (props.capabilities.maxSideboardSize > 0) {
        entries.push({ slot: "sideboard", icon: "card" });
    }
    if (props.capabilities.requiresCommander) {
        entries.push({ slot: "commander", icon: "register" });
    }
    if (props.capabilities.enforcesColorIdentity) {
        entries.push({ slot: "colorIdentity", icon: "color" });
    }
    if (props.capabilities.hasSignatureSpell) {
        entries.push({ slot: "signatureSpell", icon: "spell" });
    }
    return entries;
});
</script>

<template>
    <accordion>
        <template #head>{{ $t("components.deck_format_capabilities.title") }}</template>
        <template #body>
            <form-legend :items="items">
                <template #deckSize>
                    <i18n-t
                        v-if="isExactDeckSize"
                        keypath="components.deck_format_capabilities.deck_size_exact"
                        scope="global"
                    >
                        <template #count
                            ><strong>{{ capabilities.minDeckSize }}</strong></template
                        >
                    </i18n-t>
                    <i18n-t v-else keypath="components.deck_format_capabilities.deck_size_min" scope="global">
                        <template #count
                            ><strong>{{ capabilities.minDeckSize }}</strong></template
                        >
                    </i18n-t>
                </template>
                <template #copies>
                    <template v-if="capabilities.isSingleton">
                        {{ $t("components.deck_format_capabilities.singleton") }}
                    </template>
                    <i18n-t v-else keypath="components.deck_format_capabilities.max_copies" scope="global">
                        <template #count
                            ><strong>{{ capabilities.maxCopies }}</strong></template
                        >
                    </i18n-t>
                </template>
                <template #sideboard>
                    <i18n-t keypath="components.deck_format_capabilities.sideboard" scope="global">
                        <template #count
                            ><strong>{{ capabilities.maxSideboardSize }}</strong></template
                        >
                    </i18n-t>
                </template>
                <template #commander>
                    <template v-if="capabilities.maxCommanders > 1">
                        {{ $t("components.deck_format_capabilities.commander_partners") }}
                    </template>
                    <template v-else>
                        {{ $t("components.deck_format_capabilities.commander") }}
                    </template>
                </template>
                <template #colorIdentity>{{ $t("components.deck_format_capabilities.color_identity") }}</template>
                <template #signatureSpell>{{ $t("components.deck_format_capabilities.signature_spell") }}</template>
            </form-legend>
        </template>
    </accordion>
</template>
