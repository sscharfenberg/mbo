<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, useId, useTemplateRef, watch } from "vue";
import { useI18n } from "vue-i18n";
import Icon from "Components/UI/Icon.vue";
const { t } = useI18n();
interface SelectOption {
    value: string;
    label: string;
}
const props = defineProps<{
    options: SelectOption[];
    selected?: string;
    placeholder?: string;
    addonIcon?: string;
}>();
const effectivePlaceholder = computed(() => props.placeholder ?? t("form.elements.select_placeholder"));
const emit = defineEmits(["change"]);
const uid = useId();
const anchorName = `--select-${uid}`;
const buttonId = `select-button-${uid}`;
const listboxId = `select-listbox-${uid}`;
const menuOpen = ref(false);
const selectedValue = ref(props.selected);
const dropdown = useTemplateRef<HTMLDivElement>("dropdown");
const selectedLabel = computed(() => props.options.find(o => o.value === selectedValue.value)?.label);
/**
 * Sets the selected value and emits a change event if the value changed.
 * Always closes the menu afterwards.
 *
 * @param value - The value of the selected option.
 */
const select = (value: string) => {
    if (value !== selectedValue.value) {
        selectedValue.value = value;
        emit("change", value);
    }
    menuOpen.value = false;
};
/**
 * Toggles the dropdown menu open or closed.
 */
const toggleMenu = () => {
    menuOpen.value = !menuOpen.value;
};

/**
 * Scrolls the selected option into view after the enter transition completes.
 *
 * @param el - The transitioned element.
 */
const onAfterEnter = (el: Element) => {
    const _option = el.querySelector(`button[data-value="${selectedValue.value}"]`);
    _option?.scrollIntoView();
};
/**
 * Closes the dropdown when a click occurs outside the component.
 *
 * @param ev - The native click event.
 */
const onClickOutSide = (ev: MouseEvent) => {
    if (!(dropdown.value === ev.target || dropdown.value?.contains(ev.target as Node))) {
        menuOpen.value = false;
    }
};
watch(
    () => props.selected,
    value => {
        selectedValue.value = value;
    },
    { immediate: true }
);

onMounted(() => {
    document.addEventListener("click", onClickOutSide);
});
onUnmounted(() => {
    document.removeEventListener("click", onClickOutSide);
});
</script>

<template>
    <div class="form-select" ref="dropdown">
        <span v-if="addonIcon" class="form-select__addon"><icon :name="addonIcon" /></span>
        <button
            type="button"
            :id="buttonId"
            class="form-select__button"
            :class="{ open: menuOpen }"
            :style="{ 'anchor-name': anchorName }"
            :aria-expanded="menuOpen"
            :aria-controls="listboxId"
            aria-haspopup="listbox"
            @click.prevent="toggleMenu"
        >
            <span v-if="selectedValue">{{ selectedLabel }}</span>
            <span v-else>{{ effectivePlaceholder }}</span>
            <span class="form-select__caret" aria-hidden="true" />
        </button>
        <button
            type="button"
            class="form-select__clear"
            v-if="selectedValue"
            :style="{ 'position-anchor': anchorName }"
            @click.prevent="select('')"
            :aria-label="$t('form.elements.clear_select')"
        >
            <icon name="clear" aria-hidden="true" />
        </button>
        <Transition name="slide-down" @after-enter="onAfterEnter">
            <div
                v-if="menuOpen"
                :id="listboxId"
                role="listbox"
                :aria-labelledby="buttonId"
                class="form-select__options"
                :style="{ 'position-anchor': anchorName }"
            >
                <div class="form-select__scroll">
                    <button
                        v-for="option in options"
                        :key="option.value"
                        type="button"
                        role="option"
                        :data-value="option.value"
                        :aria-selected="selectedValue === option.value"
                        :class="{
                            'form-select__option--selected': selectedValue === option.value
                        }"
                        class="form-select__option"
                        @click.prevent="select(option.value)"
                    >
                        {{ option.label }}
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped lang="scss">
.slide-down-enter-active,
.slide-down-leave-active {
    transition: clip-path 0.15s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
    clip-path: inset(0 0 100% 0);
}

.slide-down-enter-to,
.slide-down-leave-from {
    clip-path: inset(0 0 0% 0);
}
</style>
