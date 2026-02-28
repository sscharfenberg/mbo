<script setup lang="ts">
import { nextTick, onMounted, ref, useAttrs } from "vue";
import { OTPInput as VueOTPInput } from "vue-input-otp";
import Slots from "./Slots.vue";

defineOptions({ inheritAttrs: false });

const model = defineModel<string>({ default: "" });
const attrs = useAttrs();
const emit = defineEmits<{
    complete: [value: string];
}>();

const otpInputRef = ref<{ $el?: Element } | null>(null);

const props = withDefaults(
    defineProps<{
        id?: string;
        name?: string;
        maxlength?: number;
        inputmode?: "numeric" | "text";
        autocomplete?: string;
        autofocus?: boolean;
    }>(),
    {
        maxlength: 6,
        inputmode: "numeric",
        autocomplete: "one-time-code",
        autofocus: false
    }
);

onMounted(async () => {
    if (!props.autofocus) return;

    await nextTick();
    const input =
        (props.id ? document.getElementById(props.id) : null)?.closest("input") ||
        document.querySelector<HTMLInputElement>(`input#${props.id ?? ""}`) ||
        otpInputRef.value?.$el?.querySelector<HTMLInputElement>("[data-input-otp]") ||
        document.querySelector<HTMLInputElement>("[data-input-otp]");
    input?.focus();
});
</script>

<template>
    <VueOTPInput
        ref="otpInputRef"
        v-model="model"
        :id="id"
        :name="name"
        :maxlength="maxlength"
        :inputmode="inputmode"
        :autocomplete="autocomplete"
        :autofocus="autofocus"
        container-class="form-input otp"
        v-bind="attrs"
        @complete="emit('complete', $event)"
    >
        <template #default="{ slots }">
            <Slots :slots="slots" :inputmode="inputmode" />
        </template>
    </VueOTPInput>
</template>
