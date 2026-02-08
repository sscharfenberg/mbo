<script setup lang="ts">
/******************************************************************************
 * Icon Component
 * these svg icons use an external svg sprite sheet
 * and reference the id in that file
 * icon styles are located in @/styles/components/_icon.scss
 *****************************************************************************/
import { computed } from "vue";
const props = defineProps({
    name: String,
    size: {
        type: Number,
        default: 2,
        validator(value: number) {
            return [0, 1, 2, 3, 4, 5].includes(value);
        }
    },
    rotate: {
        type: Boolean,
        default: false
    },
    required: {
        type: Boolean,
        default: false
    },
    additionalClasses: {
        type: Array,
        default: () => []
    }
});
const cssClasses = computed(() => {
    const sizeClasses = ["tiny", "small", "medium", "large", "xlarge", "max"];
    const cssClasses = [...new Set([...["icon"], ...props.additionalClasses])];
    cssClasses.push(sizeClasses[props.size]);
    cssClasses.push(props.name);
    if (props.rotate) cssClasses.push("rotate");
    if (props.required) cssClasses.push("required");
    return cssClasses.join(" ");
});
</script>

<template>
    <svg :class="cssClasses">
        <use :xlink:href="`#${name}`"></use>
    </svg>
</template>

<style lang="scss" scoped>
/**
 * styles are located in
 * resources/app/styles/components/_icons.scss
 */
</style>
