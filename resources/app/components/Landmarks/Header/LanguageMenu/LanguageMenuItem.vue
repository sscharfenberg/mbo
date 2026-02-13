<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { getI18n, loadLocaleMessages, setI18nLanguage } from "@/i18n.ts";

const props = defineProps({
    locale: {
        type: String,
        required: true
    }
});

const { locale: currentLocale } = useI18n();

const emit = defineEmits(["close"]);

const onLocaleChange = async () => {
    if (currentLocale.value === props.locale) return;

    emit("close");
    const i18n = getI18n();

    // 1) lazy load messages for the new locale
    await loadLocaleMessages(i18n, props.locale);

    // 2) switch the active locale
    setI18nLanguage(i18n, props.locale);

    // 3) persist on the backend via native fetch
    const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? "";
    await fetch("/lang/" + props.locale, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json"
        }
    });
};
</script>

<template>
    <li>
        <button
            type="button"
            class="popover-list-item"
            :class="{ 'popover-list-item--selected': currentLocale === locale }"
            @click="onLocaleChange"
        >
            <img v-if="locale === 'de'" class="flag" src="./de.svg" alt="Deutsche Sprache" />
            <img v-if="locale === 'en'" class="flag" src="./en.svg" alt="English Language" />
            {{ locale }}
        </button>
    </li>
</template>

<style scoped lang="scss">
.flag {
    width: 24px;
    height: 14.4px;
}
</style>
