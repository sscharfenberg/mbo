<script setup lang="ts">
import { useId } from "vue";
import Icon from "Components/UI/Icon.vue";
import PopOver from "Components/UI/PopOver.vue";
const popoverId = useId();
/** Close the action popover programmatically. */
function closePopover(): void {
    const el = document.getElementById(popoverId);
    if (el !== null) el.hidePopover();
}
</script>

<template>
    <pop-over
        icon="more"
        aria-label="Actions"
        class-string="popover-button--rounded popover-button--tiny"
        :reference="popoverId"
        width="14rem"
    >
        <ul class="popover-list">
            <li>
                <button type="button" class="popover-list-item" @click="closePopover">
                    <icon name="edit" :size="1" />
                    Edit
                </button>
            </li>
            <li>
                <button type="button" class="popover-list-item" @click="closePopover">
                    <icon name="image-search" :size="1" />
                    Change printing
                </button>
            </li>
            <li>
                <button type="button" class="popover-list-item popover-list-item--caution" @click="closePopover">
                    <icon name="delete" :size="1" />
                    Remove
                </button>
            </li>
        </ul>
    </pop-over>
</template>
