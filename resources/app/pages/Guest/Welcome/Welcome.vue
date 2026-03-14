<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import Stats from "@/pages/Guest/Welcome/Stats.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
import Paragraph from "Components/UI/Paragraph.vue";

defineProps<{
    oracleCards: { num: number; size: number };
    defaultCards: { num: number; size: number };
    sets: number;
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
    <stats
        v-if="symbols.length"
        :oracle-cards="oracleCards"
        :default-cards="defaultCards"
        :sets="sets"
        :symbols="symbols"
    />
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
