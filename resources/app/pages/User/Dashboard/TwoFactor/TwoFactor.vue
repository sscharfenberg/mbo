<script setup lang="ts">
import Badge from "Components/UI/Badge.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import { useTwoFactorAuth } from "Composables/useTwoFactorAuth.ts";
import TwoFactorDisabled from "./TwoFactorDisabled.vue";
import TwoFactorEnabled from "./TwoFactorEnabled.vue";
const { twoFactorEnabled } = useTwoFactorAuth();
</script>

<template>
    <headline :size="3">
        {{ $t("pages.dashboard.two_factor.headline") }}
        <template #right>
            <badge v-if="!twoFactorEnabled" type="warning"><icon name="key" />{{ $t("state.disabled") }}</badge>
            <badge v-else type="success"><icon name="security" />{{ $t("state.enabled") }}</badge>
        </template>
    </headline>
    <TwoFactorDisabled v-if="!twoFactorEnabled" />
    <TwoFactorEnabled v-else />
</template>
