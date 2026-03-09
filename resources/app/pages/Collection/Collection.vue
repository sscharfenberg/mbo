<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";
</script>

<template>
    <Head
        ><title>{{ $t("pages.collection.title") }}</title></Head
    >
    <headline>
        <icon name="collection" :size="3" />
        {{ $t("pages.collection.title") }}
    </headline>
    <Link href="/collection/containers/new">{{ $t("pages.new_container.link") }}</Link>
    <Link href="/collection/containers">My Containers</Link>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. At cumque dignissimos eius nihil perferendis possimus
        praesentium qui soluta ullam voluptatibus. Cumque ea libero non possimus soluta. Accusantium ad adipisci aliquid
        at atque commodi, consectetur corporis deserunt, dolorem dolores eos expedita id illo ipsum laborum maxime
        mollitia nam nobis nostrum perferendis quia quod ratione recusandae reiciendis rem sapiente tempora, temporibus
        vero voluptate voluptatum. A ab adipisci aliquid amet architecto asperiores assumenda beatae dolor dolores eius
        et excepturi hic ipsa itaque labore laboriosam laudantium minima minus nam nulla, numquam obcaecati officiis
        quas qui quos reiciendis repellat repellendus reprehenderit sequi suscipit tempora voluptate?
    </p>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. At cumque dignissimos eius nihil perferendis possimus
        praesentium qui soluta ullam voluptatibus. Cumque ea libero non possimus soluta. Accusantium ad adipisci aliquid
        at atque commodi, consectetur corporis deserunt, dolorem dolores eos expedita id illo ipsum laborum maxime
        mollitia nam nobis nostrum perferendis quia quod ratione recusandae reiciendis rem sapiente tempora, temporibus
        vero voluptate voluptatum. A ab adipisci aliquid amet architecto asperiores assumenda beatae dolor dolores eius
        et excepturi hic ipsa itaque labore laboriosam laudantium minima minus nam nulla, numquam obcaecati officiis
        quas qui quos reiciendis repellat repellendus reprehenderit sequi suscipit tempora voluptate?
    </p>
</template>
