<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import Paragraph from "Components/UI/Paragraph.vue";
import Stats from "Components/UI/Stats/Stats.vue";
import StatsItem from "Components/UI/Stats/StatsItem.vue";
import { useFormatting } from "Composables/useFormatting.ts";

const { formatDecimals, formatBytes } = useFormatting();

defineProps<{
    stats: {
        oracleCards: { num: number; size: number };
        defaultCards: { num: number; size: number };
        sets: number;
        artists: number;
        artCrops: { num: number; size: number };
        cardImages: { num: number; size: number };
    };
    symbols: { path: string; english: string }[];
}>();
</script>

<template>
    <Head
        ><title>{{ $t("pages.welcome.title") }}</title></Head
    >
    <headline>
        <icon name="home" :size="3" />
        {{ $t("pages.welcome.claim") }}
    </headline>
    <paragraph>{{ $t("pages.welcome.intro") }}</paragraph>
    <headline :size="3">{{ $t("pages.welcome.stats.title") }}</headline>
    <stats v-if="symbols.length">
        <stats-item>
            <template #title>{{ $t("pages.welcome.stats.oracle.title") }}</template>
            <template #icon>
                <img :src="symbols[0]!.path" :alt="symbols[0]!.english" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(stats.oracleCards.num) }}</template>
            <template #detail><icon name="file" />{{ formatBytes(stats.oracleCards.size) }}</template>
            <template #explanation>{{ $t("pages.welcome.stats.oracle.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.stats.default.title") }}</template>
            <template #icon>
                <img :src="symbols[1]!.path" :alt="symbols[1]!.english" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(stats.defaultCards.num) }}</template>
            <template #detail><icon name="file" />{{ formatBytes(stats.defaultCards.size) }}</template>
            <template #explanation>{{ $t("pages.welcome.stats.default.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.stats.sets.title") }}</template>
            <template #icon>
                <img :src="symbols[2]!.path" :alt="symbols[2]!.english" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(stats.sets) }}</template>
            <template #explanation>{{ $t("pages.welcome.stats.sets.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.stats.artists.title") }}</template>
            <template #icon>
                <img :src="symbols[3]!.path" :alt="symbols[3]!.english" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(stats.artists) }}</template>
            <template #explanation>{{ $t("pages.welcome.stats.artists.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.stats.artCrops.title") }}</template>
            <template #icon>
                <img :src="symbols[4]!.path" :alt="symbols[4]!.english" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(stats.artCrops.num) }}</template>
            <template #detail><icon name="file" />{{ formatBytes(stats.artCrops.size) }}</template>
            <template #explanation>{{ $t("pages.welcome.stats.artCrops.explanation") }}</template>
        </stats-item>
        <stats-item>
            <template #title>{{ $t("pages.welcome.stats.cardImages.title") }}</template>
            <template #icon>
                <img :src="symbols[5]!.path" :alt="symbols[5]!.english" class="icon medium" />
            </template>
            <template #value>{{ formatDecimals(stats.cardImages.num) }}</template>
            <template #detail><icon name="file" />{{ formatBytes(stats.cardImages.size) }}</template>
            <template #explanation>{{ $t("pages.welcome.stats.cardImages.explanation") }}</template>
        </stats-item>
    </stats>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis consequatur deleniti distinctio dolores eum
        eveniet nihil quibusdam saepe ut voluptates? Alias ipsam ipsum numquam sit unde. Alias dolore esse molestiae
        nobis nostrum quidem quis, repudiandae vero voluptates? Accusantium aliquid asperiores, aut delectus ducimus
        earum hic ipsam molestiae obcaecati rerum sequi tempora voluptas voluptates? A accusamus aut consequatur
        doloremque eius eligendi, esse impedit ipsam labore laudantium molestias neque quod sed sequi similique suscipit
        vel velit veniam veritatis voluptas voluptatem voluptates voluptatum. Asperiores ducimus ea earum exercitationem
        itaque nam quam soluta vel. Aspernatur esse facilis impedit magnam mollitia nesciunt, possimus repellat
        voluptate?
    </p>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis consequatur deleniti distinctio dolores eum
        eveniet nihil quibusdam saepe ut voluptates? Alias ipsam ipsum numquam sit unde. Alias dolore esse molestiae
        nobis nostrum quidem quis, repudiandae vero voluptates? Accusantium aliquid asperiores, aut delectus ducimus
        earum hic ipsam molestiae obcaecati rerum sequi tempora voluptas voluptates? A accusamus aut consequatur
        doloremque eius eligendi, esse impedit ipsam labore laudantium molestias neque quod sed sequi similique suscipit
        vel velit veniam veritatis voluptas voluptatem voluptates voluptatum. Asperiores ducimus ea earum exercitationem
        itaque nam quam soluta vel. Aspernatur esse facilis impedit magnam mollitia nesciunt, possimus repellat
        voluptate?
    </p>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis consequatur deleniti distinctio dolores eum
        eveniet nihil quibusdam saepe ut voluptates? Alias ipsam ipsum numquam sit unde. Alias dolore esse molestiae
        nobis nostrum quidem quis, repudiandae vero voluptates? Accusantium aliquid asperiores, aut delectus ducimus
        earum hic ipsam molestiae obcaecati rerum sequi tempora voluptas voluptates? A accusamus aut consequatur
        doloremque eius eligendi, esse impedit ipsam labore laudantium molestias neque quod sed sequi similique suscipit
        vel velit veniam veritatis voluptas voluptatem voluptates voluptatum. Asperiores ducimus ea earum exercitationem
        itaque nam quam soluta vel. Aspernatur esse facilis impedit magnam mollitia nesciunt, possimus repellat
        voluptate?
    </p>
</template>
