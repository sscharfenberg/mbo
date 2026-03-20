<script setup lang="ts">
import { ref } from "vue";
import FormGroup from "Components/Form/FormGroup.vue";
const props = defineProps<{
    /** CardLanguage enum values. */
    languages: string[];
}>();
const selectedLangauge = ref(props.languages[0]);
const flagSrc = (lang: string): string => new URL(`../../../assets/flags/${lang}.svg`, import.meta.url).href;
</script>

<template>
    <form-group :label="$t('form.fields.language')" :required="true">
        <div class="lang__wrapper">
            <span class="lang" v-for="lang in languages" :key="lang">
                <input
                    type="radio"
                    :id="`language-${lang}`"
                    name="language"
                    :value="lang"
                    :checked="lang === selectedLangauge"
                    class="sr-only"
                />
                <label
                    :for="`language-${lang}`"
                    :aria-label="`${lang.toUpperCase()} - ${$t('enums.card_languages.' + lang)}`"
                >
                    <img
                        :src="flagSrc(lang)"
                        :alt="`${lang.toUpperCase()} - ${$t('enums.card_languages.' + lang)}`"
                        v-tooltip="`${lang.toUpperCase()} - ${$t('enums.card_languages.' + lang)}`"
                        aria-hidden="true"
                    />
                </label>
            </span>
        </div>
    </form-group>
</template>

<style lang="scss" scoped>
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/sizes" as s;
@use "Abstracts/timings" as ti;

.lang {
    &__wrapper {
        display: flex;
        flex-wrap: wrap;

        gap: 1ch;
    }

    label {
        display: flex;
        align-items: center;

        padding: map.get(s.$form, "language", "padding");
        border: map.get(s.$form, "language", "border") solid map.get(c.$form, "language", "border");
        gap: 0.5rem;

        background-color: map.get(c.$form, "language", "background");
        border-radius: map.get(s.$form, "language", "radius");

        cursor: pointer;

        transition:
            background-color map.get(ti.$timings, "fast") linear,
            color map.get(ti.$timings, "fast") linear,
            border-color map.get(ti.$timings, "fast") linear;

        img {
            width: 27px;
            height: 18px;
        }

        &:hover {
            background-color: map.get(c.$form, "language", "background-hover");
        }
    }
}

input:checked + label {
    background-color: map.get(c.$form, "language", "background-checked");
    border-color: map.get(c.$form, "language", "border-checked");
}
</style>
